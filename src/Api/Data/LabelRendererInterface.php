<?php

/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */

declare(strict_types=1);

namespace Elgentos\StoreSwitcher\Api\Data;

use Magento\Store\Api\Data\StoreInterface;

interface LabelRendererInterface
{
    public function render(StoreInterface $store): string;
}
