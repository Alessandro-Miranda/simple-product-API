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
            $paginationInfos = $this->paginationInfos();

            array_push($result, $paginationInfos);

            return $result;
        }

        public function filterProducts($queryString)
        {
            $this->explodeQuery($queryString);
            $this->pageExists();
            
            $result = $this->model->filterProductsByQueryString($this->filters, $this->limit, $this->page);
            $paginationInfos = $this->paginationInfos();

            array_push($result, $paginationInfos);

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
                "rows" => $this->model->getNumberOfRows(),
                "actual_page" => $this->page,
                "total_pages" => $this->model->totalPages($this->limit)
            );
        }

        public function pageExists()
        {
            if($this->model->totalPages($this->limit) < $this->page)
            {
                header("HTTP/1.1 404 Not Found");
                exit();
            }
        }
    }
?>