<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
 * @UniqueEntity("name")
 */
class Event
{

    const STATE = [
        0 => "En Cours",
        1 => "Inscription Fermee - En attente",
        2 => "Confirme",
        3 => "Fini"
    ];


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Type(
     *     type="string",
     *     message="event.type.string"
     * )
     */
    private $name;


    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\Type(
     *     type="object",
     *     message="event.type.object.product"
     * )
     * @Assert\NotNull
     */
    private $product;





    /**
     * @ORM\Column(type="integer")
     *
     * @Assert\Type(
     *     type="integer",
     *     message="event.type.integer"
     * )
     * @Assert\NotNull
     */

    private $price;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(
     *     type="integer",
     *     message="event.type.integer"
     * )
     * @Assert\NotNull
     */
    private $reservations;



    /**
     * @ORM\Column(type="text")
     * @Assert\Type(
     *     type="string",
     *     message="event.type.string"
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(
     *     type="integer",
     *     message="event.type.integer"
     * )
     * @Assert\NotNull
     */
    private $reservation_places;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Type(
     *     type="integer",
     *     message="event.type.integer"
     * )
     * @Assert\NotNull
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity=School::class, inversedBy="events")
     * @Assert\Type(
     *     type="object",
     *     message="event.type.object.school"
     * )
     * @Assert\NotNull
     */
    private $school;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan("today")
     * @Assert\NotNull
     */

    private $event_datetime;

    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThan("today")
     * @Assert\NotNull
     */
    private $deadline_date;





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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }



    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getReservations(): ?int
    {
        return $this->reservations;
    }

    public function setReservations(int $reservations): self
    {
        $this->reservations = $reservations;

        return $this;
    }





    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getReservationPlaces(): ?int
    {
        return $this->reservation_places;
    }

    public function setReservationPlaces(int $reservation_places): self
    {
        $this->reservation_places = $reservation_places;

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function setSchool(?School $school): self
    {
        $this->school = $school;

        return $this;
    }

    public function getEventDateTime(): ?\DateTimeInterface
    {
        return $this->event_datetime;
    }

    public function setEventDateTime(\DateTimeInterface $event_datetime): self
    {
        $this->event_datetime = $event_datetime;

        return $this;
    }

    public function getDeadlineDate(): ?\DateTimeInterface
    {
        return $this->deadline_date;
    }

    public function setDeadlineDate(\DateTimeInterface $deadline_date): self
    {
        $this->deadline_date = $deadline_date;

        return $this;
    }






}
