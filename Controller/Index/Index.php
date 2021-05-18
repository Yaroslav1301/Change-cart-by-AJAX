<?php

namespace Kozar\UpdateCard\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Checkout\Model\Cart;

class Index extends Action
{
    protected $checkoutSession;
    protected $productRepository;
    /**
     * @var \Magento\Quote\Api\Data\CartItemInterfaceFactory
     */
    protected $cartItemFactory;

    protected $quoteModel;

    protected $quoteItem;

    protected $formKey;
    protected $cart;
    protected $configurable;
    public function __construct(
        \Magento\Quote\Api\Data\CartItemInterfaceFactory $cartItemFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\Quote $quoteModel,
        \Magento\Quote\Model\Quote\Item $quoteItem,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurable,
        FormKey $formKey,
        Cart $cart,
        Context $context
    ) {
        $this->configurable = $configurable;
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->cartItemFactory = $cartItemFactory;
        $this->productRepository = $productRepository;
        $this->checkoutSession = $checkoutSession;
        $this->quoteModel = $quoteModel;
        $this->quoteItem = $quoteItem;
        parent::__construct($context);
    }

    public function getAddConfigurableProduct($childId, $sku, $qty)
    {
        $parentId = (int)$this->configurable->getParentIdsByChild($childId)[0];
        $parent = $this->productRepository->getById($parentId);
        $child = $this->productRepository->getById($childId);
        $cart = $this->cart;

        $params = [];
        $params['product'] = $parent->getId();
        $params['qty'] = $qty;
        $options = [];

        $productAttributeOptions = $parent->getTypeInstance(true)->getConfigurableAttributesAsArray($parent);

        foreach ($productAttributeOptions as $option) {
            $options[$option['attribute_id']] = $child->getData($option['attribute_code']);
        }
        $params['super_attribute'] = $options;

        /*Add product to cart */
        $cart->addProduct($parent, $params);
        $cart->save();
        foreach ($this->checkoutSession->getQuote()->getAllVisibleItems() as $item) {
            if ($item->getData('sku') == $sku) {
                $itemId = $item->getData('item_id');
                $cart->removeItem($itemId);
            }
        }
        $cart->save();
    }
    protected function changeSku($sku, $selectedSize)
    {
        $arr = explode('-', $sku);
        $arr[1] = $selectedSize;
        return implode('-', $arr);
    }
    public function execute()
    {
        $params = $this->_request->getParams();
        $sku = $params['sku'];
        $qty = $params['qty'];
        $selectedSize = $params['selectedSize'];
        $newSku = $this->changeSku($sku, $selectedSize);
        $productNew = $this->productRepository->get($newSku);
        $quote = $this->checkoutSession->getQuote();
        $cart = $this->cart->getItems()->getData();
        $this->getAddConfigurableProduct($productNew->getId(), $sku, $qty);
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl('checkout/cart/');
        $cart = $this->cart->getItems()->getData();
        $totals = $this->checkoutSession->getQuote()->getTotals();
        return $resultRedirect;
    }
}
