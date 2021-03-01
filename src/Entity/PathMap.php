<?php

namespace App\Entity;

use App\Repository\PathMapRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PathMapRepository::class)
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="entity_region")
 *
 * A path map represents the root path object to use for generating the detailed menu of a given URL.
 */
class PathMap
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $url;

    /**
     * @ORM\ManyToOne(targetEntity=Path::class, inversedBy="pathMap")
     * @ORM\JoinColumn(nullable=false)
     */
    private Path $path;

    /**
     * @ORM\Column(type="integer")
     */
    private int $priority;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getPath(): Path
    {
        return $this->path;
    }

    public function setPath(Path $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * It seems that Doctrine objects that are cached are not able to correctly handle lazy loading when the serialized
     * object wakes up. We thus have to load each lazy loaded attributes of the entity before its serialization.
     * @noinspection PhpExpressionResultUnusedInspection
     */
    public function __sleep() : array
    {
        $this->getPath();
        return ['id', 'url', 'path', 'priority'];
    }
}
