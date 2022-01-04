<?php

use App\Model\DownloadProducts;
use App\Utils\LoadEnv;

require 'vendor/autoload.php';

LoadEnv::load(__DIR__);

$download = new DownloadProducts();

$download
    ->getPriceInformations()
    ->getProductInformations()
    ->saveProducts();