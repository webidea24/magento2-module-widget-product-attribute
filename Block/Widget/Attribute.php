<?php

namespace Webidea24\WidgetProductAttribute\Block\Widget;

use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

/**
 * @method string getAttributeCode()
 */
class Attribute extends Template implements BlockInterface, IdentityInterface
{

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    private $productResourceModel;

    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product $productResourceModel,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->productResourceModel = $productResourceModel;
    }

    protected function _toHtml(): string
    {
        if ($this->getProductId()) {
            try {
                $value = $this->productResourceModel->getAttributeRawValue($this->getProductId(), $this->getAttributeCode(), $this->_storeManager->getStore());

                if (is_numeric($roundPrecision = $this->getData('round'))) {
                    $value = round($value, $roundPrecision);
                }
                return $value;
            } catch (LocalizedException $e) {
                return '<!-- error: ' . $e->getMessage() . ' -->';
            }
        }

        return '';
    }

    public function getProductId(): ?int
    {
        $idPath = $this->getData('id_path');
        $exploded = explode('/', $idPath);

        return $exploded[1] ?? null;
    }

    public function getIdentities(): array
    {
        return [
            'widget_product_attribute__' . $this->getAttributeCode(),
            Product::CACHE_TAG . '_' . $this->$this->getProductId()
        ];
    }
}
