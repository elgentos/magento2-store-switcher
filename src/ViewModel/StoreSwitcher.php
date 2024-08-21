<?php

/**
 * Copyright Elgentos BV. All rights reserved.
 * https://www.elgentos.nl/
 */

declare(strict_types=1);

namespace Elgentos\StoreSwitcher\ViewModel;

use Elgentos\StoreSwitcher\Api\Data\LabelRendererInterface;
use Elgentos\StoreSwitcher\Model\Configuration;
use Hyva\Theme\ViewModel\StoreSwitcher as HyvaStoreSwitcher;
use Magento\Directory\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;

class StoreSwitcher extends HyvaStoreSwitcher
{
    public function __construct(
        protected LabelRendererInterface $labelRenderer,
        protected Configuration $configuration,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($storeManager, $scopeConfig);
    }

    public function getLabelRenderer(): LabelRendererInterface
    {
        return $this->labelRenderer;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRawStores(): array
    {
        if (!$this->rawStores) {
            $websiteStores = $this->getAllWebsiteStores();
            $stores        = [];
            foreach ($websiteStores as $store) {
                $stores[$store->getId()] = $this->setupWebsiteStores($store);
            }
            $this->rawStores = $stores;
        }

        return $this->rawStores;
    }

    /**
     * @return StoreInterface[]
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getStores(?string $exclude = null): array
    {
        if (!$this->stores) {
            $this->stores = array_filter(
                $this->getRawStores(),
                function($store) use ($exclude) {
                    return $store !== null
                        && $store->getCode() !== $exclude;
                }
            );
        }

        return $this->stores;
    }

    /**
     * @return StoreInterface[]
     */
    public function getAllWebsiteStores(): array
    {
        /** @var StoreInterface[] $websiteStores */
        $websiteStores = [];

        /** @var Website[] $websites */
        $websites = $this->storeManager->getWebsites();

        foreach ($websites as $website) {
            $stores = $website->getStores();

            if ($stores !== null) {
                foreach ($stores as $store) {
                    $websiteStores[] = $store;
                }
            }
        }

        return $websiteStores;
    }

    public function getCurrentStore(): ?StoreInterface
    {
        try {
            return $this->storeManager->getStore();
        } catch (NoSuchEntityException) {
            return null;
        }
    }

    public function getStoreIcon(StoreInterface $store): string
    {
        return $this->configuration->getStoreIcon($store);
    }

    /**
     * @throws NoSuchEntityException
     */
    public function setupWebsiteStores(StoreInterface $store): ?StoreInterface
    {
        /** @var Store $store */
        if (!$store->isActive()) {
            return null;
        }

        $localeCode = $this->scopeConfig->getValue(
            Data::XML_PATH_DEFAULT_LOCALE,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        $store->setData('locale_code', $localeCode);

        $params = ['_query' => []];

        if (!$this->isStoreInUrl()) {
            $params['_query']['___store'] = $store->getCode();
        }

        $baseUrl = $store->getUrl('', $params);

        $store->setData('home_url', $baseUrl);

        return $store;
    }
}
