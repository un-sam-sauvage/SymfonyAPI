<?php

namespace App\Controller;

use App\Repository\CustomersRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api', name: 'app_client_root_')]
class ClientController extends AbstractController
{
	#[Route('/users/{idClient}/customers/{idCustomer}', name: 'app_get_customer', methods: ['GET'])]
	public function getCustomerFromClient(
		int $idClient, 
		int $idCustomer, 
		CustomersRepository $customersRepository, 
		UserRepository $userRepository,
		SerializerInterface $serializer
	): JsonResponse 
	{
		$user = $userRepository->find($idClient);
		$customer = $customersRepository->getCustomerFromClient($idCustomer, $user);
		$customerSerialized = $serializer->serialize($customer, 'json', ["groups" => "getCustomer"]);
		if (empty($customer)) {
			//TODO: faire la gestion d'erreur
			return new JsonResponse("", 404, [], true);
		}
		return new JsonResponse($customerSerialized, 200, [], true);
	}

	#[Route('/users/{idClient}/customers', name: 'app_get_customers', methods: ['GET'])]
	public function getCustomersFromClient(
		int $idClient,
		CustomersRepository $customersRepository,
		UserRepository $userRepository,
		SerializerInterface $serializer
	) : JsonResponse
	{
		$user = $userRepository->find($idClient);
		$customers = $customersRepository->getCustomersFromClient($user);
		$customersSerialized = $serializer->serialize($customers, 'json', ["groups" => "getCustomer"]);
		return new JsonResponse($customersSerialized, 200, [], true);
	}
}
