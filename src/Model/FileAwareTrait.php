<?php

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\Model;

trait FileAwareTrait
{
    /** @var null|FileInterface */
    protected $file;

    /**
     * {@inheritDoc}
     */
    public function getFile(): ?FileInterface
    {
        return $this->file;
    }

    /**
     * {@inheritDoc}
     */
    public function setFile(?FileInterface $file): void
    {
        $this->file = $file;
    }
}
