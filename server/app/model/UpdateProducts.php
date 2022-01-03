<?php
namespace App\Model;

use App\Repositories\Database;
use App\Repositories\UpdateProducts as RepositoriesUpdateProducts;

class UpdateProducts extends DownloadProducts
{
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