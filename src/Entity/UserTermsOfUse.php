<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;

#[ORM\Entity]
#[ORM\Table(schema: 'management')]
#[ApiResource()]
#[Get(security: "is_granted('ROLE_USER')")]
class UserTermsOfUse 
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('read')]
    private $id;

    #[ORM\Column(type: 'boolean', nullable:true)]
    #[Groups('read')]
    private $accepted;

    #[ORM\Column(type: 'datetime')]
    #[Groups('read')]
    protected $acceptanceDate;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?TermsOfUse $terms = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(?bool $accepted): self
    {
        $this->accepted = $accepted;

        return $this;
    }

    public function getAcceptanceDate(): ?\DateTimeInterface
    {
        return $this->acceptanceDate;
    }

    public function setAcceptanceDate(\DateTimeInterface $acceptanceDate): self
    {
        $this->acceptanceDate = $acceptanceDate;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTerms(): ?TermsOfUse
    {
        return $this->terms;
    }

    public function setTerms(?TermsOfUse $terms): self
    {
        $this->terms = $terms;

        return $this;
    }

}
