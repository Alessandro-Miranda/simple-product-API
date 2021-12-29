<?php

    use App\Model\DownloadProducts;
    use Dotenv\Dotenv;

    require 'vendor/autoload.php';

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    $download = new DownloadProducts();

    $download
        ->getPriceInformations()
        ->getProductInformations()
        ->saveProducts();
?>