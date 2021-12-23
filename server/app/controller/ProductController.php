<?php
    namespace App\Controller;

    use App\Model\ProductGateway;

    class ProductController
    {
        private $model;
        private $filters = array();
        private $limit = 10;
        private $page = 1;

        public function __construct()
        {
            $this->model = new ProductGateway();
        }

        public function getAllProducts()
        {
            $result = $this->model->findAll($this->limit, $this->page);

            return $result;
        }

        public function filterProducts($queryString)
        {
            $this->explodeQuery($queryString);
            
            $result = $this->model->filterProductsByQueryString($this->filters, $this->limit, $this->page);

            return $result;
        }

        public function explodeQuery($query)
        {
            parse_str($query, $exploadedQuery);
            
            foreach($exploadedQuery as $key => $value)
            {
                if($key === 'limit')
                {
                    
                    $this->limit = intval($value) > 100 ? 100 : intval($value);
                    continue;
                }
                if($key === 'page')
                {
                    $this->page = intval($value);
                    continue;
                }
                
                $this->filters[$key] = $value;
            }
        }
    }
?>