<?php
    namespace App\Lib;

    use PDO;
    use PDOException;

    class Database
    {
        public $PDO;
        
        public function __construct()
        {
            $host = $_ENV['HOST'];
            $database = $_ENV['DATABASE'];
            $username = $_ENV['USERNAME'];
            $password = $_ENV['PASSWORD'];
            echo $host;
            try
            {
                $this->PDO = new PDO("mysql:host=$host;dbname´=$database", $username, $password);
            }
            catch(PDOException $err)
            {
                exit($err->getMessage());
            }
        }

        public function getConnection()
        {
            return $this->PDO;
        }
    }
?>