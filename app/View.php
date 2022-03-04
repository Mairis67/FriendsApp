<?php

namespace App;

class View
{
    private string $path;
    private array $vars;

    public function __construct(string $path, array $vars = [])
    {
        $this->path = $path;
        $this->vars = $vars;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getVars(): array
    {
        return $this->vars;
    }
}