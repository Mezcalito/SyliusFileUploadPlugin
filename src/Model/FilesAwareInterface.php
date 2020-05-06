<?php

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\Model;

use Doctrine\Common\Collections\Collection;

interface FilesAwareInterface
{
    /**
     * @return Collection|FileInterface[]
     */
    public function getFiles(): Collection;

    /**
     * @param Collection|FileInterface[] $files
     */
    public function setFiles(Collection $files): void;

    /**
     * @param $type string
     * @return Collection|FileInterface[]
     */
    public function getFilesByType(string $type): Collection;

    /**
     * @param string $mimeType
     * @return Collection|FileInterface[]
     */
    public function getFilesByMimeType(string $mimeType): Collection;

    /**
     * @param string $type
     * @param string $mimeType
     * @return Collection|FileInterface[]
     */
    public function getFilesByTypeAndMimeType(string $type, string $mimeType): Collection;

    public function hasFiles(): bool;

    public function hasFile(FileInterface $file): bool;

    public function addFile(FileInterface $file): void;

    public function removeFile(FileInterface $file): void;
}
