<?php

namespace App\Form\AdminMediaEdit;

use App\EntityConstraints\PathSlugConstraint;
use stdClass;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class FormData extends stdClass
{
    /**
     * @Assert\Positive
     */
    private ?int $id;

    private UploadedFile $media;

    /**
     * @Assert\NotBlank(message="At least one path is needed.")
     * @Assert\All({
     *   @PathSlugConstraint(format="complete")
     * })
     */
    private ?array $path;

    public function getId() : ?int
    {
        return $this->id;
    }

    public function getMedia() : UploadedFile
    {
        return $this->media;
    }

    public function getPath() : ?array
    {
        return $this->path;
    }

    public function setId(int $id) : void
    {
        $this->id = $id;
    }

    public function setMedia(UploadedFile $media) : void
    {
        $this->media = $media;
    }

    public function setPath(array $path) : void
    {
        $this->path = $path;
    }
}