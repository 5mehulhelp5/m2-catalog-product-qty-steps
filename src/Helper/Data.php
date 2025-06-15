<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\CatalogProductQtySteps\Helper;

use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Attribute;
use Infrangible\Core\Helper\Stores;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\StockRegistryInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Data
{
    /** @var Stores */
    protected $storeHelper;

    /** @var Attribute */
    protected $attributeHelper;

    /** @var Variables */
    protected $variables;

    /** @var StockRegistryInterface */
    protected $stockRegistry;

    public function __construct(
        Stores $storeHelper,
        Attribute $attributeHelper,
        Variables $variables,
        StockRegistryInterface $stockRegistry
    ) {
        $this->storeHelper = $storeHelper;
        $this->attributeHelper = $attributeHelper;
        $this->variables = $variables;
        $this->stockRegistry = $stockRegistry;
    }

    public function hasQtySteps(Product $product): bool
    {
        $qtySteps = $this->getQtySteps($product);

        return $qtySteps && count($qtySteps) > 0;
    }

    public function getQtySteps(Product $product): ?array
    {
        $enable = $this->storeHelper->getStoreConfigFlag('infrangible_catalogproductqtysteps/general/enable');

        if ($enable) {
            $attributeId = $this->storeHelper->getStoreConfig('infrangible_catalogproductqtysteps/general/attribute');

            if ($attributeId) {
                try {
                    $attributeValue = $this->attributeHelper->getProductAttributeValue(
                        $this->variables->intValue($product->getId()),
                        $attributeId,
                        $this->variables->intValue($this->storeHelper->getStoreId())
                    );
                } catch (\Exception $exception) {
                    $attributeValue = null;
                }

                if ($attributeValue) {
                    $qtySteps = explode(
                        ',',
                        $attributeValue
                    );
                } else {
                    $qtySteps = [];
                }
            } else {
                $qtySteps =
                    $this->storeHelper->getExplodedConfigValues('infrangible_catalogproductqtysteps/general/steps');
            }

            $qtySteps = $qtySteps ? array_map(
                'intval',
                $qtySteps
            ) : [];

            $stockItem = $this->stockRegistry->getStockItem(
                $product->getId(),
                $product->getStore()->getWebsiteId()
            );

            $minSaleQty = $stockItem ? $stockItem->getMinSaleQty() : 0;

            if ($minSaleQty > 0) {
                foreach ($qtySteps as $key => $qtyStep) {
                    if ($minSaleQty > $qtyStep) {
                        unset($qtySteps[ $key ]);
                    }
                }
            }

            natsort($qtySteps);

            return array_values($qtySteps);
        } else {
            return null;
        }
    }
}
