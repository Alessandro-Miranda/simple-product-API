<?php
    namespace App\Controller;

    use App\Model\ProductGateway;

    class ProductController
    {
        private $model;
        private $filter;
        private $limit = 10;
        private $page = 1;

        public function __construct($queryString)
        {
            $this->model = new ProductGateway();

            if(empty($queryString))
            {
                $this->getAllProducts();
            }

            $this->explodeQuery($queryString);
        }

        public function getAllProducts()
        {
            $result = $this->model->findAll();

            header("HTTP/1.1 200 OK");
            echo json_encode($result);
        }

        public function categoryFilter()
        {

        }

        public function discountFilter()
        {

        }

        public function productNameFilter()
        {

        }

        public function explodeQuery($query)
        {
            // $exploadedQuery = explode('&', $query);
            parse_str($query, $exploadedQuery);

            echo var_dump($exploadedQuery);
        }
    }
?>