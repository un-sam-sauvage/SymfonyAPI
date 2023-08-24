<?php

namespace App\DataFixtures;

use App\Entity\Customers;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Products;
use App\Entity\User;

class AppFixtures extends Fixture
{

	public function __construct(private UserPasswordHasherInterface $passwordHasher)
	{

	}
	public function load(ObjectManager $manager): void
	{
		$user = new User();
		$user->setEmail("test@api.com");
		$user->setUsername("test");
		$user->setRoles(["ROLE_USER"]);
		$user->setPassword($this->passwordHasher->hashPassword($user, "123"));
		$manager->persist($user);
		
		for ($i = 0; $i < 10; $i++) {

			$product = new Products();
			$product->setName("Product n°". $i);
			$product->setDescription("I'm the product n°".$i);
			$product->setPrice($i);
			$manager->persist($product);

			$customer = new Customers();
			$customer->setUsername("customer n°". $i);
			$customer->setEmail("customer". $i ."@api.com");
			$customer->setClient($user);
			
			$manager->persist($customer);
			
		}
		$manager->flush();
	}
}
