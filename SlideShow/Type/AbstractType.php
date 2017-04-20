<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\SlideShow\Type;

use DOMDocument;
use DOMElement;
use Ekyna\Bundle\CmsBundle\Entity\Slide;
use Ekyna\Bundle\CmsBundle\Form\Type\Slide\ImageType;
use Ekyna\Bundle\CmsBundle\Form\Type\Slide\ThemeType;
use Ekyna\Bundle\CmsBundle\SlideShow\DOMUtil;
use Ekyna\Bundle\MediaBundle\Model\MediaInterface;
use Ekyna\Bundle\MediaBundle\Repository\MediaRepositoryInterface;
use Ekyna\Bundle\MediaBundle\Service\Generator;
use Ekyna\Bundle\UiBundle\Form\Type\ColorPickerType;
use Ekyna\Bundle\UiBundle\Validator\Constraints\Color;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints as Assert;

use function Symfony\Component\Translation\t;

/**
 * Class AbstractType
 * @package Ekyna\Bundle\CmsBundle\SlideShow\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
abstract class AbstractType implements TypeInterface
{
    /**
     * Returns the default theme choices.
     */
    public static function getDefaultThemeChoices(): array
    {
        return [
            'light' => 'Light',
            'dark'  => 'Dark',
        ];
    }

    protected MediaRepositoryInterface $mediaRepository;
    protected Generator                $mediaGenerator;
    protected string                   $name;
    protected string                   $jsPath;
    protected array                    $config;
    protected string                   $label;
    protected ?string                  $domain = null;

    public function setMediaRepository(MediaRepositoryInterface $repository): void
    {
        $this->mediaRepository = $repository;
    }

    public function setMediaGenerator(Generator $generator): void
    {
        $this->mediaGenerator = $generator;
    }

    public function configure(
        string $name,
        string $label,
        string $jsPath,
        array  $config = [],
        string $domain = null
    ): void {
        $this->name = $name;
        $this->label = $label;
        $this->jsPath = $jsPath;
        $this->config = array_replace($this->getConfigDefaults(), $config);
        $this->domain = $domain;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function getJsPath(): string
    {
        return $this->jsPath;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function buildForm(FormInterface $form): void
    {
        $form
            ->add('theme', ThemeType::class, [
                'property_path' => 'data[theme]',
                'constraints'   => [
                    new Assert\NotNull(),
                ],
            ])
            ->add('background', ImageType::class, [
                'label'         => t('slide.field.background_image', [], 'EkynaCms'),
                'property_path' => 'data[background_id]',
            ])
            ->add('backgroundColor', ColorPickerType::class, [
                'label'         => t('slide.field.background_color', [], 'EkynaCms'),
                'property_path' => 'data[background_color]',
                'required'      => false,
                'constraints'   => [
                    new Color(),
                ],
            ]);
    }

    public function render(Slide $slide, DOMElement $element, DOMDocument $dom): void
    {
        $data = $slide->getData();

        // Theme
        if (isset($data['theme']) && !empty($theme = $data['theme'])) {
            DOMUtil::addClass($element, 'cms-slide-' . $theme);
        }

        // Background color
        if (isset($data['background_color']) && !empty($color = $data['background_color'])) {
            DOMUtil::addStyle($element, 'background-color', $color);
        }

        // Background image
        $path = null;
        if (isset($data['background_id']) && 0 < $id = intval($data['background_id'])) {
            /** @var MediaInterface $media */
            if (null !== $media = $this->mediaRepository->find($id)) {
                $path = $this->mediaGenerator->generateFrontUrl($media, $this->config['background_filter']);
            }
        }
        if (null === $path && isset($data['background'])) {
            $path = $data['background'];
        }
        if (!empty($path)) {
            $background = $dom->createElement('div');
            $background->setAttribute('class', 'background');
            $background->setAttribute('style', "background-image:url('$path')");
            $element->appendChild($background);
        }
    }

    /**
     * Appends a wrapper to the element.
     */
    protected function appendWrapper(DOMElement $element, DOMDocument $dom): DOMElement
    {
        $wrapper = $dom->createElement('div');
        $wrapper->setAttribute('class', 'cms-slide-wrapper');

        $element->appendChild($wrapper);

        return $wrapper;
    }

    /**
     * Returns the config defaults.
     */
    protected function getConfigDefaults(): array
    {
        return [];
    }
}
