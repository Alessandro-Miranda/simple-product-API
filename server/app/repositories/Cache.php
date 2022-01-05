<?php

namespace App\Repositories;

use App\Interfaces\ICacheRepository;
use App\Utils\RegisterLog;
use Exception;

class Cache implements ICacheRepository
{
    /**
     * Lê o arquivo em cache transformando um array associativo e retorna a informação
     *
     * @throws Exception
     * @param string      $fileName
     * @param string|null $folderPath
     * @return mixed
     */
    public function readCacheFile(string $filename, ?string $folder = "cache"): mixed
    {
        if(!file_exists($folder . DIRECTORY_SEPARATOR . $filename))
        {
            throw new Exception("The cache file doesn't exists");
        }

        $file = json_decode(file_get_contents($this->getFilePath($filename, $folder)), JSON_OBJECT_AS_ARRAY);

        return $file['content'];
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

        if(!is_dir($folder))
        {
            mkdir($folder);
        }

        $infos = json_encode(array(
            'expires' => time() + 60,
            'content' => $informationsToSave
        ), JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES);

        $state = file_put_contents($path, $infos);

        if($state === false)
        {
            throw new Exception("An Error has occured saving the cache file");
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

        if(file_exists($this->getFilePath($filename, $folder)))
        {
            $cache = json_decode(file_get_contents($this->getFilePath($filename, $folder), true), JSON_UNESCAPED_SLASHES | JSON_HEX_QUOT);
            
            if($cache->{'expires'} < time())
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

    private function getFilePath(string $filename, string $folder)
    {
        return realpath('./') . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $filename;
    }
}