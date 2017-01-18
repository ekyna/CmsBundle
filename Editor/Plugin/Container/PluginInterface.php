<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Container;

use Ekyna\Bundle\CmsBundle\Editor\Plugin\PluginInterface as BaseInterface;
use Ekyna\Bundle\CmsBundle\Editor\View\ContainerView;
use Ekyna\Bundle\CmsBundle\Editor\Model\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Interface PluginInterface
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Container
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
interface PluginInterface extends BaseInterface
{
    /**
     * Creates a new container.
     *
     * @param ContainerInterface $container
     * @param array              $data
     */
    public function create(ContainerInterface $container, array $data = []);

    /**
     * Updates a container.
     *
     * @param ContainerInterface $container
     * @param Request            $request
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     */
    public function update(ContainerInterface $container, Request $request);

    /**
     * Removes a container.
     *
     * @param ContainerInterface $container
     */
    public function remove(ContainerInterface $container);

    /**
     * Validates the container (data).
     *
     * @param ContainerInterface        $container
     * @param ExecutionContextInterface $context
     */
    public function validate(ContainerInterface $container, ExecutionContextInterface $context);

    /**
     * Returns the container content.
     *
     * @param ContainerInterface $container
     * @param ContainerView      $view
     * @param bool               $editable
     *
     * @return string
     */
    public function render(ContainerInterface $container, ContainerView $view, $editable = false);

    /**
     * Returns whether the container is supported.
     *
     * @param ContainerInterface $container
     *
     * @return boolean
     */
    public function supports(ContainerInterface $container);
}
