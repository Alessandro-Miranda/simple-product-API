<?php

namespace App\Utils;

use Dotenv\Dotenv;

class LoadEnv
{
    private $envValues;
    
    public static function load(string $path, ?string $file = '.env')
    {
        if(PHP_SAPI === 'cli' || !preg_match('/localhost|127.0.0.1/', $_SERVER['HTTP_HOST']))
        {
            $self = new self;
            $self->loadEnvFile($path, $file);
            $self->setEnvs();
        }
        else
        {
            $dotenv = Dotenv::createImmutable($path);
            $dotenv->safeLoad();
        }
    }

    /**
     * Carrega o arquivo .env
     *
     * @param string $path - Path da raíz onde o arquivo .env está localizado
     * @param string $file
     * @return void
     */
    public function loadEnvFile(string $path, string $file)
    {
        $this->envValues = str_replace("\"", "", file_get_contents($path . '\\' . $file));
        $this->envValues = array_filter(explode("\r\n", $this->envValues));
    }

    /**
     * Seta as variáveis de ambiente na seção
     *
     * @return void
     */
    public function setEnvs()
    {
        foreach($this->envValues as $value)
        {
            if(preg_match('/\s|#(?:\s|\w)\w+/' , $value))
            {
                continue;
            }

            $envArray = explode("=", $value);
            
            $_ENV[$envArray[0]] = $envArray[1];
        }
    }
}