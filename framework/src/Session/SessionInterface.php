<?php

namespace Framework\Session;

interface SessionInterface
{
    public function start(): void;

    public function set(string $key, $value): void;

    public function get(string $key, $default = null);

    public function has(string $key): bool;

    public function remove(string $key): void;

    public function setFlash(string $type, string $message): void;

    public function setFlashArray(array $messages): void;

    public function getFlash(string $type): array;

    public function hasFlash(string $type): bool;

    public function clearFlash(): void;
}
