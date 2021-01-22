<?php

namespace App\Entity;

use App\Repository\PathRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PathRepository::class)
 * @ORM\Table(name="path")
 * @ORM\Cache(usage="READ_ONLY", region="entity_region")
 */
class Path
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
    private string $slug;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

    /**
     * @ORM\ManyToOne(targetEntity=Path::class, inversedBy="child")
     */
    private ?Path $parent;

    /**
     * @ORM\OneToMany(targetEntity=Path::class, mappedBy="parent")
     */
    private Collection $child;

    /**
     * @ORM\OneToMany(targetEntity=PathMap::class, mappedBy="path", orphanRemoval=true)
     */
    private Collection $pathMap;

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="path")
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     */
    private Collection $articles;

    public function __construct()
    {
        $this->child = new ArrayCollection();
        $this->pathMap= new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getParent(): ?Path
    {
        return $this->parent;
    }

    public function setParent(?Path $parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getChild(): Collection
    {
        return $this->child;
    }

    public function addChild(self $child): self
    {
        if (!$this->child->contains($child)) {
            $this->child[] = $child;
            $child->setParent($this);
        }
        return $this;
    }

    public function removeChild(self $child): self
    {
        if ($this->child->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        $path = '';
        if ($parent = $this->getParent()) {
            $path .= $parent->__toString() . '/';
        }
        $path .= $this->getSlug();
        return $path;
    }

    public function getPathMap(): Collection
    {
        return $this->pathMap;
    }

    public function addPathMap(PathMap $pathMap): self
    {
        if (!$this->pathMap->contains($pathMap)) {
            $this->pathMap[] = $pathMap;
            $pathMap->setPath($this);
        }
        return $this;
    }

    public function removePathMap(PathMap $pathMap): self
    {
        if ($this->pathMap->removeElement($pathMap)) {
            // set the owning side to null (unless already changed)
            if ($pathMap->getPath() === $this) {
                $pathMap->setPath(null);
            }
        }
        return $this;
    }

    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setPath($this);
        }
        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            if ($article->getPath() === $this) {
                $article->setPath(null);
            }
        }
        return $this;
    }
}
