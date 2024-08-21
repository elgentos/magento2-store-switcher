<?php

/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */

declare(strict_types=1);

namespace Elgentos\StoreSwitcher\Model\Config\Source;

use Elgentos\StoreSwitcher\Model\Configuration;
use Magento\Framework\Data\OptionSourceInterface;

class RenderTypes implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return array_map(
            function ($key) {
                $label = (string) Configuration::RENDER_TYPES[$key];

                return [
                    'value' => $key,
                    'label' => __($label),
                ];
            },
            array_keys(Configuration::RENDER_TYPES)
        );
    }
}
