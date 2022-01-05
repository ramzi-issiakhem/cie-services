<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Intl\Locale;
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
     * @ORM\Column(type="string", length=255)
     * @Assert\Type(
     *     type="string",
     *     message="event.type.string"
     * )
     */
    private $nameAr;


    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=true)
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
     * @ORM\Column(type="text")
     * @Assert\Type(
     *     type="string",
     *     message="event.type.string"
     * )
     */
    private $descriptionAr;

    /**
     * @ORM\Column(type="text")
     * @Assert\Type(
     *     type="string",
     *     message="event.type.string"
     * )
     */
    private $descriptionFr;

    /**
     * @ORM\Column(type="text")
     * @Assert\Type(
     *     type="string",
     *     message="event.type.string"
     * )
     */
    private $descriptionEn;

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



    /**
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    private $reservations = [];

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
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



    /**
     * @return mixed
     */
    public function getNameAr()
    {
        return $this->nameAr;
    }

    /**
     * @param mixed $nameAr
     */
    public function setNameAr($nameAr): void
    {
        $this->nameAr = $nameAr;
    }

    public function  getFormattedName() {
        $locale = Locale::getDefault();
        if (($locale == 'fr') || ($locale == 'en')) {
            return $this->name;
        } else {
            return $this->nameAr;
        }
    }

    /**
     * @return mixed
     */
    public function getDescriptionAr()
    {
        return $this->descriptionAr;
    }

    /**
     * @param mixed $descriptionAr
     */
    public function setDescriptionAr($descriptionAr): void
    {
        $this->descriptionAr = $descriptionAr;
    }

    /**
     * @return mixed
     */
    public function getDescriptionFr()
    {
        return $this->descriptionFr;
    }

    /**
     * @param mixed $descriptionFr
     */
    public function setDescriptionFr($descriptionFr): void
    {
        $this->descriptionFr = $descriptionFr;
    }

    /**
     * @return mixed
     */
    public function getDescriptionEn()
    {
        return $this->descriptionEn;
    }

    /**
     * @param mixed $descriptionEn
     */
    public function setDescriptionEn($descriptionEn): void
    {
        $this->descriptionEn = $descriptionEn;
    }

        public function getFormattedDescription() {
            $locale = Locale::getDefault();
            switch ($locale) {
                case 'en':
                    return $this->descriptionEn;
                case 'ar':
                    return $this->descriptionAr;
                default:
                    return $this->descriptionFr;
            }
        }

    /**
     * @return array|null
     */
    public function getReservations(): ?array
        {
            return $this->reservations;
        }

        public function getReservationsNumber(): int
        {
            return count($this->reservations);
        }

        public function setReservations(?array $reservations): self
        {
            $this->reservations = $reservations;

            return $this;
        }

    public function removeReservations(?int $id): self
    {
        $array = $this->reservations;
        $index = array_search($id, $array);

        if ($index != false ) {
            unset($this->reservations[$index]);
        }

        return $this;
    }


        public function addReservation(int $child_id) : self {
            array_push($this->reservations,$child_id);
            $this->reservations = array_unique($this->reservations);
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

}
