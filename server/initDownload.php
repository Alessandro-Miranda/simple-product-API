<?php

    use App\Model\DownloadProducts;
    use Dotenv\Dotenv;

    require 'vendor/autoload.php';

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    $download = new DownloadProducts();

    try
    {
        $download
            ->getPriceInformations()
            ->getProductInformations()
            ->saveProducts();
    }
    catch(\PDOException $err)
    {
        echo "Ocorreu um erro inesperado";
    }
    
?>