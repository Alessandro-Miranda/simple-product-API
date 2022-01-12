<?php
namespace App\Utils;

class ErrorMessages
{
    public static function returnMessageError($code, $header, $error, $message = 'Erro ao buscar/filtrar os produtos')
    {
        header("HTTP/1.1 {$code} ${header}");
        
        $message = array(
            "code" => $code,
            "message" => $message,
            "error" => $error
        );

        echo json_encode($message, JSON_UNESCAPED_SLASHES);
        exit();
    }
}