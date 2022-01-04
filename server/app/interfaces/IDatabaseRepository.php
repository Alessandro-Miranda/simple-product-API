<?php

interface IDatabaseRepository
{
    public function getConnection(): \PDO;
    public function findAllProducts(int $actualPageLimitInit, int $limit): array|false;
    public function filterProducts(array $filter, int $actualPageLimitInit, int $limit): array|false;
    public function numberOfRows(?array $filters): mixed;
}