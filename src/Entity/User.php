<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['users:list', 'users:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['users:list', 'users:read'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['users:list', 'users:read'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    #[Groups(['users:list', 'users:read'])]
    private ?string $username = null;

    #[ORM\Column]
    #[Groups(['users:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: CheckList::class, cascade: ['remove', 'persist'])]
    #[Groups(['users:read'])]
    private Collection $checkLists;

    public function __construct()
    {
        $this->checkLists = new ArrayCollection();
    }

    const ROLES = [
        'admin' => ['ROLE_ADMIN'],
        'user' => ['ROLE_USER'],
        'manager' => ['ROLE_MANAGER']
    ];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
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

    public function setRoles(array $roles): self
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

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, CheckList>
     */
    public function getCheckLists(): Collection
    {
        return $this->checkLists;
    }

    public function addCheckList(CheckList $checkList): self
    {
        if (!$this->checkLists->contains($checkList)) {
            $this->checkLists->add($checkList);
            $checkList->setOwner($this);
        }

        return $this;
    }

    public function removeCheckList(CheckList $checkList): self
    {
        if ($this->checkLists->removeElement($checkList)) {
            // set the owning side to null (unless already changed)
            if ($checkList->getOwner() === $this) {
                $checkList->setOwner(null);
            }
        }

        return $this;
    }

    public function getNbCheckLists():int 
    {
        return count($this->checkLists);
    }
}
