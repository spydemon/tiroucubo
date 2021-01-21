<?php

namespace App\Entity;

use App\Repository\PathTranslationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PathTranslationRepository::class)
 */
class PathTranslation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $path_fr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $path_en;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPathFr(): ?string
    {
        return $this->path_fr;
    }

    public function setPathFr(?string $path_fr): self
    {
        $this->path_fr = $path_fr;
        return $this;
    }

    public function getPathEn(): ?string
    {
        return $this->path_en;
    }

    public function setPathEn(?string $path_en): self
    {
        $this->path_en = $path_en;
        return $this;
    }
}
