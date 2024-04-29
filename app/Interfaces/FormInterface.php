<?php

namespace App\Interfaces;

interface FormInterface
{
    public function getValidationErrors(): array;

    public function hasValidationErrors(): bool;
}
