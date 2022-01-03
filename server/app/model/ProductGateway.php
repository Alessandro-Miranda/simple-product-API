<?php
namespace App\Model;

use App\Repositories\Database;
use App\Utils\ErrorMessages;
use App\Utils\RegisterLog;

class ProductGateway
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function findAll($limit, $page)
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

    public function filterProductsByQueryString($filters, $limit, $page)
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

    public function getNumberOfRows($filters)
    {
        $tableRows = $this->db->numberOfRows($filters);

        return $tableRows;
    }

    public function totalPages($limit, $filters)
    {
        $rows = $this->getNumberOfRows($filters);

        return ceil($rows / $limit);
    }

    private function getActualPageRange($limit, $page)
    {
        return ($limit * $page) - $limit;
    }
}