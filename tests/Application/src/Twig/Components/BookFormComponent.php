<?php

namespace Tests\Mezcalito\SyliusFileUploadPlugin\Application\src\Twig\Components;

use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Tests\Mezcalito\SyliusFileUploadPlugin\Application\src\Entity\Book;
use Tests\Mezcalito\SyliusFileUploadPlugin\Application\src\Form\Type\BookType;

#[AsLiveComponent(template: '@SyliusAdmin/shared/crud/common/content/form.html.twig')]
class BookFormComponent extends AbstractController
{
    use LiveCollectionTrait;
    use DefaultActionTrait;
    use HookableLiveComponentTrait; // for Sylius Twig Hooks

    #[LiveProp]
    public Book $resource;

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(BookType::class, $this->resource);
    }
}
