<?php

namespace Ekyna\Bundle\CmsBundle\Twig;

use Ekyna\Bundle\CmsBundle\Editor\Editor;
use Ekyna\Bundle\CmsBundle\Entity\Menu;
use Ekyna\Bundle\CmsBundle\Entity\Seo;
use Ekyna\Bundle\CmsBundle\Helper\PageHelper;
use Ekyna\Bundle\CmsBundle\Menu\MenuProvider;
use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentSubjectInterface;
use Ekyna\Bundle\CmsBundle\Model\SeoInterface;
use Ekyna\Bundle\CoreBundle\Event\HttpCacheEvent;
use Ekyna\Bundle\CoreBundle\Event\HttpCacheEvents;
use Ekyna\Bundle\SettingBundle\Manager\SettingsManagerInterface;
use Knp\Menu\Twig\Helper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\HttpKernel\Fragment\FragmentHandler;

/**
 * Class CmsExtension
 * @package Ekyna\Bundle\CmsBundle\Twig
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class CmsExtension extends \Twig_Extension
{
    /**
     * @var SettingsManagerInterface
     */
    protected $settings;

    /**
     * @var Editor
     */
    protected $editor;

    /**
     * @var MenuProvider
     */
    protected $menuProvider;

    /**
     * @var Helper
     */
    protected $menuHelper;

    /**
     * @var PageHelper
     */
    private $pageHelper;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var FragmentHandler
     */
    protected $fragmentHandler;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var \Twig_Template
     */
    protected $template;


    /**
     * Constructor.
     *
     * @param SettingsManagerInterface $settings
     * @param Editor                   $editor
     * @param MenuProvider             $menuProvider
     * @param Helper                   $menuHelper
     * @param PageHelper               $pageHelper
     * @param EventDispatcherInterface $eventDispatcher
     * @param FragmentHandler          $fragmentHandler
     * @param array                    $config
     */
    public function __construct(
        SettingsManagerInterface $settings,
        Editor $editor,
        MenuProvider $menuProvider,
        Helper $menuHelper,
        PageHelper $pageHelper,
        EventDispatcherInterface $eventDispatcher,
        FragmentHandler $fragmentHandler,
        array $config = []
    ) {
        $this->settings = $settings;
        $this->editor = $editor;
        $this->menuProvider = $menuProvider;
        $this->menuHelper = $menuHelper;
        $this->pageHelper = $pageHelper;
        $this->eventDispatcher = $eventDispatcher;
        $this->fragmentHandler = $fragmentHandler;

        $this->config = array_merge([
            'template' => 'EkynaCmsBundle:Editor:content.html.twig',
            'home_route' => 'home',
            'seo' => [
                'no_follow' => true,
                'no_index' => true,
                'title_append' => null,
            ],
            'page' => [
                'controllers' => [],
            ],
            'esi_flashes' => false,
        ], $config);
    }

    /**
     * {@inheritDoc}
     */
    public function initRuntime(\Twig_Environment $twig)
    {
        $this->template = $twig->loadTemplate($this->config['template']);
    }

    /**
     * {@inheritDoc}
     */
    public function getGlobals()
    {
        return [
            'ekyna_cms_home_route' => $this->config['home_route'],
            'ekyna_cms_seo_title_append' => $this->config['seo']['title_append'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('cms_page', [$this, 'getPage']),
            new \Twig_SimpleFunction('cms_metas', [$this, 'renderMetas'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_seo', [$this, 'renderSeo'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_meta', [$this, 'renderMeta'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_title', [$this, 'renderTitle'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_content', [$this, 'renderContent'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_content_block', [$this, 'renderContentBlock'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_block', [$this, 'renderBlock'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_menu', [$this, 'renderMenu'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_breadcrumb', [$this, 'renderBreadcrumb'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_flashes', [$this, 'renderFlashes'], ['is_safe' => ['html']]),
            new \Twig_SimpleFunction('cms_page_controller', [$this, 'getPageControllerTitle'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Returns the current page.
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\PageInterface|null
     */
    public function getPage()
    {
        return $this->pageHelper->getCurrent();
    }

    /**
     * Generates document title and metas tags from the given Seo object or form the current page.
     *
     * @param SeoInterface $seo
     * @return string
     * @deprecated use renderSeo()
     */
    public function renderMetas(SeoInterface $seo = null)
    {
        return $this->renderSeo($seo);
    }

    /**
     * Generates document title and metas tags from the given Seo object or form the current page.
     *
     * @param SeoInterface $seo
     * @return string
     */
    public function renderSeo(SeoInterface $seo = null)
    {
        if (null === $seo) {
            if (null !== $page = $this->getPage()) {
                $seo = $page->getSeo();
            } else {
                $seo = new Seo();
                $seo
                    ->setTitle($this->settings->getParameter('seo.title'))
                    ->setDescription($this->settings->getParameter('seo.description'))
                    ->setIndex(!$this->config['seo']['no_index'])
                    ->setFollow(!$this->config['seo']['no_follow'])
                ;
            }
        }

        if (null !== $seo) {
            $follow = !$this->config['seo']['no_follow'] ? ($seo->getFollow() ? 'follow' : 'nofollow') : 'nofollow';
            $index = !$this->config['seo']['no_index'] ? ($seo->getIndex() ?  'index'  : 'noindex') : 'noindex';

            $metas =
                $this->renderTitle('title', $seo->getTitle().$this->config['seo']['title_append']) . "\n" .
                $this->renderMeta('description', $seo->getDescription()) . "\n" .
                $this->renderMeta('robots', $follow.','.$index)
            ;

            if (0 < strlen($canonical = $seo->getCanonical())) {
                $metas .= "\n" .$this->renderTag('link', null, [
                    'rel' => 'canonical',
                    'href' => $canonical,
                ]);
            }

            // Tags the response as Seo relative
            if ($seo->getId()) {
                $this->eventDispatcher->dispatch(
                    HttpCacheEvents::TAG_RESPONSE,
                    new HttpCacheEvent($seo->getEntityTag())
                );
            }
        } else {
            $metas = '<title>Undefined</title>' . $this->renderMeta('robots', 'follow,noindex');
        }

        return $metas;
    }

    /**
     * Generates a meta tag.
     *
     * @param string $name
     * @param string $content
     *
     * @return string
     */
    public function renderMeta($name, $content)
    {
        return $this->renderTag('meta', null, ['name' => $name, 'content' => $content]);
    }

    /**
     * Returns current page's title.
     *
     * @param string $tag
     * @param string $content
     *
     * @return string
     */
    public function renderTitle($tag = 'h1', $content = null)
    {
        if (null === $content && null !== $page = $this->getPage()) {
            $content = $page->getTitle();

            // Tags the response as Page relative
            $this->eventDispatcher->dispatch(
                HttpCacheEvents::TAG_RESPONSE,
                new HttpCacheEvent($page->getEntityTag())
            );
        }

        if (0 == strlen($content)) {
            $content = 'Undefined title';
        }

        return $this->renderTag($tag, $content);
    }

    /**
     * Renders the html tag.
     *
     * @param $tag
     * @param string $content
     * @param array $attributes
     *
     * @return string
     */
    private function renderTag($tag, $content = null, array $attributes = [])
    {
        $attr = [];

        foreach($attributes as $key => $value) {
            $attr[] = sprintf(' %s="%s"', $key, $value);
        }

        if (0 < strlen($content)) {
            return sprintf('<%s%s>%s</%s>', $tag, implode('', $attr), $content, $tag);
        } else {
            return sprintf('<%s%s />', $tag, implode('', $attr));
        }
    }

    /**
     * Generates html from given Content.
     *
     * @param mixed $subject
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function renderContent($subject = null)
    {
        $content = null;

        if ($subject instanceOf ContentInterface) {
            $content = $subject;
        } elseif ($subject instanceof ContentSubjectInterface) {
            if (null === $content = $subject->getContent()) {
                $content = $this->editor->createDefaultContent($subject);
            }
        } elseif (null === $subject) {
            if (null !== $page = $this->getPage()) {
                if (null === $content = $page->getContent()) {
                    if ($page->getAdvanced()) {
                        $content = $this->editor->createDefaultContent($page);
                    } elseif (0 < strlen($html = $page->getHtml())) {
                        return $html;
                    } else {
                        return '<p></p>'; // TODO default content
                    }
                }
            }
        }

        if (null === $content) {
            throw new \RuntimeException('Undefined content.');
        }

        // TODO : hasBlock() does not use template inheritance.
        if (!$this->template->hasBlock('cms_block_content')) {
            throw new \RuntimeException('Unable to find "cms_block_content" twig block.');
        }

        $this->editor->setEnabled(true);

        // Tag response as Content relative
        if (null !== $content->getId()) {
            $this->eventDispatcher->dispatch(
                HttpCacheEvents::TAG_RESPONSE,
                new HttpCacheEvent($content->getEntityTag())
            );
        }

        return $this->template->renderBlock('cms_block_content', [
            'content' => $content,
            'editable' => $this->editor->getEnabled()
        ]);
    }

    /**
     * Generates html from given Content Block.
     *
     * @param BlockInterface $block
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function renderContentBlock(BlockInterface $block)
    {
        $blockName = sprintf('cms_block_%s', $block->getType());

        if (!$this->template->hasBlock($blockName)) {
            throw new \RuntimeException('Unable to find "%s" twig block.', $blockName);
        }

        return trim($this->template->renderBlock($blockName, ['block' => $block]));
    }

    /**
     * Generates html from given Block.
     *
     * @param BlockInterface|string $block The block or the block name
     * @param string $type the block type
     * @param array $datas the block datas
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function renderBlock($block, $type = null, array $datas = [])
    {
        if (is_string($block)) {
            $block = $this->editor->findBlockByName($block, $type, $datas);
        }
        if (!$block instanceof BlockInterface) {
            throw new \InvalidArgumentException('Expected instance of Ekyna\Bundle\CmsBundle\Model\BlockInterface');
        }

        $this->editor->setEnabled(true);

        // Tags the response as Block relative
        $this->eventDispatcher->dispatch(
            HttpCacheEvents::TAG_RESPONSE,
            new HttpCacheEvent($block->getEntityTag())
        );

        return $this->template->renderBlock('cms_block', [
            'block' => $block,
            'editable' => $this->editor->getEnabled()
        ]);
    }

    /**
     * Renders the menu by his name.
     *
     * @param string $name
     * @param array  $options
     * @param string $renderer
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function renderMenu($name, array $options = [], $renderer = null)
    {
        if (null === $menu = $this->menuProvider->findByName($name)) {
            throw new \InvalidArgumentException(sprintf('Menu named "%s" not found.', $name));
        }

        // Tags the response as Menu relative
        $this->eventDispatcher->dispatch(
            HttpCacheEvents::TAG_RESPONSE,
            new HttpCacheEvent([
                Menu::getEntityTagPrefix(),
                sprintf('%s[id:%s]', Menu::getEntityTagPrefix(), $menu['id'])
            ])
        );

        return $this->menuHelper->render($name, $options, $renderer);
    }

    /**
     * Renders the breadcrumb.
     *
     * @param array $options
     *
     * @return string
     */
    public function renderBreadcrumb(array $options = [])
    {
        return $this->menuHelper->render('breadcrumb', array_merge([
            'template' => 'EkynaCmsBundle:Cms:breadcrumb.html.twig',
            //'currentAsLink' => false,
            'depth' => 1,
        ], $options));
    }

    /**
     * Renders the session flashes.
     *
     * @return string
     */
    public function renderFlashes()
    {
        if ($this->config['esi_flashes']) {
            return $this->fragmentHandler->render(new ControllerReference('EkynaCmsBundle:Cms:flashes'), 'esi');
        }
        return '<div id="cms-flashes"></div>';
    }

    /**
     * Returns the page controller title.
     *
     * @param string $name
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getPageControllerTitle($name)
    {
        if (!array_key_exists($name, $this->config['page']['controllers'])) {
            throw new \InvalidArgumentException(sprintf('Undefined controller "%s".', $name));
        }
        return $this->config['page']['controllers'][$name]['title'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms';
    }
}
