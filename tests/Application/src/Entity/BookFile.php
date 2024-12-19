<?php

namespace Tests\Mezcalito\SyliusFileUploadPlugin\Application\src\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Mezcalito\SyliusFileUploadPlugin\Model\File;
use Sylius\Resource\Metadata\AsResource;
use Tests\Mezcalito\SyliusFileUploadPlugin\Application\src\Form\Type\BookFileType;

#[ORM\Entity]
#[ORM\Table(name: 'test_book_file')]
#[AsResource(alias: 'test.book_file', section: 'admin', formType: BookFileType::class, routePrefix: 'admin')]
class BookFile extends File
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected ?int $id = null;

    #[ORM\ManyToOne( targetEntity: Book::class, inversedBy: 'files')]
    #[ORM\JoinColumn(name: 'owner_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    protected mixed $owner;
}
