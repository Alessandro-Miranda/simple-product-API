<?php
use App\Model\UpdateProducts;
use Dotenv\Dotenv;

require 'vendor/autoload.php';

if(PHP_SAPI === 'cli')
{
    $dotenvValues = str_replace("\"", "", file_get_contents('.env'));
    $exploadedValues = array_filter(explode("\r\n", $dotenvValues));

    foreach($exploadedValues as $value)
    {
        if(preg_match('/\s|#^\d/' , $value))
        {
            continue;
        }

        $envArray = explode("=", $value);
        
        $_ENV[$envArray[0]] = $envArray[1];
    }
}
else
{
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
}

$update = new UpdateProducts();

$update
    ->getPriceInformations()
    ->getProductInformations()
    ->update();