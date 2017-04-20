<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Install;

use Ekyna\Bundle\CmsBundle\Install\Generator\MenuGenerator;
use Ekyna\Bundle\CmsBundle\Install\Generator\PageGenerator;
use Ekyna\Bundle\CmsBundle\Install\Generator\SlideShowGenerator;
use Ekyna\Bundle\InstallBundle\Install\AbstractInstaller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CmsInstaller
 * @package Ekyna\Bundle\CmsBundle\Install
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class CmsInstaller extends AbstractInstaller
{
    private PageGenerator      $pageGenerator;
    private MenuGenerator      $menuGenerator;
    private SlideShowGenerator $slideShowGenerator;


    /**
     * Constructor.
     *
     * @param PageGenerator $pageGenerator
     * @param MenuGenerator $menuGenerator
     * @param SlideShowGenerator $slideShowGenerator
     */
    public function __construct(
        PageGenerator $pageGenerator,
        MenuGenerator $menuGenerator,
        SlideShowGenerator $slideShowGenerator
    ) {
        $this->pageGenerator = $pageGenerator;
        $this->menuGenerator = $menuGenerator;
        $this->slideShowGenerator = $slideShowGenerator;
    }

    /**
     * @inheritDoc
     */
    public function install(Command $command, InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('<info>[CMS] Generating menus:</info>');
        $this->menuGenerator->generate($output);
        $output->writeln('');

        $output->writeln('<info>[CMS] Generating pages based on routing configuration:</info>');
        $this->pageGenerator->generate($output);
        $output->writeln('');

        $output->writeln('<info>[CMS] Generating slide shows:</info>');
        $this->slideShowGenerator->generate($output);
        $output->writeln('');
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'ekyna_cms';
    }
}
