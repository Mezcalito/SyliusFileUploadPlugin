<?php

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('mezcalito_sylius_file_upload_plugin');
        if (\method_exists($treeBuilder, 'getRootNode')) {
            $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $treeBuilder->root('mezcalito_sylius_file_upload_plugin');
        }

        return $treeBuilder;
    }
}
