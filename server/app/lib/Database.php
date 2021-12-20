<?php
    namespace App\Lib;

    use PDO;
    use PDOException;

    // @codeCoverageIgnoreStart
    class Database
    {
        public $PDO;
        
        public function __construct()
        {
            $host = $_ENV['HOST'];
            $database = $_ENV['DATABASE'];
            $username = $_ENV['USERNAME'];
            $password = $_ENV['PASSWORD'];
            
            try
            {
                $this->PDO = new pdo("mysql:host=$host;dbname=$database", $username, $password);
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

        public function findAllProducts()
        {
            $stmt = $this->PDO->prepare("SELECT * FROM produtos");
            $stmt->execute();

            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return $result;
        }

        public function findProductByName()
        {

        }
    }
    // @codeCoverageIgnoreEnd
?>