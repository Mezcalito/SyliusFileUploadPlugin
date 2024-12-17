<?php

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Mezcalito\SyliusFileUploadPlugin\Model\FileInterface;
use Mezcalito\SyliusFileUploadPlugin\Uploader\FileUploaderInterface;

final class FilesRemoveListener
{

    /** @var string[] */
    private array $filesToDelete = [];

    public function __construct(
        protected readonly FileUploaderInterface $uploader,
        protected readonly CacheManager $cacheManager,
        protected readonly FilterManager $filterManager) {
    }

    public function onFlush(OnFlushEventArgs $event): void
    {
        foreach ($event->getObjectManager()->getUnitOfWork()->getScheduledEntityDeletions() as $entityDeletion) {
            if (!$entityDeletion instanceof FileInterface) {
                continue;
            }

            if (!in_array($entityDeletion->getPath(), $this->filesToDelete, true)) {
                $this->filesToDelete[] = (string) $entityDeletion->getPath();
            }
        }
    }

    public function postFlush(PostFlushEventArgs $event): void
    {
        foreach ($this->filesToDelete as $key => $filePath) {
            $this->uploader->remove($filePath);
            $this->cacheManager->remove(
                $filePath,
                array_keys($this->filterManager->getFilterConfiguration()->all())
            );
            unset($this->filesToDelete[$key]);
        }
    }
}
