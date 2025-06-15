<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductQtySteps\Block\Cart;

use FeWeDev\Base\Json;
use Infrangible\CatalogProductQtySteps\Helper\Data;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Block\Cart\AbstractCart;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Magento\Quote\Model\Quote\Item;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class QtySteps extends AbstractCart
{
    /** @var Data */
    protected $helper;

    /** @var Json */
    protected $json;

    public function __construct(
        Context $context,
        Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        Data $helper,
        Json $json,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $checkoutSession,
            $data
        );

        $this->helper = $helper;
        $this->json = $json;
    }

    public function getItemProduct(Item $item): Product
    {
        return $item->getProduct();
    }

    public function hasItemQtySteps(Item $item): bool
    {
        return $this->helper->hasQtySteps($this->getItemProduct($item));
    }

    public function getItemQtySteps(Item $item): string
    {
        $qtySteps = $this->helper->getQtySteps($this->getItemProduct($item));

        return $this->json->encode($qtySteps);
    }

    public function getQuoteQtySteps(): string
    {
        $qtySteps = [];

        /** @var Item $item */
        foreach ($this->getItems() as $item) {
            if ($this->hasItemQtySteps($item)) {
                $qtySteps[ $item->getId() ] = [
                    'itemId'    => $item->getId(),
                    'productId' => $this->getItemProduct($item)->getId(),
                    'steps'     => $this->helper->getQtySteps($this->getItemProduct($item))
                ];
            }
        }

        return $this->json->encode($qtySteps);
    }
}
