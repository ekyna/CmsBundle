<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Container;

use Ekyna\Bundle\CmsBundle\Editor\Model\ContainerInterface;
use Ekyna\Bundle\CmsBundle\Editor\View\ContainerView;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\CopyContainerType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
    public function update(ContainerInterface $container, Request $request)
    {
        $form = $this->formFactory->create(CopyContainerType::class, $container, [
            'action'     => $this->urlGenerator->generate(
                'ekyna_cms_editor_container_edit',
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

        return $this->createModal('Modifier le conteneur.', $form->createView()); // TODO trans
    }

    /**
     * @inheritDoc
     */
    public function remove(ContainerInterface $container)
    {
        $container->setCopy(null);
    }

    /**
     * @inheritDoc
     */
    public function validate(ContainerInterface $container, ExecutionContextInterface $context)
    {
        /*if (null === $container->getCopy()) {
            $context
                ->buildViolation('Please select a container to copy.')
                ->atPath('copy')
                ->addViolation();
        }*/
    }

    /**
     * @inheritDoc
     */
    public function render(ContainerInterface $container, ContainerView $view, $editable = false)
    {
        if (is_null($container->getCopy())) {
            $view->innerContent = '<p>Please select a container to copy.</p>';
        }
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return 'Copy';
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'ekyna_container_copy';
    }
}
