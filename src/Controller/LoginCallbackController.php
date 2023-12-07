<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\AmoAuthService;

class LoginCallbackController extends AbstractController
{
    public function __construct(
        private readonly AmoAuthService $amoService,
    ) {
    }

    #[Route('/api/login/callback', name: 'api_login_callback')]
    public function index(Request $request): JsonResponse
    {
        $code = $request->query->get('code');

        $token = $this->amoService->setAccessToken($code);

        return $this->json([
            'message' => 'OK',
            'token' => $token,
        ]);
    }
}
