<?php

namespace App\Core;

class DotEnvLoader
{
    public static function load($path)
    {
        if (!file_exists($path)) {
            // Em desenvolvimento, mostrar erro. Em produção, você pode querer apenas logar
            throw new \InvalidArgumentException(sprintf('Arquivo .env não encontrado: %s', $path));
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Ignora comentários
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Verifica se a linha contém um sinal de igual
            if (strpos($line, '=') === false) {
                continue;
            }

            // Divide a linha em nome e valor
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Remove aspas do valor se existirem
            if (preg_match('/^"(.*)"$/', $value, $matches)) {
                $value = $matches[1];
            } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
                $value = $matches[1];
            }

            // Define a variável de ambiente se ainda não estiver definida
            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}