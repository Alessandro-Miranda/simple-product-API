<?php
    namespace App\Model;

    use App\Lib\Database;
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
                RegisterLog::RegisterExceptionLog("Database Exception", $err->getMessage());
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
                RegisterLog::RegisterExceptionLog("Database Exception", $err->getMessage());
                ErrorMessages::returnMessageError(500, "Internal Server Error", $err);
            }
        }

        public function getNumberOfRows()
        {
            $tableRows = $this->db->numberOfRows();

            return $tableRows;
        }

        public function totalPages($limit)
        {
            $rows = $this->getNumberOfRows();

            return ceil($rows / $limit);
        }

        private function getActualPageRange($limit, $page)
        {
            return ($limit * $page) - $limit;
        }
    }
?>