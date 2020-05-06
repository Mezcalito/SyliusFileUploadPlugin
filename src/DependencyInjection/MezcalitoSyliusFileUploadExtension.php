<?php

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class MezcalitoSyliusFileUploadExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $driver = 'doctrine/orm';
        $container->setParameter(sprintf('%s.driver.%s', $this->getAlias(), $driver), true);
        $container->setParameter(sprintf('%s.driver', $this->getAlias()), $driver);
    }
}
