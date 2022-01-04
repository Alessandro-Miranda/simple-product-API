<?php

namespace App\Interfaces;

interface IProductsBase
{
    /**
     * Realiza a inserção dos produtos no banco
     *
     * @param array  $values
     * @param string $tableName
     * @return void
     */
    public function insertProducts(array $values, string $tableName): void;
}