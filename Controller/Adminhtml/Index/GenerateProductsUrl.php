<?php

namespace Railsformers\ChatGPT\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\MassAction\Filter;
use Railsformers\ChatGPT\Helper\Data as HelperData;
use Railsformers\ChatGPT\Model\Query;
use Magento\Framework\UrlInterface;

class GenerateProductsUrl extends Action
{
    /**
     * @var HelperData
     */
    protected $helper;

    protected $storeManager;

    protected $collectionFactory;

    protected $request;
    protected $chatGPT;

    protected $form_key;

    protected $filter;

    protected $url;

    public function __construct(
        Context               $context,
        StoreManagerInterface $storeManager,
        Query\Completions     $chatGPT,
        FormKey               $form_key,
        CollectionFactory     $collectionFactory,
        RequestInterface      $request,
        Filter                $filter,
        HelperData            $helper,
        UrlInterface          $url
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->storeManager = $storeManager;
        $this->chatGPT = $chatGPT;
        $this->form_key = $form_key;
        $this->helper = $helper;
        $this->request = $request;
        $this->url = $url;
        parent::__construct($context);
    }

    public function execute()
    {


        $cron_model = $this->_objectManager->create('Railsformers\ChatGPT\Model\ChatgptCron');

        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $productIds = $collection->getAllIds();

        $params = ['_query' => ['form_key' => $this->form_key->getFormKey()]];
        $url = $this->url->getUrl('chatgpt/index/generateproducts/', $params);

        $data = [
            'cron_url' => str_replace('admin/', '', $url),
            'cron_ids' => implode(',', $productIds),
            'cron_type' => $this->request->getParam('type')
        ];
        $cron_model->setData($data)->save();

        $this->messageManager->addSuccessMessage(__('Funkce byla úspěšně provedena.'));
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_url->getUrl('catalog/product/index', ['_secure' => true, '_nosid' => true]));

        return $resultRedirect;


    }
}
