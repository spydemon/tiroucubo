<?php

namespace App\Entity;

use App\Exception\InvalidEntityParameterException;
use App\Repository\PathRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=PathRepository::class)
 * @ORM\Table(name="path")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="entity_region")
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

    /**
     * @ORM\Column(type="integer")
     */
    private int $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $customTemplate = null;

    /**
     * If the path has the "dynamic" type, it means that it represents stuff that could be edited from the back-office.
     * It's displaying state should thus be computed in order to determine if the path should be displayed on the menu
     * and if it should return a 404 error or not.
     */
    public const TYPE_DYNAMIC = 1;

    /**
     * If the path has the "always visible" type, it means that its content is static and that its displaying state
     * has be set manually to visible.
     */
    public const TYPE_ALWAYS_VISIBLE = 2;

    /**
     * Path with the "media" type will represent static contents like WebP images. They will never be present in the
     * menu.
     */
    public const TYPE_MEDIA = 3;

    public function __construct()
    {
        $this->child = new ArrayCollection();
        $this->pathMap= new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->setType(self::TYPE_DYNAMIC);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setSlug(string $slug): self
    {
        if (preg_match('#[^a-z.\d_-]#', $slug)) {
            throw new InvalidEntityParameterException('Slug contains invalid characters.', $this);
        }
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

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $allowedValues = [self::TYPE_DYNAMIC, self::TYPE_ALWAYS_VISIBLE, self::TYPE_MEDIA];
        if (!in_array($type, $allowedValues)) {
            throw new Exception('Invalid type set.');
        }
        $this->type = $type;
        return $this;
    }

    public function isFinal() : bool
    {
        return count($this->getChild()) > 0 ? false : true;
    }

    public function getCustomTemplate() : ?string
    {
        return $this->customTemplate;
    }

    public function setCustomTemplate(?string $customTemplate) : self
    {
        $this->customTemplate = $customTemplate;
        return $this;
    }

    /**
     * It seems that Doctrine objects that are cached are not able to correctly handle lazy loading when the serialized
     * object wakes up. We thus have to load each lazy loaded attributes of the entity before its serialization.
     * @noinspection PhpExpressionResultUnusedInspection
     */
    public function __sleep() : array
    {
        $this->getChild();
        $this->getPathMap();
        $this->getArticles();
        $this->getParent();
        return ['id', 'slug', 'title', 'parent', 'child', 'pathMap', 'articles', 'type', 'customTemplate'];
    }
}
