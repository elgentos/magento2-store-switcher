<?php

/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */

declare(strict_types=1);

namespace Elgentos\StoreSwitcher\Model;

use Elgentos\StoreSwitcher\Api\Data\LabelRendererInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Api\Data\StoreInterface;

class LabelRenderer implements LabelRendererInterface
{
    public function __construct(
        protected Configuration $configuration,
        protected LayoutInterface $layout
    ) {
    }

    public function render(StoreInterface $store): string
    {
        /** @var Template $block */
        $block = $this->layout->getBlock(
            sprintf(
            'store-switcher.label.%s',
                $this->configuration->getRenderType($store)
            )
        );

        if (!$block) {
            return '';
        }

        return $block
                ->setData('store', $store)
                ->toHtml();
    }
}
