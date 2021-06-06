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
     * @PathSlugConstraint(format="complete")
     */
    private ?string $path;

    public function getId() : ?int
    {
        return $this->id;
    }

    public function getMedia() : UploadedFile
    {
        return $this->media;
    }

    public function getPath() : string
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

    public function setPath(string $path) : void
    {
        $this->path = $path;
    }
}