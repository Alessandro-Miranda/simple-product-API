<?php

namespace App\Repositories;

use App\Utils\RegisterLog;
use Error;
use PDO;
use PDOException;

class UpdateProducts extends Database
{
    function __construct()
    {
        parent::__construct();
    }

    function update($products)
    {
        try
        {
            $this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->PDO->beginTransaction();

            foreach($products as $value)
            {
                $whereFilter = "productID={$value["productId"]}";
                $columnAndValue = array();
    
                array_walk(
                    $value,
                    function($item, $key) use (&$columnAndValue) {
                        array_push($columnAndValue, "{$key}='{$item}'");
                    }
                );
                
                $setColumnValues = implode(",", $columnAndValue);
                $this->PDO->exec("UPDATE produtos SET {$setColumnValues} WHERE {$whereFilter}");
            }

            if($this->PDO->commit())
            {
                RegisterLog::RegisterLog('Info', 'Atualização dos produtos concluída com sucesso', 'update-info.log');
            }
            else
            {
                throw new Error("Erro ao commitar a transação");
            }
        }
        catch(PDOException $err)
        {
            $errorMessage = $err->getMessage() . " On product {$value["productId"]}";
            RegisterLog::RegisterLog('error', $errorMessage, 'update-info.log');
        }
    }
}