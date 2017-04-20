<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor;

/**
 * Interface EditorAwareInterface
 * @package Ekyna\Bundle\CmsBundle\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface EditorAwareInterface
{
    /**
     * Sets the editor.
     *
     * @param Editor $editor
     */
    public function setEditor(Editor $editor): void;
}
