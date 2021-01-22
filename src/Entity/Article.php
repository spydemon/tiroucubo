<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use DateTimeInterface;
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?Path
    {
        return $this->path;
    }

    public function setPath(?Path $path): self
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
        $this->title = $title;

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

    public function getUpdateDate(): DateTimeInterface
    {
        return $this->update_date;
    }

    public function setUpdateDate(DateTimeInterface $update_date): self
    {
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
        $this->content = $content;
        return $this;
    }
}
