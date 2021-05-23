<?php

namespace Kozar\UpdateCard\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

/**
 * Class Index
 * @package Kozar\UpdateCard\Controller\Index
 * Changing product attributes
 */

class Delete extends Action
{
    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote
     */
    protected $quoteResource;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $jsonResultFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;


    public function __construct(
        \Magento\Quote\Model\ResourceModel\Quote $quoteResource,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        Context $context
    ) {
        $this->quoteResource = $quoteResource;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $data = json_decode($params['data']);
        $value = get_object_vars($data)['data'];
        $id = (int)get_object_vars($value)['id'];

        $items = $this->checkoutSession->getQuote()->getAllVisibleItems();

        foreach ($items as $item) {
            if ($item->getData('item_id') == $id) {
                $this->checkoutSession->getQuote()->removeItem($id);
                $this->quoteResource->save($this->checkoutSession->getQuote());
            }
        }

        $data = ['success' => true, 'message' => 'Cart updating successful'];
        $result = $this->jsonResultFactory->create();
        $result->setData($data);
        return $result;
    }
}
