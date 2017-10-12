<?php

namespace Ekyna\Bundle\CmsBundle\SlideShow\Type;

use Ekyna\Bundle\CmsBundle\Entity\Slide;
use Symfony\Component\Form\FormInterface;

/**
 * Interface SlideTypeInterface
 * @package Ekyna\Bundle\CmsBundle\SlideShow\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface TypeInterface
{
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
