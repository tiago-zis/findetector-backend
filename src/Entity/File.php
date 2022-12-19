<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(schema: 'data')]
#[ApiResource]
class File extends EntityBase
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('read')]
    private $id;

    #[ORM\Column(type: 'string')]
    private $name;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups('read')]
    private $mime;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups('read')]
    private $size;

    #[ORM\Column(type: 'string', nullable: true)]
    private $driveId;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups('read')]
    private $originalName;

    public function __construct(?string $originalName, ?string $name, ?string $mime, ?int $size, ?string $driveId, ?User $createdBy, ?\DateTime $createdAt)
    {
        $this->originalName = $originalName;
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

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMime(): ?string
    {
        return $this->mime;
    }

    public function setMime(?string $mime): self
    {
        $this->mime = $mime;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getDriveId(): ?string
    {
        return $this->driveId;
    }

    public function setDriveId(?string $driveId): self
    {
        $this->driveId = $driveId;

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }
}
