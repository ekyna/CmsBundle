<?php

namespace Ekyna\Bundle\CmsBundle\Command;

use Ekyna\Bundle\CmsBundle\Command\Route\RouteDefinition;
use Ekyna\Bundle\CmsBundle\Entity\Seo;
use Ekyna\Bundle\CmsBundle\Model\PageInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\Route;

/**
 * Class GeneratePagesCommand
 * @package Ekyna\Bundle\CmsBundle\Command
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class GeneratePagesCommand extends ContainerAwareCommand
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Ekyna\Bundle\CmsBundle\Entity\PageRepository
     */
    private $repository;

    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    private $routes;

    /**
     * @var \Symfony\Component\OptionsResolver\OptionsResolverInterface
     */
    private $optionResolver;

    /**
     * @var string
     */
    private $homeRouteName;

    /**
     * @var \Ekyna\Bundle\CmsBundle\Command\Route\RouteDefinition
     */
    private $homeDefinition;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ekyna:cms:generate-pages')
            ->addOption('truncate', null, InputOption::VALUE_NONE, 'Whether to first remove the pages or not.')
            ->setDescription('Generates CMS pages.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $truncate = $input->getOption('truncate');

        $output->writeln(sprintf('Loading pages with truncate <info>%s</info>.', $truncate ? 'true' : 'false'));

        /** @var \Symfony\Component\Console\Helper\DialogHelper $dialog */
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog->askConfirmation(
            $output,
            '<question>Do you want to continue ? (y/n)</question>',
            false
        )
        ) {
            return;
        }

        $this->routes = $this->getContainer()->get('router')->getRouteCollection();
        $this->homeRouteName = $this->getContainer()->getParameter('ekyna_cms.home_route_name');

        $this->em = $this->getContainer()->get('ekyna_cms.page.manager');
        $this->repository = $this->getContainer()->get('ekyna_cms.page.repository');

        if ($truncate) {
            $this->truncate($output);
        }

        $output->writeln('Generating pages based and routing configuration :');

        $this->configureOptionResolver();
        $this->gatherRoutesDefinitions();
        $this->createPage($this->homeDefinition, $output);

        $output->writeln('Done.');
    }

    /**
     * Removes all the pages.
     *
     * @param OutputInterface $output
     */
    private function truncate(OutputInterface $output)
    {
        $output->writeln('Removing pages ...');

        $count = 0;
        $pages = $this->repository->findAll();
        foreach ($pages as $page) {
            $this->em->remove($page);
            $count++;
        }
        $this->em->flush();
        $this->em->clear();

        $class = $this->getContainer()->getParameter('ekyna_cms.page.class');
        $cmd = $this->em->getClassMetadata($class);
        $connection = $this->em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->beginTransaction();
        try {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
            $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
            $connection->executeUpdate($q);
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
            $connection->commit();
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Failed to truncate table for class %s.</error>', $class));
            $connection->rollback();
        }

        $output->writeln(sprintf('<info>%s</info> pages removed.', $count));
    }

    private function configureOptionResolver()
    {
        $this->optionResolver = new OptionsResolver();

        $this->optionResolver
            ->setDefaults(array(
                'name' => null,
                'path' => null,
                'parent' => null,
                'locked' => true,
                'menu' => false,
                'footer' => false,
                'advanced' => false,
                'position' => 0,
            ))
            ->setAllowedTypes(array(
                'name' => 'string',
                'path' => 'string',
                'parent' => array('string', 'null'),
                'locked' => 'bool',
                'menu' => 'bool',
                'footer' => 'bool',
                'advanced' => 'bool',
                'position' => 'int',
            ))
            ->setRequired(array('name', 'path'))
            ->setNormalizers(array(
                'locked' => function (Options $options, $value) {
                    // Lock pages with parameters in path
                    if (preg_match('#\{.*\}#', $options['path'])) {
                        return true;
                    }
                    return $value;
                },
            ))
        ;
    }

    /**
     * Resolve route options.
     *
     * @param Route $route
     * @param string $routeName
     * @return array
     * @throws \InvalidArgumentException
     */
    private function resolveRouteOptions(Route $route, $routeName)
    {
        if (null === $cmsOptions = $route->getDefault('_cms')) {
            throw new \InvalidArgumentException(sprintf('Route "%s" does not have "_cms" defaults attributes.', $routeName));
        }
        return $this->optionResolver->resolve(array_merge($cmsOptions, array('path' => $route->getPath())));
    }

    /**
     * Creates a tree of RouteDefinition
     *
     * @throws \RuntimeException
     */
    private function gatherRoutesDefinitions()
    {
        $route = $this->findRouteByName($this->homeRouteName);
        $this->homeDefinition = new RouteDefinition($this->homeRouteName, $this->resolveRouteOptions($route, $this->homeRouteName));

        /** @var Route $route */
        foreach ($this->routes as $name => $route) {
            if ($this->homeRouteName !== $name && null !== $cms = $route->getDefault('_cms')) {
                $this->createRouteDefinition($name, $route);
            }
        }

        $this->homeDefinition->sortChildren();
    }

    /**
     * Creates a route definition
     *
     * @param string $routeName
     * @param \Symfony\Component\Routing\Route $route
     *
     * @return RouteDefinition
     */
    private function createRouteDefinition($routeName, Route $route)
    {
        if (null === $definition = $this->findRouteDefinitionByRouteName($routeName)) {
            $definition = new RouteDefinition($routeName, $this->resolveRouteOptions($route, $routeName));
            if (null === $parentRouteName = $definition->getParentRouteName()) {
                // If parent route name is null => home page child
                $definition->setParentRouteName($this->homeRouteName);
                $this->homeDefinition->appendChild($definition);
            } else {
                // Creates parent route definition if needed 
                $parentRoute = $this->findRouteByName($parentRouteName);
                $parentDefinition = $this->createRouteDefinition($parentRouteName, $parentRoute);
                $parentDefinition->appendChild($definition);
            }
        }
        return $definition;
    }

    /**
     * Finds a route by name
     *
     * @param string $name
     *
     * @throws \RuntimeException
     *
     * @return \Symfony\Component\Routing\Route|NULL
     */
    private function findRouteByName($name)
    {
        if (null === $route = $this->routes->get($name)) {
            throw new \RuntimeException(sprintf('"%s" route can\'t be found.', $name));
        }
        return $route;
    }

    /**
     * Finds a RouteDefinition by route name
     *
     * @param string $routeName
     *
     * @return \Ekyna\Bundle\CmsBundle\Command\Route\RouteDefinition
     */
    private function findRouteDefinitionByRouteName($routeName)
    {
        if ($routeName === $this->homeRouteName) {
            return $this->homeDefinition;
        }
        return $this->homeDefinition->findChildByRouteName($routeName);
    }

    /**
     * Finds a page by route
     *
     * @param string $routeName
     *
     * @return PageInterface|NULL
     */
    private function findPageByRouteName($routeName)
    {
        return $this->repository->findOneBy(array('route' => $routeName));
    }

    /**
     * Creates a Page from given Route
     *
     * @param RouteDefinition $definition
     * @param OutputInterface $output
     * @param PageInterface $parentPage
     *
     * @throws \InvalidArgumentException
     */
    private function createPage(RouteDefinition $definition, OutputInterface $output, PageInterface $parentPage = null)
    {
        if (null !== $page = $this->findPageByRouteName($definition->getRouteName())) {
            $output->writeln(sprintf('- "<info>%s</info>" page allready exists.', $page->getName()));
        } else {
            $page = $this->repository->createNew();

            if (null !== $parentPage && $parentPage->getRoute() !== $this->homeRouteName) {
                $title = sprintf('%s - %s', $parentPage->getSeo()->getTitle(), $definition->getPageName());
            } else {
                $title = $definition->getPageName();
            }

            // Seo
            $seo = new Seo();
            $seo
                ->setTitle($title)
                ->setDescription('') // empty to force edition in backend
            ;

            $page
                ->setName($definition->getPageName())
                ->setTitle($definition->getPageName())
                ->setRoute($definition->getRouteName())
                ->setPath($definition->getPath())
                ->setStatic(true)
                ->setLocked($definition->getLocked())
                ->setMenu($definition->getMenu())
                ->setFooter($definition->getFooter())
                ->setAdvanced($definition->getAdvanced())
                ->setParent($parentPage)
                ->setSeo($seo)
                ->setHtml('<p>Page en cours de rédaction.</p>');
            // Page

            $this->em->persist($page);
            $this->em->flush();

            $output->writeln(sprintf('- "<info>%s</info>" page created for "%s" route.', $page->getName(), $page->getRoute()));
        }

        // Creates children pages
        foreach ($definition->getChildren() as $child) {
            $this->createPage($child, $output, $page);
        }
    }
}
