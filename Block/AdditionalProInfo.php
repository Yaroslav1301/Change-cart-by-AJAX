<?php
namespace Kozar\UpdateCard\Block;

/**
 * AdditionalProInfo
 */
class AdditionalProInfo extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Catalog\Model\ProductRepository $productRepository
     */
    protected $productRepository;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
    }

    public function getAttributes($productId)
    {
        $product = $this->productRepository->getById($productId);
        $productAttributeOptions = $product->getTypeInstance(true)->getConfigurableAttributesAsArray($product);
        $attributeOptions = [];
        foreach ($productAttributeOptions as $productAttribute) {
            foreach ($productAttribute['values'] as $attribute) {
                $attributeOptions[$productAttribute['label']][$attribute['value_index']] = $attribute['store_label'];
            }
        }
        return $attributeOptions;
    }

    public function getSize($sku)
    {
        return explode("-", $sku)[1];
    }
    public function getColor($sku)
    {
        return explode("-", $sku)[2];
    }
}
