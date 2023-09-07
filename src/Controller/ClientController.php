<?php

namespace App\Controller;

use App\Entity\Customers;
use App\Repository\CustomersRepository;
use App\Repository\UserRepository;
use App\Service\VersioningService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
// use Symfony\Component\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
		VersioningService $versioningService
	): JsonResponse 
	{
		$user = $userRepository->find($idClient);
		$customer = $customersRepository->getCustomerFromClient($idCustomer, $user);
		$context = SerializationContext::create()->setGroups(['getCustomer']);
		$context->setVersion($versioningService->getVersion());
		$customerSerialized = $serializer->serialize($customer, 'json', $context);
		if (empty($customer)) {
			throw new Exception("No customer found", 404);
		}
		return new JsonResponse($customerSerialized, 200, [], true);
	}

	/**
	 * This allows to get all customers from a specified client
	 * 
	 * @OA\Response (
	 * 		response=200,
	 * 		description="Return all customers from a specified client",
	 * 		@OA\JsonContent(
	 * 			type="array",
	 * 			@OA\Items(ref=@Model(type=Customers::class, groups={"getCustomer"}))
	 * 		)
	 * )
	 * @OA\Parameter(
	 * 		name="page",
	 * 		in="query",
	 * 		description="The page you want to get",
	 * 		@OA\Schema(type="int")
	 * )
	 * 
	 * @OA\Parameter(
	 * 		name="limit",
	 * 		in="query",
	 * 		description="The number of customers you want",
	 * 		@OA\Schema(type="int")
	 * )
	 * 
	 * @OA\Tag(name="Customers")
	 * 
	 * @param CustomersRepository $customersRepository
	 * @param UserRepository $userRepository
	 * @param SerliazerInterface $serializer
	 * @param Request $request
	 * @return JsonResponse
	 */
	#[Route('/users/{idClient}/customers', name: 'app_get_customers', methods: ['GET'])]
	public function getCustomersFromClient(
		int $idClient,
		CustomersRepository $customersRepository,
		UserRepository $userRepository,
		SerializerInterface $serializer,
		TagAwareCacheInterface $cachePool,
		Request $request,
		VersioningService $versioningService
	) : JsonResponse
	{
		$page = $request->get("page", 1);
		$limit = $request->get("limit", 3);
		$user = $userRepository->find($idClient);
		$idCache = "getCustomersFromClient";
		$customers = $cachePool->get($idCache, function (ItemInterface $item) use ($customersRepository, $user, $idClient, $page, $limit) {
			$item->tag("customersCache");
			return $customersRepository->getCustomersFromClient($user, $idClient, $page, $limit);
		});
		$context = SerializationContext::create()->setGroups(["getCustomer"]);
		$context->setVersion($versioningService->getVersion());
		$customersSerialized = $serializer->serialize($customers, 'json', $context);
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
		UrlGeneratorInterface $urlGenerator,
		ValidatorInterface $validator
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
			$errors = $validator->validate($newCustomer);
			if (!empty($errors)) {
				throw new Exception("Errors while creating customer : ". (string) $errors);
			}
			$em->persist($newCustomer);
			$em->flush();

			$context = SerializationContext::create()->setGroups(["getCustomer"]);
			$jsonCustomer = $serializer->serialize($customer, 'json', $context);

			$location = $urlGenerator->generate('app_client_root_app_get_customer', ['idCustomer' => $newCustomer->getId(), 'idClient' => $idClient], UrlGeneratorInterface::ABSOLUTE_URL);

			return new JsonResponse($jsonCustomer, 201, ["Location" => $location], true);
	}
}
