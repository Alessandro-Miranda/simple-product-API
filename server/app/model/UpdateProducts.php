<?php
namespace App\Model;

use App\Repositories\Database;
use App\Repositories\UpdateProducts as RepositoriesUpdateProducts;
use App\Utils\RegisterLog;
use PDOException;

class UpdateProducts extends DownloadProducts
{
    private $db;

    function __construct()
    {
        parent::__construct();

        $this->db = new Database();
    }

    public function update()
    {
        $db = new RepositoriesUpdateProducts();
        $db->update($this->products);
    }
}