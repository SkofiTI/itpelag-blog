<?php

namespace Framework\Authentication;

use Framework\Session\SessionInterface;

class SessionAuthentication implements SessionAuthInterface
{
    private AuthUserInterface $user;

    public function __construct(
        private AuthUserServiceInterface $userService,
        private SessionInterface $session
    ) {
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
        $this->session->set('user_id', $user->getId());

        $this->user = $user;
    }

    public function logout()
    {

    }

    public function getUser(): AuthUserInterface
    {
        return $this->user;
    }
}
