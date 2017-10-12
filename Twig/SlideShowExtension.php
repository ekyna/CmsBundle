<?php

namespace Ekyna\Bundle\CmsBundle\Twig;

use Ekyna\Bundle\CmsBundle\Entity\SlideShow;
use Ekyna\Bundle\CmsBundle\SlideShow\RendererInterface;
use Ekyna\Component\Resource\Doctrine\ORM\ResourceRepositoryInterface;

/**
 * Class SlideShowExtension
 * @package Ekyna\Bundle\CmsBundle\Twig
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class SlideShowExtension extends \Twig_Extension
{
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var ResourceRepositoryInterface
     */
    private $repository;


    /**
     * Constructor.
     *
     * @param RendererInterface           $renderer
     * @param ResourceRepositoryInterface $repository
     */
    public function __construct(
        RendererInterface $renderer,
        ResourceRepositoryInterface $repository
    ) {
        $this->renderer = $renderer;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                'slide_show_render',
                [$this, 'renderSlideShow'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * Returns the slide show config.
     *
     * @param SlideShow|string $slideShowOrTag
     * @param array            $options
     *
     * @return string
     */
    public function renderSlideShow($slideShowOrTag, array $options = [])
    {
        if (is_string($slideShowOrTag)) {
            $slideShowOrTag = $this->repository->findOneBy(['tag' => $slideShowOrTag]);
        }
        if (!$slideShowOrTag instanceof SlideShow) {
            throw new \InvalidArgumentException("Expected tag or instance of " . SlideShow::class);
        }

        return $this->renderer->render($slideShowOrTag, $options);
    }
}
