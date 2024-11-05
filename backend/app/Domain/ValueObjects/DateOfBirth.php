<?php

namespace App\Domain\ValueObjects;


use DateTimeImmutable;

class DateOfBirth
{
    private DateTimeImmutable $value;

    public function __construct(string $date)
    {
        $this->value = new \DateTimeImmutable($date);
    }

    public function getValue(): \DateTimeImmutable
    {
        return $this->value;
    }
}
