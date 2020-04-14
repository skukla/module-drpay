<?php
/**
 *
 * @category Digitalriver
 * @package  Digitalriver_DrPay
 */
namespace Digitalriver\DrPay\Model\Total\Quote;

use Magento\Framework\App\Area;

class DrTax extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->setCode('dr_tax');
        $this->_checkoutSession = $checkoutSession;
    }
    
    /**
     * Collect totals process.
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        $address = $shippingAssignment->getShipping()->getAddress();
        $billingaddress = $quote->getBillingAddress();
        $items = $shippingAssignment->getItems();
        if (!count($items)) {
            return $this;
        }

        $drtax = $this->_checkoutSession->getDrTax();
        $magentoTax = $total->getTaxAmount();
        $quote->setDrTax($drtax);
        $total->setDrTax($drtax);
        $total->setTaxAmount($drtax);
        //$magentoTax = $this->_checkoutSession->getMagentoAppliedTax();
        $baseGrandTotal = ($total->getBaseGrandTotal())?$total->getBaseGrandTotal():0;
        $grandTotal = ($total->getGrandTotal())?$total->getGrandTotal():0;
        if ($baseGrandTotal > 0 && $grandTotal > 0) {
            $total->setBaseGrandTotal($total->getBaseGrandTotal() - $magentoTax + $drtax);
            $total->setGrandTotal($total->getGrandTotal() - $magentoTax + $drtax);
        }
        return $this;
    }
    /**
     * Fetch (Retrieve data as array)
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     * @internal param \Magento\Quote\Model\Quote\Address $address
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $result = null;
        $amount = $quote->getDrTax();
        if ($amount == 0) {
            $billingaddress = $quote->getBillingAddress();
            $amount = $billingaddress->getTaxAmount();
        }
        $result = [
            'code' => $this->getCode(),
            'title' => __('Tax'),
            'value' => $amount
        ];
        
        return $result;
    }
}
