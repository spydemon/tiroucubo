<?php

namespace App\Entity;

use App\Repository\ArticleVersionRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=ArticleVersionRepository::class)
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE", region="entity_region")
 */
class ArticleVersion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="articleVersions")
     * @ORM\JoinColumn(nullable=false)
     */
    private Article $article;

    /**
     * Allow the specific version to be shown from a URL.
     * @ORM\Column(type="string", length=8)
     */
    private string $slug;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private DateTimeInterface $creation_date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $commit_message;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $active;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $summary;

    /**
     * @ORM\Column(type="text")
     */
    private string $content;

    public function __construct()
    {
        $this->active = false;
        $this->slug = substr(md5(microtime()), 0, 8);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(Article $article): self
    {
        $this->article = $article;
        return $this;
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

    public function getCreationDate(): DateTimeInterface
    {
        return $this->creation_date;
    }

    public function setCreationDate(DateTimeInterface $creation_date): self
    {
        $this->creation_date = $creation_date;
        return $this;
    }

    public function getCommitMessage(): string
    {
        return $this->commit_message;
    }

    public function setCommitMessage(string $commit_message): self
    {
        $this->commit_message = $commit_message;

        return $this;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
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

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }
}
