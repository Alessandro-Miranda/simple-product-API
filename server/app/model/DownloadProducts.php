<?php
    namespace App\Model;
    
    set_time_limit(3600);

use App\Repositories\ProductsBase;
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

            echo "baixando os produtos... <br />";
        }

        /**
         * Obtém as informações: 
         *
         * @return this
         */
        public function getPriceInformations()
        {   
            echo "Obtendo as informações de preço<br />";
            foreach($this->skus as $value)
            {
                $this->prepareRequest("https://{$this->accountName}.vtexcommercestable.com.br/api/catalog_system/pub/products/variations/{$value}");

                $prices = curl_exec($this->curl);
                $error = curl_error($this->curl);

                $this->checkError("Error downloading product price", $error);

                $pricesDecoded = json_decode($prices, true);
                $keys = array(0 => "productId");

                if($pricesDecoded === "ProductId not found")
                {
                    continue;
                }

                array_push(
                    $this->productsPrice,
                    $this->setProductInformations($keys, $pricesDecoded, $pricesDecoded["skus"], "ProductPrice")
                );

                $this->clearVariables($pricesDecoded);
                $this->sleep();
            }

            return $this;
        }

        /**
         * Obtém as informações: url da imagem. url da página de destino, categorias, id e nome do produto com base no EAN
         *
         * @return this
         */
        public function getProductInformations()
        {
            echo "Buscando as informações adicionais do produto";

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

                $this->clearVariables($jsonDecoded);
                $this->sleep();
            }
            
            return $this;
        }

        public function saveProducts()
        {
            $db = new ProductsBase();

            $db->insert($this->products, "produtos");
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
                    $discountTag = $sku["listPrice"] !== 0 ? round(100 - ($sku["bestPrice"] / $sku["listPrice"]) * 100) : 0;

                    $productInformation["sellerID"] = $sku["sellerId"];
                    $productInformation["listPrice"] = $sku["listPrice"];
                    $productInformation["bestPrice"] = $sku["bestPrice"];
                    $productInformation["discountTag"] = $discountTag;
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
                        $productInformation["sellerID"] = $price["sellerID"];
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
                echo "Aguardando o script reiniciar";
                $this->counter=0;
                sleep(20);
            }
            else
            {
                $this->counter++;
            }
        }

        /**
         * Verifica se a houve algum erro na requisição e, caso tenha, registra o log e finaliza a execução
         *
         * @param [string] $message
         * @param [Error] $error
         * @return void
         */
        private function checkError($message, $error)
        {
            if($error)
            {
                echo "Ocorreu um erro, cheque os logs para maiores informações";
                RegisterLog::RegisterLog($message, $error, "exceptions.log");
                exit();
            }
        }

        /**
         * Prepara a requisição a ser feita
         *
         * @param [string] $url
         * @return void
         */
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

        private function clearVariables($var)
        {
            unset($var);
        }

        function __destruct()
        {
            curl_close($this->curl);
        }
    }
?>