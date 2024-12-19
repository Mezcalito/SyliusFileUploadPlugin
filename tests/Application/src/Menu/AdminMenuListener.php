<?php

declare(strict_types=1);

namespace Tests\Mezcalito\SyliusFileUploadPlugin\Application\src\Menu;

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
