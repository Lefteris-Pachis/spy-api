<?php

namespace App\Domain\ValueObjects;

class Agency
{
    private string $agency;

    private const VALID_AGENCIES = ['CIA', 'MI6', 'KGB'];

    public function __construct(string $agency)
    {
        if (!in_array($agency, self::VALID_AGENCIES)) {
            throw new \InvalidArgumentException("Invalid agency");
        }
        $this->agency = $agency;
    }

    public function getValue(): string
    {
        return $this->agency;
    }
}
