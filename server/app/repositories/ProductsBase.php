<?php

namespace App\Repositories;

use App\Interfaces\IProductsBase;
use App\Utils\RegisterLog;
use Error;
use PDO;
use PDOException;

class ProductsBase extends Database implements IProductsBase
{
    function __construct()
    {
        parent::__construct();
    }

    /**
     * Realiza a inserção dos produtos no banco
     *
     * @param array  $values
     * @param string $tableName
     * @return void
     */
    public function insertProducts($values, $tableName): void
    {
        try
        {
            $this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->PDO->beginTransaction();

            foreach($values as $actualItem)
            {
                $columns = array();
                $columnValues = array();

                // Separa as chaves do array como coluna da tabela e os valores para criar a query de insert
                array_walk(
                    $actualItem,
                    function($item, $key) use (&$columns, &$columnValues) {

                        $itemFormated = gettype($item) === "string" ? "'{$item}'" : $item;

                        array_push($columnValues, $itemFormated);
                        array_push($columns, $key);
                    }
                );

                $teste1 = implode(",", $columns);
                $teste2 = implode(",", $columnValues);
                echo "INSERT INTO {$tableName} ({$teste1}) VALUES ({$teste2})";
                exit();
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
        catch(PDOException $err)
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