<?php
namespace App\Repositories;

use App\Utils\ErrorMessages;
use App\Utils\RegisterLog;
use IDatabaseRepository;
use PDO;
use PDOException;

class Database implements IDatabaseRepository
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
            $this->PDO = new PDO(
                "mysql:host=$host;dbname=$database",
                $username,
                $password,
                array(PDO::ATTR_PERSISTENT => true)
            );
        }
        catch(PDOException $err)
        {
            RegisterLog::RegisterLog("Database Exception", $err->getMessage(), "exceptions.log");
            ErrorMessages::returnMessageError(500, "Internal Server Error",$err, "Erro conectando ao banco de dados");
        }
    }

    /**
     * Obtém a instância de acesso ao banco
     *
     * @return PDO
     */
    public function getConnection(): PDO
    {
        return $this->PDO;
    }

    /**
     * Encontra todos os produtos limitando a busca com os valores passados
     *
     * @param int $actualPageLimitInit
     * @param int $limit
     * @return array|false
     */
    public function findAllProducts($actualPageLimitInit, $limit): array|false
    {
        $stmt = $this->PDO->prepare("SELECT * FROM produtos LIMIT $actualPageLimitInit,$limit");
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    /**
     * Filtra os produtos com base na query string passada
     *
     * @param array $filter
     * @param int   $actualPageLimitInit
     * @param int   $limit
     * @return array|false
     */
    public function filterProducts($filter, $actualPageLimitInit, $limit): array|false
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

    /**
     * Verifica a quantidade de linhas para o resultado buscado via query string ou de todos os registros do banco
     *
     * @param array $filters
     * @return mixed
     */
    public function numberOfRows($filters): mixed
    {
        $stmt = "";

        if(!empty($filters))
        {
            $whereFilters = $this->performWhereFilters($filters);
            $stmt = $this->PDO->prepare("SELECT COUNT(*) FROM produtos WHERE {$whereFilters}");
        }
        else
        {
            $stmt = $this->PDO->prepare("SELECT COUNT(*) FROM produtos");
        }
        
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }

    /**
     * Cria a clausula where para realizar o filtro especificado na queryString passada
     *
     * @param array $filters
     * @return string
     */
    private function performWhereFilters($filters): string
    {
        $whereFilter = array();

        foreach($filters as $key => $value)
        {
            if($key === 'discountTag')
            {
                array_push(
                    $whereFilter,
                    "{$key} BETWEEN " . intval($value) - 10 . " AND " . intval($value)
                );
                continue;
            }

            if($key === 'productName')
            {
                array_push($whereFilter, "{$key} LIKE '%{$value}%'");
                continue;
            }

            array_push(
                $whereFilter,
                $this->createWhereRegex($key, $value)
            );
        }

        return implode(" AND ", $whereFilter);
    }

    /**
     * Cria a regex responsável por encontrar os produtos pertencentes à categoria solicitada pois um produtos pode ter diversas categorias registradas
     *
     * @param string $columnName
     * @param string $valuesToRegexCreate
     * @return void
     */
    private function createWhereRegex($columnName, $valuesToCreateRegex)
    {
        return "{$columnName} REGEXP '" . implode("|", explode(" ", $valuesToCreateRegex)) . "'";
    }
}