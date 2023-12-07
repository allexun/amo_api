<?php

namespace App\Controller;

use App\Service\AmoAuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends AbstractController
{
    public function __construct(
        private readonly AmoAuthService $amoService,
    ) {
    }

    #[Route('/api/login', name: 'api_login', methods: ['GET'])]
    public function index(): Response
    {
        return $this->redirect($this->amoService->getAuthUrl());
    }
}
