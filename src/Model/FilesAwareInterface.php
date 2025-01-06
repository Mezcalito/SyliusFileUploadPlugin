<?php

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\Model;

use Doctrine\Common\Collections\Collection;

interface FilesAwareInterface
{
    /**
     * @return Collection<int,FileInterface>
     */
    public function getFiles(): Collection;

    /**
     * @param Collection<int,FileInterface> $files
     */
    public function setFiles(Collection $files): void;

    /**
     * @return Collection<int,FileInterface>
     */
    public function getFilesByType(string $type): Collection;

    /**
     * @return Collection<int,FileInterface>
     */
    public function getFilesByMimeType(string $mimeType): Collection;

    /**
     * @return Collection<int,FileInterface>
     */
    public function getFilesByTypeAndMimeType(string $type, string $mimeType): Collection;

    public function hasFiles(): bool;

    public function hasFile(FileInterface $file): bool;

    public function addFile(FileInterface $file): void;

    public function removeFile(FileInterface $file): void;
}
