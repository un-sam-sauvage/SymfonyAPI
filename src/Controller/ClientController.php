<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
	#[Route('/users/{id}/customers', name: 'app_customer', methods: ['GET'])]
	public function getCustomersFromClient(UserRepository $userRepository): JsonResponse
	{

		return new JsonResponse("coucou");
	}
}
