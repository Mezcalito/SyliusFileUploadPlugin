<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\EventListener;

use Mezcalito\SyliusFileUploadPlugin\Model\FileAwareInterface;
use Mezcalito\SyliusFileUploadPlugin\Uploader\FileUploaderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class FileUploadListener
{
    /** @var FileUploaderInterface */
    private $uploader;

    public function __construct(FileUploaderInterface $uploader)
    {
        $this->uploader = $uploader;
    }

    public function uploadFile(GenericEvent $event): void
    {
        /** @var FileAwareInterface $subject */
        $subject = $event->getSubject();
        Assert::isInstanceOf($subject, FileAwareInterface::class);

        if (null !== $uploadedFile = $subject->getFile()) {
            $this->uploader->upload($uploadedFile);
        }
    }
}
