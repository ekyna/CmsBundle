<?php

namespace Ekyna\Bundle\CmsBundle\DependencyInjection;

use Ekyna\Bundle\AdminBundle\DependencyInjection\AbstractExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Definition;

/**
 * EkynaCmsExtension
 */
class EkynaCmsExtension extends AbstractExtension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        list($config, $loader) = $this->configure($configs, 'ekyna_cms', new Configuration(), $container);

        $container->setParameter('ekyna_cms.content_enabled', $config['enable_contents']);
        $container->setParameter('ekyna_cms.home_route_name', $config['defaults']['home_route']);
        $container->setParameter('ekyna_cms.default_template', $config['defaults']['template']);
        $container->setParameter('ekyna_cms.default_controller', $config['defaults']['controller']);

        foreach($config['layouts'] as $id => $layout) {
            $container->setDefinition(
                sprintf('ekyna_cms.layout.%s', $id), 
                $this->createLayoutDefinition($id, $layout)
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $config = array(
            'enable_contents' => false,
            'defaults' => array(
        	    'home_route' => 'home',
        	    'template'   => 'EkynaCmsBundle:Cms:default.html.twig',
        	    'controller' => 'EkynaCmsBundle:Cms:default',
            ),
            'layouts' => array(
                'default' => array(
                    'name' => 'Par défaut',
            	    'blocks' => array(
        	            array('type' => 'text', 'width' => 12), // Block 0.0
        	            array('type' => 'tinymce', 'width' => 6), // Block 1.0
        	            array('type' => 'image', 'width' => 6), // Block 1.1
        	            array('type' => 'tinymce', 'width' => 12), // Block 2.0
            	    ), 
                ),
            ),
        );
        $container->prependExtensionConfig('ekyna_cms', $config);
    }

    /**
     * Creates a layout service definition
     * 
     * @param string $id
     * @param array  $config
     * 
     * @return \Symfony\Component\DependencyInjection\Definition
     */
    private function createLayoutDefinition($id, array $config)
    {
        $serviceId = sprintf('ekyna_cms.layout.%s', $id);
        
        $definition = new Definition('Ekyna\Bundle\CmsBundle\Layout\Layout');
        $definition
            ->setFactoryService('ekyna_cms.layout_factory')
            ->setFactoryMethod('createLayout')
            ->setArguments(array($id, $config['name'], $config['blocks']))
            ->addTag('ekyna_cms.layout', array('alias' => $id))
        ;
        return $definition;
    }
}
