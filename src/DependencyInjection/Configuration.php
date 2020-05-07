<?php

declare(strict_types=1);

namespace Mezcalito\SyliusFileUploadPlugin\DependencyInjection;

use Sylius\Bundle\AdminApiBundle\Form\Type\ClientType;
use Sylius\Bundle\AdminApiBundle\Model\AccessToken;
use Sylius\Bundle\AdminApiBundle\Model\AccessTokenInterface;
use Sylius\Bundle\AdminApiBundle\Model\AuthCode;
use Sylius\Bundle\AdminApiBundle\Model\AuthCodeInterface;
use Sylius\Bundle\AdminApiBundle\Model\Client;
use Sylius\Bundle\AdminApiBundle\Model\ClientInterface;
use Sylius\Bundle\AdminApiBundle\Model\RefreshToken;
use Sylius\Bundle\AdminApiBundle\Model\RefreshTokenInterface;
use Sylius\Bundle\AdminApiBundle\Model\UserInterface;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root('mezcalito_sylius_file_upload_plugin');
        }

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
