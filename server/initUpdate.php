<?php
use App\Model\UpdateProducts;
use App\Utils\LoadEnv;

require 'vendor/autoload.php';

LoadEnv::load(__DIR__);

$update = new UpdateProducts();

$update
    ->getPriceInformations()
    ->getProductInformations();

$update->update();