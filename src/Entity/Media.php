<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=MediaRepository::class)
 */
class Media
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="blob")
     * @var resource
     */
    private $content;

    private array $allowedResourceType = ['image/webp'];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content): self
    {
        if (!in_array(mime_content_type($content), $this->allowedResourceType)) {
             throw new Exception("Content type is not allowed.");
        }
        $this->content = $content;
        return $this;
    }
}
