<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Editor\Plugin\Container\CopyPlugin;
use Ekyna\Bundle\CmsBundle\Editor\Plugin\PluginRegistry;
use Ekyna\Bundle\CmsBundle\Editor\Model\ContainerInterface;
use Ekyna\Bundle\CmsBundle\Entity\ContainerRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class ContainerValidator
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class ContainerValidator extends ConstraintValidator
{
    /**
     * @var PluginRegistry
     */
    private $pluginRegistry;

    /**
     * @var ContainerRepository
     */
    private $containerRepository;


    /**
     * Constructor.
     *
     * @param PluginRegistry      $pluginRegistry
     * @param ContainerRepository $containerRepository
     */
    public function __construct(PluginRegistry $pluginRegistry, ContainerRepository $containerRepository)
    {
        $this->pluginRegistry = $pluginRegistry;
        $this->containerRepository = $containerRepository;
    }

    /**
     * @inheritdoc
     */
    public function validate($container, Constraint $constraint)
    {
        if (!$constraint instanceof Container) {
            throw new UnexpectedTypeException($constraint, Container::class);
        }
        if (!$container instanceof ContainerInterface) {
            throw new UnexpectedTypeException($container, ContainerInterface::class);
        }

        /**
         * @var ContainerInterface $container
         * @var Container          $constraint
         */
        $content = $container->getContent();
        $name = $container->getName();

        // Checks that Content or Name is set, but not both.
        if ((null === $content && 0 === strlen($name)) || (null !== $content && 0 < strlen($name))) {
            $this
                ->context
                ->buildViolation($constraint->contentOrNameButNotBoth)
                ->atPath($name)
                ->addViolation();
        }

        // Plugin validation
        $plugin = $this->pluginRegistry->getContainerPlugin($container->getType());
        $plugin->validate($container, $this->context);

        // Skip if copy plugin
        if ($plugin instanceof CopyPlugin) {
            // Title must be empty
            if (!empty($container->getTitle())) {
                $this
                    ->context
                    ->buildViolation($constraint->titleMustBeEmpty)
                    ->atPath('title')
                    ->addViolation();
            }

            return;
        }

        // Title must be filled if this container as been copied
        if (empty($container->getTitle()) && 0 < $container->getId()) {
            if (0 < $count = $this->containerRepository->getCopyCount($container)) {
                $this
                    ->context
                    ->buildViolation($constraint->titleMustBeFilled)
                    ->atPath('title')
                    ->addViolation();
            }
        }

        // TODO Abort if !$plugin->needsRows()

        // Rows validation
        $violationList = $this
            ->context
            ->getValidator()
            ->validate($container->getRows(), [
                new Assert\Count([
                    'min' => 1,
                ]),
                new Assert\Valid(),
            ]);

        /** @var \Symfony\Component\Validator\ConstraintViolationInterface $violation */
        foreach ($violationList as $violation) {
            $this
                ->context
                ->buildViolation($violation->getMessage())
                ->atPath('rows')
                ->addViolation();
        }
    }
}
