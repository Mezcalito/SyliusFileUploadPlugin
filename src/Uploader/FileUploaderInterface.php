<?php

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\Uploader;

use Mezcalito\SyliusFileUploadPlugin\Model\FileInterface;

interface FileUploaderInterface
{
    public function upload(FileInterface $file): void;

    public function remove(string $path): bool;
}
