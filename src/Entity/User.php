<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Since;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	#[Groups(["getCustomer"])]
	#[Since("1.0")]
	private ?int $id = null;

	#[ORM\Column(length: 180, unique: true)]
	#[Since("1.0")]
	private ?string $email = null;

	#[ORM\Column]
	#[Since("1.0")]
	private array $roles = [];

	/**
	 * @var string The hashed password
	 */
	#[ORM\Column]
	#[Since("1.0")]
	private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Customers::class)]
	#[Since("1.0")]
    private Collection $customers;

    #[ORM\Column(length: 255)]
	#[Since("1.0")]
    private ?string $username = null;

    public function __construct()
    {
        $this->customers = new ArrayCollection();
    }

	public function getId(): ?int
                              	{
                              		return $this->id;
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

	/**
	 * A visual identifier that represents this user.
	 *
	 * @see UserInterface
	 */
	public function getUserIdentifier(): string
                              	{
                              		return (string) $this->email;
                              	}

	/**
	 * @see UserInterface
	 */
	public function getRoles(): array
                              	{
                              		$roles = $this->roles;
                              		// guarantee every user at least has ROLE_USER
                              		$roles[] = 'ROLE_USER';
                              
                              		return array_unique($roles);
                              	}

	public function getUsername(): string {
                              		return $this->email;
                              	}


	public function setRoles(array $roles): static
                              	{
                              		$this->roles = $roles;
                              
                              		return $this;
                              	}

	/**
	 * @see PasswordAuthenticatedUserInterface
	 */
	public function getPassword(): string
                              	{
                              		return $this->password;
                              	}

	public function setPassword(string $password): static
                              	{
                              		$this->password = $password;
                              
                              		return $this;
                              	}

	/**
	 * @see UserInterface
	 */
	public function eraseCredentials(): void
                              	{
                              		// If you store any temporary, sensitive data on the user, clear it here
                              		// $this->plainPassword = null;
                              	}

    /**
     * @return Collection<int, Customers>
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }

    public function addCustomer(Customers $customer): static
    {
        if (!$this->customers->contains($customer)) {
            $this->customers->add($customer);
            $customer->setClient($this);
        }

        return $this;
    }

    public function removeCustomer(Customers $customer): static
    {
        if ($this->customers->removeElement($customer)) {
            // set the owning side to null (unless already changed)
            if ($customer->getClient() === $this) {
                $customer->setClient(null);
            }
        }

        return $this;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }
}
