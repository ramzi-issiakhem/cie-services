<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Asserts;

/**
 * @ORM\Entity(repositoryClass=EventRepository::class)
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
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;





    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     */
    private $reservations;



    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $reservation_places;

    /**
     * @ORM\Column(type="integer")
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity=School::class, inversedBy="events")
     */
    private $school;

    /**
     * @ORM\Column(type="datetime")
     */
    private $event_datetime;

    /**
     * @ORM\Column(type="date")
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
