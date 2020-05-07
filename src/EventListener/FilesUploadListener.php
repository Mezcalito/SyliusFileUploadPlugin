<?php

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\EventListener;

use Mezcalito\SyliusFileUploadPlugin\Model\FilesAwareInterface;
use Mezcalito\SyliusFileUploadPlugin\Uploader\FileUploaderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class FilesUploadListener
{
    /** @var FileUploaderInterface */
    private $uploader;

    public function __construct(FileUploaderInterface $uploader)
    {
        $this->uploader = $uploader;
    }

    public function uploadFiles(GenericEvent $event): void
    {
        /** @var FilesAwareInterface $subject */
        $subject = $event->getSubject();
        Assert::isInstanceOf($subject, FilesAwareInterface::class);

        $files = $subject->getFiles();
        foreach ($files as $file) {
            if ($file->hasFile()) {
                $this->uploader->upload($file);
            }

            // upload failed ? Let's remove that file.
            if (null === $file->getPath()) {
                $files->removeElement($file);
            }
        }
    }
}
