<?php

namespace Ekyna\Bundle\CmsBundle\Editor;

/**
 * Trait EditorAwareTrait
 * @package Ekyna\Bundle\CmsBundle\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
trait EditorAwareTrait
{
    /**
     * @var Editor
     */
    protected $editor;


    /**
     * Sets the editor.
     *
     * @param Editor $editor
     */
    public function setEditor(Editor $editor)
    {
        $this->editor = $editor;
    }
}
