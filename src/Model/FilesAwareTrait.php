<?php

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

trait FilesAwareTrait
{
    /** @var Collection<int, FileInterface> */
    protected Collection $files;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function setFiles(Collection $files): void
    {
        $this->files = $files;
    }

    public function getFilesByType(string $type): Collection
    {
        return $this->files->filter(function (FileInterface $file) use ($type): bool {
            return $type === $file->getType();
        });
    }

    public function getFilesByMimeType(string $mimeType): Collection
    {
        return $this->files->filter(function (FileInterface $file) use ($mimeType): bool {
            return $mimeType === $file->getMimeType();
        });
    }

    public function getFilesByTypeAndMimeType(string $type, string $mimeType): Collection
    {
        return $this->files->filter(function (FileInterface $file) use ($type, $mimeType): bool {
            return $type === $file->getType() && $mimeType === $file->getMimeType();
        });
    }

    public function hasFiles(): bool
    {
        return !$this->files->isEmpty();
    }

    public function hasFile(FileInterface $file): bool
    {
        return $this->files->contains($file);
    }

    public function addFile(FileInterface $file): void
    {
        $file->setOwner($this);
        $this->files->add($file);
    }

    public function removeFile(FileInterface $file): void
    {
        if ($this->hasFile($file)) {
            $file->setOwner(null);
            $this->files->removeElement($file);
        }
    }
}
