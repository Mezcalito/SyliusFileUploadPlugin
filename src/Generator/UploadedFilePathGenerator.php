<?php

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\Generator;

use Mezcalito\SyliusFileUploadPlugin\Model\FileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadedFilePathGenerator implements FilePathGeneratorInterface
{
    public function generate(FileInterface $file): string
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $file->getFile();

        $hash = bin2hex(random_bytes(16));

        return $this->expandPath($hash.'.'.$uploadedFile->guessExtension());
    }

    private function expandPath(string $path): string
    {
        return sprintf('%s/%s/%s', substr($path, 0, 2), substr($path, 2, 2), substr($path, 4));
    }
}
