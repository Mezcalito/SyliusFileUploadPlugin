<?php

namespace Tests\Mezcalito\SyliusFileUploadPlugin\Application\src\Entity;

use Tests\Mezcalito\SyliusFileUploadPlugin\Application\src\Form\Type\BookType;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Mezcalito\SyliusFileUploadPlugin\Model\FilesAwareInterface;
use Mezcalito\SyliusFileUploadPlugin\Model\FilesAwareTrait;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Resource\Metadata\AsResource;
use Sylius\Resource\Metadata\BulkDelete;
use Sylius\Resource\Metadata\Create;
use Sylius\Resource\Metadata\Delete;
use Sylius\Resource\Metadata\Index;
use Sylius\Resource\Metadata\Show;
use Sylius\Resource\Metadata\Update;


#[ORM\Entity]
#[ORM\Table(name: 'test_book')]
#[AsResource(alias: 'test.book', section: 'admin', formType: BookType::class, templatesDir: '@SyliusAdmin\\shared\\crud', routePrefix: 'admin')]
#[Index(grid: 'test_book')]
#[Create]
#[Update]
#[Delete]
#[Show]
#[BulkDelete]
#[Create(formType: BookType::class)]
class Book implements ResourceInterface, FilesAwareInterface
{

    use FilesAwareTrait {
        FilesAwareTrait::__construct as private initializeFilesCollection;
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected ?int $id = null;

    #[ORM\OneToMany( mappedBy: 'owner', targetEntity: BookFile::class, cascade: ['all'], orphanRemoval: true)]
    #[ORM\JoinTable(name: 'test_book_file')]
    protected Collection $files;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function __construct() {
        $this->initializeFilesCollection();
    }


}
