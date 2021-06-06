<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Intl\Locale;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
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
    private $descriptionEn;

    /**
     * @ORM\Column(type="text")
     * @Assert\Type(
     *     type="string",
     *     message="event.type.string"
     * )
     */
    private $descriptionFr;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Image(groups = {"create"})
     */
    private $logo;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Image(groups = {"create"})
     */
    private $cover_image;



    /**
     * @ORM\OneToMany(targetEntity=Event::class, mappedBy="product")
     */
    private $events;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @Assert\All({
     *     @Assert\Image(groups = {"create"})
     * })
     */
    private $images = [];

    public function __construct()
    {
        $this->events = new ArrayCollection();
    }

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


    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->cover_image;
    }

    public function setCoverImage(string $cover_image): self
    {
        $this->cover_image = $cover_image;

        return $this;
    }



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
            $event->setProduct($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getProduct() === $this) {
                $event->setProduct(null);
            }
        }

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

    public function getFormattedName() {
        $locale = Locale::getDefault();
        if (($locale == 'fr') || ($locale == 'en')) {
            return $this->name;
        } else {
            return $this->nameAr;
        }

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

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): self
    {
        $this->images = $images;

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
}
