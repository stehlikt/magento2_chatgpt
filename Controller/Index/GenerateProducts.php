<?php

namespace Railsformers\ChatGPT\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Railsformers\ChatGPT\Helper\Data as HelperData;
use Railsformers\ChatGPT\Model\Query;
use Magento\Framework\UrlInterface;
use Railsformers\ChatGPT\Model\ChatgptCronFactory;

class GenerateProducts extends Action
{
    /**
     * @var HelperData
     */
    protected $helper;

    protected $collectionFactory;

    protected $request;
    protected $chatGPT;

    protected $url;

    protected $chatgptCronFactory;


    public function __construct(
        Context            $context,
        Query\Completions  $chatGPT,
        CollectionFactory  $collectionFactory,
        RequestInterface   $request,
        HelperData         $helper,
        UrlInterface       $url,
        ChatgptCronFactory $chatgptCronFactory
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->chatGPT = $chatGPT;
        $this->helper = $helper;
        $this->request = $request;
        $this->url = $url;
        $this->chatgptCronFactory = $chatgptCronFactory;
        parent::__construct($context);
    }

    public function execute()
    {


        $cronModel = $this->chatgptCronFactory->create();
        $collection = $cronModel->getCollection();
        $collection->addFieldToFilter('cron_url', $this->url->getCurrentUrl());
        $cron = $collection->getFirstItem();

        $ids = $cron->getData('cron_ids');
        $type = explode(',',$cron->getData('cron_type'));

        $lang = $this->helper->getChatGptLanguage();

        $products = $this->collectionFactory->create()
            ->setFlag('has_stock_status_filter', false)
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('entity_id', ['in' => explode(',', $ids)]);

        foreach ($products as $item) {

            $product_name = $item->getName();
            if (in_array('all',$type) || in_array('metaTitle',$type)) {
                $meta_title = $this->chatGPT->makeRequest($product_name, 'meta_title', $lang, 'product');
                $item->setMetaTitle($meta_title['text']);
            }

            if (in_array('all',$type) || in_array('metaDescription',$type)) {
                $meta_description = $this->chatGPT->makeRequest($product_name, 'meta_description', $lang, 'product');
                $item->setMetaDescription($meta_description['text']);
            }
            if (in_array('all',$type) || in_array('description',$type)) {
                $descritpion = $this->chatGPT->makeRequest($product_name, 'full', $lang, 'product');
                $item->setDescription($descritpion['text']);
            }
            if (in_array('all',$type) || in_array('shortDescription',$type)) {
                $short_descritpion = $this->chatGPT->makeRequest($product_name, 'short', $lang, 'product');
                $item->setShortDescription($short_descritpion['text']);
            }

            $item->save();
        }
    }
}
