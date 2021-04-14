<?php

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

trait FilesAwareTrait
{
    /** @var Collection|FileInterface[] */
    protected $files;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    /**
     * {@inheritDoc}
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    /**
     * {@inheritDoc}
     */
    public function setFiles(Collection $files): void
    {
        $this->files = $files;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilesByType(string $type): Collection
    {
        return $this->files->filter(function (FileInterface $file) use ($type): bool {
            return $type === $file->getType();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getFilesByMimeType(string $mimeType): Collection
    {
        return $this->files->filter(function (FileInterface $file) use ($mimeType): bool {
            return $mimeType === $file->getMimeType();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getFilesByTypeAndMimeType(string $type, string $mimeType): Collection
    {
        return $this->files->filter(function (FileInterface $file) use ($type, $mimeType): bool {
            return $type === $file->getType() && $mimeType === $file->getMimeType();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function hasFiles(): bool
    {
        return !$this->files->isEmpty();
    }

    /**
     * {@inheritDoc}
     */
    public function hasFile(FileInterface $file): bool
    {
        return $this->files->contains($file);
    }

    /**
     * {@inheritDoc}
     */
    public function addFile(FileInterface $file): void
    {
        $file->setOwner($this);
        $this->files->add($file);
    }

    /**
     * {@inheritDoc}
     */
    public function removeFile(FileInterface $file): void
    {
        if ($this->hasFile($file)) {
            $file->setOwner(null);
            $this->files->removeElement($file);
        }
    }
}
