<?php

namespace App\Models;

class Article
{
    private string $title;
    private string $description;
    private string $createdAt;
    private ?int $id;

    public function __construct(string $title, string $description, string $createdAt, ?int $id = null)
    {
        $this->title = $title;
        $this->description = $description;
        $this->createdAt = $createdAt;
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}