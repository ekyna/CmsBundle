<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Editor\Adapter\AdapterInterface;
use Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\Block\Model\Tabs;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\PropertyDefaults;
use Ekyna\Bundle\CmsBundle\Editor\View\WidgetView;
use Ekyna\Bundle\CmsBundle\Form\Type\Editor\TabsType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Twig\Environment;
use Twig\TemplateWrapper;

/**
 * Class TabsPlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TabsPlugin extends AbstractPlugin
{
    const NAME = 'ekyna_block_tabs';

    private Serializer       $serializer;
    private Environment      $twig;
    private ?TemplateWrapper $template = null;


    /**
     * Constructor.
     *
     * @param array       $config
     * @param Serializer  $serializer
     * @param Environment $twig
     */
    public function __construct(
        array $config,
        Serializer $serializer,
        Environment $twig
    ) {
        parent::__construct(array_replace([
            'themes'         => PropertyDefaults::getDefaultThemeChoices(),
            'default_poster' => '/bundles/ekynacms/img/default-image.gif',
        ], $config));

        $this->serializer = $serializer;
        $this->twig = $twig;
    }

    /**
     * @inheritDoc
     */
    public function create(BlockInterface $block, array $data = []): void
    {
        parent::create($block, $data);

        $tabs = new Model\Tabs();
        $tabs
            ->setTheme(array_keys($this->config['themes'])[0])
            ->setAlign('left')
            ->setCurrentLocale($this->localeProvider->getCurrentLocale())
            ->setFallbackLocale($this->localeProvider->getFallbackLocale())
            ->translate($this->localeProvider->getCurrentLocale())
            ->setTitle('Default tabs')
            ->setContent('<p>Edit the container to configure tabs.</p>')
            ->setButtonLabel('Some button')
            ->setButtonUrl('javascript: void(0)');

        $tab = new Model\Tab();
        $tab
            ->setCurrentLocale($this->localeProvider->getCurrentLocale())
            ->setFallbackLocale($this->localeProvider->getFallbackLocale())
            ->translate($this->localeProvider->getCurrentLocale())
            ->setTitle('Default tab');

        $tabs->addTab($tab);

        $block->setData(array_merge($this->serializer->normalize($tabs), $data));
    }

    /**
     * @inheritDoc
     */
    public function update(BlockInterface $block, Request $request, array $options = []): ?Response
    {
        $options = array_replace([
            'action' => $this->urlGenerator->generate('admin_ekyna_cms_editor_block_edit', [
                'blockId'         => $block->getId(),
                'widgetType'      => $request->get('widgetType', $block->getType()),
                '_content_locale' => $this->localeProvider->getCurrentLocale(),
            ]),
            'method' => 'post',
            'attr'   => [
                'class' => 'form-horizontal',
            ],
            'themes' => $this->config['themes'],
        ], $options);

        $tabs = $this->serializer->denormalize($block->getData(), Tabs::class);

        $form = $this->formFactory->create(TabsType::class, $tabs, $options);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $this->serializer->normalize($form->getData());

            $block->setData($data);

            return null;
        }

        return $this->createModalResponse('Modifier le bloc tabs.', $form->createView());
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
        /** @var Tabs $tabs */
        $tabs = $this->serializer->denormalize($block->getData(), Tabs::class);

        $view = parent::createWidget($block, $adapter, $options, $position);
        $view->getAttributes()->addClass('cms-tabs ' . $tabs->getTheme() . ' ' . $tabs->getAlign());

        $view->content = $this->getTemplate()->renderBlock($tabs->getAlign(), [
            'block_id' => $block->getId(),
            'tabs'     => $tabs,
        ]);

        return $view;
    }

    /**
     * Returns the template.
     *
     * @return TemplateWrapper
     */
    private function getTemplate(): TemplateWrapper
    {
        if ($this->template) {
            return $this->template;
        }

        return $this->template = $this->twig->load('@EkynaCms/Editor/Block/tabs.html.twig');
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return 'Tabs';
    }

    /**
     * @inheritDoc
     */
    public function getJavascriptFilePath(): string
    {
        return 'ekyna-cms/editor/plugin/block/tabs';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return static::NAME;
    }
}
