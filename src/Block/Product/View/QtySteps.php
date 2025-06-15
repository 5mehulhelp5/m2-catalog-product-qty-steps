<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductQtySteps\Block\Product\View;

use FeWeDev\Base\Json;
use Infrangible\CatalogProductQtySteps\Helper\Data;
use Infrangible\Core\Helper\Registry;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Template;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class QtySteps extends Template
{
    /** @var Registry */
    protected $registryHelper;

    /** @var Data */
    protected $helper;

    /** @var Json */
    protected $json;

    /** @var Product */
    private $product;

    public function __construct(
        Template\Context $context,
        Registry $registryHelper,
        Data $helper,
        Json $json,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $data
        );

        $this->registryHelper = $registryHelper;
        $this->helper = $helper;
        $this->json = $json;
    }

    public function getProduct(): Product
    {
        if (! $this->product) {
            if ($this->registryHelper->registry('current_product')) {
                $this->product = $this->registryHelper->registry('current_product');
            } else {
                throw new \LogicException('Product is not defined');
            }
        }

        return $this->product;
    }

    public function hasQtySteps(): bool
    {
        return $this->helper->hasQtySteps($this->getProduct());
    }

    public function getQtySteps(): string
    {
        $qtySteps = $this->helper->getQtySteps($this->getProduct());

        return $this->json->encode($qtySteps);
    }
}
