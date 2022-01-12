<?php

declare(strict_types=1);

namespace Ekyna\Bundle\CmsBundle\Editor\Plugin\Block;

use Ekyna\Bundle\CmsBundle\Editor\Adapter\AdapterInterface;
use Ekyna\Bundle\CmsBundle\Editor\Exception\InvalidOperationException;
use Ekyna\Bundle\CmsBundle\Editor\Model\BlockInterface;
use Ekyna\Bundle\CmsBundle\Editor\View\WidgetView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class TinymcePlugin
 * @package Ekyna\Bundle\CmsBundle\Editor\Plugin\Block
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class TinymcePlugin extends AbstractPlugin
{
    public const NAME = 'ekyna_block_tinymce';


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
     * @inheritDoc
     */
    public function create(BlockInterface $block, array $data = []): void
    {
        parent::create($block, $data);

        $data = array_replace([
            'default_content' => $this->config['default_content'],
        ], $data);

        $block
            ->translate($this->localeProvider->getCurrentLocale())
            ->setData('content', $data['default_content']);
    }

    /**
     * @inheritDoc
     */
    public function update(BlockInterface $block, Request $request, array $options = []): ?Response
    {
        if (!$request->isMethod('POST')) {
            throw new InvalidOperationException('Tinymce block plugin only supports POST request.');
        }

        $data = (array)$request->request->get('data');
        if (!array_key_exists('content', $data)) {
            throw new InvalidOperationException('Invalid POST data.');
        }

        $block
            ->translate($this->localeProvider->getCurrentLocale(), true)
            ->setData('content', $data['content']);

        return null;
    }

    /**
     * @inheritDoc
     */
    public function remove(BlockInterface $block): void
    {
        // TODO remove content images ?

        parent::remove($block);
    }

    /**
     * @inheritDoc
     */
    public function validate(BlockInterface $block, ExecutionContextInterface $context): void
    {
        $translationData = $block->translate($this->localeProvider->getCurrentLocale())->getData();
        if (!array_key_exists('content', $translationData) || empty($translationData['content'])) {
            $context->addViolation(self::INVALID_DATA);
        }
    }

    /**
     * @inheritDoc
     */
    public function createWidget(
        BlockInterface $block,
        AdapterInterface $adapter,
        array $options,
        int $position = 0
    ): WidgetView {
        $view = parent::createWidget($block, $adapter, $options, $position);

        $translationData = $block->translate($this->localeProvider->getCurrentLocale(), true)->getData();
        if (array_key_exists('content', $translationData)) {
            $view->content = $translationData['content'];
        } else {
            $view->content = $this->config['default_content'];
        }

        return $view;
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return 'Html';
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return static::NAME;
    }

    /**
     * @inheritDoc
     */
    public function getJavascriptFilePath(): string
    {
        return 'ekyna-cms/editor/plugin/block/tinymce';
    }
}
