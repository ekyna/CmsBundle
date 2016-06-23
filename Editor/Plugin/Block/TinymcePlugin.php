<?php

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Editor\Exception\InvalidOperationException;
use Ekyna\Bundle\CmsBundle\Editor\View\BlockView;
use Ekyna\Bundle\CmsBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class TinymcePlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class TinymcePlugin extends AbstractPlugin
{
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
     * {@inheritdoc}
     */
    public function create(BlockInterface $block, array $data = [])
    {
        parent::create($block, $data);

        $block->setData(array());

        $block->translate($this->localeProvider->getCurrentLocale(), true)->setData([
            'content' => $this->config['default_content'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function update(BlockInterface $block, Request $request)
    {
        if (!$request->isMethod('POST')) {
            throw new InvalidOperationException('Tinymce block plugin only supports POST request.');
        }

        $data = $request->request->get('data');
        if (!array_key_exists('content', $data)) {
            throw new InvalidOperationException('Invalid POST data.');
        }

        $block->translate($this->localeProvider->getCurrentLocale(), true)->setData([
            'content' => $data['content']
        ]);

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(BlockInterface $block)
    {
        // TODO remove content images ?

        parent::remove($block);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(BlockInterface $block, ExecutionContextInterface $context)
    {
        $data = $block->getData();
        if (0 < count($data)) {
            $context->addViolation(self::INVALID_DATA);
        }

        $translationData = $block->translate($this->localeProvider->getCurrentLocale())->getData();
        if (!array_key_exists('content', $translationData) || 0 == strlen($translationData['content'])) {
            $context->addViolation(self::INVALID_DATA);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function render(BlockInterface $block, BlockView $view)
    {
        $translationData = $block->translate($this->localeProvider->getCurrentLocale())->getData();

        if (array_key_exists('content', $translationData)) {
            $view->content =  $translationData['content'];
        } else {
            $view->content = $this->config['default_content'];
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return 'Html';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ekyna_block_tinymce';
    }

    /**
     * {@inheritdoc}
     */
    public function getJavascriptFilePath()
    {
        return 'ekyna-cms/editor/plugin/block/tinymce';
    }
}
