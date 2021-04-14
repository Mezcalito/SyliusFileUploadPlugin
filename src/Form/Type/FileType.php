<?php

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\FileType as SymfonyFileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class FileType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', TextType::class, [
                'label' => 'sylius.form.file.type',
                'required' => false,
            ])
            ->add('file', SymfonyFileType::class, [
                'label' => 'sylius.form.file.file',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_file';
    }
}
