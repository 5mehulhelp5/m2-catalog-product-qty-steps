<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\CatalogProductQtySteps\Plugin\Catalog\Block\Product;

use Infrangible\CatalogProductQtySteps\Helper\Data;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class AbstractProduct
{
    /** @var Data */
    protected $helper;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
    }

    public function afterGetMinimalQty(
        \Magento\Catalog\Block\Product\AbstractProduct $subject,
        ?float $result
    ): ?float {
        if ($this->helper->hasQtySteps($subject->getProduct())) {
            $qtySteps = $this->helper->getQtySteps($subject->getProduct());

            $minQty = reset($qtySteps);

            return $result === null || $minQty > $result ? $minQty : $result;
        } else {
            return $result;
        }
    }
}
