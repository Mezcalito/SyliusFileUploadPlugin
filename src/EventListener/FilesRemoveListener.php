<?php

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Mezcalito\SyliusFileUploadPlugin\Model\FileInterface;
use Mezcalito\SyliusFileUploadPlugin\Uploader\FileUploaderInterface;

final class FilesRemoveListener
{
    /** @var FileUploaderInterface */
    private $uploader;

    /** @var CacheManager */
    private $cacheManager;

    /** @var FilterManager */
    private $filterManager;

    public function __construct(FileUploaderInterface $uploader, CacheManager $cacheManager, FilterManager $filterManager) {
        $this->uploader = $uploader;
        $this->cacheManager = $cacheManager;
        $this->filterManager = $filterManager;
    }

    public function postRemove(LifecycleEventArgs $event): void
    {
        $file = $event->getEntity();

        if ($file instanceof FileInterface) {
            $this->uploader->remove($file->getPath());
            $this->cacheManager->remove(
                $file->getPath(),
                array_keys($this->filterManager->getFilterConfiguration()->all())
            );
        }
    }
}
