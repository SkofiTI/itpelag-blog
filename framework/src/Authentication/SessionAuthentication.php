<?php

namespace Framework\Authentication;

use Framework\Http\Exceptions\NotFoundedException;
use Framework\Interfaces\Authentication\AuthUserInterface;
use Framework\Interfaces\Authentication\AuthUserServiceInterface;
use Framework\Interfaces\Authentication\SessionAuthInterface;
use Framework\Interfaces\Session\SessionInterface;
use Framework\Session\Session;

class SessionAuthentication implements SessionAuthInterface
{
    private AuthUserInterface $user;

    public function __construct(
        private readonly AuthUserServiceInterface $userService,
        private readonly SessionInterface $session
    ) {
        if ($this->check()) {
            try {
                $user = $this->userService->findOrFail($session->get(Session::AUTH_KEY));

                $this->login($user);
            } catch (NotFoundedException $e) {
                $this->logout();
            }
        }
    }

    public function authenticate(string $username, string $password): bool
    {
        $user = $this->userService->findByUsername($username);

        if (! $user) {
            return false;
        }

        if (password_verify($password, $user->getPassword())) {
            $this->login($user);

            return true;
        }

        return false;
    }

    public function login(AuthUserInterface $user): void
    {
        $this->session->set(Session::AUTH_KEY, $user->getId());

        $this->user = $user;
    }

    public function logout(): void
    {
        $this->session->remove(Session::AUTH_KEY);
    }

    public function getUser(): AuthUserInterface
    {
        return $this->user;
    }

    public function check(): bool
    {
        return $this->session->has(Session::AUTH_KEY);
    }
}
