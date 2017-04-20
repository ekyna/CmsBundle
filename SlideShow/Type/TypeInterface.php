<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\SlideShow\Type;

use DOMDocument;
use DOMElement;
use Ekyna\Bundle\CmsBundle\Entity\Slide;
use Ekyna\Bundle\MediaBundle\Repository\MediaRepositoryInterface;
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
     * @param MediaRepositoryInterface $repository
     */
    public function setMediaRepository(MediaRepositoryInterface $repository): void;

    /**
     * Sets the media generator.
     *
     * @param Generator $generator
     */
    public function setMediaGenerator(Generator $generator): void;

    /**
     * Configures the type.
     *
     * @param string      $name
     * @param string      $label
     * @param string      $jsPath
     * @param array       $config
     * @param string|null $domain
     */
    public function configure(
        string $name,
        string $label,
        string $jsPath,
        array $config = [],
        string $domain = null
    ): void;

    /**
     * Builds the slide form.
     *
     * @param FormInterface $form
     */
    public function buildForm(FormInterface $form): void;

    /**
     * Renders the slide.
     *
     * @param Slide       $slide
     * @param DOMElement  $element
     * @param DOMDocument $dom
     */
    public function render(Slide $slide, DOMElement $element, DOMDocument $dom): void;

    /**
     * returns an example slide.
     *
     * @param Slide $slide
     */
    public function buildExample(Slide $slide): void;

    /**
     * Returns the type name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the label.
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Returns the label translation domain.
     *
     * @return string|null
     */
    public function getDomain(): ?string;

    /**
     * Returns the jsPath.
     *
     * @return string
     */
    public function getJsPath(): string;

    /**
     * Returns the config.
     *
     * @return array
     */
    public function getConfig(): array;
}
