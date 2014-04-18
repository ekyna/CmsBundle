<?php

namespace Ekyna\Bundle\CmsBundle\Command;

use Ekyna\Bundle\CmsBundle\Command\Route\RouteDefinition;
use Ekyna\Bundle\CmsBundle\Entity\Page;
use Ekyna\Bundle\CmsBundle\Entity\Seo;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Route;

/**
 * GeneratePagesCommand
 *
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class GeneratePagesCommand extends ContainerAwareCommand
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Ekyna\Bundle\CmsBundle\Entity\PageRepository
     */
    protected $repository;

    /**
     * @var \Symfony\Component\Routing\RouteCollection
     */
    protected $routes;

    /**
     * @var string
     */
    protected $homeRouteName;

    /**
     * @var \Ekyna\Bundle\CmsBundle\Command\Route\RouteDefinition
     */
    protected $homeDefinition;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('ekyna:cms:generate-pages')
            ->setDescription('Generates CMS pages.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->routes = $this->getContainer()->get('router')->getRouteCollection();
        $this->homeRouteName = $this->getContainer()->getParameter('ekyna_cms.home_route_name');

        $this->gatherRoutesDefinitions();

        $output->writeln('Generating pages based and routing configuration :');

        $this->em = $this->getContainer()->get('ekyna_cms.page.manager');
        $this->repository = $this->getContainer()->get('ekyna_cms.page.repository');

        $this->createPage($this->homeDefinition, $output);

        $output->writeln('done.');
    }

    /**
     * Creates a tree of RouteDefinition
     * 
     * @throws \RuntimeException
     */
    private function gatherRoutesDefinitions()
    {
        $route = $this->findRouteByName($this->homeRouteName);
        $this->homeDefinition = new RouteDefinition($this->homeRouteName, $route);

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
     * @param string                           $name
     * @param \Symfony\Component\Routing\Route $route
     * 
     * @return RouteDefinition
     */
    private function createRouteDefinition($routeName, Route $route)
    {
        if (null === $definition = $this->findRouteDefinitionByRouteName($routeName)) {
            $definition = new RouteDefinition($routeName, $route);
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
     * @return \Ekyna\Module\Cms\Entity\Page|NULL
     */
    private function findPageByRouteName($routeName)
    {
        return $this->repository->findOneBy(array('route' => $routeName));
    }

    /**
     * Creates a Page from given Route
     * 
     * @param \Ekyna\Bundle\CmsBundle\Command\Route\RouteDefinition $definition
     * @param \Symfony\Component\Console\Output\OutputInterface     $output
     * 
     * @throws \InvalidArgumentException
     */
    private function createPage(RouteDefinition $definition, OutputInterface $output, Page $parentPage = null)
    {
        if (null !== $page = $this->findPageByRouteName($definition->getRouteName())) {
            $output->writeln(sprintf('- "<info>%s</info>" page allready exists.', $page->getName()));
        } else {
            $page = $this->repository->createNew();

            if (null !== $parentPage && $parentPage->getRoute() !== $this->homeRouteName) {
                $title = sprintf('%s - %s', $parentPage->getSeo()->getTitle(), $definition->getPageName());
            }else{
                $title = $definition->getPageName();
            }

            $seo = new Seo();
            $seo
                ->setTitle($title)
                ->setDescription('') // empty to force edition in backend
            ;

            $page
                ->setName($definition->getPageName())
                ->setSeo($seo)
                ->setRoute($definition->getRouteName())
                ->setPath($definition->getPath())
                ->setHtml(sprintf('<p>Page en cours de rédaction.</p>', $definition->getPageName()))
                ->setStatic(true)
                ->setLocked($definition->getLocked())
                ->setMenu($definition->getMenu())
                ->setFooter($definition->getFooter())
                ->setParent($parentPage)
            ;

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
