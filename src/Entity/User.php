<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User extends  AbstractLocalisation implements UserInterface,\Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;





    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mobile_phone;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday_date;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $scholar_level;



    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=School::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $related_school;


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
    public function getUsername(): string
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
        $roles[] = 'ROLE_ADMIN';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
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

    public function getMobilePhone(): ?string
    {
        return $this->mobile_phone;
    }

    public function setMobilePhone(string $mobile_phone): self
    {
        $this->mobile_phone = $mobile_phone;

        return $this;
    }





    public function getBirthdayDate(): ?\DateTimeInterface
    {
        return $this->birthday_date;
    }

    public function setBirthdayDate(?\DateTimeInterface $birthday_date): self
    {
        $this->birthday_date = $birthday_date;

        return $this;
    }





    public function getScholarLevel(): ?int
    {
        return $this->scholar_level;
    }

    public function setScholarLevel(?int $scholar_level): self
    {
        $this->scholar_level = $scholar_level;

        return $this;
    }




    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRelatedSchool(): ?School
    {
        return $this->related_school;
    }

    public function setRelatedSchool(?School $related_school): self
    {
        $this->related_school = $related_school;

        return $this;
    }


    public function serialize()
    {
        return serialize([
            $this->id,
            $this->name,
            $this->email,
            $this->password,
            $this->related_school,
            $this->scholar_level,
            $this->mobile_phone,
            $this->birthday_date

        ]);
    }

    public function unserialize($data)
    {
         list(
            $this->id,
            $this->name,
            $this->email,
            $this->password,
            $this->related_school,
            $this->scholar_level,
            $this->mobile_phone,
            $this->birthday_date
            ) = unserialize($data,['allowed_classes' => false]);
    }
}
