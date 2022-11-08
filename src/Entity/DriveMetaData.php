<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(schema: 'management')]
class DriveMetaData
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string')]
    private $driveId;

    #[ORM\Column(type: 'string')]
    private $folderName;

    #[ORM\Column(type: 'string')]
    private $dataType;

    #[ORM\Column(type: 'json', nullable: true)]
    private $metaData;

    #[ORM\Column(type: 'string', nullable: true)]
    private $parentId;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getFolderName(): ?string
    {
        return $this->folderName;
    }

    public function setFolderName(string $folderName): self
    {
        $this->folderName = $folderName;

        return $this;
    }

    public function getDataType(): ?string
    {
        return $this->dataType;
    }

    public function setDataType(string $dataType): self
    {
        $this->dataType = $dataType;

        return $this;
    }

    public function getMetaData(): ?array
    {
        return $this->metaData;
    }

    public function setMetaData(?array $metaData): self
    {
        $this->metaData = $metaData;

        return $this;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function setParentId(?string $parentId): self
    {
        $this->parentId = $parentId;

        return $this;
    }

    
}
