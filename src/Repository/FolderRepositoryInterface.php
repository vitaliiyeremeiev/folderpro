<?php

namespace App\Repository;

use App\Entity\Folder;

interface FolderRepositoryInterface
{
    public function loadFolderByHash($folderHash): ?Folder;

    public function save(Folder $folder): int;

    public function setAutocommitFalse(): void;

    public function commit(): void;
}