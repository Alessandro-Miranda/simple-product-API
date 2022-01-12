<?php
namespace App\Model;

use App\Interfaces\IProductGateway;
use App\Repositories\Database;
use App\Utils\ErrorMessages;
use App\Utils\RegisterLog;

class ProductGateway implements IProductGateway
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Solicita ao banco o retorno de todos os produtos passando o limite de itens e a pagina atual a ser buscada
     *
     * @param int $limit
     * @param int $page
     * @return array|false
     */
    public function findAll($limit, $page): array|false
    {
        $actualPageLimit = $this->getActualPageRange($limit, $page);

        try
        {
            $result = $this->db->findAllProducts($actualPageLimit, $limit);

            return $result;
        }
        catch(\PDOException $err)
        {
            RegisterLog::RegisterLog("Database Exception", $err->getMessage(), "exceptions.log");
            ErrorMessages::returnMessageError(500, "Internal Server Error", $err);
        }
    }

    /**
     * Solicita a busca dos produtos com base nos filtros passados via query string
     *
     * @param array $filters
     * @param int   $limit
     * @param int   $page
     * @return array|false
     */
    public function filterProductsByQueryString($filters, $limit, $page): array|false
    {
        $actualPageLimit = $this->getActualPageRange($limit, $page);

        try
        {
            $result = $this->db->filterProducts($filters, $actualPageLimit, $limit);
            return $result;
        }
        catch(\PDOException $err)
        {
            RegisterLog::RegisterLog("Database Exception", $err->getMessage(), "exceptions.log");
            ErrorMessages::returnMessageError(500, "Internal Server Error", $err);
        }
    }

    /**
     * Obtém o número de resultados existentes para o filtro solicitado
     *
     * @param array $filters
     * @return int
     */
    public function getNumberOfRows($filters): int
    {
        $tableRows = $this->db->numberOfRows($filters);

        return $tableRows;
    }

    /**
     * Obtém a paginação com base no limite de resultados por busca e com base no filtro solicitado
     *
     * @param int   $limit
     * @param array $filters
     * @return float
     */
    public function totalPages($limit, $filters): float
    {
        $rows = $this->getNumberOfRows($filters);

        return ceil($rows / $limit);
    }

    /**
     * Obtém o limite inicial da clausula LiMIT das queries com base na página atual solicitada e o limite de resultados a serem retornados
     *
     * @param int $limit
     * @param int $page
     * @return int
     */
    private function getActualPageRange($limit, $page)
    {
        return ($limit * $page) - $limit;
    }
}