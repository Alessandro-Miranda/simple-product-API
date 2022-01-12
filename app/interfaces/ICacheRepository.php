<?php

namespace App\Interfaces;

interface ICacheRepository
{
    /**
     * Lê o arquivo em cache transformando um array associativo e retorna a informação
     *
     * @throws Exception
     * @param string      $fileName
     * @param string|null $folderPath
     * @return mixed
     */
    public function readCacheFile(string $filename, ?string $folder): mixed;

    /**
     * Salva o arquivo na pasta especificada ou, por padrão, na pasta chamada cache
     * 
     * @throws Exception
     *
     * @param string      $informationsToSave
     * @param string      $fileName
     * @param string|null $folderPath
     * @return void
     */
    public function save(mixed $informationsToSave, string $filename, ?string $folder): void;

    /**
     * Verifica se tem um arquivo de cache válido
     *
     * @param string      $fileName
     * @param string|null $folderPath
     * @return boolean
     */
    public function hasValidCache(string $filename, ?string $folder): bool;
}