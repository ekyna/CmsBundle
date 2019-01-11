<?php

namespace Ekyna\Bundle\CmsBundle\Table\Column;

use Ekyna\Bundle\CmsBundle\Service\Renderer\TagRenderer;
use Ekyna\Component\Table\Column\AbstractColumnType;
use Ekyna\Component\Table\Column\ColumnBuilderInterface;
use Ekyna\Component\Table\Column\ColumnInterface;
use Ekyna\Component\Table\Extension\Core\Type\Column\PropertyType;
use Ekyna\Component\Table\Source\RowInterface;
use Ekyna\Component\Table\View\CellView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TagsType
 * @package Ekyna\Bundle\CmsBundle\Table\Column
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TagsType extends AbstractColumnType
{
    /**
     * @var TagRenderer
     */
    static private $renderer;


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
     * @inheritDoc
     */
    public function buildColumn(ColumnBuilderInterface $builder, array $options)
    {
        $builder->setSortable(false);
    }

    /**
     * @inheritDoc
     */
    public function buildCellView(CellView $view, ColumnInterface $column, RowInterface $row, array $options)
    {
        $view->vars['value'] = static::$renderer->renderTags($view->vars['value'], [
            'text'  => $options['text'],
            'badge' => $options['badge'],
        ]);
        $view->vars['block_prefix'] = 'text';
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => 'ekyna_cms.tag.label.plural',
            'text'  => false,
            'badge' => false,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getParent()
    {
        return PropertyType::class;
    }
}
