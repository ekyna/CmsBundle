<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Table\Type;

use Ekyna\Bundle\AdminBundle\Action;
use Ekyna\Bundle\ResourceBundle\Table\Type\AbstractResourceType;
use Ekyna\Bundle\TableBundle\Extension\Type as BType;
use Ekyna\Component\Table\Extension\Core\Type\Column\TextType;
use Ekyna\Component\Table\TableBuilderInterface;
use Ekyna\Component\Table\Util\ColumnSort;

use function Symfony\Component\Translation\t;

/**
 * Class SlideType
 * @package Ekyna\Bundle\CmsBundle\Table\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class SlideType extends AbstractResourceType
{
    public function buildTable(TableBuilderInterface $builder, array $options): void
    {
        $builder
            ->setSortable(false)
            ->setConfigurable(false)
            ->setBatchable(false)
            ->setExportable(false)
            ->setProfileable(false)
            ->addDefaultSort('position', ColumnSort::ASC)
            ->addColumn('name', TextType::class, [
                'label'        => t('field.name', [], 'EkynaUi'),
                'position'     => 10,
            ])
            ->addColumn('actions', BType\Column\ActionsType::class, [
                'resource' => $this->dataClass,
                'actions'  => [
                    Action\MoveUpAction::class,
                    Action\MoveDownAction::class,
                    Action\UpdateAction::class,
                    Action\DeleteAction::class,
                ],
            ]);
    }
}
