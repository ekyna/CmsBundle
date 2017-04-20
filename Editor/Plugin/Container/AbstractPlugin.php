<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Container;

use Ekyna\Bundle\CmsBundle\Editor\Model\ContainerInterface;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\AbstractPlugin as BasePlugin;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class AbstractPlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Container
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractPlugin extends BasePlugin implements PluginInterface
{
    public const INVALID_DATA = 'ekyna_cms.container.invalid_data';


    /**
     * @inheritDoc
     */
    public function create(ContainerInterface $container, array $data = []): void
    {
        $container->setData($data);
    }

    /**
     * @inheritDoc
     */
    public function remove(ContainerInterface $container): void
    {
        $container->unsetData();
    }

    /**
     * @inheritDoc
     */
    public function validate(ContainerInterface $container, ExecutionContextInterface $context): void
    {

    }

    /**
     * @inheritDoc
     */
    public function supports(ContainerInterface $container): bool
    {
        return $container->getType() === $this->getName();
    }

    /**
     * @inheritDoc
     */
    public function getJavascriptFilePath(): string
    {
        return 'ekyna-cms/editor/plugin/container/default';
    }
}
