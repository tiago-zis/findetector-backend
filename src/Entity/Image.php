<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Filter\StatusFilter;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    order: ['createdAt' => 'DESC']

)]
#[
    ApiFilter(
        SearchFilter::class,
        properties: [
            'file.originalName' => 'partial'
        ]
    )
]

#[
    ApiFilter(
        StatusFilter::class,
        properties: [
            'status'
        ]
    )
]

#[ORM\Entity]
#[ORM\Table(schema: 'data')]
class Image extends EntityBase
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('read')]
    private $id;

    #[ORM\ManyToOne(targetEntity: File::class), ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups('read')]
    private File $file;

    #[ORM\Column(length: 20)]
    #[Assert\NotNull]
    #[Assert\Choice(['uploaded', 'processing', 'finished'])]
    #[Groups('read')]
    private string $status = 'uploaded';

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups('read')]
    private ?array $processedData = [];

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups('read')]
    protected ?\DateTime $processingDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getProcessedData(): array
    {
        return $this->processedData;
    }

    public function setProcessedData(?array $processedData): self
    {
        $this->processedData = $processedData;

        return $this;
    }

    public function getProcessingDate(): ?\DateTimeInterface
    {
        return $this->processingDate;
    }

    public function setProcessingDate(?\DateTimeInterface $processingDate): self
    {
        $this->processingDate = $processingDate;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    #[Groups('read')]
    public function getCreationDate()
    {
        return $this->getCreatedAt();
    }
}
