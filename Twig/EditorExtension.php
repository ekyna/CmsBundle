<?php

namespace Ekyna\Bundle\CmsBundle\Twig;

use Ekyna\Bundle\CmsBundle\Editor\Renderer\Renderer;

/**
 * Class EditorExtension
 * @package Ekyna\Bundle\CmsBundle\Twig
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class EditorExtension extends \Twig_Extension
{
    /**
     * @var Renderer
     */
    private $renderer;


    /**
     * Constructor.
     *
     * @param Renderer $renderer
     */
    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'cms_document_data',
                [$this->renderer, 'renderDocumentData'],
                ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction(
                'cms_content',
                [$this->renderer, 'renderContent'],
                ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction(
                'cms_container',
                [$this->renderer, 'renderContainer'],
                ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction(
                'cms_row',
                [$this->renderer, 'renderRow'],
                ['is_safe' => ['html']]
            ),
            new \Twig_SimpleFunction(
                'cms_block',
                [$this->renderer, 'renderBlock'],
                ['is_safe' => ['html']]
            ),
        ];
    }
}
