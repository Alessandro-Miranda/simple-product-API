<?php
    require 'vendor/autoload.php';

    use App\Controller\ProductController;
    use Dotenv\Dotenv;

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    $uri = explode('/', $uri);

    if($uri[1] != 'products')
    {
        header("HTTP/1.1 404 Not Found");
        exit();
    }

    $queryString = $_SERVER['QUERY_STRING'];

    new ProductController($queryString);
?>