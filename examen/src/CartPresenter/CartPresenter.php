<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter\Cart;

use Cart;
use CartRule;
use Configuration;
use Context;
use Hook;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Presenter\PresenterInterface;
use PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\ProductPresentationSettings;
use Product;
use Symfony\Component\Translation\TranslatorInterface;
use TaxConfiguration;
use Tools;

class CartPresenter implements PresenterInterface
{
    /**
     * @var PriceFormatter
     */
    private $priceFormatter;

    /**
     * @var \Link
     */
    private $link;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ImageRetriever
     */
    private $imageRetriever;

    /**
     * @var TaxConfiguration
     */
    private $taxConfiguration;

    public function __construct()
    {
        $context = Context::getContext();
        $this->priceFormatter = new PriceFormatter();
        $this->link = $context->link;
        $this->translator = $context->getTranslator();
        $this->imageRetriever = new ImageRetriever($this->link);
        $this->taxConfiguration = new TaxConfiguration();
    }

    /**
     * @return bool
     */
    private function includeTaxes()
    {
        return $this->taxConfiguration->includeTaxes();
    }

    /**
     * @param array $rawProduct
     *
     * @return \PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductLazyArray|\PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingLazyArray
     */
    private function presentProduct(array $rawProduct)
    {
        $settings = new ProductPresentationSettings();

        $settings->catalog_mode = Configuration::isCatalogMode();
        $settings->catalog_mode_with_prices = (int) Configuration::get('PS_CATALOG_MODE_WITH_PRICES');
        $settings->include_taxes = $this->includeTaxes();
        $settings->allow_add_variant_to_cart_from_listing = (int) Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY');
        $settings->stock_management_enabled = Configuration::get('PS_STOCK_MANAGEMENT');
        $settings->showPrices = Configuration::showPrices();

        if (isset($rawProduct['attributes']) && is_string($rawProduct['attributes'])) {
            $rawProduct['attributes'] = $this->getAttributesArrayFromString($rawProduct['attributes']);
        }
        $rawProduct['remove_from_cart_url'] = $this->link->getRemoveFromCartURL(
            $rawProduct['id_product'],
            $rawProduct['id_product_attribute']
        );

        $rawProduct['up_quantity_url'] = $this->link->getUpQuantityCartURL(
            $rawProduct['id_product'],
            $rawProduct['id_product_attribute']
        );

        $rawProduct['down_quantity_url'] = $this->link->getDownQuantityCartURL(
            $rawProduct['id_product'],
            $rawProduct['id_product_attribute']
        );

        $rawProduct['update_quantity_url'] = $this->link->getUpdateQuantityCartURL(
            $rawProduct['id_product'],
            $rawProduct['id_product_attribute']
        );

        $resetFields = array(
            'ecotax_rate',
            'specific_prices',
            'customizable',
            'online_only',
            'reduction',
            'reduction_without_tax',
            'new',
            'condition',
            'pack',
        );
        foreach ($resetFields as $field) {
            if (!array_key_exists($field, $rawProduct)) {
                $rawProduct[$field] = '';
            }
        }

        if ($this->includeTaxes()) {
            $rawProduct['price_amount'] = $rawProduct['price_wt'];
            $rawProduct['price'] = $this->priceFormatter->format($rawProduct['price_wt']);
        } else {
            $rawProduct['price_amount'] = $rawProduct['price'];
            $rawProduct['price'] = $rawProduct['price_tax_exc'] = $this->priceFormatter->format($rawProduct['price']);
        }

        if ($rawProduct['price_amount'] && $rawProduct['unit_price_ratio'] > 0) {
            $rawProduct['unit_price'] = $rawProduct['price_amount'] / $rawProduct['unit_price_ratio'];
        }

        $rawProduct['total'] = $this->priceFormatter->format(
            $this->includeTaxes() ?
            $rawProduct['total_wt'] :
            $rawProduct['total']
        );

        $rawProduct['quantity_wanted'] = $rawProduct['cart_quantity'];

        $presenter = new ProductListingPresenter(
            $this->imageRetriever,
            $this->link,
            $this->priceFormatter,
            new ProductColorsRetriever(),
            $this->translator
        );

        return $presenter->present(
            $settings,
            $rawProduct,
            Context::getContext()->language
        );
    }

}
