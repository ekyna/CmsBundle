<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor;

/**
 * Trait EditorAwareTrait
 * @package Ekyna\Bundle\CmsBundle\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
trait EditorAwareTrait
{
    protected Editor $editor;


    /**
     * Sets the editor.
     *
     * @param Editor $editor
     */
    public function setEditor(Editor $editor): void
    {
        $this->editor = $editor;
    }
}
