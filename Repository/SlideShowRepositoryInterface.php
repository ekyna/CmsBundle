<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Repository;

use Ekyna\Bundle\CmsBundle\Model\SlideShowInterface;
use Ekyna\Component\Resource\Repository\ResourceRepositoryInterface;

/**
 * Interface SlideShowRepositoryInterface
 * @package Ekyna\Bundle\CmsBundle\Repository
 * @author  Étienne Dauvergne <contact@ekyna.com>
 *
 * @implements ResourceRepositoryInterface<SlideShowInterface>
 */
interface SlideShowRepositoryInterface extends ResourceRepositoryInterface
{
    /**
     * Finds one slideshow by its tag.
     */
    public function findOnByTag(string $tag): ?SlideShowInterface;
}
