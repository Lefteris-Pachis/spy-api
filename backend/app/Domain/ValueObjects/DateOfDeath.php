<?php

namespace App\Domain\ValueObjects;


use DateTimeImmutable;

class DateOfDeath
{
    private ?DateTimeImmutable $value;

    public function __construct(?string $date)
    {
        if ($date) {
            $this->value = new \DateTimeImmutable($date);
        } else {
            $this->value = null;
        }
    }

    public function getValue(): ?\DateTimeImmutable
    {
        return $this->value;
    }
}
