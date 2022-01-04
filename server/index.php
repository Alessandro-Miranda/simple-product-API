<?php
require 'vendor/autoload.php';

use App\Controller\ProductController;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

if($_SERVER['REQUEST_METHOD'] === 'POST')
{
    header("HTTP/1.1 405 Method Not Allowed");
    exit();
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$hasProductPath = array_search('products', explode('/', $uri));

if($hasProductPath === false)
{
    header("HTTP/1.1 404 Not Found");
    exit();
}

$queryString = $_SERVER['QUERY_STRING'];

$productController = new ProductController();

if(empty($queryString))
{
    $productController->getAllProducts();
}
else
{
    $productController->filterProducts($queryString);
}