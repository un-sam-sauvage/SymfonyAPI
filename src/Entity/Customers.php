<?php

namespace App\Entity;

use App\Repository\CustomersRepository;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Since;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * 
 * @Hateoas\Relation(
* 		"self",
* 		href = @Hateoas\Route(
* 			"app_client_root_app_get_customer",
*			parameters = { 
*				"idClient" = "expr(object.getClient().getId())",
*				"idCustomer" = "expr(object.getId())",
*			}
* 		),
* 		exclusion = @Hateoas\Exclusion(groups="getCustomer", excludeIf="expr(not is_granted('ROLE_USER'))"),
 * ),
 *  * @Hateoas\Relation(
* 		"delete",
* 		href = @Hateoas\Route(
* 			"app_client_root_app_delete_customer",
*			parameters = { 
*				"idClient" = "expr(object.getClient().getId())",
*				"idCustomer" = "expr(object.getId())",
*			}
* 		),
* 		exclusion = @Hateoas\Exclusion(groups="getCustomer", excludeIf="expr(not is_granted('ROLE_ADMIN'))"),
 * ),
 */


#[ORM\Entity(repositoryClass: CustomersRepository::class)]
class Customers
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	#[Groups(["getCustomer"])]
	#[Since("1.0")]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	#[Groups(["getCustomer"])]
	#[Assert\NotBlank]
	#[Assert\Type('string')]
	#[Since("1.0")]
	private ?string $username = null;

	#[ORM\Column(length: 255)]
	#[Groups(["getCustomer"])]
	#[Assert\NotBlank]
	#[Assert\Type('string')]
	#[Since("1.0")]
	private ?string $email = null;

	#[ORM\ManyToOne(inversedBy: 'customers')]
	#[ORM\JoinColumn(nullable: false)]
	#[Groups(["getCustomer"])]
	#[Assert\NotBlank]
	#[Assert\Type(User::class)]
	#[Since("1.0")]
	private ?User $client = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getUsername(): ?string
	{
		return $this->username;
	}

	public function setUsername(string $username): static
	{
		$this->username = $username;

		return $this;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(string $email): static
	{
		$this->email = $email;

		return $this;
	}

	public function getClient(): ?User
	{
		return $this->client;
	}

	public function setClient(?User $client): static
	{
		$this->client = $client;

		return $this;
	}
}
