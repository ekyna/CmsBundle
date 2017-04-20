<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Twig;

use Ekyna\Bundle\CmsBundle\Editor\Renderer\EditorRenderer;
use Ekyna\Bundle\CmsBundle\Service\Renderer\CmsRenderer;
use Ekyna\Bundle\CmsBundle\Service\Renderer\LocaleSwitcherRenderer;
use Ekyna\Bundle\CmsBundle\Service\Renderer\MediaRenderer;
use Ekyna\Bundle\CmsBundle\Service\Renderer\MenuRenderer;
use Ekyna\Bundle\CmsBundle\Service\Renderer\NoticeRenderer;
use Ekyna\Bundle\CmsBundle\Service\SchemaOrg\Builder;
use Ekyna\Bundle\CmsBundle\SlideShow\SlideShowRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class CmsExtension
 * @package Ekyna\Bundle\CmsBundle\Twig
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class CmsExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'cms_tags',
                [CmsRenderer::class, 'renderTags'],
                ['is_safe' => ['html']]
            ),
            new TwigFilter(
                'cms_theme',
                [CmsRenderer::class, 'renderTheme'],
                ['is_safe' => ['html']]
            ),
            new TwigFilter(
                'cms_image',
                [MediaRenderer::class, 'renderImage'],
                ['is_safe' => ['html']]
            ),
            new TwigFilter(
                'json_ld',
                [Builder::class, 'build'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'cms_metas',
                [CmsRenderer::class, 'renderMetas'],
                ['is_safe' => ['html'], 'deprecated' => true, 'alternative' => 'cms_seo']
            ),
            new TwigFunction(
                'cms_seo',
                [CmsRenderer::class, 'renderSeo'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'cms_meta',
                [CmsRenderer::class, 'renderMeta'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'cms_title',
                [CmsRenderer::class, 'renderTitle'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'cms_page',
                [CmsRenderer::class, 'getPage']
            ),
            new TwigFunction(
                'cms_page_controller',
                [CmsRenderer::class, 'getPageControllerTitle']
            ),
            new TwigFunction(
                'cms_notices',
                [NoticeRenderer::class, 'render'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'cms_menu',
                [MenuRenderer::class, 'renderMenu'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'cms_breadcrumb',
                [MenuRenderer::class, 'renderBreadcrumb'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'cms_locale_switcher',
                [LocaleSwitcherRenderer::class, 'renderLocaleSwitcher'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'cms_document_data',
                [EditorRenderer::class, 'renderDocumentData'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'cms_content',
                [EditorRenderer::class, 'renderContent'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'cms_container',
                [EditorRenderer::class, 'renderContainer'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'cms_row',
                [EditorRenderer::class, 'renderRow'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'cms_block',
                [EditorRenderer::class, 'renderBlock'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'slide_show_render',
                [SlideShowRenderer::class, 'renderSlideShow'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}
