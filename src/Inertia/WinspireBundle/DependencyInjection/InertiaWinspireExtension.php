<?php
/**
 * PHP version 5.3
 */
namespace Inertia\WinspireBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class InertiaWinspireExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $processor->process($this->getConfigTree(), $configs);
    }

    private function getConfigTree()
    {
        $tb = new TreeBuilder();

        $tb
            ->root('inertia_winspire')
                ->children()
                    ->scalarNode('sf_user')->isRequired()->end()
                    ->scalarNode('sf_password')->isRequired()->end()
                    ->scalarNode('sf_wsdl_path')->isRequired()->end()
                    ->scalarNode('sf_upload_dir')->isRequired()->end()
                ->end()
            ->end()
        ;

        return $tb->buildTree();
    }
}
