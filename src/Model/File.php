<?php

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\Model;

use SplFileInfo;
use Symfony\Component\Mime\MimeTypes;

abstract class File implements FileInterface
{

    protected ?int $id = null;

    protected ?string $type = null;

    protected ?string $mimeType = null;

    protected ?SplFileInfo $file;

    protected ?string $path = null;

    protected mixed $owner;

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * {@inheritDoc}
     */
    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile(): ?SplFileInfo
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function setFile(?SplFileInfo $file): void
    {
        $this->file = $file;

        if (null !== $file) {
            $this->mimeType = (new MimeTypes())->guessMimeType($file->getRealPath());
        } else {
            $this->mimeType = null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasFile(): bool
    {
        return null !== $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPath(): bool
    {
        return null !== $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getOwner(): mixed
    {
        return $this->owner;
    }

    /**
     * {@inheritdoc}
     */
    public function setOwner(mixed $owner): void
    {
        $this->owner = $owner;
    }
}
