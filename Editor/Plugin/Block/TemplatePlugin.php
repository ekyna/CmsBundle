<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Editor\Adapter\AdapterInterface;
use Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Editor\View\WidgetView;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\TemplateBlockType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Twig\Environment;

/**
 * Class TemplatePlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TemplatePlugin extends AbstractPlugin
{
    public const NAME = 'ekyna_block_template';

    private Environment $twig;


    /**
     * Constructor.
     *
     * @param array       $config
     * @param Environment $twig
     */
    public function __construct(array $config, Environment $twig)
    {
        parent::__construct(array_replace([
            'templates' => [],
        ], $config));

        $this->twig = $twig;
    }

    /**
     * @inheritDoc
     */
    public function update(BlockInterface $block, Request $request, array $options = []): ?Response
    {
        $choices = [];
        foreach ($this->config['templates'] as $name => $config) {
            $choices[$config['title']] = $name;
        }

        // Feature update modal
        $form = $this->formFactory->create(TemplateBlockType::class, $block, [
            'action'    => $this->urlGenerator->generate('admin_ekyna_cms_editor_block_edit', [
                'blockId'         => $block->getId(),
                'widgetType'      => $request->get('widgetType', $block->getType()),
                '_content_locale' => $this->localeProvider->getCurrentLocale(),
            ]),
            'method'    => 'post',
            'attr'      => [
                'class' => 'form-horizontal',
            ],
            'templates' => $choices,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return null;
        }

        return $this->createModalResponse('Modifier le bloc static.', $form->createView());
    }

    /**
     * @inheritDoc
     */
    public function validate(BlockInterface $block, ExecutionContextInterface $context): void
    {
        $data = $block->getData();

        if (!array_key_exists('content', $data)) {
            return;
        }
        if (!isset($this->config['templates'][$data['content']])) {
            $context->addViolation(self::INVALID_DATA);
        }
    }

    /**
     * @inheritDoc
     */
    public function createWidget(
        BlockInterface $block,
        AdapterInterface $adapter,
        array $options,
        int $position = 0
    ): WidgetView {
        $view = parent::createWidget($block, $adapter, $options, $position);

        $data = $block->getData();

        if (isset($data['content']) && isset($this->config['templates'][$data['content']])) {
            $template = $this->config['templates'][$data['content']]['path'];
            $view->content = $this->twig->render($template);
        } else {
            $view->content = 'Please select a template.';
        }

        return $view;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return 'Template';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return static::NAME;
    }
}
