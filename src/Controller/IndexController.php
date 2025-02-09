<?php

namespace Adictiz\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route('/')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Hello from Adictiz Events API!',
        ]);
    }

    /**
     * Cette route permet de tester le déclenchement d'une erreur et active le handler Monolog "fingers crossed".
     */
    #[Route('/exception')]
    public function exception(): never
    {
        throw new BadRequestHttpException('Oups!');
    }
}
