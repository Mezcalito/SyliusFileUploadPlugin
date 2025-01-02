<?php

declare(strict_types=1);

namespace Tests\Mezcalito\SyliusFileUploadPlugin\Application\src\Form\Type;

use Sylius\Bundle\AdminBundle\Form\Type\AddButtonType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\UX\LiveComponent\Form\Type\LiveCollectionType;

class BookType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('files', LiveCollectionType::class, [
            'entry_type' => BookFileType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'button_add_type' => AddButtonType::class,
            'button_add_options' => [
                'label' => 'sylius.ui.add',
            ],
            'button_delete_options' => [
                'label' => 'sylius.ui.delete',
            ],
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'test_admin_book';
    }
}
