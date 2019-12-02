<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Repository;

use Doctrine\Common\Collections\Collection;
use Ekyna\Bundle\CmsBundle\Repository as ER;
use Ekyna\Bundle\CmsBundle\Editor\Model as EM;
use Ekyna\Bundle\CmsBundle\Model\ContentSubjectInterface;
use Ekyna\Component\Resource\Model\SortableInterface;

/**
 * Class Repository
 * @package Ekyna\Bundle\CmsBundle\Editor\Repository
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Repository implements RepositoryInterface
{
    /**
     * @var ER\ContentRepository
     */
    private $contentRepository;

    /**
     * @var ER\ContainerRepository
     */
    private $containerRepository;

    /**
     * @var ER\RowRepository
     */
    private $rowRepository;

    /**
     * @var ER\BlockRepository
     */
    private $blockRepository;


    /**
     * Constructor.
     *
     * @param ER\ContentRepository   $contentRepository
     * @param ER\ContainerRepository $containerRepository
     * @param ER\RowRepository       $rowRepository
     * @param ER\BlockRepository     $blockRepository
     */
    public function __construct(
        ER\ContentRepository $contentRepository,
        ER\ContainerRepository $containerRepository,
        ER\RowRepository $rowRepository,
        ER\BlockRepository $blockRepository
    ) {
        $this->contentRepository = $contentRepository;
        $this->containerRepository = $containerRepository;
        $this->rowRepository = $rowRepository;
        $this->blockRepository = $blockRepository;
    }

    /**
     * @inheritdoc
     */
    public function createContent()
    {
        return $this->contentRepository->createNew();
    }

    /**
     * @inheritdoc
     */
    public function createContainer()
    {
        return $this->containerRepository->createNew();
    }

    /**
     * @inheritdoc
     */
    public function createRow()
    {
        return $this->rowRepository->createNew();
    }

    /**
     * @inheritdoc
     */
    public function createBlock()
    {
        return $this->blockRepository->createNew();
    }

    /**
     * @inheritdoc
     */
    public function findContentById($id)
    {
        return $this->contentRepository->findOneById($id);
    }

    /**
     * @inheritdoc
     */
    public function findContainerById($id)
    {
        return $this->containerRepository->findOneById($id);
    }

    /**
     * @inheritdoc
     */
    public function findRowById($id)
    {
        return $this->rowRepository->findOneById($id);
    }

    /**
     * @inheritdoc
     */
    public function findBlockById($id)
    {
        return $this->blockRepository->findOneById($id);
    }

    /**
     * @inheritdoc
     */
    public function findContentByName($name)
    {
        return $this->contentRepository->findOneByName($name);
    }

    /**
     * @inheritdoc
     */
    public function findContainerByName($name)
    {
        return $this->containerRepository->findOneByName($name);
    }

    /**
     * @inheritdoc
     */
    public function findRowByName($name)
    {
        return $this->rowRepository->findOneByName($name);
    }

    /**
     * @inheritdoc
     */
    public function findBlockByName($name)
    {
        return $this->blockRepository->findOneByName($name);
    }

    /**
     * Returns the sibling of the given container.
     *
     * @param EM\ContainerInterface $container
     * @param bool            $next Whether to look for the next or the previous
     *
     * @return EM\ContainerInterface|null
     */
    public function findSiblingContainer(EM\ContainerInterface $container, $next = false)
    {
        if (null === $content = $container->getContent()) {
            return null;
        }

        return $this->findSibling($content->getContainers(), $container, $next);
    }

    /**
     * Returns the sibling of the given row.
     *
     * @param EM\RowInterface $row
     * @param bool            $next Whether to look for the next or the previous
     *
     * @return EM\RowInterface|null
     */
    public function findSiblingRow(EM\RowInterface $row, $next = false)
    {
        if (null === $container = $row->getContainer()) {
            return null;
        }

        return $this->findSibling($container->getRows(), $row, $next);
    }

    /**
     * Returns the sibling of the given block.
     *
     * @param EM\BlockInterface $block
     * @param bool            $next Whether to look for the next or the previous
     *
     * @return EM\BlockInterface|null
     */
    public function findSiblingBlock(EM\BlockInterface $block, $next = false)
    {
        if (null === $row = $block->getRow()) {
            return null;
        }

        return $this->findSibling($row->getBlocks(), $block, $next);
    }

    /**
     * Finds the previous element based on position.
     *
     * @param Collection        $elements
     * @param SortableInterface $current
     * @param bool              $next Whether to look for the next or the previous
     *
     * @return mixed
     */
    private function findSibling(Collection $elements, SortableInterface $current, $next = false)
    {
        if ($next) {
            $sibling = $elements->filter(function (SortableInterface $s) use ($current) {
                return $s->getPosition() > $current->getPosition();
            })->first();
        } else {
            $sibling = $elements->filter(function (SortableInterface $s) use ($current) {
                return $s->getPosition() < $current->getPosition();
            })->last();
        }

        return $sibling ?: null;
    }

    /**
     * @inheritdoc
     */
    public function loadSubjectContent(ContentSubjectInterface $subject)
    {
        return $this->contentRepository->findBySubject($subject);
    }
}
