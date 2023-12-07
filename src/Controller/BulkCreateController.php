<?php

namespace App\Controller;

use App\Service\AmoBulkCreateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BulkCreateController extends AbstractController
{
    public function __construct(
        private readonly AmoBulkCreateService $service,
    ) {
    }

    #[Route('/api/bulk-create', name: 'api_bulk_create', methods: ['GET'])]
    public function create(): JsonResponse
    {
        $count = $this->service->create1000Models();

        return $this->json([
            'message' => 'OK',
            'count' => $count,
        ]);
    }
}
