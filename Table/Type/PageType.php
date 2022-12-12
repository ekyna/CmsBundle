<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Table\Type;

use Ekyna\Bundle\AdminBundle\Action;
use Ekyna\Bundle\ResourceBundle\Table\Type\AbstractResourceType;
use Ekyna\Bundle\TableBundle\Extension\Type as BType;
use Ekyna\Component\Table\Extension\Core\Type as CType;
use Ekyna\Component\Table\Source\RowInterface;
use Ekyna\Component\Table\TableBuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function Symfony\Component\Translation\t;

/**
 * Class PageType
 * @package Ekyna\Bundle\CmsBundle\Table\Type
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageType extends AbstractResourceType
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function buildTable(TableBuilderInterface $builder, array $options): void
    {
        $builder
            ->addDefaultSort('root')
            ->addDefaultSort('left')
            ->setSortable(false)
            ->setFilterable(false)
            ->setPerPageChoices([100])
            ->addColumn('name', BType\Column\NestedAnchorType::class, [
                'label'    => t('field.name', [], 'EkynaUi'),
                'position' => 10,
            ])
            ->addColumn('enabled', CType\Column\BooleanType::class, [
                'disable_property_path' => 'static',
                'label'                 => t('field.enabled', [], 'EkynaUi'),
                'property'              => 'enabled',
                'position'              => 20,
            ])
            ->addColumn('actions', BType\Column\NestedActionsType::class, [
                'roots'                 => true,
                'resource'              => $this->dataClass,
                'disable_property_path' => 'locked',
                'actions'               => [
                    Action\UpdateAction::class,
                    Action\DeleteAction::class,
                ],
                'buttons'               => [
                    function (RowInterface $row): ?array {
                        $page = $row->getData(null);

                        if (!$page->isEnabled() || $page->isDynamicPath()) {
                            return null;
                        }

                        return [
                            'label'  => t('resource.button.show_front', [], 'EkynaAdmin'),
                            'theme'  => 'default',
                            'icon'   => 'eye-open',
                            'target' => '_blank',
                            'path'   => $this->urlGenerator->generate($page->getRoute()),
                        ];
                    },
                    function (RowInterface $row): ?array {
                        $page = $row->getData(null);

                        if (!$page->isEnabled() || $page->isDynamicPath()) {
                            return null;
                        }

                        return [
                            'label'  => t('resource.button.show_editor', [], 'EkynaAdmin'),
                            'theme'  => 'default',
                            'icon'   => 'edit',
                            'target' => '_blank',
                            'path'   => $this->urlGenerator->generate('admin_ekyna_cms_editor_index', [
                                'path' => $this->urlGenerator->generate($page->getRoute()),
                            ]),
                        ];
                    },
                ],
            ]);
    }
}
