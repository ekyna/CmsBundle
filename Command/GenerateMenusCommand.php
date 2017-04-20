<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Command;

use Ekyna\Bundle\CmsBundle\Install\Generator\MenuGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Class GenerateMenusCommand
 * @package Ekyna\Bundle\CmsBundle\Command
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class GenerateMenusCommand extends Command
{
    protected static $defaultName = 'ekyna:cms:generate:menu';

    private MenuGenerator $menuGenerator;


    /**
     * Constructor.
     *
     * @param MenuGenerator $menuGenerator
     */
    public function __construct(MenuGenerator $menuGenerator)
    {
        parent::__construct();

        $this->menuGenerator = $menuGenerator;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->addOption('truncate', null, InputOption::VALUE_NONE, 'Whether to first remove the menus or not.')
            ->setDescription('Generates CMS menus.');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $truncate = (bool)$input->getOption('truncate');

        $output->writeln(sprintf('Loading menus with truncate <info>%s</info>.', $truncate ? 'true' : 'false'));

        /** @var QuestionHelper $helper */
        $helper = $this->getHelperSet()->get('question');
        $question = new ConfirmationQuestion('Do you want to continue ?', false);

        if (!$helper->ask($input, $output, $question)) {
            return 0;
        }

        if ($truncate) {
            $this->menuGenerator->truncate($output);
        }

        $output->writeln('Generating menus based and routing configuration :');

        $this->menuGenerator->generate($output);

        return 0;
    }
}
