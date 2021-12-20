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

        public function findAll()
        {
            $result = $this->db->findAllProducts();

            return $result;
        }

        public function findByName($productName)
        {
            $result = $this->db->findProductByName();
            
            return $result;
        }
    }
?>