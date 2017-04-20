<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\View;

/**
 * Class ContainerView
 * @package Ekyna\Bundle\CmsBundle\Editor\View
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ContainerView extends AbstractView
{
    private AttributesInterface $innerAttributes;

    /** @var RowView[] */
    public array  $rows         = [];
    public string $content      = '';
    public string $innerContent = '';


    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->innerAttributes = new Attributes();
    }

    /**
     * Returns the inner attributes.
     *
     * @return AttributesInterface
     */
    public function getInnerAttributes(): AttributesInterface
    {
        return $this->innerAttributes;
    }
}
