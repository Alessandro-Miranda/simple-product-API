<?php
    namespace App\Model;

    use App\Lib\Database;

    class ProductGateway
    {
        private $db;
        private $rows;

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
                header("HTTP/1.1 500 Internal Server Error");
                echo $err;
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
                header("HTTP/1.1 500 Internal Server Error");
                echo $err;
            }
        }

        private function getActualPageRange($limit, $page)
        {
            return ($limit * $page) - $limit;
        }
    }
?>