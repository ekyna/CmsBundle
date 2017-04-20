<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Container;

use Ekyna\Bundle\CmsBundle\Editor\Model\ContainerInterface;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\PluginInterface as BaseInterface;
use Ekyna\Bundle\CmsBundle\Editor\View\ContainerView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function create(ContainerInterface $container, array $data = []): void;

    /**
     * Updates a container.
     *
     * @param ContainerInterface $container
     * @param Request            $request
     *
     * @return Response|null
     */
    public function update(ContainerInterface $container, Request $request): ?Response;

    /**
     * Removes a container.
     *
     * @param ContainerInterface $container
     */
    public function remove(ContainerInterface $container): void;

    /**
     * Validates the container (data).
     *
     * @param ContainerInterface        $container
     * @param ExecutionContextInterface $context
     */
    public function validate(ContainerInterface $container, ExecutionContextInterface $context): void;

    /**
     * Returns the container content.
     *
     * @param ContainerInterface $container
     * @param ContainerView      $view
     * @param bool               $editable
     */
    public function render(ContainerInterface $container, ContainerView $view, bool $editable = false): void;

    /**
     * Returns whether the container is supported.
     *
     * @param ContainerInterface $container
     *
     * @return bool
     */
    public function supports(ContainerInterface $container): bool;
}
