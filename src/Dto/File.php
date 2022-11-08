<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class File
{
    
    #[Assert\Type("string")]
    #[Assert\NotBlank]
    protected $fileName;

    #[Assert\Type("int")]
    #[Assert\NotBlank]
    #[Assert\Positive]
    protected $size;

    #[Assert\Type("string")]
    protected $type;

    #[Assert\Type("string")]
    #[Assert\NotBlank]
    protected $content;
    
    public function __construct(
        $fileName = '', $size = 0, $type = '', $content = '')
    {
        $this->fileName = $fileName;
        $this->size = $size;
        $this->type = $type;
        $this->content = $content;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
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
    
}
