<?php

namespace App\Entity;

class Folder
{
    public function __construct(
        private ?int $id,
        private string $name,
        private string $nameHash,
        private int $updated,
    )
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getNameHash(): string
    {
        return $this->nameHash;
    }

    public function getUpdated(): int
    {
        return $this->updated;
    }
}