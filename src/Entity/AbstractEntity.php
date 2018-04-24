<?php

namespace Common\Entity;

use Doctrine\ORM\Mapping as ORM;

abstract class AbstractEntity implements EntityInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    /**
     * Get the value of id
     *
     * @return int
     */
    public function getId(): integer
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @param int  $id
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the value of createdAt
     *
     * @return datetime
     */
    public function getCreatedAt(): datetime
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     * Set the value of createdAt
     *
     * @return self
     */
    public function setCreatedAt(): self
    {
        $this->createdAt = new \DateTime('now');
        return $this;
    }

    /**
     * Get the value of updatedAt
     *
     * @return datetime
     */
    public function getUpdatedAt(): datetime
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PreUpdate
     * Set the value of updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(): self
    {
        $this->updatedAt = new \DateTime('now');
        return $this;
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function exchangeArray(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->{$key} = (!empty($value)) ? $value : null;
        }
    }
}
