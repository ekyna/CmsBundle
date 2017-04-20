<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Service\Renderer;

use Ekyna\Bundle\CmsBundle\Factory\SeoFactoryInterface;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CmsBundle\Model\SeoInterface;
use Ekyna\Bundle\CmsBundle\Model\SeoSubjectInterface;
use Ekyna\Bundle\CmsBundle\Service\Helper\PageHelper;
use Ekyna\Bundle\ResourceBundle\Service\Http\TagManager;
use Ekyna\Bundle\SettingBundle\Manager\SettingManagerInterface;
use InvalidArgumentException;

use function array_key_exists;
use function array_merge;
use function implode;
use function sprintf;

/**
 * Class CmsRenderer
 * @package Ekyna\Bundle\CmsBundle\Service\Renderer
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class CmsRenderer
{
    protected SettingManagerInterface $settings;
    protected PageHelper              $pageHelper;
    protected SeoFactoryInterface     $seoFactory;
    protected TagManager              $tagManager;
    protected TagRenderer             $tagRenderer;
    protected array                   $config;

    public function __construct(
        SettingManagerInterface $settings,
        PageHelper $pageHelper,
        SeoFactoryInterface $seoFactory,
        TagManager $tagManager,
        array $config
    ) {
        $this->settings = $settings;
        $this->pageHelper = $pageHelper;
        $this->seoFactory = $seoFactory;
        $this->tagManager = $tagManager;

        $this->config = array_merge($this->getDefaultConfig(), $config);
        $this->tagRenderer = new TagRenderer();
    }

    /**
     * Generates document title and metas tags from the given Seo object or form the current page.
     *
     * @param mixed $seoOrSubject
     *
     * @deprecated use renderSeo()
     */
    public function renderMetas($seoOrSubject = null): string
    {
        if ($seoOrSubject instanceof SeoSubjectInterface) {
            $seoOrSubject = $seoOrSubject->getSeo();
        }

        return $this->renderSeo($seoOrSubject);
    }

    /**
     * Generates document title and metas tags from the given Seo object or form the current page.
     */
    public function renderSeo(SeoInterface $seo = null): string
    {
        if (null === $seo) {
            if (null !== $page = $this->getPage()) {
                $seo = $page->getSeo();
            } else {
                $seo = $this->seoFactory->create();
                $seo
                    ->setTitle($this->settings->getParameter('seo.title'))
                    ->setDescription($this->settings->getParameter('seo.description'))
                    ->setIndex(!$this->config['seo']['no_index'])
                    ->setFollow(!$this->config['seo']['no_follow']);
            }
        }

        if (null !== $seo) {
            $follow = !$this->config['seo']['no_follow'] ? ($seo->getFollow() ? 'follow' : 'nofollow') : 'nofollow';
            $index = !$this->config['seo']['no_index'] ? ($seo->getIndex() ? 'index' : 'noindex') : 'noindex';

            $metas =
                $this->renderTitle('title', $seo->getTitle() . $this->config['seo']['title_append']) . "\n" .
                $this->renderMeta('description', $seo->getDescription()) . "\n" .
                $this->renderMeta('robots', $follow . ',' . $index);

            if (!empty($canonical = $seo->getCanonical())) {
                $metas .= "\n" . $this->renderTag('link', null, [
                        'rel'  => 'canonical',
                        'href' => $canonical,
                    ]);
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
     */
    public function renderMeta(string $name, string $content = null): string
    {
        return $this->renderTag('meta', null, ['name' => $name, 'content' => $content]);
    }

    /**
     * Returns current page's title.
     */
    public function renderTitle(string $tag = 'h1', string $content = null): string
    {
        if (null === $content && null !== $page = $this->getPage()) {
            $content = $page->getTitle();

            // Tags the response as Page relative
            $this->tagManager->addTags($page->getEntityTag());
        }

        if (empty($content)) {
            $content = 'Undefined title';
        }

        return $this->renderTag($tag, $content);
    }

    /**
     * Renders the tags.
     *
     * @param mixed $subjectOrTags
     */
    public function renderTags($subjectOrTags, array $options = []): string
    {
        if (null === $this->tagRenderer) {
            $this->tagRenderer = new TagRenderer();
        }

        return $this->tagRenderer->renderTags($subjectOrTags, $options);
    }

    /**
     * Returns the current page.
     */
    public function getPage(): ?PageInterface
    {
        return $this->pageHelper->getCurrent();
    }

    /**
     * Returns the page controller title.
     */
    public function getPageControllerTitle(string $name): string
    {
        if (!array_key_exists($name, $this->config['page']['controllers'])) {
            throw new InvalidArgumentException(sprintf('Undefined controller "%s".', $name));
        }

        return $this->config['page']['controllers'][$name]['title'];
    }

    /**
     * Renders the theme.
     */
    public function renderTheme(string $theme): string
    {
        return sprintf('<span class="label label-%s">%s</span>', $theme, $theme);
    }

    /**
     * Renders the html tag.
     */
    private function renderTag(string $tag, string $content = null, array $attributes = []): string
    {
        $attr = [];

        foreach ($attributes as $key => $value) {
            $attr[] = sprintf(' %s="%s"', $key, $value);
        }

        if (!empty($content)) {
            return sprintf('<%s%s>%s</%s>', $tag, implode('', $attr), $content, $tag);
        } else {
            return sprintf('<%s%s />', $tag, implode('', $attr));
        }
    }

    /**
     * Returns the default configuration.
     */
    private function getDefaultConfig(): array
    {
        return [
            'seo'  => [
                'no_follow'    => true,
                'no_index'     => true,
                'title_append' => null,
            ],
            'page' => [
                'cookie_content' => [
                    'enabled' => false,
                ],
                'wide_search'    => [
                    'enabled' => false,
                ],
                'controllers'    => [],
            ],
        ];
    }
}
