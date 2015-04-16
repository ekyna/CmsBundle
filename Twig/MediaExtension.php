<?php

namespace Ekyna\Bundle\CmsBundle\Twig;

use Gaufrette\Filesystem;
use Ekyna\Bundle\CoreBundle\Model\FileInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class MediaExtension
 * @package Ekyna\Bundle\CmsBundle\Twig
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class MediaExtension extends \Twig_Extension
{
    const VIDEO_HTML5 = <<<HTML
<div class="embed-responsive embed-responsive-%aspect_ratio%">
    <video class="video embed-responsive-item" controls width="%width%" height="%height%">
        <source src="%src%" type="%mime_type%" />
        Your browser does not support the video tag.
    </video>
</div>
HTML;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;


    /**
     * Constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem, UrlGeneratorInterface $urlGenerator)
    {
        $this->filesystem = $filesystem;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritDoc}
     */
    /*public function initRuntime(\Twig_Environment $twig)
    {
        //$this->template = $twig->loadTemplate($this->config['template']);
    }*/

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('video', array($this, 'renderVideo'), array('is_safe' => array('html'))),
        );
    }

    /**
     * Renders the video.
     *
     * @param FileInterface $video
     * @return string
     */
    public function renderVideo(FileInterface $video)
    {
        return strtr(self::VIDEO_HTML5, array(
            '%aspect_ratio%' => '16by9',
            '%width%' => '720',
            '%height%' => '480',
            '%src%' => $this->urlGenerator->generate('ekyna_cms_file', array('key' => $video->getPath())),
            '%mime_type%' => $this->filesystem->mimeType($video->getPath()),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_media';
    }
}
