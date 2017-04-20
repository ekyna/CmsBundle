<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Container;

use Ekyna\Bundle\CmsBundle\Editor\Model\ContainerInterface;
use Ekyna\Bundle\CmsBundle\Editor\View\ContainerView;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\CopyContainerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CopyPlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Container
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class CopyPlugin extends AbstractPlugin
{
    /**
     * @inheritDoc
     */
    public function update(ContainerInterface $container, Request $request): ?Response
    {
        $form = $this->formFactory->create(CopyContainerType::class, $container, [
            'action'     => $this->urlGenerator->generate(
                'admin_ekyna_cms_editor_container_edit',
                ['containerId' => $container->getId()]
            ),
            'method'     => 'post',
            'attr'       => [
                'class' => 'form-horizontal',
            ],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return null;
        }

        return $this->createModalResponse('Modifier le conteneur.', $form->createView()); // TODO trans
    }

    /**
     * @inheritDoc
     */
    public function remove(ContainerInterface $container): void
    {
        $container->setCopy();
    }

    /**
     * @inheritDoc
     */
    public function render(ContainerInterface $container, ContainerView $view, $editable = false): void
    {
        if (is_null($container->getCopy())) {
            $view->innerContent = '<p>Please select a container to copy.</p>';
        }
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return 'Copy';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'ekyna_container_copy';
    }
}
