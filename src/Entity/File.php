<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(schema: 'data')]
#[ApiResource]
class File extends EntityBase
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string')]
    private $name;

    #[ORM\Column(type: 'string')]
    private $mime;
    
    #[ORM\Column(type: 'integer')]
    private $size;

    #[ORM\Column(type: 'string')]
    private $driveId;

    public function __construct(string $name, string $mime, int $size, string $driveId, User $createdBy, \DateTime $createdAt)
    {
        $this->name = $name;
        $this->mime = $mime;
        $this->size = $size;
        $this->driveId = $driveId;
        $this->createdBy = $createdBy;
        $this->createdAt = $createdAt;
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

    public function getMime(): ?string
    {
        return $this->mime;
    }

    public function setMime(string $mime): self
    {
        $this->mime = $mime;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getDriveId(): ?string
    {
        return $this->driveId;
    }

    public function setDriveId(string $driveId): self
    {
        $this->driveId = $driveId;

        return $this;
    }

    
}