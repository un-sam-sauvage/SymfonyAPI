<?php

namespace App\Controller;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[Route('/api', name: 'app_products_root_')]
class ProductController extends AbstractController
{
	#[Route('/products', name: 'app_products', methods: ['GET'])]
	public function getProducts(
		ProductsRepository $productsRepository, 
		SerializerInterface $serializer,
		TagAwareCacheInterface $cachePool
	): JsonResponse
	{
		$idCache = "getProducts";
		$products = $cachePool->get($idCache, function (ItemInterface $item) use ($productsRepository) {
			$item->tag("productsCache");
			return $productsRepository->findAll();
		});
		$jsonProducts = $serializer->serialize($products, "json");
		return new JsonResponse($jsonProducts, 200, [], true);
	}

	#[Route('/products/{id}', name: 'app_product', methods:['GET'])]
	public function getProduct (Products $product,SerializerInterface $serializer) : JsonResponse {
		$jsonProduct = $serializer->serialize($product, "json");
		return new JsonResponse($jsonProduct, 200, [], true);
	}
}
