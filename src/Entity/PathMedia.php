<?php

namespace App\Entity;

use App\Repository\PathMediaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PathMediaRepository::class)
 */
class PathMedia
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Path::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Path $path;

    /**
     * @ORM\ManyToOne(targetEntity=Media::class)
     */
    private Media $media;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?Path
    {
        return $this->path;
    }

    public function setPath(Path $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function getMedia(): Media
    {
        return $this->media;
    }

    public function setMedia(Media $media): self
    {
        $this->media = $media;
        return $this;
    }
}
