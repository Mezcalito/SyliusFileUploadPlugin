<?php

declare(strict_types=1);

namespace Tests\Mezcalito\SyliusFileUploadPlugin\Application\src\Form\Type;

use Mezcalito\SyliusFileUploadPlugin\Form\Type\FileType;

class BookFileType extends FileType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'test_admin_book_file';
    }
}
