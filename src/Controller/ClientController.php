<?php

namespace App\Controller;

use App\Entity\Customers;
use App\Repository\CustomersRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route('/api', name: 'app_client_root_')]
class ClientController extends AbstractController
{
	#[Route('/users/{idClient}/customers/{idCustomer}', name: 'app_get_customer', methods: ['GET'])]
	public function getCustomerFromClient(
		int $idClient, 
		int $idCustomer, 
		CustomersRepository $customersRepository, 
		UserRepository $userRepository,
		SerializerInterface $serializer,
	): JsonResponse 
	{
		$user = $userRepository->find($idClient);
		$customer = $customersRepository->getCustomerFromClient($idCustomer, $user);
		$customerSerialized = $serializer->serialize($customer, 'json', ["groups" => "getCustomer"]);
		if (empty($customer)) {
			throw new Exception("No customer found", 404);
		}
		return new JsonResponse($customerSerialized, 200, [], true);
	}

	#[Route('/users/{idClient}/customers', name: 'app_get_customers', methods: ['GET'])]
	public function getCustomersFromClient(
		int $idClient,
		CustomersRepository $customersRepository,
		UserRepository $userRepository,
		SerializerInterface $serializer,
		TagAwareCacheInterface $cachePool
	) : JsonResponse
	{
		$user = $userRepository->find($idClient);
		$idCache = "getCustomersFromClient";
		$customers = $cachePool->get($idCache, function (ItemInterface $item) use ($customersRepository, $user, $idClient) {
			$item->tag("customersCache");
			return $customersRepository->getCustomersFromClient($user, $idClient);
		});
		$customersSerialized = $serializer->serialize($customers, 'json', ["groups" => "getCustomer"]);
		return new JsonResponse($customersSerialized, 200, [], true);
	}

	#[Route('/users/{idClient}/customers/{idCustomer}', name: 'app_delete_customer', methods: ['DELETE'])]
	#[IsGranted('ROLE_ADMIN', message: "You don't have the enough rights to do this")]
	public function deleteCustomerFromClient (
		int $idClient,
		int $idCustomer,
		CustomersRepository $customersRepository,
		UserRepository $userRepository,
		SerializerInterface $serializer
	) : JsonResponse {
		$user = $userRepository->find($idClient);
		$customer = $customersRepository->find($idCustomer);
		if (empty($customer)) {
			throw new Exception("No customer found", 404);
		}
		$customersRepository->deleteCustomerFromClient($idCustomer, $user);
		$data = ["message" => "The customer has successfully been deleted"];
		$dataSerialized = $serializer->serialize($data, 'json');
		return new JsonResponse($dataSerialized, 200, [], true);
	}

	#[Route('/users/{idClient}/customers', name: 'app_create_customer', methods: ['POST'])]
	#[IsGranted('ROLE_ADMIN', message: "You don't have the enough rights to do this")]
	public function createCustomer(
		int $idClient,
		Request $request,
		SerializerInterface $serializer,
		UserRepository $userRepository,
		EntityManagerInterface $em,
		UrlGeneratorInterface $urlGenerator
		) : JsonResponse{
			$input = str_replace(["{", "}", '"'], "", $request->getContent());
			$input = explode(",", $input);
			$customer = [];

			foreach($input as &$value) {
				$key = trim(explode(":", $value)[0]);
				$value = explode(":", $value)[1];
				$customer[$key] = trim($value);
			}

			$newCustomer = new Customers();
			$newCustomer->setUsername($customer["username"]);
			$newCustomer->setEmail($customer["email"]);
			$newCustomer->setClient($userRepository->find($idClient));
			
			$em->persist($newCustomer);
			$em->flush();

			$jsonCustomer = $serializer->serialize($customer, 'json', ['groups' => 'getCustomer']);

			$location = $urlGenerator->generate('app_client_root_app_get_customer', ['idCustomer' => $newCustomer->getId(), 'idClient' => $idClient], UrlGeneratorInterface::ABSOLUTE_URL);

			return new JsonResponse($jsonCustomer, 201, ["Location" => $location], true);
	}
}
