<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

use App\Service\CustomersService;

class CustomersController extends AbstractController
{
   
    public function __construct(
        private CustomersService $CustomersService
    ){
        parent::__construct();
    }

    #[Route('/customers', name: 'app_customers')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CustomersController.php',
        ]);
    }
}
