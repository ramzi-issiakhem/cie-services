<?php

namespace App\Entity;


use DateTime;
use Doctrine\ORM\Mapping as ORM;


class EventSearch
{
        /**
         * @var Product|null
         */
        private $product;
        /**
         * @var int|null
         */
        private $state;
        /**
         * @var DateTime|null
         */
        private $event_datetime;
        /**
         * @var DateTime|null
         */
        private $deadline_date;

        /**
         * @var User|null
         */
        private $school;

     /**
     * @var String|null
     */
     private $order;


    /**
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product|null $product
     */
    public function setProduct(?Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @return User|null
     */
    public function getSchool(): ?User
    {
        return $this->school;
    }

    /**
     * @param User|null $school
     */
    public function setSchool(?User $school): void
    {
        $this->school = $school;
    }

    /**
     * @return DateTime|null
     */
    public function getDeadlineDate(): ?DateTime
    {
        return $this->deadline_date;
    }

    /**
     * @param DateTime|null $deadline_date
     */
    public function setDeadlineDate(?DateTime $deadline_date): void
    {
        $this->deadline_date = $deadline_date;
    }

    /**
     * @return DateTime|null
     */
    public function getEventDatetime(): ?DateTime
    {
        return $this->event_datetime;
    }

    /**
     * @param DateTime|null $event_datetime
     */
    public function setEventDatetime(?DateTime $event_datetime): void
    {
        $this->event_datetime = $event_datetime;
    }

    /**
     * @return int|null
     */
    public function getState(): ?int
    {
        return $this->state;
    }

    /**
     * @param int|null $state
     */
    public function setState(?int $state): void
    {
        $this->state = $state;
    }

    /**
     * @return String|null
     */
    public function getOrder(): ?string
    {
        return $this->order;
    }

    /**
     * @param String|null $order
     */
    public function setOrder(?string $order): void
    {
        $this->order = $order;
    }


}
