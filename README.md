# Mezcalito Sylius File Upload Plugin

This plugin works almost the same as the standard Sylius images uploading (see [How to add images to an entity?][1] from the Sylius Documentation), except that it accepts any kind of file types.

Initially this was proposed as a pull request on Sylius core, but it was declined as it was not backwards compatible with Sylius 1.x (Here is the [link to the initial PR](https://github.com/Sylius/Sylius/pull/9224)), so here it is as a Sylius plugin.

## Installation

First require this package with composer

```shell script
$ composer require mezcalito/sylius-file-upload-plugin
```

Then add the bundle to your `config/bundles.php` file

```php
// bundles.php

return [
    // ...
    Mezcalito\SyliusFileUploadPlugin\MezcalitoSyliusFileUploadPlugin::class => ['all' => true], 
];
```

In your `config/packages/_sylius.yaml` file, add the following

```yaml
# config/packages/_sylius.yaml
imports:
    - { resource: "@MezcalitoSyliusFileUploadPlugin/Resources/config/app/config.yml" }
```

> This file defines the gaufrette `filesystem` and `adapter` used by the plugin, which you can override.

## Usage

> This section is an adaptation of the official Sylius documentation about [How to add images to an entity ?][1].
>
>Extending entities with an ``files`` field is quite a popular use case.
 In this cookbook we will present how to **add files to the Shipping Method entity**.

you can see this sample in test/Application.

### 1. Create the book class with the FilesAwareInterface


```php
# src/entity/book.php
<?php

namespace App\Entity;

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
#[ORM\Table(name: 'app_book')]
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
    #[ORM\JoinTable(name: 'app_book_file')]
    protected Collection $files;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function __construct() {
        $this->initializeFilesCollection();
    }


}

```

> Here we used the `FilesAwareTrait` which is provided for convenience.

### 2. Register your book as a resource's model class

We will use the attributes:

```php
# src/entity/Book.php
#[AsResource(alias: 'app.book', section: 'admin', formType: BookType::class, templatesDir: '@SyliusAdmin\\shared\\crud', routePrefix: 'admin')]
#[Index(grid: 'app_book')]
#[Create]
#[Update]
#[Delete]
#[Show]
#[BulkDelete]
#[Create(formType: BookType::class)]
```

### 3. Create the BookFile class

In the `App\Entity` namespace place the `BookFile` class which should look like this:

```php
# src/entity/BookFile.php
<?php

declare(strict_types=1);

namespace App\Entity;

use Mezcalito\SyliusFileUploadPlugin\Model\File;

class BookFile extends File
{
}
```

### 4. Add the mapping and resource attributes for the BookFile

Your new entity will be saved in the database, therefore it needs a mapping file, where you will set the `Book` as the `owner`
of the `BookFile`.

```php
# src/entity/BookFile.php

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
```

### 5. Create the book form type

It needs to have the files field as a LiveCollectionType.

```php
<?php

declare(strict_types=1);

namespace App\Form\Type;

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
                'label' => 'sylius.ui.add_rule',
            ],
            'button_delete_options' => [
                'label' => false,
            ],
        ]);
    }
    public function getBlockPrefix(): string
    {
        return 'app_admin_book';
    }
}

```

Declare the form

```yaml
    app.book.form.type:
        class: App\Form\Type\BookType
        tags:
            - { name: form.type }
        arguments: ['%app.model.book.class%', '%app.book.form.type.validation_groups%']
```

In case you need only a single file upload, this can be done in 2 very easy steps.

First, in the code for the form provided above set `allow_add` and `allow_delete` to `false`

Second, in the `__construct` method of the `Book` entity you defined earlier add the following:

```php
public function __construct()
{
    parent::__construct();
    $this->files = new ArrayCollection();
    $this->addFile(new BookFile());
}
```


### 6. Create the book file form type

```php
<?php

declare(strict_types=1);

namespace App\Form\Type;

use Mezcalito\SyliusFileUploadPlugin\Form\Type\FileType;

class BookFileType extends FileType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_admin_book_file';
    }
}
```
Declare the form
```yaml
    app.book_file.form.type:
        class: App\Form\Type\BookFileType
        tags:
            - { name: form.type }
        arguments: ['%app.model.book_file.class%', '%app.book_file.form.type.validation_groups%']

```


### 7. Create the BookGrid
create the file template field for the grid you can use `bin/console make:grid`


```php
# src/Grid/BookGrid.php
<?php

namespace App\Grid;

use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;
use App\Entity\Book;

final class BookGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public static function getName(): string
    {
        return 'app_book';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            // see https://github.com/Sylius/SyliusGridBundle/blob/master/docs/field_types.md
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create(),
                )
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    // ShowAction::create(),
                    UpdateAction::create(),
                    DeleteAction::create()
                )
            )
            ->addActionGroup(
                BulkActionGroup::create(
                    DeleteAction::create()
                )
            )
            ->addField(
                TwigField::create(name: 'files', template: 'book/grid/field/files.html.twig')->setPath('files'),
            )
        ;
    }

    public function getResourceClass(): string
    {
        return Book::class;
    }
}

```
and the template for files
```twig
# templates/book/grid/field/file.html.twig
{% for file in data %}
    <a class="button" href="{{ asset('media/file/'~ file.path) }}" target="_blank">
        {{ 'sylius.ui.preview_file'|trans }}
    </a>
{% endfor %}
```

### 8. Create the book form twig component

```php
# src/Twig/Components/BookFormComponent.php
<?php
namespace App\Twig\Components;

use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use App\Entity\Book;
use App\Form\Type\BookType;

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

```

### 9. Declare the FilesUploadListener service

In order to handle the file upload you need to attach the `FilesUploadListener` to the `Book` entity events:


```yaml
# services.yml
services:
    app.listener.files_upload:
        class: Mezcalito\SyliusFileUploadPlugin\EventListener\FilesUploadListener
        autowire: true
        autoconfigure: false
        public: false
        tags:
            - { name: kernel.event_listener, event: app.book.pre_create, method: uploadFiles }
            - { name: kernel.event_listener, event: app.book.pre_update, method: uploadFiles }
```

### 10. Add Book section to menu

```php
# src/Menu/AdminMenuListener.php
<?php

declare(strict_types=1);

namespace App\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{

    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $contentAdmin = $menu
            ->addChild('new')
            ->setLabel('app.ui.nav.menu.content_admin');

        $contentAdmin->addChild('admin_book', ['route' => 'app_admin_book_index'])
            ->setLabel('book')
            ->setLabelAttribute('icon', 'window minimize');

    }
}

```
### 11. Validation

Your form so far is working fine, but don't forget about validation.
The easiest way is using validation config files under the `App/Resources/config/validation` folder.

This could look like this for an image e.g.:

```yaml
# src\Resources\config\validation\BookFile.yml
App\Entity\BookFile:
  properties:
    file:
      - Image:
          groups: [sylius]
          maxHeight: 1000
          maxSize: 10240000
          maxWidth: 1000
          mimeTypes:
            - "image/png"
            - "image/jpg"
            - "image/jpeg"
            - "image/gif"
          mimeTypesMessage: 'This file format is not allowed. Please use PNG, JPG or GIF files.'
          minHeight: 200
          minWidth: 200
```

or for a PDF e.g.:

```yaml
# src\Resources\config\validation\BookFile.yml
App\Entity\BookFile:
  properties:
    file:
      - File:
          groups: [sylius]
          maxSize: 10240000
          mimeTypes:
            - "application/pdf"
            - "application/x-pdf"
          mimeTypesMessage: 'This file format is not allowed. Only PDF files are allowed.'
```

This defines the validation constraints for each file entity.

Finally, connect the validation of the `ShippingMethod` to the validation of each single `File Entity`:

```yaml
# src\Resources\config\validation\ShippingMethod.yml
App\Entity\ShippingMethod:
  properties:
    ...
    images:
      - Valid: ~
```

[1]: https://docs.sylius.com/en/latest/cookbook/images/images-on-entity.html

### 12. Migration

Do the database migration

```bash
$ bin/console doctrine:migrations:diff
$ bin/console doctrine:migrations:migrate
```
