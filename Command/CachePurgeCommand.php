<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Command;

use Ekyna\Bundle\CmsBundle\Service\Helper\CacheHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CachePurgeCommand
 * @package Ekyna\Bundle\CmsBundle\Command
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class CachePurgeCommand extends Command
{
    protected static $defaultName = 'ekyna:cms:cache:purge';

    public function __construct(
        private readonly CacheHelper $cacheHelper
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->cacheHelper->purgeRoutesCache();

        return Command::SUCCESS;
    }
}
