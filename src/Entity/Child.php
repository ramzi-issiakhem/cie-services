<?php

namespace App\Entity;

use App\Repository\ChildRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=ChildRepository::class)
 */
class Child
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

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;



    /**
     * @ORM\Column(type="integer")
     */
    private $schoolar_level;



    /**
     * @ORM\Column(type="date")
     */
    private $birthday_date;



    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="children")
     * @ORM\JoinColumn(nullable=false)
     */
    private $parent;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=true)
     */
    private $school;

    public function getId(): ?int
    {
        return $this->id;
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



    public function getSchoolarLevel(): ?int
    {
        return $this->schoolar_level;
    }

    public function setSchoolarLevel(int $schoolar_level): self
    {
        $this->schoolar_level = $schoolar_level;

        return $this;
    }



    public function getBirthdayDate(): ?\DateTimeInterface
    {
        return $this->birthday_date;
    }

    public function setBirthdayDate(\DateTimeInterface $birthday_date): self
    {
        $this->birthday_date = $birthday_date;

        return $this;
    }

    public function getFormattedSchoolarLevel(): string
    {
        return self::SCHOOLAR_LEVEL[$this->schoolar_level];
    }



    public function getParent(): ?User
    {
        return $this->parent;
    }

    public function setParent(?User $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getSchool(): ?User
    {
        return $this->school;
    }

    public function setSchool(?User $school): self
    {
        $this->school = $school;

        return $this;
    }

    public function getSlug() {
        $slug = new Slugify();
        return $slug->slugify($this->name);
    }


}
