<?php
namespace App\Utils;

use DateTime;
use DateTimeZone;

class RegisterLog
{
    public static function RegisterLog($infoLevel, $message, $fileName)
    {
        $date = new DateTime("now", new DateTimeZone('America/Sao_Paulo'));
        $date = $date->format('l jS \of F Y h:i:s A');
        $msg = "{$date} - " . strtoupper($infoLevel) . " - Message: {$message} \n";

        file_put_contents($fileName, $msg, FILE_APPEND);
    }
}