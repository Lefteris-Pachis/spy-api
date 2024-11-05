<?php

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

class Name
{
    private string $name;
    private string $surname;

    public function __construct(string $name, string $surname)
    {
        if (empty($name) || empty($surname)) {
            throw new InvalidArgumentException("Name components cannot be empty.");
        }

        $this->name = $name;
        $this->surname = $surname;
    }

    public function getValue(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }
}
