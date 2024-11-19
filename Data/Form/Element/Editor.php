<?php

namespace Railsformers\ChatGPT\Data\Form\Element;

use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;
use Magento\Framework\Math\Random;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Railsformers\ChatGPT\Helper\Data as HelperData;
use Magento\Framework\App\RequestInterface;

class Editor extends \Magento\Framework\Data\Form\Element\Editor
{
    public const ALLOWED_FIELDS_HTML_ID = [
        'product_form_description',
        'product_form_short_description',
        'category_form_description'
    ];

    /**
     * @var HelperData
     */
    protected $helper;

    protected $request;

    /**
     * Editor constructor.
     *
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param HelperData $helper
     * @param array $data
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     * @param Random|null $random
     * @param SecureHtmlRenderer|null $secureRenderer
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        HelperData $helper,
        RequestInterface $request,
        $data = [],
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        ?Random $random = null,
        ?SecureHtmlRenderer $secureRenderer = null,
    ) {
        $this->helper = $helper;
        $this->request = $request;
        parent::__construct(
            $factoryElement,
            $factoryCollection,
            $escaper,
            $data,
            $serializer,
            $random,
            $secureRenderer
        );
    }

    /**
     * Return HTML button to toggling WYSIWYG
     *
     * @param bool $visible
     * @return string
     */
    protected function _getToggleButtonHtml($visible = true)
    {
        $html = parent::_getToggleButtonHtml($visible);
        $isEnabled = $this->helper->isEnabled();
        if ($isEnabled && in_array($this->getHtmlId(), self::ALLOWED_FIELDS_HTML_ID) && $this->request->getParam('id')) {
            $html .= $this->_getButtonHtml(
                [
                    'title' => $this->translate('Generovat obsah pomocí ChatGPT'),
                    'class' => 'generate-chatgpt-short-content',
                    'style' => $visible ? '' : 'display:none',
                    'id' => $this->getHtmlId() . '_chatgpt',
                ]
            );
        }
        return $html;
    }
}
