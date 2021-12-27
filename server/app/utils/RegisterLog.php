<?php
    namespace App\Utils;
    
    class RegisterLog
    {
        public static function RegisterLog($infoLevel, $message, $fileName)
        {
            $date = date('l jS \of F Y h:i:s A');
            $msg = "{$date} - " . strtoupper($infoLevel) . " - Message: {$message} \n";

            file_put_contents($fileName, $msg, FILE_APPEND);
        }
    }
?>