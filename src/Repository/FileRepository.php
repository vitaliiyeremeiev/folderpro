<?php

namespace App\Repository;

use App\Entity\File;

class FileRepository implements FileRepositoryInterface
{
    public function __construct(private \mysqli $db)
    {
    }

    public function loadFilesByFolderId(int $folderId): ?array
    {
        $stmt = $this->db->prepare("SELECT fil_id, fol_id, fil_name, fil_size, fil_updated FROM file WHERE fol_id = ?");
        $stmt->bind_param("i", $folderId);
        $stmt->execute();
        return $this->getResults($stmt->get_result());
    }

    public function loadFilesByFolderName(string $folderName): ?array
    {
        $stmt = $this->db->prepare("SELECT fl.fil_id, fl.fol_id, fl.fil_name, fl.fil_size, fl.fil_updated FROM file AS fl INNER JOIN folder AS fd ON (fl.fol_id = fd.fol_id) WHERE fd.fol_name = ?");
        $stmt->bind_param("s", $folderName);
        $stmt->execute();
        return $this->getResults($stmt->get_result());
    }

    private function getResults(\mysqli_result|false $result): ?array
    {
        $files = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $files[] = new File($row['fil_id'], $row['fol_id'], $row['fil_name'], $row['fil_size'], $row['fil_updated']);
            }
        }

        return $files;
    }

    public function save(File $file): int
    {
        $stmt = $this->db->prepare("INSERT INTO file (fil_id, fol_id, fil_name, fil_size, fil_updated) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE fil_size=?, fil_updated=?");
        $stmt->execute([$file->getId(), $file->getFolderId(), $file->getName(), $file->getSize(), $file->getUpdated(), $file->getSize(), $file->getUpdated()]);
        return mysqli_insert_id($this->db);
    }

    public function delete(array $fileIdCollection): bool
    {
        $inExpression = implode(',', $fileIdCollection);
        $stmt = $this->db->prepare("DELETE FROM file WHERE fil_id IN ({$inExpression})");
        return $stmt->execute();
    }
}