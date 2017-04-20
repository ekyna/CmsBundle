<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Install\Generator;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Ekyna\Bundle\CmsBundle\Factory\MenuFactoryInterface;
use Ekyna\Bundle\CmsBundle\Manager\MenuManagerInterface;
use Ekyna\Bundle\CmsBundle\Repository\MenuRepositoryInterface;
use Exception;
use Symfony\Component\Console\Output\OutputInterface;

use function sprintf;

/**
 * Class MenuGenerator
 * @package Ekyna\Bundle\CmsBundle\Install\Generator
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class MenuGenerator
{
    private MenuManagerInterface    $menuManager;
    private MenuRepositoryInterface $menuRepository;
    private MenuFactoryInterface    $menuFactory;
    private EntityManagerInterface  $entityManager;
    private array                   $config;
    private array                   $locales;

    public function __construct(
        MenuManagerInterface $manager,
        MenuRepositoryInterface $repository,
        MenuFactoryInterface $factory,
        EntityManagerInterface  $entityManager,
        array $config,
        array $locales
    ) {
        $this->menuManager = $manager;
        $this->menuRepository = $repository;
        $this->menuFactory = $factory;
        $this->entityManager = $entityManager;
        $this->config = $config;
        $this->locales = $locales;
    }

    /**
     * Generates the menus based on configuration.
     *
     * @param OutputInterface $output
     */
    public function generate(OutputInterface $output)
    {
        foreach ($this->config['roots'] as $name => $config) {
            $output->write(sprintf(
                '- <comment>%s</comment> %s ',
                $name,
                str_pad('.', 44 - mb_strlen($name), '.', STR_PAD_LEFT)
            ));

            if (null !== $this->menuRepository->findOneByName($name)) {
                $output->writeln('already exists.');

                continue;
            }

            $menu = $this->menuFactory->create();
            $menu
                ->setName($name)
                ->setDescription($config['description'])
                ->setLocked(true);

            foreach ($this->locales as $locale) {
                $menuTranslation = $menu->translate($locale, true);
                $menuTranslation
                    ->setTitle($config['title']);
            }

            $this->menuManager->save($menu);

            $output->writeln('created.');
        }

        $this->menuManager->clear();
    }

    /**
     * Removes all menus.
     *
     * @param OutputInterface $output
     *
     * @noinspection PhpDocMissingThrowsInspection
     */
    public function truncate(OutputInterface $output): void
    {
        $output->writeln('Removing menus ...');

        $count = 0;
        $menus = $this->menuRepository->findAll();
        foreach ($menus as $menu) {
            $this->menuManager->remove($menu);
            $count++;
        }
        $this->menuManager->flush();
        $this->menuManager->clear();

        $metadata = $this->entityManager->getClassMetadata($this->menuRepository->getClassName());
        $connection = $this->entityManager->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();

        try {
            $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
            $q = $dbPlatform->getTruncateTableSQL($metadata->getTableName());
            $connection->executeQuery($q);
            $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (Exception $e) {
            $output->writeln('<error>Failed to remove menus.</error>');
            $connection->rollBack();
        }

        $output->writeln(sprintf('<info>%s</info> menus removed.', $count));
    }
}
