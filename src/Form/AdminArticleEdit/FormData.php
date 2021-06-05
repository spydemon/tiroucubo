<?php

namespace App\Form\AdminArticleEdit;

use App\Entity\Article;
use App\Entity\ArticleVersion;
use App\EntityConstraints\PathSlugConstraint;
use stdClass;
use Symfony\Component\Validator\Constraints as Assert;

class FormData extends stdClass
{
    /**
     * @Assert\Positive
     */
    private ?int $id;

    /**
     * @Assert\NotBlank
     */
    private ?string $body;

    /**
     * @Assert\NotBlank
     */
    private ?string $commit;

    /**
     * @PathSlugConstraint(format="complete")
     */
    private ?string $path;

    /**
     * @Assert\NotBlank
     */
    private ?string $summary;

    /**
     * @Assert\NotBlank
     */
    private ?string $title;

    public function feed(Article $article, ArticleVersion $version)
    {
        $this->setId($article->getId());
        $this->setBody($version->getContent());
        $this->setTitle($article->getTitle());
        $this->setPath($article->getPath());
        $this->setSummary($version->getSummary());
    }

    public function getBody() : ?string
    {
        return $this->body;
    }

    public function getCommit() : ?string
    {
        return $this->commit;
    }

    public function getId() : ?int
    {
        return $this->id;
    }

    public function getPath() : ?string
    {
        return $this->path;
    }

    public function getSummary() : ?string
    {
        return $this->summary;
    }

    public function getTitle() : ?string
    {
        return $this->title;
    }

    public function setBody(?string $body) : void
    {
        $this->body = $body;
    }

    public function setCommit(?string $commit) : void
    {
        $this->commit = $commit;
    }

    public function setId(int $id) : void
    {
        $this->id = $id;
    }

    public function setPath(string $path) : void
    {
        $this->path = $path;
    }

    public function setSummary(?string $summary) : void
    {
        $this->summary = $summary;
    }

    public function setTitle(string $title) : void
    {
        $this->title = $title;
    }


}