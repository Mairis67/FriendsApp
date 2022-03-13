<?php

namespace App\Models;

class User
{
    private string $email;
    private string $password;
    private string $createdAt;
    private ?int $id;
    private string $name;
    private string $surname;
    private string $birthday;

    public function __construct(string $name, string $surname, string $birthday, $email, string $password, string $createdAt, ?int $id)
    {
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = $createdAt;
        $this->id = $id;
        $this->name = $name;
        $this->surname = $surname;
        $this->birthday = $birthday;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getBirthday(): string
    {
        return $this->birthday;
    }
}