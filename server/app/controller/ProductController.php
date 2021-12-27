<?php
    namespace App\Controller;

    use App\Model\ProductGateway;
    use App\Utils\ErrorMessages;

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
            $this->sendResponse($this->model->findAll($this->limit, $this->page));
        }

        public function filterProducts($queryString)
        {
            $this->explodeQuery($queryString);
            $this->pageExists();
            
            $this->sendResponse($this->model->filterProductsByQueryString($this->filters, $this->limit, $this->page));
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
                
                if(
                    $key !== 'productName' ||
                    $key !== 'discountTag' ||
                    $key !== 'productCategories' ||
                    $key !== 'productID' ||
                    $key !== 'sku'
                )
                {
                    continue;
                }

                $this->filters[$key] = $value;
            }
        }

        public function paginationInfos()
        {
            return array(
                "totalProducts" => $this->model->getNumberOfRows(),
                "actualPage" => $this->page,
                "totalPages" => $this->model->totalPages($this->limit),
                "perPage" => $this->limit
            );
        }

        public function pageExists()
        {
            if($this->model->totalPages($this->limit) < $this->page)
            {
                ErrorMessages::returnMessageError(404, "Not Found", "Page not found", "Página não existe ou o valor passado é diferente de um número");
            }
        }

        public function sendResponse($data)
        {
            $data = array(
                "data" => $data,
                "pagination" => $this->paginationInfos()
            );
            
            header("Content-Type: application/json");
            
            echo json_encode($data, JSON_UNESCAPED_SLASHES);
        }
    }
?>