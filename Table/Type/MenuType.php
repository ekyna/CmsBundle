<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Table\Type;

use Ekyna\Bundle\AdminBundle\Action;
use Ekyna\Bundle\ResourceBundle\Table\Type\AbstractResourceType;
use Ekyna\Bundle\TableBundle\Extension\Type as BType;
use Ekyna\Component\Table\Extension\Core\Type as CType;
use Ekyna\Component\Table\TableBuilderInterface;

use function Symfony\Component\Translation\t;

/**
 * Class MenuType
 * @package Ekyna\Bundle\CmsBundle\Table\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MenuType extends AbstractResourceType
{
    public function buildTable(TableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addDefaultSort('root')
            ->addDefaultSort('left')
            ->setSortable(false)
            ->setFilterable(false)
            ->setPerPageChoices([100])
            ->addColumn('title', BType\Column\NestedAnchorType::class, [
                'label'    => t('field.title', [], 'EkynaUi'),
                'position' => 10,
            ])
            ->addColumn('name', CType\Column\TextType::class, [
                'label'    => t('field.name', [], 'EkynaUi'),
                'position' => 20,
            ])
            ->addColumn('enabled', CType\Column\BooleanType::class, [
                'label'                 => t('field.enabled', [], 'EkynaUi'),
                'disable_property_path' => 'locked',
                'property'              => 'enabled',
                'position'              => 30,
            ])
            ->addColumn('actions', BType\Column\NestedActionsType::class, [
                'roots'                 => true,
                'resource'              => $this->dataClass,
                'disable_property_path' => 'locked',
                'actions'               => [
                    Action\UpdateAction::class,
                    Action\DeleteAction::class,
                ],
            ]);
    }
}
