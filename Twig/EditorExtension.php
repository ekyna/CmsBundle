<?php

namespace Ekyna\Bundle\CmsBundle\Twig;

use Ekyna\Bundle\CmsBundle\Editor\Renderer\Renderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class EditorExtension
 * @package Ekyna\Bundle\CmsBundle\Twig
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class EditorExtension extends AbstractExtension
{
    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'cms_document_data',
                [Renderer::class, 'renderDocumentData'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'cms_content',
                [Renderer::class, 'renderContent'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'cms_container',
                [Renderer::class, 'renderContainer'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'cms_row',
                [Renderer::class, 'renderRow'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'cms_block',
                [Renderer::class, 'renderBlock'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}
