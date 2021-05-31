<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Cocur\Slugify\Slugify;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email")
 */
class User extends AbstractLocalisation implements UserInterface
{


   /* const ROLES = [
        "roles.superadministrator" => ["ROLE_SUPER_ADMIN","ROLE_ADMIN","ROLE_MODERATOR"],
        "roles.administrator" => ["ROLE_ADMIN","ROLE_MODERATOR"],
        "roles.moderator" => ["ROLE_MODERATOR","ROLE_USER"] ,
        "roles.user" => ["ROLE_USER"],
    ];*/

    const ROLES = [
        "roles.superadministrator" => "ROLE_SUPER_ADMIN",
        "roles.administrator" => "ROLE_ADMIN",
        "roles.moderator" => "ROLE_MODERATOR" ,
        "roles.user" => "ROLE_USER",
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(
     *     message="user.type.emails"
     * )
     *
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255)
     * @var string The hashed password
     * @Assert\NotCompromisedPassword(
     *     message="user.type.password.compromised"
     *  )
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *     pattern="/\d{9}/",
     *     message="user.type.regex.mobilephone"
     * )
     */
    private $mobile_phone;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $birthday_date;



    /**
     * @ORM\Column(type="string", length=255)
     *
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(
     *     type="integer",
     *     message="user.type.integer"
     * )
     */
    private $type;





    /**
     * @ORM\Column(type="string", length=255)
     */
    private $logo;


    /**
     * @ORM\OneToMany(targetEntity=Event::class, mappedBy="school")
     */
    private $events;

    /**
     * @ORM\OneToMany(targetEntity=Child::class, mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\OneToMany(targetEntity=Child::class, mappedBy="school")
     */
    private $users;


    public function __construct()
    {




        $this->events = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->users = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     *
     *
     * @see UserInterface
     */
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
        return (string) $this->name;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;


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


    /**
     * @return DateTimeInterface|null
     */
    public function getBirthdayDate(): ?\DateTimeInterface
    {
        return $this->birthday_date;
    }


    /**
     * @param DateTimeInterface $birthday_date
     * @return $this
     */
    public function setBirthdayDate(\DateTimeInterface $birthday_date): self
    {
        $this->birthday_date = $birthday_date;

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

    public function slugify() : string {
        $slug = new Slugify();
        return $slug->slugify($this->name);
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }








    /**
     * @return Collection
     */


    public function serialize(): string
    {

        return serialize([
            $this->id,
            $this->name,
            $this->password]);

    }

    public function unserialize($data): array
    {

        return  list (
            $this->id,
            $this->name,
            $this->password
            ) =  unserialize($data,['allowed_classes' => false]);
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        $slug = new Slugify();
        return $slug->slugify($this->name);
    }

    /**
     * @return string
     */






    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->setSchool($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getSchool() === $this) {
                $event->setSchool(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Child[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Child $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Child $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Child[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(Child $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setSchool($this);
        }

        return $this;
    }

    public function removeUser(Child $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getSchool() === $this) {
                $user->setSchool(null);
            }
        }

        return $this;
    }
}
