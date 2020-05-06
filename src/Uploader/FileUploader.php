<?php

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\Uploader;

use Gaufrette\Filesystem;
use Mezcalito\SyliusFileUploadPlugin\Generator\FilePathGeneratorInterface;
use Mezcalito\SyliusFileUploadPlugin\Model\FileInterface;
use Symfony\Component\HttpFoundation\File\File;
use Webmozart\Assert\Assert;

final class FileUploader implements FileUploaderInterface
{
    /** @var Filesystem */
    private $filesystem;

    /** @var FilePathGeneratorInterface */
    private $filePathGenerator;

    public function __construct(Filesystem $filesystem, FilePathGeneratorInterface $filePathGenerator)
    {
        $this->filesystem = $filesystem;
        $this->filePathGenerator = $filePathGenerator;
    }

    public function upload(FileInterface $file): void
    {
        if (!$file->hasFile()) {
            return;
        }

        /** @var File $uploadedFile */
        $uploadedFile = $file->getFile();

        Assert::isInstanceOf($uploadedFile, File::class);

        if (null !== $file->getPath() && $this->filesystem->has($file->getPath())) {
            $this->remove($file->getPath());
        }

        do {
            $path = $this->filePathGenerator->generate($file);
        } while ($this->isAdBlockingProne($path) || $this->filesystem->has($path));

        $file->setPath($path);

        $this->filesystem->write(
            $file->getPath(),
            file_get_contents($uploadedFile->getPathname())
        );
    }

    public function remove(string $path): bool
    {
        if ($this->filesystem->has($path)) {
            return $this->filesystem->delete($path);
        }

        return false;
    }

    private function isAdBlockingProne(string $path): bool
    {
        return false !== strpos($path, 'ad');
    }
}
