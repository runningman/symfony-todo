<?php

namespace App\Entity\Traits;

use Ramsey\Uuid\Uuid;

trait UuidTrait
{
    /**
     * @ORM\Column(type="uuid", unique=true)
     */
    private $uuid;

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    protected function generateUuid(): string
    {
        return Uuid::uuid4()->toString();
    }
}
