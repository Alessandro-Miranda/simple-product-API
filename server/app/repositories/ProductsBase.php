<?php
    namespace App\Repositories;

    use App\Utils\RegisterLog;
    use Error;
    use Exception;
    use PDO;

    class ProductsBase extends Database
    {
        function __construct()
        {
            parent::__construct();
        }

        public function insert($values, $tableName)
        {
            try
            {
                $this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->PDO->beginTransaction();

                foreach($values as $actualItem)
                {
                    $lenght = count($actualItem);
                    $counter = 0;
                    $columns = "";
                    $columnValues = "";

                    array_walk($actualItem, function($item, $key) use (&$columns, &$columnValues, &$counter, &$lenght) {
                        
                        if($counter >= $lenght - 1)
                        {
                            $columns .= $key;
                            $columnValues .= gettype($item) === "string" ? "'{$item}'" : $item;
                        }
                        else
                        {
                            $columns .= "{$key},";
                            $columnValues .= gettype($item) === "string" ? "'{$item}'," : "{$item},";
                        }

                        $counter++;
                    });

                    $stmt = "INSERT INTO {$tableName} ({$columns}) VALUES ({$columnValues})";
                    
                    $this->PDO->exec($stmt);
                }

                if($this->PDO->commit())
                {
                    RegisterLog::RegisterLog("Insert completion", "Produtos inseridos no banco", "Insert-infos.log");
                }
                else
                {
                    throw new Error("Erro ao commitar a transação");
                }

            }
            catch(Exception $err)
            {
                RegisterLog::RegisterLog(
                    "Database Exception (Insert Transaction)",
                    $err->getMessage(),
                    "exceptions.log"
                );

                $this->PDO->rollBack();
                exit();
            }
        }
    }
?>