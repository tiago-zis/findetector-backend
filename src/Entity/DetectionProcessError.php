<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(schema: 'management')]
class DetectionProcessError extends EntityBase
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('read')]
    private $id;

    #[ORM\Column(type: 'text', nullable: true)]
    private $message;

    #[ORM\Column(type: 'json', nullable: true)]
    private $error;

    #[ORM\ManyToOne(targetEntity: Image::class), ORM\JoinColumn(nullable: false)]
    private Image $image;

    public function __construct(Image $image, string $message = null, array $error = null)
    {
        $this->image = $image;
        $this->message = $message;
        $this->error = $error;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getError(): array
    {
        return $this->error;
    }

    public function setError(?array $error): self
    {
        $this->error = $error;

        return $this;
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function setImage(Image $image): self
    {
        $this->image = $image;

        return $this;
    }

}
