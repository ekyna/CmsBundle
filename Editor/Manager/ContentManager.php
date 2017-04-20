<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Manager;

use Ekyna\Bundle\CmsBundle\Editor\Exception\InvalidOperationException;
use Ekyna\Bundle\CmsBundle\Editor\Model\ContentInterface;
use Ekyna\Bundle\CmsBundle\Model\ContentSubjectInterface;

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
     * @param ContentSubjectInterface|string $subjectOrName
     *
     * @return ContentInterface
     * @throws InvalidOperationException
     */
    public function create($subjectOrName): ContentInterface
    {
        // Check if container or name is defined
        if (
            !$subjectOrName instanceof ContentSubjectInterface &&
            !(is_string($subjectOrName) && !empty($subjectOrName))
        ) {
            throw new InvalidOperationException('Excepted instance of ContentSubjectInterface or string.');
        }

        // New instance
        $content = $this->editor->getRepository()->createContent();

        // Create default container
        $this->editor->getContainerManager()->create($content);

        // Add to container if available
        if ($subjectOrName instanceof ContentSubjectInterface) {
            $subjectOrName->setContent($content);
        } else {
            $content->setName($subjectOrName);
        }

        return $content;
    }

    /**
     * Fix the containers positions.
     *
     * @param ContentInterface $content
     *
     * @return ContentManager
     */
    public function fixContainersPositions(ContentInterface $content): ContentManager
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
