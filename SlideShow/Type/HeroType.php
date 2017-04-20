<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\SlideShow\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use DOMDocument;
use DOMElement;
use Ekyna\Bundle\CmsBundle\Entity\Slide;
use Ekyna\Bundle\CmsBundle\Form\Type\Slide\HeroTranslationType;
use Ekyna\Bundle\CmsBundle\Form\Type\Slide\ImageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints as Assert;

use function Symfony\Component\Translation\t;

/**
 * Class HeroType
 * @package Ekyna\Bundle\CmsBundle\SlideShow\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class HeroType extends AbstractType
{
    public function buildForm(FormInterface $form): void
    {
        parent::buildForm($form);

        $form
            ->add('media', ImageType::class, [
                'property_path' => 'data[media_id]',
                'constraints'   => [
                    new Assert\NotNull(),
                ],
            ])
            ->add('button_url', TextType::class, [
                'label'         => t('slide.type.default.button_url', [], 'EkynaCms'),
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

    public function render(Slide $slide, DOMElement $element, DOMDocument $dom): void
    {
        parent::render($slide, $element, $dom);

        $wrapper = $this->appendWrapper($element, $dom);

        $data = $slide->getData();
        $transData = $slide->translate()->getData();

        // Left column
        $left = $dom->createElement('div');
        $left->setAttribute('class', 'left');
        $wrapper->appendChild($left);

        // Title
        $title = $dom->createElement('h2');
        $title->setAttribute('class', 'title h1'); // TODO Config
        $title->textContent = $transData['title'];
        $left->appendChild($title);

        // Content
        $content = $dom->createElement('div');
        $content->setAttribute('class', 'content'); // TODO Config
        $c = $dom->createDocumentFragment();
        $c->appendXML($transData['content']);
        $content->appendChild($c);
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

        // Right column
        $right = $dom->createElement('div');
        $right->setAttribute('class', 'right');
        $wrapper->appendChild($right);

        // Image
        $path = null;
        if (isset($data['media_id']) && 0 < $mediaId = intval($data['media_id'])) {
            /** @var \Ekyna\Bundle\MediaBundle\Model\MediaInterface $media */
            if (null !== $media = $this->mediaRepository->find($mediaId)) {
                $path = $this->mediaGenerator->generateFrontUrl($media, $this->config['image_filter']);
            }
        }
        if (null === $path && isset($data['image'])) {
            $path = $data['image'];
        }
        $right->setAttribute('style', "background-image: url('$path')");
    }

    public function buildExample(Slide $slide): void
    {
        $slide
            ->setName('Default example')
            ->setType($this->getName())
            ->setData([
                'image'      => 'http://fakeimg.pl/640x320/fff,0/000,255?text=Image',
                // TODO Background
                'button_url' => 'javascript: void(0)',
            ])
            ->translate()
            ->setData([
                'title'        => '[Title] Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'content'      => '<p>[Content] Lorem ipsum <strong>dolor sit amet</strong>, consectetur adipiscing elit.</p><p>Donec consequat dictum ullamcorper.</p>',
                'button_label' => '[Button label]',
            ]);
    }

    protected function getConfigDefaults(): array
    {
        return [
            'image_filter'      => 'cms_slideshow_hero',
            'background_filter' => 'cms_container_background',
        ];
    }
}
