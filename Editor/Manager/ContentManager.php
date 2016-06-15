<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Manager;

use Ekyna\Bundle\CmsBundle\Editor\Exception\InvalidOperationException;
use Ekyna\Bundle\CmsBundle\Entity;
use Ekyna\Bundle\CmsBundle\Model;

/**
 * Class ContentManager
 * @package Ekyna\Bundle\CmsBundle\Editor\Manager
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class ContentManager extends AbstractManager
{
    /**
     * Creates a new content.
     *
     * @param Model\ContentSubjectInterface|string $subjectOrName
     *
     * @return Model\ContentInterface
     * @throws InvalidOperationException
     */
    public function create($subjectOrName)
    {
        // Check if container or name is defined
        if (!$subjectOrName instanceof Model\ContentSubjectInterface
            || (is_string($subjectOrName) && 0 == strlen($subjectOrName))
        ) {
            throw new InvalidOperationException("Excepted instance of ContentSubjectInterface or string.");
        }

        // New instance
        $content = new Entity\Content();

        // Create default container
        $this->getEditor()->getContainerManager()->create($content);

        // Add to container if available
        if ($subjectOrName instanceof Model\ContentSubjectInterface) {
            $subjectOrName->setContent($content);
        } else {
            $content->setName($subjectOrName);
        }

        return $content;
    }

    /**
     * Fix the container positions.
     *
     * @param Model\ContentInterface $content
     *
     * @return ContentManager
     */
    public function fixContainerPositions(Model\ContentInterface $content)
    {
        $this->sortChildrenByPosition($content, 'containers');

        $containers = $content->getContainers();

        $position = 0;
        foreach ($containers as $container) {
            $container->setPosition($position);
            $position++;
        }

        return $this;
    }
}
