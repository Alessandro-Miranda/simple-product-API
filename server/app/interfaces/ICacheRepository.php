<?php

namespace App\Interfaces;

interface ICacheRepository
{
    /**
     * Lê o arquivo em cache e se passado o parâmetro $return, será retornado a informação; senão, armazena na propriedade $file para ser utilizada por outros métodos da classe
     *
     * @throws Exception
     * @param string      $fileName
     * @param bool|null   $isReturnable
     * @param string|null $folderPath
     * @return mixed
     */
    public function readCacheFile(string $filename, bool $isReturnable, ?string $folder): mixed;

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