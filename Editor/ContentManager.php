<?php

namespace Ekyna\Bundle\CmsBundle\Editor;

use Ekyna\Bundle\CmsBundle\Model\ContentInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentSubjectInterface;

/**
 * Class ContentManager
 * @package Ekyna\Bundle\CmsBundle\Editor
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ContentManager
{
    /**
     * Creates a new content.
     *
     * @param ContentSubjectInterface $subject
     *
     * @return ContentInterface
     */
    public function create(ContentSubjectInterface $subject):ContentInterface
    {
        // New instance

        // New default container (with new default block)

        // Add to subject

        // Return
    }

    /**
     * Updates a content layout (containers positions).
     *
     * @param ContentInterface $content
     * @param array            $layout
     */
    public function updateLayout(ContentInterface $content, array $layout)
    {
        // Validate layout

        // For each container
            // Set position

        // Reorder containers
    }
}
