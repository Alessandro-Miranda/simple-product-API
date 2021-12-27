<?php
    set_time_limit(1800);
    
    require 'vendor/autoload.php';

    use App\Utils\RegisterLog;
    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    (function () {
        $skus = explode("\r\n", file_get_contents('skus.txt'));
        $eans = explode("\r\n", file_get_contents('eans.txt'));

        $curl = curl_init();
        $accountName = $_ENV["VTEXACCOUNTNAME"];

        $counter = 0;
        $productsPrice = array();
        $products = array();

        // Percorre cada sku fazendo uma requisição para obter o id, sku, nome seller e preços dos produtoss
        foreach($skus as $value)
        {
            $actualProduct = array();
            prepareRequest($curl, "https://{$accountName}.vtexcommercestable.com.br/api/catalog_system/pub/products/variations/{$value}");
        
            $prices = curl_exec($curl);
            $error = curl_error($curl);

            if($error)
            {
                RegisterLog::RegisterLog("Error downloading product price", $error, "exceptions.log");
                exit();
            }

            $pricesDecoded = json_decode($prices, true);

            if($pricesDecoded === "ProductId not found")
            {
                continue;
            }

            $actualProduct["productID"] = $pricesDecoded["productId"];
            $actualProduct["sku"] = $value;
            $actualProduct["productName"] = $pricesDecoded["name"];
            $actualProduct["sellerID"] = $pricesDecoded["salesChannel"];

            foreach($pricesDecoded["skus"] as $sku)
            {
                $actualProduct["listPrice"] = $sku["listPrice"];
                $actualProduct["bestPrice"] = $sku["bestPrice"];
                $actualProduct["discountTag"] = round(100 - ($sku["bestPrice"] / $sku["listPrice"]) * 100);
            }

            array_push($productsPrice, $actualProduct);

            if($counter === 500)
            {
                sleep(30);
                $counter=0;
            }
            else
            {
                $counter++;
            }
        }

        $counter = 0;

        foreach($eans as $value)
        {
            // Obtém a url da imagem e da página de destino e a lista de categorias
            prepareRequest($curl, "https://{$accountName}.vtexcommercestable.com.br/api/catalog_system/pvt/sku/stockkeepingunitbyean/{$value}");
    
            $basicInfos = curl_exec($curl);
            $basicInfosErr = curl_error($curl);

            $productInformation = array();
            
            if($basicInfosErr)
            {
                RegisterLog::RegisterLog("Error downloading products", $basicInfosErr, "exceptions.log");
                exit();
            }

            $jsonDecoded = json_decode($basicInfos, true);
           
            $productInformation["imageUrl"] = $jsonDecoded["ImageUrl"];
            $productInformation["detailUrl"] = $jsonDecoded["DetailUrl"];
            $productInformation["productCategories"] = implode("," ,$jsonDecoded["ProductCategories"]);

            foreach($productsPrice as $price)
            {
                if(in_array($jsonDecoded["ProductId"], $price))
                {
                    $productInformation["listPrice"] = $price["listPrice"];
                    $productInformation["bestPrice"] = $price["bestPrice"];
                    $productInformation["discountTag"] = round(100 - ($price["bestPrice"] / $price["listPrice"]) * 100);

                    break;
                }
            }

            array_push($products, $productInformation);

            if($counter === 1000)
            {
                sleep(30);
                $counter=0;
            }
            else
            {
                $counter++;
            }
        }
        
        curl_close($curl);
    })();

    function prepareRequest($curl, $url)
    {
        $appKey = $_ENV["XVTEXAPIAppKey"];
        $appToken = $_ENV["XVTEXAPIAppToken"];
        
        curl_setopt_array($curl, [
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
                "X-VTEX-API-AppKey: {$appKey}",
                "X-VTEX-API-AppToken: {$appToken}"
            ]
        ]);
    }
?>