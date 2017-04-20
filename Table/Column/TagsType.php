<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Table\Column;

use Ekyna\Bundle\CmsBundle\Service\Renderer\TagRenderer;
use Ekyna\Component\Table\Column\AbstractColumnType;
use Ekyna\Component\Table\Column\ColumnBuilderInterface;
use Ekyna\Component\Table\Column\ColumnInterface;
use Ekyna\Component\Table\Extension\Core\Type\Column\PropertyType;
use Ekyna\Component\Table\Source\RowInterface;
use Ekyna\Component\Table\View\CellView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

/**
 * Class TagsType
 * @package Ekyna\Bundle\CmsBundle\Table\Column
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TagsType extends AbstractColumnType
{
    private static ?TagRenderer $renderer = null;


    public function __construct()
    {
        if (null === static::$renderer) {
            static::$renderer = new TagRenderer();
        }
    }

    /**
     * @inheritDoc
     */
    public function buildColumn(ColumnBuilderInterface $builder, array $options): void
    {
        $builder->setSortable(false);
    }

    /**
     * @inheritDoc
     */
    public function buildCellView(CellView $view, ColumnInterface $column, RowInterface $row, array $options): void
    {
        $view->vars['attr']['class'] = 'flags-icons';
        $view->vars['block_prefix'] = 'text';
        $view->vars['value'] = static::$renderer->renderTags($view->vars['value'], [
            'text'  => $options['text'],
            'badge' => $options['badge'],
        ]);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => t('tag.label.plural', [], 'EkynaCms'),
            'text'  => false,
            'badge' => false,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getParent(): ?string
    {
        return PropertyType::class;
    }
}
