<?php

namespace Framework\Template;

use Framework\Authentication\SessionAuthInterface;
use Framework\Session\SessionInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class TwigFactory
{
    public function __construct(
        private readonly string $viewsPath,
        private readonly SessionInterface $session,
        private readonly SessionAuthInterface $sessionAuth
    ) {
    }

    public function create(): Environment
    {
        $loader = new FilesystemLoader($this->viewsPath);

        $twig = new Environment($loader, [
            'debug' => true,
            'cache' => false,
        ]);

        $twig->addExtension(new DebugExtension());
        $twig->addFunction(new TwigFunction('session', [$this, 'getSession']));
        $twig->addFunction(new TwigFunction('isAuth', [$this->sessionAuth, 'check']));
        $twig->addFunction(new TwigFunction('isCreator', [$this, 'isCreator']));

        return $twig;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function isCreator(string $username): bool
    {
        return $this->sessionAuth->getUser()->getUsername() === $username;
    }
}
