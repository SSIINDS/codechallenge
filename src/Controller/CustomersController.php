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

    #[Route('/customers', name: 'app_customer_list')]
    public function app_customer_list(): JsonResponse
    {
        return new JsonResponse([
            "status"    => true,
            "data"      => $this->customerService->list(),
            "message"   => ""
        ]);
    }

    #[Route('/customers/{id}', name: 'app_customer_details')]
    public function customer_details($id): JsonResponse
    {
        $customer = $this->customerService->read($id);
        if(!$customer){
            return new JsonResponse([
                "status"    => false,
                "message"   => "Customer not found."
            ]);
        }
        return new JsonResponse([
            "status"    => true,
            "data"      => $this->customerService->read($id),
            "message"   => ""
        ]);
    }
}
