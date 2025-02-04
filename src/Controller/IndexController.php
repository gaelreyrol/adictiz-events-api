<?php

namespace Adictiz\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/')]
    public function index(): Response
    {
        return new Response('Hello World!', 200, ['Content-Type' => 'text/plain']);
    }

    #[Route('/exception')]
    public function exception(): Response
    {
        throw new BadRequestHttpException('Oups!');
    }
}
