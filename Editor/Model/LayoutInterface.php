<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Model;

/**
 * Interface LayoutInterface
 * @package Ekyna\Bundle\CmsBundle\Editor\Model
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface LayoutInterface
{
    /**
     * Sets the layout.
     *
     * @param array $layout
     */
    public function setLayout(array $layout): void;

    /**
     * Returns the layout.
     *
     * @return array
     */
    public function getLayout(): array;

    /**
     * Returns the layout styles configuration.
     *
     * ['property'] => [
     *     'type'     => {string}, // 'int' or 'string'
     *     'default   => {int|string}
     *     'min'      => {int}, // if type = int
     *     'max'      => {int}, // if type = int
     *     'values'   => {string}[], // Valid values (type = int|string)
     *     'template' => '[string]', // for addStyle: sprintf($template, $value)
     * ]
     *
     * @return array
     *
     * @todo Implements and use un AbstractAdapter::validateLayoutStyles and AbstractAdapter::applyLayoutStyles
     */
    //public function getLayoutStylesConfiguration();
}
