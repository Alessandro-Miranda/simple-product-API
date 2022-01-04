<?php

namespace App\Interfaces;

interface IProductGateway
{
    /**
     * Solicita ao banco o retorno de todos os produtos passando o limite de itens e a pagina atual a ser buscada
     *
     * @param int $limit
     * @param int $page
     * @return array|false
     */
    public function findAll(int $limit, int $page): array|false;

    /**
     * Solicita a busca dos produtos com base nos filtros passados via query string
     *
     * @param array $filters
     * @param int   $limit
     * @param int   $page
     * @return array|false
     */
    public function filterProductsByQueryString(array $filters, int $limit, int $page): array|false;

    /**
     * Obtém o número de resultados existentes para o filtro solicitado
     *
     * @param array $filters
     * @return int
     */
    public function getNumberOfRows(array $filters): int;

    /**
     * Obtém a paginação com base no limite de resultados por busca e com base no filtro solicitado
     *
     * @param int   $limit
     * @param array $filters
     * @return float
     */
    public function totalPages($limit, $filters): float;

}