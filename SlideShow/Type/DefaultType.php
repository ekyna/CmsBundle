<?php

namespace Ekyna\Bundle\CmsBundle\SlideShow\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use Ekyna\Bundle\CmsBundle\Entity\Slide;
use Ekyna\Bundle\CmsBundle\Form\Type\Slide\DefaultTranslationType;
use Ekyna\Bundle\CmsBundle\Form\Type\Slide\ThemeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class DefaultType
 * @package Ekyna\Bundle\CmsBundle\SlideShow\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class DefaultType extends AbstractType
{
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
            ->add('button_url', TextType::class, [
                'label' => 'ekyna_cms.slide.type.default.button_url',
                'property_path' => 'data[button_url]',
                'constraints'   => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('translations', TranslationsFormsType::class, [
                'form_type' => DefaultTranslationType::class,
                'label'     => false,
                'attr'      => [
                    'widget_col' => 12,
                ],
                'constraints'   => [
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

        // Title
        $title = $dom->createElement('h2');
        $title->setAttribute('class', 'h1'); // TODO Config
        $title->textContent = $transData['title'];
        $wrapper->appendChild($title);

        // Content
        $content = $dom->createElement('p');
        $content->setAttribute('class', 'lead'); // TODO Config
        $content->textContent = $transData['content'];
        $wrapper->appendChild($content);

        // Button
        $button = $dom->createElement('p');
        $button->setAttribute('class', 'button');
        $a = $dom->createElement('a');
        $a->setAttribute('href', $data['button_url']);
        $a->setAttribute('class', 'btn btn-lg btn-primary'); // TODO Config
        $a->textContent = $transData['button_label'];
        $button->appendChild($a);
        $wrapper->appendChild($button);
    }

    /**
     * @inheritDoc
     */
    public function buildExample(Slide $slide)
    {
        // TODO background color/image

        $slide
            ->setName('Default example')
            ->setType($this->getName())
            ->setData([
                'button_url' => 'javascript: void(0)',
            ])
            ->translate()
            ->setData([
                'title'        => '[Title] Lorem ipsum dolor sit amet',
                'content'      => '[Content] Lorem ipsum dolor sit amet, consectetur adipiscing elit',
                'button_label' => '[Button label]',
            ]);
    }
}
