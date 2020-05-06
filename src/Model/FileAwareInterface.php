<?php

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\Model;

interface FileAwareInterface
{
    public function getFile(): FileInterface;

    public function setFile(FileInterface $file): void;
}
