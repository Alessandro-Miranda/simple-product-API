<?php
namespace App\Controller;

use App\Model\ProductGateway;
use App\Repositories\Cache;
use App\Utils\ErrorMessages;
use App\Utils\RegisterLog;
use Exception;

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
        $products = $this->model->findAll($this->limit, $this->page);
        
        $cache = new Cache();

        if($cache->hasValidCache('products.cache'))
        {
            try
            {
                $products = $cache->readCacheFile('products.cache');
                $this->sendResponse($products);
            }
            catch(Exception $err)
            {
                RegisterLog::RegisterLog('Error', $err->getMessage(), 'cache-infos.log');
            }
        }
        else
        {
            try
            {
                $cache->save($products, "products.cache");
                $this->sendResponse($products);
            }
            catch(Exception $err)
            {
                RegisterLog::RegisterLog('Error', $err->getMessage(), 'cache-infos.log');
            }
        }
    }

    public function filterProducts($queryString)
    {
        $this->explodeQuery($queryString);
        $this->pageExists();
        $this->sendResponse(
            $this->model->filterProductsByQueryString(
                $this->filters,
                $this->limit,
                $this->page
            )
        );
    }

    public function explodeQuery($query)
    {
        parse_str($query, $exploadedQuery);
        $paramsAllowed = array("productName", "discountTag", "productCategories", "productID", "sku");
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
            
            if(!in_array($key, $paramsAllowed))
            {
                continue;
            }

            $this->filters[$key] = $value;
        }
    }

    public function paginationInfos()
    {
        return array(
            "totalProducts" => $this->model->getNumberOfRows($this->filters),
            "actualPage" => $this->page,
            "totalPages" => $this->model->totalPages($this->limit, $this->filters),
            "perPage" => $this->limit
        );
    }

    public function pageExists()
    {
        if($this->model->totalPages($this->limit, $this->filters) < $this->page)
        {
            RegisterLog::RegisterLog("Warning", "Página solicitada não encontrada", "warnings.log");
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