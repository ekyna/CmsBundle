<?php

namespace Ekyna\Bundle\CmsBundle\Search\Wide;

/**
 * Interface ProviderInterface
 * @package Ekyna\Bundle\CmsBundle\Search\Wide
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
interface ProviderInterface
{
    /**
     * Returns the results for the given expression.
     *
     * @param string $expression
     * @param int    $limit
     *
     * @return array|Result[]
     */
    public function search(string $expression, int $limit = 10): array;

    /**
     * Returns the name.
     *
     * @return string
     */
    public function getName(): string;
}
