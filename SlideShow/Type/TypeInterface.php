<?php

namespace Ekyna\Bundle\CmsBundle\SlideShow\Type;

use Ekyna\Bundle\CmsBundle\Entity\Slide;
use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Bundle\MediaBundle\Service\Generator;
use Symfony\Component\Form\FormInterface;

/**
 * Interface SlideTypeInterface
 * @package Ekyna\Bundle\CmsBundle\SlideShow\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface TypeInterface
{
    /**
     * Sets the media repository.
     *
     * @param MediaRepository $repository
     */
    public function setMediaRepository(MediaRepository $repository);

    /**
     * Sets the media generator.
     *
     * @param Generator $generator
     */
    public function setMediaGenerator(Generator $generator);

    /**
     * Configures the type.
     *
     * @param string $name
     * @param string $label
     * @param string $jsPath
     * @param array  $config
     */
    public function configure($name, $label, $jsPath, array $config = []);

    /**
     * Builds the slide form.
     *
     * @param FormInterface $form
     */
    public function buildForm(FormInterface $form);

    /**
     * Renders the slide.
     *
     * @param Slide        $slide
     * @param \DOMElement  $element
     * @param \DOMDocument $dom
     */
    public function render(Slide $slide, \DOMElement $element, \DOMDocument $dom);

    /**
     * returns an example slide.
     *
     * @param Slide $slide
     */
    public function buildExample(Slide $slide);

    /**
     * Returns the type name.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the label.
     *
     * @return string
     */
    public function getLabel();

    /**
     * Returns the jsPath.
     *
     * @return string
     */
    public function getJsPath();

    /**
     * Returns the config.
     *
     * @return array
     */
    public function getConfig();
}
