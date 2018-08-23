<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Editor\Adapter\AdapterInterface;
use Ekyna\Bundle\CmsBundle\Editor\Exception\InvalidOperationException;
use Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class TinymcePlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class TinymcePlugin extends AbstractPlugin
{
    const NAME = 'ekyna_block_tinymce';


    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct(array_replace([
            'default_content' => '<p>Default content.</p>',
        ], $config));
    }

    /**
     * @inheritdoc
     */
    public function create(BlockInterface $block, array $data = [])
    {
        parent::create($block, $data);

        //$block->setData([]);

        $block
            ->translate($this->localeProvider->getCurrentLocale(), true)
            ->setData('content', $this->config['default_content']);
    }

    /**
     * @inheritdoc
     */
    public function update(BlockInterface $block, Request $request, array $options = [])
    {
        if (!$request->isMethod('POST')) {
            throw new InvalidOperationException('Tinymce block plugin only supports POST request.');
        }

        $data = $request->request->get('data');
        if (!array_key_exists('content', $data)) {
            throw new InvalidOperationException('Invalid POST data.');
        }

        $block
            ->translate($this->localeProvider->getCurrentLocale(), true)
            ->setData('content', $data['content']);

        return null;
    }

    /**
     * @inheritdoc
     */
    public function remove(BlockInterface $block)
    {
        // TODO remove content images ?

        parent::remove($block);
    }

    /**
     * @inheritdoc
     */
    public function validate(BlockInterface $block, ExecutionContextInterface $context)
    {
        /*$data = $block->getData();
        if (0 < count($data)) {
            $context->addViolation(self::INVALID_DATA);
        }*/

        $translationData = $block->translate($this->localeProvider->getCurrentLocale())->getData();
        if (!array_key_exists('content', $translationData) || 0 == strlen($translationData['content'])) {
            $context->addViolation(self::INVALID_DATA);
        }
    }

    /**
     * @inheritDoc
     */
    public function createWidget(BlockInterface $block, AdapterInterface $adapter, array $options, $position = 0)
    {
        $view = parent::createWidget($block, $adapter, $options, $position);

        $translationData = $block->translate($this->localeProvider->getCurrentLocale())->getData();
        if (array_key_exists('content', $translationData)) {
            $view->content = $translationData['content'];
        } else {
            $view->content = $this->config['default_content'];
        }

        return $view;
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return 'Html';
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * @inheritdoc
     */
    public function getJavascriptFilePath()
    {
        return 'ekyna-cms/editor/plugin/block/tinymce';
    }
}
