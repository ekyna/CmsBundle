<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\SlideShow\Type;

use A2lix\TranslationFormBundle\Form\Type\TranslationsFormsType;
use DOMDocument;
use DOMElement;
use Ekyna\Bundle\CmsBundle\Entity\Slide;
use Ekyna\Bundle\CmsBundle\Form\Type\Slide\DefaultTranslationType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints as Assert;

use function Symfony\Component\Translation\t;

/**
 * Class DefaultType
 * @package Ekyna\Bundle\CmsBundle\SlideShow\Type
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class DefaultType extends AbstractType
{
    public function buildForm(FormInterface $form): void
    {
        parent::buildForm($form);

        $form
            ->add('button_url', TextType::class, [
                'label'         => t('slide.type.default.button_url', [], 'EkynaCms'),
                'property_path' => 'data[button_url]',
                'constraints'   => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('translations', TranslationsFormsType::class, [
                'form_type'   => DefaultTranslationType::class,
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

        // Wrapper
        $inner = $dom->createElement('div');
        $inner->setAttribute('class', 'inner');
        $wrapper->appendChild($inner);

        // Title
        $title = $dom->createElement('h2');
        $title->setAttribute('class', 'title h1'); // TODO Config
        $title->textContent = $transData['title'];
        $inner->appendChild($title);

        // Content
        $content = $dom->createElement('div');
        $content->setAttribute('class', 'content'); // TODO Config
        $c = $dom->createDocumentFragment();
        $c->appendXML($transData['content']);
        $content->appendChild($c);
        $inner->appendChild($content);

        // Button
        $button = $dom->createElement('p');
        $button->setAttribute('class', 'button');
        $a = $dom->createElement('a');
        $a->setAttribute('href', $data['button_url']);
        $a->setAttribute('class', 'btn btn-lg btn-primary'); // TODO Config
        $a->textContent = $transData['button_label'];
        $button->appendChild($a);
        $inner->appendChild($button);
    }

    public function buildExample(Slide $slide): void
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
                'content'      => '<p>[Content] Lorem ipsum <strong>dolor sit amet</strong>.</p><p>Consectetur adipiscing elit.</p>',
                'button_label' => '[Button label]',
            ]);
    }

    protected function getConfigDefaults(): array
    {
        return [
            'background_filter' => 'cms_container_background',
        ];
    }
}
