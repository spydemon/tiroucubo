<?php

namespace App\Entity;

use App\Exception\InvalidEntityParameterException;
use App\Repository\ArticleRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="entity_region")
 */
class Article
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Path::class, inversedBy="articles")
     */
    private ?Path $path;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $title;

    /**
     * @ORM\Column(type="datetimetz")
     * @Gedmo\Timestampable(on="create")
     */
    private DateTimeInterface $creation_date;

    /**
     * @ORM\Column(type="datetimetz")
     * @Gedmo\Timestampable(on="update")
     */
    private DateTimeInterface $update_date;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $summary;

    /**
     * @ORM\Column(type="text")
     */
    private string $content;

    /**
     * @ORM\OneToMany(targetEntity=ArticleVersion::class, mappedBy="article", orphanRemoval=true)
     * @ORM\Cache("NONSTRICT_READ_WRITE")
     */
    private Collection $articleVersions;

    public function __construct()
    {
        $this->articleVersions = new ArrayCollection();
    }

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        if (!$title) {
            throw new InvalidEntityParameterException('The title can not be null.', $this);
        }
        $this->title = $title;
        return $this;
    }

    public function getCreationDate(): DateTimeInterface
    {
        return $this->creation_date;
    }

    public function setCreationDate(DateTimeInterface $creation_date): self
    {
        if (isset($this->update_date) && $creation_date > $this->update_date) {
            throw new InvalidEntityParameterException('The creation date can not be later than the update one.', $this);
        }
        $this->creation_date = $creation_date;
        return $this;
    }

    public function getUpdateDate(): DateTimeInterface
    {
        return $this->update_date;
    }

    public function setUpdateDate(DateTimeInterface $update_date): self
    {
        if (isset($this->creation_date) && $update_date < $this->creation_date) {
            throw new InvalidEntityParameterException('The update date can not be before the creation one.', $this);
        }
        $this->update_date = $update_date;
        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        if (!$content) {
            throw new InvalidEntityParameterException('The content can not be null.', $this);
        }
        $this->content = $content;
        return $this;
    }

    /**
     * @return Collection|ArticleVersion[]
     */
    public function getArticleVersions(): Collection
    {
        return $this->articleVersions;
    }

    public function addArticleVersion(ArticleVersion $articleVersion): self
    {
        if (!$this->articleVersions->contains($articleVersion)) {
            $this->articleVersions[] = $articleVersion;
            $articleVersion->setArticle($this);
        }
        return $this;
    }

    public function removeArticleVersion(ArticleVersion $articleVersion): self
    {
        if ($this->articleVersions->removeElement($articleVersion)) {
            // set the owning side to null (unless already changed)
            if ($articleVersion->getArticle() === $this) {
                $articleVersion->setArticle(null);
            }
        }
        return $this;
    }
}
