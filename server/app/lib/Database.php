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

        public function findAllProducts($actualPageLimitInit, $limit)
        {
            $stmt = $this->PDO->prepare("SELECT * FROM produtos LIMIT $actualPageLimitInit,$limit");
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        }

        public function filterProducts($filter, $actualPageLimitInit, $limit)
        {
            if(empty($filter))
            {
                return $this->findAllProducts($actualPageLimitInit, $limit);
            }
            
            $whereFilters = $this->performWhereFilters($filter);

            $stmt = $this->PDO->prepare("SELECT * FROM produtos WHERE $whereFilters LIMIT $actualPageLimitInit,$limit");
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        }

        private function performWhereFilters($filters)
        {
            $whereFilter = '';

            foreach($filters as $key => $value)
            {
                if($key === 'discountTag')
                {
                    $whereFilter = "$key <= " . intval($value);
                    continue;
                }

                if($whereFilter === ''  )
                {
                    $whereFilter .= $key . " REGEXP '" . implode("|", explode(" ", $value)) . "'";
                }
                else
                {
                    $whereFilter .= "AND " . $key . " REGEXP '" . implode("|", explode(" ", $value)) . "'";
                }
            }
            
            return $whereFilter;
        }
    }
?>