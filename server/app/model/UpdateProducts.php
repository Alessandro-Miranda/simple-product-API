<?php
namespace App\Model;

use App\Repositories\Database;

class UpdateProducts extends DownloadProducts
{
    function __construct()
    {
        parent::__construct();
    }

    public function update()
    {
        $db = new Database();
        $db->updateProducts($this->products);
    }
}