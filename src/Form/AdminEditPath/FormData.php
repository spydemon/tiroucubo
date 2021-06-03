<?php

namespace App\Form\AdminEditPath;

use App\Entity\Path;
use stdClass;
use Symfony\Component\Validator\Constraints as Assert;
use App\EntityConstraints\PathSlugConstraint;

/**
 * TODO: set validation assert.
 */
class FormData extends stdClass
{
    private ?string $customTemplate = null;
    private ?int $id = null;

    /**
     * @PathSlugConstraint(format="complete")
     */
    private ?string $slug = null;

    /**
     * @Assert\NotBlank
     */
    private ?string $title = null;

    private ?int $type = null;

    public function feed(Path $path) : self
    {
        $this->setCustomTemplate($path->getCustomTemplate());
        $this->setId($path->getId());
        $this->setSlug($path->getSlug());
        $this->setTitle($path->getTitle());
        $this->setType($path->getType());
        return $this;
    }

    public function getCustomTemplate(): ?string
    {
        return $this->customTemplate;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setCustomTemplate(?string $customTemplate): void
    {
        $this->customTemplate = $customTemplate;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function setType(?int $type): void
    {
        $this->type = $type;
    }
}