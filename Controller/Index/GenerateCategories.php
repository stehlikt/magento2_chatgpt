<?php

namespace Railsformers\ChatGPT\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Data\Form\FormKey;
use Railsformers\ChatGPT\Helper\Data as HelperData;
use Railsformers\ChatGPT\Model\Query\Completions;
use Magento\Framework\UrlInterface;
use Railsformers\ChatGPT\Model\ChatgptCronFactory;

class GenerateCategories extends Action
{
    /**
     * @var HelperData
     */
    protected $helper;

    protected $collectionFactory;

    protected $request;

    protected $form_key;

    protected $chatGPT;

    protected $url;

    protected $chatgptCronFactory;

    public function __construct(
        Context            $context,
        CollectionFactory  $collectionFactory,
        HelperData         $helper,
        Completions        $chatGPT,
        RequestInterface   $request,
        FormKey            $form_key,
        UrlInterface       $url,
        ChatgptCronFactory $chatgptCronFactory
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->helper = $helper;
        $this->chatGPT = $chatGPT;
        $this->request = $request;
        $this->form_key = $form_key;
        $this->url = $url;
        $this->chatgptCronFactory = $chatgptCronFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        if ($this->request->getParam('saveUrl')) {
            $cron_model = $this->_objectManager->create('Railsformers\ChatGPT\Model\ChatgptCron');

            $params = ['_query' => ['form_key' => $this->form_key->getFormKey()]];
            $url = $this->url->getUrl('chatgpt/index/generatecategories/', $params);

            $data = [
                'cron_url' => $url,
                'cron_type' => $this->request->getParam('type')
            ];
            $cron_model->setData($data)->save();

            $this->messageManager->addSuccessMessage(__('Funkce byla úspěšně provedena.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('admin/catalog/category/index');

            return $resultRedirect;
        } else {
            $lang = $this->helper->getChatGptLanguage();

            $cronModel = $this->chatgptCronFactory->create();
            $collection = $cronModel->getCollection();
            $collection->addFieldToFilter('cron_url', $this->url->getCurrentUrl());
            $cron = $collection->getFirstItem();

            $type = explode(',', $cron->getData('cron_type'));

            $categories = $this->collectionFactory->create()
                ->addAttributeToSelect('*')
                ->addIsActiveFilter();

            foreach ($categories as $item) {

                $category_name = $item->getName();

                if (in_array('all', $type) || in_array('metaTitle', $type)) {
                    $meta_title = $this->chatGPT->makeRequest($category_name, 'meta_title', $lang, 'category');
                    $item->setMetaTitle($meta_title['text']);
                }
                if (in_array('all', $type) || in_array('metaDescription', $type)) {
                    $meta_description = $this->chatGPT->makeRequest($category_name, 'meta_description', $lang, 'category');
                    $item->setMetaDescription($meta_description['text']);
                }
                if (in_array('all', $type) || in_array('description', $type)) {
                    $description = $this->chatGPT->makeRequest($category_name, 'full', $lang, 'category');
                    $item->setDescription($description['text']);
                }
                $item->save();
            }
        }
    }
}
