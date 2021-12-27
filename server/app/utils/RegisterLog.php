<?php
    namespace App\Utils;
    
    class RegisterLog
    {
        public static function RegisterExceptionLog($infoLevel, $message, $fileName = 'exceptions.log')
        {
            $date = date('l jS \of F Y h:i:s A');
            $msg = "{$date} - " . strtoupper($infoLevel) . " - Message: {$message} \n";

            file_put_contents($fileName, $msg, FILE_APPEND);
        }
    }
?>