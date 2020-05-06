<?php

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\Generator;

use Mezcalito\SyliusFileUploadPlugin\Model\FileInterface;

interface FilePathGeneratorInterface
{
    public function generate(FileInterface $file): string;
}
