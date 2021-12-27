<?php
    namespace App\Lib;

    use App\Utils\ErrorMessages;
    use App\Utils\RegisterLog;
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
                RegisterLog::RegisterExceptionLog("Database Exception", $err->getMessage());
                ErrorMessages::returnMessageError(500, "Internal Server Error",$err, "Erro conectando ao banco de dados");
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

            $stmt = $this->PDO->prepare("SELECT * FROM produtos WHERE {$whereFilters} LIMIT {$actualPageLimitInit},{$limit}");
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $result;
        }

        public function numberOfRows()
        {
            $stmt = $this->PDO->query("SELECT COUNT(*) FROM produtos");
            $stmt->execute();

            return $stmt->fetchColumn();
        }

        private function performWhereFilters($filters)
        {
            $whereFilter = '';

            foreach($filters as $key => $value)
            {
                if($key === 'discountTag')
                {
                    $whereFilter = "{$key} <= " . intval($value);
                    continue;
                }

                if($key === 'productName')
                {
                    $whereFilter .= "{$key} LIKE '%{$value}%'";
                    continue;
                }

                if($whereFilter === ''  )
                {
                    $whereFilter .= $this->createWhereRegex($key, $value);
                }
                else
                {
                    $whereFilter .= " AND " . $this->createWhereRegex($key, $value);
                }
            }
            
            return $whereFilter;
        }

        private function createWhereRegex($columnName, $valuesToRegexCreate)
        {
            return "{$columnName} REGEXP '" . implode("|", explode(" ", $valuesToRegexCreate)) . "'";
        }
    }
?>