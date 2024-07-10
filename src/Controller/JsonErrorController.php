<?php
namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

class JsonErrorController
{
    public function show(Throwable $exception, LoggerInterface $logger)
    {
      return new JsonResponse([ "status" => $exception->getStatusCode(), "message" => $exception->getMessage()]);
    }
}