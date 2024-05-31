<?php
declare(strict_types=1);

namespace ControlBit\Dto\Bridge\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension as SymfonyExtension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class Extension extends SymfonyExtension
{
    public function getAlias(): string
    {
        return 'dto_bundle';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $container->setParameter('dto_bundle.validation_json_bad_request', $config['validation_json_bad_request']);
        $container->setParameter('dto_bundle.map_private_properties', $config['map_private_properties']);

        $container->setAlias('dto_bundle.case_transformer', $config['case_transformer']);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/Config'));

        $loader->load('case_transformer.xml');
        $loader->load('finder.xml');
        $loader->load('factory.xml');
        $loader->load('value_converter.xml');
        $loader->load('mapper.xml');
        $loader->load('event_listener.xml');
    }
}