<?php

namespace App\Repositories;

use App\Interfaces\ICacheRepository;
use App\Utils\RegisterLog;
use Exception;

class Cache implements ICacheRepository
{
    private $file;

    /**
     * Lê o arquivo em cache e se passado o parâmetro $return, será retornado a informação; senão, armazena na propriedade $file para ser utilizada por outros métodos da classe
     *
     * @throws Exception
     * @param string      $fileName
     * @param bool|null   $isReturnable
     * @param string|null $folderPath
     * @return mixed
     */
    public function readCacheFile(string $filename, ?bool $isReturnable = false, ?string $folder = "cache"): mixed
    {
        if(!file_exists($folder . DIRECTORY_SEPARATOR . $filename))
        {
            throw new Exception("The cache file doesn't exists");
        }

        $file = file_get_contents($this->getFilePath($filename, $folder));

        if($isReturnable)
        {
            return $file;
        }
        else
        {
            $this->file = $file;   
        }
    }

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
    public function save(mixed $informationsToSave, string $filename, ?string $folder = "cache"): void
    {
        $path = $this->getFilePath($filename, $folder);

        $state = file_put_contents($path, $informationsToSave);

        if($state === false)
        {
            RegisterLog::RegisterLog('Error', 'Failed to save cache file', 'cache-infos.log');
        }
    }

    /**
     * Verifica se tem um arquivo de cache válido
     *
     * @param string      $fileName
     * @param string|null $folderPath
     * @return boolean
     */
    public function hasValidCache(string $filename, ?string $folder = "cache"): bool
    {
        $isValidCache = false;

        if(file_exists($this->getFilePath($filename, $folder)) && is_readable($this->getFilePath($filename, $folder)))
        {
            $cache = file_get_contents($filename);

            if($cache['expires'] < time())
            {
                $isValidCache = true;
            }
            else
            {
                $state = unlink($this->getFilePath($filename, $folder));

                if(!$state)
                {
                    RegisterLog::RegisterLog('error', 'Failed to remove expired cache file', 'cache-infos.log');
                }
            }
        }

        return $isValidCache;
    }

    /**
     * Converte o arquivo de cache para o formato json
     *
     * @return void
     */
    public function toJson(): string | false
    {
        return json_encode($this->file, JSON_UNESCAPED_SLASHES);
    }

    private function getFilePath(string $filename, string $folder)
    {
        return $folder . DIRECTORY_SEPARATOR . $filename;
    }
}