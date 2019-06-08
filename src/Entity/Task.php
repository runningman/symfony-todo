<?php

namespace App\Entity;

use App\Entity\Traits\UuidTrait;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task implements JsonSerializable
{
    use UuidTrait;
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="TodoList", inversedBy="tasks")
     */
    private $todoList;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $completedAt;

    public function __construct()
    {
        $this->uuid = $this->generateUuid();

        // TODO: why isn't this picked up by trait, issue with form validation.
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isComplete(): bool
    {
        return $this->completedAt ? true : false;
    }

    public function getTodoList(): TodoList
    {
        return $this->todoList;
    }

    public function setTodoList(TodoList $todoList): self
    {
        $this->todoList = $todoList;

        return $this;
    }

    public function getCompletedAt(): ?DateTimeInterface
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?DateTimeInterface $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * Return the data for json serialization.
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'uid' => $this->getUuid(),
            'description' => $this->getDescription(),
            'is_complete' => $this->isComplete(),
        ];
    }
}
