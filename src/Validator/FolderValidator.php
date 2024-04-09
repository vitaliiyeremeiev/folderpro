<?php

namespace App\Validator;

class FolderValidator
{
    /**
     * @param string $folder
     *
     * @return void
     * @throws \Exception
     */
    public static function validate(string $folder): void
    {
        if (!file_exists($folder) || !is_dir($folder)) {
            throw new \Exception('Error: Directory does not exist');
        }
    }
}