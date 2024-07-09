<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

use App\Service\CustomersService;

class CustomersController extends AbstractController
{

    protected $customerService;
   
    public function __construct(
        CustomersService $CustomersService
    ){
        $this->customerService = $CustomersService;
    }

    #[Route('/customers', name: 'app_customers')]
    public function index(): JsonResponse
    {
        return $this->json([
            "status"    => true,
            "data"      => $this->customerService->list(),
            "message"   => ""
        ]);
    }
}
