<?php
/**
 * @category Digitalriver
 * @package  Digitalriver_DrPay
 */

namespace Digitalriver\DrPay\Controller\Applepay;

use Magento\Framework\Controller\ResultFactory;

class Savedrsource extends \Magento\Framework\App\Action\Action
{

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Digitalriver\DrPay\Helper\Data $helper
    ) {
        $this->helper =  $helper;
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    public function execute()
    {
        $responseContent = [
            'success'        => false
        ];
        if ($this->getRequest()->getParam('source_id')) {
            $source_id = $this->getRequest()->getParam('source_id');
            $paymentResult = $this->helper->applyQuotePayment($source_id);
            $accessToken = $this->_checkoutSession->getDrAccessToken();
            if ($paymentResult) {
                $responseContent = [
                    'success'        => true,
                    'content'        => $paymentResult
                ];
            }
        }
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $response->setData($responseContent);

        return $response;
    }
}
