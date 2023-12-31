<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Hateoas\Configuration\Annotation as Hateoas;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Since;

/**
 * 
 * @Hateoas\Relation(
 * 		"self",
 * 		href = @Hateoas\Route(
 * 			"app_products_root_app_product",
 *			parameters = { "id" = "expr(object.getId())" }
 * 		),
 * )
 */


#[ORM\Entity(repositoryClass: ProductsRepository::class)]
class Products
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	#[Since("1.0")]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	#[Since("1.0")]
	private ?string $name = null;

	#[ORM\Column(length: 255, nullable: true)]
	#[Since("1.0")]
	private ?string $description = null;

	#[ORM\Column]
	#[Since("1.0")]
	private ?int $price = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): static
	{
		$this->name = $name;

		return $this;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): static
	{
		$this->description = $description;

		return $this;
	}

	public function getPrice(): ?int
	{
		return $this->price;
	}

	public function setPrice(int $price): static
	{
		$this->price = $price;

		return $this;
	}
}
