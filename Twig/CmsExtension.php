<?php

namespace Ekyna\Bundle\CmsBundle\Twig;

use Ekyna\Bundle\CmsBundle\Entity\Menu;
use Ekyna\Bundle\CmsBundle\Entity\SeoRepository;
use Ekyna\Bundle\CmsBundle\Helper\PageHelper;
use Ekyna\Bundle\CmsBundle\Menu\MenuProvider;
use Ekyna\Bundle\CmsBundle\Model\SeoInterface;
use Ekyna\Bundle\CmsBundle\Model\SeoSubjectInterface;
use Ekyna\Bundle\CoreBundle\Cache\TagManager;
use Ekyna\Bundle\SettingBundle\Manager\SettingsManagerInterface;
use Knp\Menu\Twig\Helper;
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
    protected $pageHelper;

    /**
     * @var SeoRepository
     */
    protected $seoRepository;

    /**
     * @var TagManager
     */
    protected $tagManager;

    /**
     * @var FragmentHandler
     */
    protected $fragmentHandler;

    /**
     * @var array
     */
    protected $config;


    /**
     * Constructor.
     *
     * @param array                    $config
     * @param SettingsManagerInterface $settings
     * @param MenuProvider             $menuProvider
     * @param Helper                   $menuHelper
     * @param PageHelper               $pageHelper
     * @param SeoRepository            $seoRepository
     * @param TagManager               $tagManager
     * @param FragmentHandler          $fragmentHandler
     */
    public function __construct(
        array $config,
        SettingsManagerInterface $settings,
        MenuProvider             $menuProvider,
        Helper                   $menuHelper,
        PageHelper               $pageHelper,
        SeoRepository            $seoRepository,
        TagManager               $tagManager,
        FragmentHandler          $fragmentHandler
    ) {
        $this->config = array_merge(array(
            'home_route' => 'home',
            'seo' => array(
                'no_follow' => true,
                'no_index' => true,
                'title_append' => null,
            ),
            'page' => array(
                'controllers' => array(),
            ),
            'esi_flashes' => false,
        ), $config);

        $this->settings        = $settings;
        $this->menuProvider    = $menuProvider;
        $this->menuHelper      = $menuHelper;
        $this->pageHelper      = $pageHelper;
        $this->tagManager      = $tagManager;
        $this->seoRepository   = $seoRepository;
        $this->fragmentHandler = $fragmentHandler;
    }

    /**
     * {@inheritDoc}
     */
    public function getGlobals()
    {
        return array(
            'ekyna_cms_home_route'       => $this->config['home_route'],
            'ekyna_cms_seo_title_append' => $this->config['seo']['title_append'],
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('cms_page', array($this, 'getPage')),
            new \Twig_SimpleFunction('cms_metas', array($this, 'renderMetas'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_seo', array($this, 'renderSeo'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_meta', array($this, 'renderMeta'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_title', array($this, 'renderTitle'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_menu', array($this, 'renderMenu'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_breadcrumb', array($this, 'renderBreadcrumb'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_flashes', array($this, 'renderFlashes'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('cms_page_controller', array($this, 'getPageControllerTitle'), array('is_safe' => array('html'))),
        );
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
     * @param mixed $seoOrSubject
     * @return string
     * @deprecated use renderSeo()
     */
    public function renderMetas($seoOrSubject = null)
    {
        if ($seoOrSubject instanceof SeoSubjectInterface) {
            $seoOrSubject = $seoOrSubject->getSeo();
        }

        return $this->renderSeo($seoOrSubject);
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
                /** @var SeoInterface $seo */
                $seo = $this->seoRepository->createNew();
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
                $metas .= "\n" .$this->renderTag('link', null, array(
                    'rel' => 'canonical',
                    'href' => $canonical,
                ));
            }

            // Tags the response as Seo relative
            if ($seo->getId()) {
                $this->tagManager->addTags($seo->getEntityTag());
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
        return $this->renderTag('meta', null, array('name' => $name, 'content' => $content));
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
            $this->tagManager->addTags($page->getEntityTag());
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
    private function renderTag($tag, $content = null, array $attributes = array())
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
    public function renderMenu($name, array $options = array(), $renderer = null)
    {
        if (null === $menu = $this->menuProvider->findByName($name)) {
            throw new \InvalidArgumentException(sprintf('Menu named "%s" not found.', $name));
        }

        // Tags the response as Menu relative
        $this->tagManager->addTags(array(
            Menu::getEntityTagPrefix(),
            sprintf('%s[id:%s]', Menu::getEntityTagPrefix(), $menu['id'])
        ));

        return $this->menuHelper->render($name, $options, $renderer);
    }

    /**
     * Renders the breadcrumb.
     *
     * @param array $options
     *
     * @return string
     */
    public function renderBreadcrumb(array $options = array())
    {
        return $this->menuHelper->render('breadcrumb', array_merge(array(
            'template' => 'EkynaCmsBundle:Cms:breadcrumb.html.twig',
            //'currentAsLink' => false,
            'depth' => 1,
        ), $options));
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
