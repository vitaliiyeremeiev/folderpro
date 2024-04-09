<?php

namespace App\Repository;

use App\Entity\Folder;

class FolderRepository implements FolderRepositoryInterface
{
    public function __construct(private \mysqli $db)
    {
    }

    public function loadFolderByHash($folderHash): ?Folder
    {
        $stmt = $this->db->prepare("SELECT fol_id, fol_name, fol_name_hash, fol_updated FROM folder WHERE fol_name_hash = ?");
        $stmt->bind_param("s", $folderHash);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            return new Folder($row['fol_id'], $row['fol_name'], $row['fol_name_hash'], $row['fol_updated']);
        }

        return null;
    }

    public function save(Folder $folder): int
    {
        $stmt = $this->db->prepare("INSERT INTO folder (fol_id, fol_name, fol_name_hash, fol_updated) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE fol_updated=?");
        $stmt->execute([$folder->getId(), $folder->getName(), $folder->getNameHash(), $folder->getUpdated(), $folder->getUpdated()]);
        return mysqli_insert_id($this->db);
    }

    public function setAutocommitFalse(): void
    {
        $this->db->autocommit(FALSE);
    }

    public function commit(): void
    {
        $this->db->commit();
    }
}