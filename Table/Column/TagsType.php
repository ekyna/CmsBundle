<?php

namespace Ekyna\Bundle\CmsBundle\Table\Column;

use Ekyna\Bundle\CmsBundle\Service\Renderer\TagRenderer;
use Ekyna\Component\Table\Extension\Core\Type\Column\TextType;
use Ekyna\Component\Table\Table;
use Ekyna\Component\Table\View\Cell;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TagsType
 * @package Ekyna\Bundle\CmsBundle\Table\Column
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TagsType extends TextType
{
    /**
     * @var TagRenderer
     */
    static $renderer;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        if (null === static::$renderer) {
            static::$renderer = new TagRenderer();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildViewCell(Cell $cell, Table $table, array $options)
    {
        $tags = $table->getCurrentRowData($options['property_path']);

        $cell->setVars([
            'type'   => 'text',
            'value'  => static::$renderer->renderTags($tags, [
                'text'  => false,
            ]),
            'sorted' => $options['sorted'],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('label', 'ekyna_cms.tag.label.plural');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_cms_tags';
    }
}
