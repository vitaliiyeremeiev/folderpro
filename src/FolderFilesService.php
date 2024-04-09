<?php

namespace App;

use App\Entity\File;
use App\Entity\Folder;
use App\Repository\FileRepositoryInterface;
use App\Repository\FolderRepositoryInterface;

class FolderFilesService
{
    public function __construct(
        private readonly FileRepositoryInterface $fileRepository,
        private readonly FolderRepositoryInterface $folderRepository)
    {
    }

    public function getFilesCollectionByFolder(string $folder): array
    {
        $filesCollection = $this->getFilesInFolder($folder);
        $lastFolderUpdate = filemtime($folder);

        $folderHash = hash('sha512', $folder);
        $savedFolder = $this->getSavedFolder($folderHash);

        // The folder was not saved before
        if ($this->isNewFolder($savedFolder)) {
            $this->saveNewFolder($folder, $folderHash, $lastFolderUpdate, $filesCollection);
        }

        // The folder was changed since last saving
        if ($this->isFolderChanged($savedFolder, $lastFolderUpdate)) {
            $this->updateFolder($folder, $folderHash, $lastFolderUpdate, $filesCollection, $savedFolder);
        }

        // If folder wasn't changed just return files collection
        return $filesCollection;
    }
    private function isNewFolder(?Folder $savedFolder): bool
    {
        return is_null($savedFolder);
    }

    private function isFolderChanged(?Folder $savedFolder, int $lastFolderUpdate): bool
    {
        return !is_null($savedFolder) && $savedFolder->getUpdated() !== $lastFolderUpdate;
    }

    private function getFilesInFolder(string $folder): array
    {
        $files = scandir($folder);
        $filesCollection = [];
        foreach ($files as $filename) {
            if (filetype($folder . DIRECTORY_SEPARATOR . $filename)==='file') {
                $filesCollection[] = new File(null,null, $filename, filesize($folder . DIRECTORY_SEPARATOR . $filename), filemtime($folder . DIRECTORY_SEPARATOR . $filename));
            }
        }

        return $filesCollection;
    }

    private function findFileByName(array $fileCollection, string $name): bool
    {
        foreach ($fileCollection as $file) {
            if ($file->getName() === $name) {
                return true;
            }
        }
        return false;
    }

    private function findFileToChangeByName(array $fileCollection, File $folderFile): int|null|bool
    {
        foreach ($fileCollection as $file) {
            if ($file->getName() === $folderFile->getName()) {
                if ($file->getUpdated() !== $folderFile->getUpdated()) {
                    return $file->getId();      // File changed. Needs to update it in DB
                }
                return false;                   // File wasn't changed. No needs to update it in DB
            }
        }
        return null;                            // File wasn't saved before. Needs to save it into DB
    }

    private function getSavedFolder(string $folderHash): ?Folder
    {
        return  $this->folderRepository->loadFolderByHash($folderHash);
    }

    private function saveNewFolder(string $folder, string $folderHash, int $lastFolderUpdate, array $filesCollection): void
    {
        $this->folderRepository->setAutocommitFalse();
        $folId = $this->saveFolder(null, $folder, $folderHash, $lastFolderUpdate);
        $this->saveFiles($filesCollection,$folId);
        $this->folderRepository->commit();
    }

    private function updateFolder(string $folder, string $folderHash, int $lastFolderUpdate, array $filesCollection, Folder $savedFolder): void
    {
        $savedFiles = $this->fileRepository->loadFilesByFolderId($savedFolder->getId());
        $this->folderRepository->setAutocommitFalse();
        $this->deleteFiles($savedFiles, $filesCollection);
        $folId = $this->saveFolder($savedFolder->getId(), $folder, $folderHash, $lastFolderUpdate);
        $this->updateFiles($savedFiles, $filesCollection, $folId);
        $this->folderRepository->commit();
    }

    private function saveFolder(?int $folId, string $folder, string $folderHash, int $lastFolderUpdate): int
    {
        $folder = new Folder($folId, $folder, $folderHash, $lastFolderUpdate);
        return $this->folderRepository->save($folder);
    }

    private function saveFiles(array $filesCollection, int $folId): void
    {
        foreach ($filesCollection as $file) {
            $file->setFolderId($folId);
            $this->fileRepository->save($file);
        }
    }

    private function deleteFiles(?array $savedFiles, array $filesCollection): void
    {
        $deleteFromDb = [];
        foreach ($savedFiles as $file) {
            if (!$this->findFileByName($filesCollection, $file->getName())) {
                $deleteFromDb[] = $file->getId();
            }
        }
        if (count($deleteFromDb) > 0) {
            $this->fileRepository->delete($deleteFromDb);
        }
    }

    private function updateFiles(?array $savedFiles, array $filesCollection, int $folId): void
    {
        $updateFiles = [];
        foreach ($filesCollection as $file) {
            $filId = $this->findFileToChangeByName($savedFiles, $file);
            if (is_null($filId) || $filId) {
                $file->setId($filId);
                $updateFiles[] = $file;
            }
        }
        if (count($updateFiles) > 0) {
            $this->saveFiles($updateFiles, $folId);
        }
    }
}