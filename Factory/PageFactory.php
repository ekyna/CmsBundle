<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Factory;

use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Ekyna\Bundle\CmsBundle\Repository\PageRepositoryInterface;
use Ekyna\Component\Resource\Doctrine\ORM\Factory\TranslatableFactory;
use Ekyna\Component\Resource\Exception\LogicException;
use Ekyna\Component\Resource\Exception\RuntimeException;
use Ekyna\Component\Resource\Model\ResourceInterface;

use function sprintf;
use function uniqid;

/**
 * Class PageFactory
 * @package Ekyna\Bundle\CmsBundle\Factory
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PageFactory extends TranslatableFactory implements PageFactoryInterface
{
    private PageRepositoryInterface $pageRepository;


    /**
     * Constructor.
     *
     * @param PageRepositoryInterface $pageRepository
     */
    public function __construct(PageRepositoryInterface $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    /**
     * @inheritDoc
     */
    public function create(): ResourceInterface
    {
        $page = parent::create();

        if (!$page instanceof PageInterface) {
            throw new LogicException('This factory should create instance of ' . PageInterface::class);
        }

        $parent = $page->getParent();
        if ($parent && $parent->isLocked()) {
            throw new RuntimeException('Cannot create child page under a locked parent page.');
        }

        $this->updateRoute($page);

        return $page;
    }

    /**
     * Updates the page's route property.
     *
     * @param PageInterface $page
     */
    protected function updateRoute(PageInterface $page): void
    {
        // Generate random route name.
        if (null !== $page->getRoute()) {
            return;
        }

        // Prevent duplicate
        do {
            $route = sprintf('cms_page_%s', uniqid());
        } while (null !== $this->pageRepository->findOneByRoute($route, false));

        $page->setRoute($route);
    }
}
