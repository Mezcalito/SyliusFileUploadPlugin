<?php

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\Model;

use Symfony\Component\Mime\MimeTypes;

abstract class File implements FileInterface
{
    protected ?int $id = null;

    protected ?string $type = null;

    protected ?string $mimeType = null;

    protected ?\SplFileInfo $file = null;

    protected ?string $path = null;

    protected mixed $owner;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function getFile(): ?\SplFileInfo
    {
        return $this->file;
    }

    public function setFile(?\SplFileInfo $file): void
    {
        $this->file = $file;

        if (null !== $file) {
            $this->mimeType = (new MimeTypes())->guessMimeType($file->getRealPath());
        } else {
            $this->mimeType = null;
        }
    }

    public function hasFile(): bool
    {
        return null !== $this->file;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    public function hasPath(): bool
    {
        return null !== $this->path;
    }

    public function getOwner(): mixed
    {
        return $this->owner;
    }

    public function setOwner(mixed $owner): void
    {
        $this->owner = $owner;
    }
}
