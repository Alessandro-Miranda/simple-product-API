<?php

namespace App\Interfaces;

interface IDatabaseRepository
{
    /**
     * Obtém a instância de acesso ao banco
     *
     * @return PDO
     */
    public function getConnection(): \PDO;

    /**
     * Encontra todos os produtos limitando a busca com os valores passados
     *
     * @param int $actualPageLimitInit
     * @param int $limit
     * @return array|false
     */
    public function findAllProducts(int $actualPageLimitInit, int $limit): array|false;

    /**
     * Filtra os produtos com base na query string passada
     *
     * @param array $filter
     * @param int   $actualPageLimitInit
     * @param int   $limit
     * @return array|false
     */
    public function filterProducts(array $filter, int $actualPageLimitInit, int $limit): array|false;

    /**
     * Verifica se o produto já existe na base e se não existir, insere
     *
     * @param int $productID
     * @param array $columnsAndValues
     * @return mixed
     */
    public function insertProductIfNotExists(int $productID, array $columnsAndValues): mixed;

    /**
     * Atualiza os produtos já existentes e, caso não exista, insere na base
     *
     * @param array $products
     * @return void
     */
    public function updateProducts(array $products): void;
    
    /**
     * Verifica a quantidade de linhas para o resultado buscado via query string ou de todos os registros do banco
     *
     * @param array $filters
     * @return mixed
     */
    public function numberOfRows(?array $filters): mixed;
}