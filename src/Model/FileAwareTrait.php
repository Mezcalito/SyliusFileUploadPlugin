<?php

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\Model;

trait FileAwareTrait
{
    protected ?FileInterface $file;

    public function getFile(): ?FileInterface
    {
        return $this->file;
    }

    public function setFile(?FileInterface $file): void
    {
        $this->file = $file;
    }
}
