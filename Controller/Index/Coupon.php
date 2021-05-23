<?php

namespace Kozar\UpdateCard\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

/**
 * Class Index
 * @package Kozar\UpdateCard\Controller\Index
 * Changing product attributes
 */

class Coupon extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFatory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $jsonResultFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Index constructor.
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFatory
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFatory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        Context $context
    ) {
        $this->resultPageFatory = $resultPageFatory;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    public function execute()
    {

        $result = $this->jsonResultFactory->create();
        $resultPage = $this->resultPageFatory->create();
        $block = $resultPage->getLayout()->createBlock('Magento\Checkout\Block\Cart\Coupon')
            ->setTemplate('Magento_Checkout::cart/coupon.phtml')
            ->toHtml();

        $result->setData(['output' => $block]);
        return $result;
    }
}
