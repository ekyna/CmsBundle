<?php

namespace Ekyna\Bundle\CmsBundle\SlideShow\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Ekyna\Bundle\CmsBundle\Entity\Slide;
use Ekyna\Bundle\CmsBundle\Form\Type\Slide\HeroTranslationType;
use Ekyna\Bundle\CmsBundle\Form\Type\Slide\ImageType;
use Ekyna\Bundle\CmsBundle\Form\Type\Slide\ThemeType;
use Ekyna\Bundle\CoreBundle\Form\Type\ColorPickerType;
use Ekyna\Bundle\CoreBundle\Validator\Constraints\Color;
use Ekyna\Bundle\MediaBundle\Entity\MediaRepository;
use Ekyna\Bundle\MediaBundle\Service\Renderer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class HeroType
 * @package Ekyna\Bundle\CmsBundle\SlideShow\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class HeroType extends AbstractType
{
    /**
     * @var MediaRepository
     */
    private $mediaRepository;

    /**
     * @var Renderer
     */
    private $mediaRenderer;


    /**
     * Constructor.
     *
     * @param MediaRepository $mediaRepository
     * @param Renderer        $mediaRenderer
     */
    public function __construct(MediaRepository $mediaRepository, Renderer $mediaRenderer)
    {
        $this->mediaRepository = $mediaRepository;
        $this->mediaRenderer = $mediaRenderer;
    }

    /**
     * @inheritDoc
     */
    public function buildForm(FormInterface $form)
    {
        $form
            ->add('theme', ThemeType::class, [
                'property_path' => 'data[theme]',
                'constraints'   => [
                    new Assert\NotNull(),
                ],
            ])
            ->add('media', ImageType::class, [
                'property_path' => 'data[media_id]',
                'constraints'   => [
                    new Assert\NotNull(),
                ],
            ])
            ->add('backgroundColor', ColorPickerType::class, [
                'property_path' => 'data[background_color]',
                'required'      => false,
                'constraints'   => [
                    new Color(),
                ],
            ])
            ->add('button_url', TextType::class, [
                'label'         => 'ekyna_cms.slide.type.default.button_url',
                'property_path' => 'data[button_url]',
                'required'      => false,
            ])
            ->add('translations', TranslationsFormsType::class, [
                'form_type'   => HeroTranslationType::class,
                'label'       => false,
                'attr'        => [
                    'widget_col' => 12,
                ],
                'constraints' => [
                    new Assert\Count(['min' => 1]),
                ],
            ]);
    }

    /**
     * @inheritDoc
     */
    public function render(Slide $slide, \DOMElement $element, \DOMDocument $dom)
    {
        $wrapper = $this->appendWrapper($element, $dom);

        $data = $slide->getData();
        $transData = $slide->translate()->getData();

        // Theme
        if (isset($data['theme']) && !empty($theme = $data['theme'])) {
            $classes = $element->hasAttribute('class') ? explode(' ', $element->getAttribute('class')) : [];
            $classes[] = 'cms-slide-' . $theme;
            $element->setAttribute('class', implode(' ', $classes));
        }

        $styles = $element->hasAttribute('style') ? $this->explodeStyle($element->getAttribute('style')) : [];

        // Background
        $path = null;
        if (isset($data['background_color']) && !empty($color = $data['background_color'])) {
            $styles['background-color'] = $color;
        }
        if (!empty($styles)) {
            $element->setAttribute('style', $this->implodeStyle($styles));
        }

        // Left column
        $left = $dom->createElement('div');
        $left->setAttribute('class', 'left');
        $wrapper->appendChild($left);

        // Title
        $title = $dom->createElement('h2');
        $title->setAttribute('class', 'h1'); // TODO Config
        $title->textContent = $transData['title'];
        $left->appendChild($title);

        // Content
        $content = $dom->createElement('p');
        $content->setAttribute('class', 'lead'); // TODO Config
        $content->textContent = $transData['content'];
        $left->appendChild($content);

        // Button
        if (!empty($data['button_url']) && !empty($transData['button_label'])) {
            $button = $dom->createElement('p');
            $button->setAttribute('class', 'button');
            $a = $dom->createElement('a');
            $a->setAttribute('href', $data['button_url']);
            $a->setAttribute('class', 'btn btn-primary'); // TODO Config
            $a->textContent = $transData['button_label'];
            $button->appendChild($a);
            $left->appendChild($button);
        }

        // Image
        $path = null;
        if (isset($data['media_id']) && 0 < $mediaId = intval($data['media_id'])) {
            /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media */
            if (null !== $media = $this->mediaRepository->find($mediaId)) {
                $path = $this
                    ->mediaRenderer
                    ->getGenerator()
                    ->generateFrontUrl($media, $this->config['image_filter']);

            }
        }
        if (null === $path && isset($data['image'])) {
            $path = $data['image'];
        }
        $image = $dom->createElement('img');
        $image->setAttribute('src', $path);
        $wrapper->appendChild($image);

        /*$button = $dom->createElement('p');
        $button->setAttribute('class', 'button');
        $a = $dom->createElement('a');
        $a->setAttribute('href', $data['button_url']);
        $a->setAttribute('class', 'btn btn-lg btn-primary'); // TODO Config
        $a->textContent = $transData['button_label'];
        $button->appendChild($a);
        $wrapper->appendChild($button);*/
    }

    /**
     * @inheritDoc
     */
    public function buildExample(Slide $slide)
    {
        $slide
            ->setName('Default example')
            ->setType($this->getName())
            ->setData([
                // TODO MEDIA
                'image' => 'http://fakeimg.pl/640x320/fff,0/000,255?text=Image',
                //'button_url'   => 'javascript: void(0)',
            ])
            ->translate()
            ->setData([
                'title'   => '[Title] Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'content' => '[Content] Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur dapibus lorem at leo bibendum consectetur. Donec consequat dictum ullamcorper.',
                //'button_label' => '[Button label]',
            ]);
    }

    /**
     * @inheritDoc
     */
    protected function getConfigDefaults()
    {
        return [
            'image_filter' => 'cms_slideshow_hero',
        ];
    }
}
