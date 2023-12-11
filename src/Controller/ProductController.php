<?php

namespace App\Controller;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;


#[Route('/api', name: 'app_products_root_')]
class ProductController extends AbstractController
{
	/**
	 * This allows to get all products
	 * 
	 * @OA\Response (
	 * 		response=200,
	 * 		description="Return all products",
	 * 		@OA\JsonContent(
	 * 			type="array",
	 * 			@OA\Items(ref=@Model(type=Products::class))
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
	 * 		description="The number of products you want",
	 * 		@OA\Schema(type="int")
	 * )
	 * 
	 * @OA\Tag(name="Products")
	 * 
	 * @param ProductsRepository $customersRepository
	 * @param SerliazerInterface $serializer
	 * @param Request $request
	 * @return JsonResponse
	 */
	#[Route('/products', name: 'app_products', methods: ['GET'])]
	public function getProducts(
		ProductsRepository $productsRepository, 
		SerializerInterface $serializer,
		TagAwareCacheInterface $cachePool,
		Request $request,
	): JsonResponse
	{
		$page = $request->get("page", 1);
		$limit = $request->get("limit", 3);
		$idCache = "getProducts". $page. "_". $limit;
		$products = $cachePool->get($idCache, function (ItemInterface $item) use ($productsRepository, $page, $limit) {
			$item->tag("productsCache");
			return $productsRepository->getAllProducts($page, $limit);
		});
		$jsonProducts = $serializer->serialize($products, "json");
		return new JsonResponse($jsonProducts, 200, [], true);
	}

	#[Route('/products/{id}', name: 'app_product', methods:['GET'])]
	public function getProduct (
		Products $product, 
		SerializerInterface $serializer
	) : JsonResponse {
		$jsonProduct = $serializer->serialize($product, "json");
		return new JsonResponse($jsonProduct, 200, [], true);
	}
}
