<?php

namespace App\Entity;


use DateTime;
use Doctrine\ORM\Mapping as ORM;


class UserSearch
{
    /**
     * @var string|null
     */
    private $roles;

    /**
     * @var int|null
     */
    private $scholar_level;

    /**
     * @var int|null
     */
    private $type;
    /**
     * @var User|null
     */
    private $related_school;

    /**
     * @return User|null
     */
    public function getRelatedSchool(): ?User
    {
        return $this->related_school;
    }

    /**
     * @param User|null $related_school
     */
    public function setRelatedSchool(?User $related_school): void
    {
        $this->related_school = $related_school;
    }

    /**
     * @return array|null
     */


    /**
     * @return int|null
     */
    public function getScholarLevel(): ?int
    {
        return $this->scholar_level;
    }

    /**
     * @param int|null $scholar_level
     */
    public function setScholarLevel(?int $scholar_level): void
    {
        $this->scholar_level = $scholar_level;
    }

    /**
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param int|null $type
     */
    public function setType(?int $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getRoles(): ?string
    {
        return $this->roles;
    }

    /**
     * @param string|null $roles
     */
    public function setRoles(?string $roles): void
    {
        $this->roles = $roles;
    }


}
