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

And that's it.

## Usage

> This section is an adaptation of the official Sylius documentation about [How to add images to an entity ?][1].
>
>Extending entities with an ``files`` field is quite a popular use case.
 In this cookbook we will present how to **add files to the Shipping Method entity**.

### 1. Extend the ShippingMethod class with the FilesAwareInterface

In order to override the `ShippingMethod` that lives inside of the SyliusCoreBundle,
you have to create your own ShippingMethod class that will extend it:

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Mezcalito\SyliusFileUploadPlugin\Model\FilesAwareInterface;
use Mezcalito\SyliusFileUploadPlugin\Model\FilesAwareTrait;
use Sylius\Component\Core\Model\ShippingMethod as BaseShippingMethod;

class ShippingMethod extends BaseShippingMethod implements FilesAwareInterface
{
    use FilesAwareTrait {
        __construct as private initializeFilesCollection;
    }
    
    public function __construct() {
        $this->initializeFilesCollection();
    }
}
```

> Here we used the `FilesAwareTrait` which is provided for convenience.

### 2. Register your extended ShippingMethod as a resource's model class

With such a configuration you will register your `ShippingMethod` class in order to override the default one:

```yaml
# config/packages/sylius_shipping.yaml
sylius_shipping:
    resources:
        shipping_method:
            classes:
                model: App\Entity\ShippingMethod
```

### 3. Create the ShippingMethodFile class

In the `App\Entity` namespace place the `ShippingMethodFile` class which should look like this:

```php
<?php

declare(strict_types=1);

namespace App\Entity;

use Mezcalito\SyliusFileUploadPlugin\Model\File;

class ShippingMethodFile extends File
{
}
```

### 4. Add the mapping file for the ShippingMethodFile

Your new entity will be saved in the database, therefore it needs a mapping file, where you will set the `ShippingMethod` as the `owner`
of the `ShippingMethodFile`.

```yaml
# App/Resources/config/doctrine/ShippingMethodFile.orm.yml
App\Entity\ShippingMethodFile:
    type: entity
    table: app_shipping_method_file
    manyToOne:
        owner:
            targetEntity: App\Entity\ShippingMethod
            inversedBy: files
            joinColumn:
                name: owner_id
                referencedColumnName: id
                nullable: false
                onDelete: CASCADE
```

### 5. Modify the ShippingMethod's mapping file

The newly added `files` field has to be added to the mapping, with a relation to the `ShippingMethodFile`:

```yaml
# App/Resources/config/doctrine/ShippingMethod.orm.yml
App\Entity\ShippingMethod:
    type: entity
    table: sylius_shipping_method
    oneToMany:
        files:
            targetEntity: App\Entity\ShippingMethodFile
            mappedBy: owner
            orphanRemoval: true
            cascade:
                - all
```

### 6. Register the ShippingMethodFile as a resource

The `ShippingMethodFile` class needs to be registered as a Sylius resource:

```yaml
# app/config/config.yml
sylius_resource:
    resources:
        app.shipping_method_file:
            classes:
                model: App\Entity\ShippingMethodFile
```

### 7. Create the ShippingMethodFileType class

This is how the class for `ShippingMethodFileType` should look like. Place it in the `App\Form\Type\` directory.

.. code-block:: php

```php
<?php

declare(strict_types=1);

namespace App\Form\Type;

use Mezcalito\SyliusFileUploadPlugin\Form\Type\FileType;

final class ShippingMethodFileType extends FileType
{
    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'app_shipping_method_file';
    }
}
```

### 8. Register the ShippingMethodFileType as a service

After creating the form type class, you need to register it as a `form.type` service like below:

```yaml
# services.yml
services:
    app.form.type.shipping_method_file:
        class: App\Form\Type\ShippingMethodFileType
        tags:
            - { name: form.type }
        arguments: ['%app.model.shipping_method_file.class%']
```

### 9. Add the ShippingMethodFileType to the resource form configuration

What is more the new form type needs to be configured as the resource form of the `ShippingMethodFile`:

```yaml
# app/config/config.yml
sylius_resource:
    resources:
        app.shipping_method_file:
            classes:
                form: App\Form\Type\ShippingMethodFileType
```

### 10. Extend the ShippingMethodType with the files field

**Create the form extension class** for the `Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType`:

It needs to have the files field as a CollectionType.

```php
<?php

declare(strict_types=1);

namespace App\Form\Extension;

use App\Form\Type\ShippingMethodFileType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

final class ShippingMethodTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('files', CollectionType::class, [
            'entry_type' => ShippingMethodFileType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'label' => 'sylius.form.shipping_method.files',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType(): string
    {
        return ShippingMethodType::class;
    }
}
```

In case you need only a single file upload, this can be done in 2 very easy steps.

First, in the code for the form provided above set `allow_add` and `allow_delete` to `false`

Second, in the `__construct` method of the `ShippingMethod` entity you defined earlier add the following:

```php
public function __construct()
{
    parent::__construct();
    $this->files = new ArrayCollection();
    $this->addFile(new ShippingMethodFile());
}
```

```yaml
# services.yml
services:
    app.form.extension.type.shipping_method:
        class: App\Form\Extension\ShippingMethodTypeExtension
        tags:
            - { name: form.type_extension, extended_type: Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType }
```

### 11. Declare the FilesUploadListener service

In order to handle the file upload you need to attach the `FilesUploadListener` to the `ShippingMethod` entity events:

.. code-block:: yaml

```yaml
# services.yml
services:
    app.listener.files_upload:
        class: Mezcalito\SyliusFileUploadPlugin\FilesUploadListener
        autowire: true
        autoconfigure: false
        public: false
        tags:
            - { name: kernel.event_listener, event: sylius.shipping_method.pre_create, method: uploadFiles }
            - { name: kernel.event_listener, event: sylius.shipping_method.pre_update, method: uploadFiles }
```

### 12. Render the files field in the form view

In order to achieve that you will need to customize the form view from the `SyliusAdminBundle/views/ShippingMethod/_form.html.twig` file.

Copy and paste its contents into your own `app/Resources/SyliusAdminBundle/views/ShippingMethod/_form.html.twig` file,
and render the `{{ form_row(form.files) }}` field.

```twig
{# app/Resources/SyliusAdminBundle/views/ShippingMethod/_form.html.twig #}

{% from '@SyliusAdmin/Macro/translationForm.html.twig' import translationForm %}

<div class="ui two column stackable grid">
    <div class="column">
        <div class="ui segment">
            {{ form_errors(form) }}
            <div class="three fields">
                {{ form_row(form.code) }}
                {{ form_row(form.zone) }}
                {{ form_row(form.position) }}
            </div>
            {{ form_row(form.enabled) }}
            <h4 class="ui dividing header">{{ 'sylius.ui.availability'|trans }}</h4>
            {{ form_row(form.channels) }}
            <h4 class="ui dividing header">{{ 'sylius.ui.category_requirements'|trans }}</h4>
            {{ form_row(form.category) }}
            {% for categoryRequirementChoiceForm in form.categoryRequirement %}
                {{ form_row(categoryRequirementChoiceForm) }}
            {% endfor %}
            <h4 class="ui dividing header">{{ 'sylius.ui.taxes'|trans }}</h4>
            {{ form_row(form.taxCategory) }}
            <h4 class="ui dividing header">{{ 'sylius.ui.shipping_charges'|trans }}</h4>
            {{ form_row(form.calculator) }}
            {% for name, calculatorConfigurationPrototype in form.vars.prototypes %}
                <div id="{{ form.calculator.vars.id }}_{{ name }}" data-container=".configuration"
                     data-prototype="{{ form_widget(calculatorConfigurationPrototype)|e }}">
                </div>
            {% endfor %}

            {# Here you go! #}
            {{ form_row(form.files) }}

            <div class="ui segment configuration">
                {% if form.configuration is defined %}
                    {% for field in form.configuration %}
                        {{ form_row(field) }}
                    {% endfor %}
                {% endif %}
            </div>
        </div>
    </div>
    <div class="column">
        {{ translationForm(form.translations) }}
    </div>
</div>
```

### 13. Validation

Your form so far is working fine, but don't forget about validation.
The easiest way is using validation config files under the `App/Resources/config/validation` folder.

This could look like this for an image e.g.:

```yaml
# src\Resources\config\validation\ShippingMethodFile.yml
App\Entity\ShippingMethodFile:
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
# src\Resources\config\validation\ShippingMethodFile.yml
App\Entity\ShippingMethodFile:
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
