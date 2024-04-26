<?php

namespace Framework\Controller;

use Framework\Http\Request;
use Framework\Http\Response;
use Psr\Container\ContainerInterface;

abstract class AbstractController
{
    protected ?ContainerInterface $container = null;
    protected Request $request;

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function render(string $view, array $data = [], Response $response = null): Response
    {
       $content = $this->container->get('twig')->render($view, $data);

       $response ??= new Response();
       
       $response->setContent($content);

       return $response;
    }
}