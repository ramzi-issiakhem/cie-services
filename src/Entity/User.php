<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email")
 */
class User extends AbstractLocalisation implements UserInterface
{
    const SCHOOLAR_LEVEL = [
        0 => "levels.section.little",
        1 => "levels.section.middle",
        2 => "levels.section.big",
        3 => "levels.primary.one",
        4 => "levels.primary.two",
        5 => "levels.primary.three",
        6 => "levels.primary.four",
        7 => "levels.primary.five",
        8 => "levels.secondary.one",
        9 => "levels.secondary.two",
        10 => "levels.secondary.three",
        11 => "levels.secondary.four",
        12 => "levels.high.one",
        13 => "levels.high.two",
        14 => "levels.high.three",
    ];

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
     *     message="user.type.email"
     * )
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
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Type(
     *     type="integer",
     *     message="user.type.integer"
     * )
     */
    private $scholar_level;



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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="users")
     * @Assert\Valid
     */
    private $related_school;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="related_school")
     * @Assert\Valid
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=Event::class, mappedBy="school")
     * @Assert\Valid
     */
    private $events;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $logo;


    public function __construct()
    {

        $this->users = new ArrayCollection();
        $this->events = new ArrayCollection();
    }


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

    public function getRelatedSchool(): ?self
    {
        return $this->related_school;
    }

    public function setRelatedSchool(?self $related_school): self
    {
        $this->related_school = $related_school;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers(): ArrayCollection
    {
        return $this->users;
    }

    public function addUser(self $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setRelatedSchool($this);
        }

        return $this;
    }

    public function removeUser(self $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getRelatedSchool() === $this) {
                $user->setRelatedSchool(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getEvents(): ArrayCollection
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
    public function serialize()
    {

        return serialize([
            $this->id,
            $this->name,
            $this->password]);

    }

    public function unserialize($data)
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
    public function getFormattedSchoolarLevel(): string
    {
        return self::SCHOOLAR_LEVEL[$this->scholar_level];
    }
}
