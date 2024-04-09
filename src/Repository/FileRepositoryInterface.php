<?php

namespace App\Repository;

use App\Entity\File;

interface FileRepositoryInterface
{
    public function loadFilesByFolderId(int $folderId): ?array;

    public function loadFilesByFolderName(string $folderName): ?array;

    public function save(File $file): int;

    public function delete(array $fileIdCollection): bool;
}