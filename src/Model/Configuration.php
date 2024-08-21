<?php

/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */

declare(strict_types=1);

namespace Elgentos\StoreSwitcher\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;

readonly class Configuration
{
    public const string RENDER_TYPE_CODE = 'code';
    public const string RENDER_TYPE_ICON = 'icon';
    public const string RENDER_TYPE_NAME = 'name';
    public const array RENDER_TYPES = [
        self::RENDER_TYPE_CODE => 'Code',
        self::RENDER_TYPE_ICON => 'Icon',
        self::RENDER_TYPE_NAME => 'Name',
    ];
    public const string RENDER_TYPE_XML_PATH = 'store_switcher/general/render_type';
    public const string ICON_NAME_XML_PATH = 'store_switcher/general/icon_name';

    public function __construct(
        protected ScopeConfigInterface $scopeConfig
    ){
    }

    public function getRenderType(StoreInterface $store = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::RENDER_TYPE_XML_PATH,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getStoreIcon(StoreInterface $store = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::ICON_NAME_XML_PATH,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
