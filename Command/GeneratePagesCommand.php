<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Command;

use Ekyna\Bundle\CmsBundle\Install\Generator\PageGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

use function sprintf;

/**
 * Class GeneratePagesCommand
 * @package Ekyna\Bundle\CmsBundle\Command
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class GeneratePagesCommand extends Command
{
    protected static $defaultName = 'ekyna:cms:generate:page';

    private PageGenerator $pageGenerator;


    /**
     * Constructor.
     *
     * @param PageGenerator $pageGenerator
     */
    public function __construct(PageGenerator $pageGenerator)
    {
        parent::__construct();

        $this->pageGenerator = $pageGenerator;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('ekyna:cms:generate:page')
            ->addOption('truncate', null, InputOption::VALUE_NONE, 'Whether to first remove the pages or not.')
            ->setDescription('Generates CMS pages.');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $truncate = (bool)$input->getOption('truncate');

        $output->writeln(sprintf('Loading pages with truncate <info>%s</info>.', $truncate ? 'true' : 'false'));

        /** @var QuestionHelper $helper */
        $helper = $this->getHelperSet()->get('question');
        $question = new ConfirmationQuestion('Do you want to continue ?', false);

        if (!$helper->ask($input, $output, $question)) {
            return 0;
        }

        if ($truncate) {
            $this->pageGenerator->truncate($output);
        }

        $output->writeln('Generating pages based and routing configuration :');

        $this->pageGenerator->generate($output);

        return 0;
    }
}
