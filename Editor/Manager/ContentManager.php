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
     * @throws InvalidOperationException
     */
    public function create(ContentSubjectInterface|string $subjectOrName): ContentInterface
    {
        // Check if container or name is defined
        if (is_string($subjectOrName) && empty($subjectOrName)) {
            throw new InvalidOperationException('Excepted instance of ContentSubjectInterface or non-empty string.');
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
     * Fixes the containers positions.
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
