<?php

namespace App\Controller;

use App\Service\AmoCustomFieldsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class CreateFieldController extends AbstractController
{
    public function __construct(
        private readonly AmoCustomFieldsService $customFieldsService,
    ) {
    }

    #[Route('/api/field', name: 'api_field_create', methods: ['POST'])]
    public function createField(): JsonResponse
    {
        $this->customFieldsService->createCustomFieldMultiList();

        return $this->json([
            'message' => 'Created',
        ]);
    }
}
