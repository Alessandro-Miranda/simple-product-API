<?php
    namespace App\Model;

    use App\Lib\Database;

    class ProductGateway
    {
        private $db;

        public function __construct($db = null)
        {
            if(is_null($db))
            {
                $db = new Database();
            }

            $this->db = $db;
        }

        public function findAll()
        {

        }
    }
?>