<?php
    namespace App\Model;
    
    set_time_limit(3600);

    use App\Utils\RegisterLog;

    /**
    * Inicia o download dos produtos e salva no banco
    */
    class DownloadProducts
    {
        private $curl;
        private $skus;
        private $eans;
        private $accountName;
        private $appKey;
        private $appToken;
        private $products = array();
        private $productsPrice = array();
        
        function __construct()
        {
            $this->eans = explode("\r\n", file_get_contents('eans.txt'));
            $this->skus = explode("\r\n", file_get_contents('skus.txt'));
            $this->accountName = $_ENV["VTEXACCOUNTNAME"];
            $this->appKey = $_ENV["XVTEXAPIAppKey"];
            $this->appToken = $_ENV["XVTEXAPIAppToken"];
            $this->curl = curl_init();
            $this->counter = 0;
        }

        public function getPriceInformations()
        {   
            foreach($this->skus as $value)
            {
                $this->prepareRequest("https://{$this->accountName}.vtexcommercestable.com.br/api/catalog_system/pub/products/variations/{$value}");

                $prices = curl_exec($this->curl);
                $error = curl_error($this->curl);

                $this->checkError("Error downloading product price", $error);

                $pricesDecoded = json_decode($prices, true);
                $keys = array(
                    0 => "productId",
                    1 => "name",
                    2 => "salesChannel"
                );

                if($pricesDecoded === "ProductId not found")
                {
                    continue;
                }

                array_push(
                    $this->productsPrice,
                    $this->setProductInformations($keys, $pricesDecoded, $pricesDecoded["skus"], "ProductPrice")
                );

                $this->sleep();
            }

            return $this;
        }

        public function getProductInformations()
        {
            foreach($this->eans as $value)
            {
                $keys = array(
                    0 => "imageUrl",
                    1 => "detailUrl",
                    2 => "productCategories",
                    3 => "productId",
                    4 => "productName"
                );
                // Obtém a url da imagem e da página de destino e a lista de categorias
                $this->prepareRequest("https://{$this->accountName}.vtexcommercestable.com.br/api/catalog_system/pvt/sku/stockkeepingunitbyean/{$value}");
        
                $infos = curl_exec($this->curl);
                $error = curl_error($this->curl);
                
                $this->checkError("Error downloading products", $error);

                $jsonDecoded = json_decode($infos, true);
                
                array_push($this->products, $this->setProductInformations($keys, $jsonDecoded, $this->productsPrice));

                $this->sleep();
            }
            
            return $this;
        }

        public function saveProducts()
        {
            return $this->products;
        }

        /**
         * Preenche as informações de preço em um array temporário e, também, repassa as informações finais
         * para o array com todas as informações do produto e limpa as chaves do array temporário
         *
         * @param [array] $keys
         * @param [array] $json
         * @param [array] $internalArray
         * @param string $internalLoopType
         * @return array
         */
        private function setProductInformations($keys, $json, $internalArray, $internalLoopType = "ProductInfos")
        {
            $productInformation = array();
            
            if($internalLoopType === "ProductInfos")
            {
                $productInformation["sku"] = $json["Id"];
            }

            // Preenche os valores nas chaves passadas no paramêtro
            foreach($keys as $keyValue)
            {
                $formatedKey = $internalLoopType === "ProductInfos" ? ucfirst($keyValue) : $keyValue;

                if($keyValue === "productCategories")
                {
                    $productInformation[$keyValue] = implode(",", $json[$formatedKey]);
                    continue;
                }

                $productInformation[$keyValue] = $json[$formatedKey];
            }

            if($internalLoopType === "ProductPrice")
            {
                // Insere nas informações temporárias do produto os preços e descontos
                foreach($internalArray as $sku)
                {
                    $productInformation["listPrice"] = $sku["listPrice"];
                    $productInformation["bestPrice"] = $sku["bestPrice"];
                    $productInformation["discountTag"] = round(100 - ($sku["bestPrice"] / $sku["listPrice"]) * 100);
                }
            }
            else
            {
                /* 
                    Checa se o produto atual existe dentro da lista de preços e
                    transfere para o array que será retornado removendo a chave atual
                */
                foreach($internalArray as $key => $price)
                {
                    if(in_array($json["ProductId"], $price))
                    {
                        $productInformation["listPrice"] = $price["listPrice"];
                        $productInformation["bestPrice"] = $price["bestPrice"];
                        $productInformation["discountTag"] = $price["discountTag"];

                        unset($internalArray[$key]);
                        break;
                    }
                }
            }

            return $productInformation;
        }

        /**
         * Pausa a execução do script ao atingir 500 solicitações para evitar ultrapassar
         * 5 mil requisições por minuto
         *
         * @return void
         */
        private function sleep()
        {
            if($this->counter === 500)
            {
                sleep(20);
                $this->counter=0;
            }
            else
            {
                $this->counter++;
            }
        }

        private function checkError($message, $error)
        {
            if($error)
            {
                RegisterLog::RegisterLog($message, $error, "exceptions.log");
                exit();
            }
        }

        private function prepareRequest($url)
        {
            curl_setopt_array($this->curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "Accept: application/json",
                    "Content-type: application/json",
                    "X-VTEX-API-AppKey: {$this->appKey}",
                    "X-VTEX-API-AppToken: {$this->appToken}"
                ]
            ]);
        }

        function __destruct()
        {
            curl_close($this->curl);
        }
    }

    // (function () {

    //     $curl = curl_init();
    //     $accountName = $_ENV["VTEXACCOUNTNAME"];

    //     $counter = 0;
    //     $productsPrice = array();
    //     $products = array();

    //     // Percorre cada sku fazendo uma requisição para obter o id, sku, nome seller e preços dos produtoss
    //     foreach($skus as $value)
    //     {
    //         prepareRequest($curl, "https://{$accountName}.vtexcommercestable.com.br/api/catalog_system/pub/products/variations/{$value}");
        
    //         $prices = curl_exec($curl);
    //         $error = curl_error($curl);

    //         checkError("Error downloading product price", $error);

    //         $pricesDecoded = json_decode($prices, true);
    //         $keys = array(
    //             0 => "productId",
    //             1 => "name",
    //             2 => "salesChannel"
    //         );

    //         if($pricesDecoded === "ProductId not found")
    //         {
    //             continue;
    //         }

    //         array_push($productsPrice, setInformations($keys, $pricesDecoded, $pricesDecoded["skus"], "ProductPrice"));

    //         if($counter === 500)
    //         {
    //             sleep(30);
    //             $counter=0;
    //         }
    //         else
    //         {
    //             $counter++;
    //         }
    //     }

    //     $counter = 0;

    //     foreach($eans as $value)
    //     {
    //         $keys = array(
    //             0 => "imageUrl",
    //             1 => "detailUrl",
    //             2 => "productCategories"
    //         );
    //         // Obtém a url da imagem e da página de destino e a lista de categorias
    //         prepareRequest($curl, "https://{$accountName}.vtexcommercestable.com.br/api/catalog_system/pvt/sku/stockkeepingunitbyean/{$value}");
    
    //         $basicInfos = curl_exec($curl);
    //         $error = curl_error($curl);
            
    //         checkError("Error downloading products", $error);

    //         $jsonDecoded = json_decode($basicInfos, true);
           
    //         array_push($products, setInformations($keys, $jsonDecoded, $productsPrice));

    //         if($counter === 1000)
    //         {
    //             sleep(30);
    //             $counter=0;
    //         }
    //         else
    //         {
    //             $counter++;
    //         }
    //     }

    //     curl_close($curl);
    //     echo var_dump($products);
    // })();

    // function setInformations($keys, $json, $internalArray, $internalLoopType = "ProductInfos")
    // {
    //     $productInformation = array();

    //     foreach($keys as $keyValue)
    //     {
    //         $formatedKey = $internalLoopType === "ProductInfos" ? ucfirst($keyValue) : $keyValue;

    //         if($keyValue === "productCategories")
    //         {
    //             $productInformation[$keyValue] = implode(",", $json[$formatedKey]);
    //             continue;
    //         }

    //         $productInformation[$keyValue] = $json[$formatedKey];
    //     }

    //     if($internalLoopType === "ProductPrice")
    //     {
    //         // Insere nas informações temporárias do produto os preços e descontos
    //         foreach($internalArray as $sku)
    //         {
    //             $productInformation["sku"] = $sku["sku"];
    //             $productInformation["listPrice"] = $sku["listPrice"];
    //             $productInformation["bestPrice"] = $sku["bestPrice"];
    //             $productInformation["discountTag"] = round(100 - ($sku["bestPrice"] / $sku["listPrice"]) * 100);
    //         }
    //     }
    //     else
    //     {
    //         /* 
    //             Checa se o produto atual existe dentro da lista de preços e
    //             transfere para o array que será retornado removendo a chave atual
    //         */
    //         foreach($internalArray as $key => $price)
    //         {
    //             if(in_array($json["ProductId"], $price))
    //             {
    //                 $productInformation["listPrice"] = $price["listPrice"];
    //                 $productInformation["bestPrice"] = $price["bestPrice"];
    //                 $productInformation["discountTag"] = $price["discountTag"];

    //                 unset($internalArray[$key]);
    //                 break;
    //             }
    //         }
    //     }

    //     return $productInformation;
    // }
?>