<?php

    use App\Model\DownloadProducts;
    use Dotenv\Dotenv;

    require 'vendor/autoload.php';

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    $download = new DownloadProducts();

    try
    {
        $response = $download
            ->getPriceInformations()
            ->getProductInformations()
            ->saveProducts();

        echo var_dump($response);
    }
    catch(\PDOException $err)
    {
        echo "Ocorreu um erro inesperado";
    }
    
?>