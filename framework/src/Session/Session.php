<?php

namespace Framework\Session;

use Framework\Interfaces\Session\SessionInterface;

class Session implements SessionInterface
{
    private const FLASH_KEY = 'flash';

    public const AUTH_KEY = 'user_id';

    public function start(): void
    {
        session_start();
    }

    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function setFlash(string $type, string|array $messages): void
    {
        $flash = $this->get(self::FLASH_KEY, []);

        if (is_array($messages)) {
            foreach ($messages as $message) {
                $flash[$type][] = $message;
            }
        } else {
            $flash[$type][] = $messages;
        }

        $this->set(self::FLASH_KEY, $flash);
    }

    public function setFlashArray(array $messages): void
    {
        $flash = $this->get(self::FLASH_KEY, []);

        foreach ($messages as $type => $message) {
            $flash[$type][] = $message;
        }

        $this->set(self::FLASH_KEY, $flash);
    }

    public function getFlash(string $type): array
    {
        $flash = $this->get(self::FLASH_KEY, []);

        if (isset($flash[$type])) {
            $messages = $flash[$type];

            unset($flash[$type]);

            $this->set(self::FLASH_KEY, $flash);

            return $messages;
        }

        return [];
    }

    public function hasFlash(string $type): bool
    {
        return isset($_SESSION[self::FLASH_KEY][$type]);
    }

    public function clearFlash(): void
    {
        unset($_SESSION[self::FLASH_KEY]);
    }
}
