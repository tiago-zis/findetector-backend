<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;

#[ORM\Entity]
#[ORM\Table(schema: 'management')]
#[ApiResource()]
#[Get(security: "is_granted('ROLE_USER')")]
class TermsOfUse extends EntityBase 
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('read')]
    private $id;

    #[ORM\Column(type: 'text')]
    #[Groups('read')]
    private $content;

    #[ORM\Column(type: 'string')]
    #[Groups('read')]
    private $version;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

}
