<?php

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
     *
     * @return mixed
     */
    public function setEditor(Editor $editor);
}
