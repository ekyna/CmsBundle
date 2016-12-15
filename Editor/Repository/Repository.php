<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Repository;

use Ekyna\Bundle\CmsBundle\Entity as R;
use Ekyna\Bundle\CmsBundle\Model\ContentSubjectInterface;

/**
 * Class Repository
 * @package Ekyna\Bundle\CmsBundle\Editor\Repository
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class Repository implements RepositoryInterface
{
    /**
     * @var R\ContentRepository
     */
    private $contentRepository;

    /**
     * @var R\ContainerRepository
     */
    private $containerRepository;

    /**
     * @var R\RowRepository
     */
    private $rowRepository;

    /**
     * @var R\BlockRepository
     */
    private $blockRepository;


    /**
     * Constructor.
     *
     * @param R\ContentRepository $contentRepository
     * @param R\ContainerRepository $containerRepository
     * @param R\RowRepository $rowRepository
     * @param R\BlockRepository $blockRepository
     */
    public function __construct(
        R\ContentRepository $contentRepository,
        R\ContainerRepository $containerRepository,
        R\RowRepository $rowRepository,
        R\BlockRepository $blockRepository
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
     * Loads and returns the subject's content.
     *
     * @param ContentSubjectInterface $subject
     *
     * @return \Ekyna\Bundle\CmsBundle\Model\ContentInterface|null
     */
    public function loadSubjectContent(ContentSubjectInterface $subject)
    {
        return $this->contentRepository->findBySubject($subject);
    }
}
