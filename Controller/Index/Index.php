<?php

namespace Kozar\UpdateCard\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

/**
 * Class Index
 * @package Kozar\UpdateCard\Controller\Index
 * Changing product attributes
 */

class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFatory;
    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote
     */
    protected $quoteResource;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $jsonResultFactory;
    /**
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
     */
    protected $configurable;
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Index constructor.
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFatory
     * @param \Magento\Quote\Model\ResourceModel\Quote $quoteResource
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurable
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFatory,
        \Magento\Quote\Model\ResourceModel\Quote $quoteResource,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurable,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Checkout\Model\Session $checkoutSession,
        Context $context
    ) {
        $this->resultPageFatory = $resultPageFatory;
        $this->quoteResource = $quoteResource;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->configurable = $configurable;
        $this->productRepository = $productRepository;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    public function getNewSuperAttributes($childId, $qty):array
    {
        $parentId = (int)$this->configurable->getParentIdsByChild($childId)[0];
        $parent = $this->productRepository->getById($parentId);
        $child = $this->productRepository->getById($childId);

        $params = [];
        $params['product'] = $parent->getId();
        $params['qty'] = $qty;
        $options = [];

        $productAttributeOptions = $parent->getTypeInstance()->getConfigurableAttributesAsArray($parent);

        foreach ($productAttributeOptions as $option) {
            $options[$option['attribute_id']] = $child->getData($option['attribute_code']);
        }
        $params['super_attribute'] = $options;

        return $params;
    }

    protected function changeSku($sku, $selectedSize, $attribute): string
    {
        if ($attribute == 'color') {
            $index = 2;
        } elseif ($attribute == 'size') {
            $index = 1;
        }
        $arr = explode('-', $sku);
        $arr[$index] = $selectedSize;
        return implode('-', $arr);
    }
    public function execute()
    {
        $params = $this->_request->getParams();
        $sku = $params['sku'];
        $qty = $params['qty'];

        if (!empty($selectedSize = $params['selectedSize'])) {
            $newSku = $this->changeSku($sku, $selectedSize, 'size');
        } elseif (!empty($selectedSize = $params['selectedColor'])) {
            $newSku = $this->changeSku($sku, $selectedSize, 'color');
        }

        $productNew = $this->productRepository->get($newSku);
        $params = $this->getNewSuperAttributes($productNew->getId(), $qty);
        $quote = $this->checkoutSession->getQuote();
        $allItems = $quote->getAllVisibleItems();

        foreach ($allItems as $item) {
            if ($item->getData('sku') == $sku) {
                $itemId = $item->getData('item_id');
                $quote->updateItem($itemId, new \Magento\Framework\DataObject($params));
                $this->quoteResource->save($quote);
            }
        }

        $data = ['success' => true, 'message' => 'Cart updating successful'];
        $result = $this->jsonResultFactory->create();
        $result->setData($data);
        return $result;
    }
}
