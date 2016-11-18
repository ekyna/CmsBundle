<?php

namespace Ekyna\Bundle\CmsBundle\Validator\Constraints;

use Ekyna\Bundle\CmsBundle\Editor\Plugin\PluginRegistry;
use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class BlockValidator
 * @package Ekyna\Bundle\CmsBundle\Validator\Constraints
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class BlockValidator extends ConstraintValidator
{
    /**
     * @var PluginRegistry
     */
    private $pluginRegistry;


    /**
     * Constructor.
     *
     * @param PluginRegistry $pluginRegistry
     */
    public function __construct(PluginRegistry $pluginRegistry)
    {
        $this->pluginRegistry = $pluginRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($block, Constraint $constraint)
    {
        if (!$constraint instanceof Block) {
            throw new UnexpectedTypeException($constraint, Block::class);
        }
        if (!$block instanceof BlockInterface) {
            throw new UnexpectedTypeException($block, BlockInterface::class);
        }

        /**
         * @var BlockInterface $block
         * @var Block          $constraint
         */
        $row = $block->getRow();
        $name = $block->getName();

        // Checks that Content or Name is set, but not both.
        if ((null === $row && 0 === strlen($name)) || (null !== $row && 0 < strlen($name))) {
            $this->context->addViolation($constraint->rowOrNameButNotBoth);
        }

        if (2 > $block->getSize()) { // TODO min size parameter
            $this->context->addViolation($constraint->tooSmallBlock);
        }

        // Plugin validation
        $plugin = $this->pluginRegistry->getBlockPlugin($block->getType());
        $plugin->validate($block, $this->context);
    }
}
