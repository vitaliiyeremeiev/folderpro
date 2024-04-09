<?php

namespace App\Entity;

class File
{
    public function __construct(
        private ?int $id,
        private ?int $folderId,
        private string $name,
        private int $size,
        private int $updated
    )
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getFolderId(): ?int
    {
        return $this->folderId;
    }

    public function setFolderId(?int $folderId): void
    {
        $this->folderId = $folderId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUpdated(): int
    {
        return $this->updated;
    }

    public function getSize(): int
    {
        return $this->size;
    }
}