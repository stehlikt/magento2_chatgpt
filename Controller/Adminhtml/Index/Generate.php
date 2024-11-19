<?php

namespace Railsformers\ChatGPT\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Railsformers\ChatGPT\Helper\Data as HelperData;
use Railsformers\ChatGPT\Model\Query\completions;
use Railsformers\ChatGPT\Model\Query\QueryException;
use Magento\Catalog\Api\CategoryRepositoryInterface;

class Generate extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Railsformers_ChatGPT::generate';

    /**
     * @var JsonFactory
     */
    protected $resultJson;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var completions
     */
    protected $queryCompletion;

    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * @var CategoryRepositoryInterface
     */

    protected $categoryRepository;

    /**
     * Generate constructor.
     *
     * @param Action\Context $context
     * @param JsonFactory $resultJson
     * @param ProductRepositoryInterface $productRepository
     * @param completions $queryCompletion
     * @param CategoryRepositoryInterface $categoryRepository
     * @param HelperData $helper
     */
    public function __construct(
        Action\Context              $context,
        JsonFactory                 $resultJson,
        ProductRepositoryInterface  $productRepository,
        completions                 $queryCompletion,
        CategoryRepositoryInterface $categoryRepository,
        HelperData                  $helper
    )
    {
        $this->resultJson = $resultJson;
        $this->productRepository = $productRepository;
        $this->queryCompletion = $queryCompletion;
        $this->categoryRepository = $categoryRepository;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * Generate Content
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $response = ['error' => true, 'data' => 'unknown'];
        $isEnabled = $this->helper->isEnabled();
        $controller = $this->getRequest()->getParam('controller');
        $log_text = '';

        if ($isEnabled) {
            try {
                if ($controller == 'product') {
                    $sku = $this->getRequest()->getParam('sku', false);
                    if ($sku) {
                        $product = $this->productRepository->get($sku);
                        $attribute = $this->helper->getProductAttribute();
                        $lang = $this->helper->getChatGptLanguage();
                        $type = $this->getRequest()->getParam('type');
                        $queryValue = $product->getData($attribute);
                        if ($queryValue) {

                            $data = $this->queryCompletion->makeRequest($queryValue, $type, $lang, $controller);
                            $response = ['error' => false, 'data' => $data['text']];
                        }
                    }
                } elseif ($controller == 'category') {

                    $category = $this->categoryRepository->get($this->getRequest()->getParam('id'));
                    $lang = $this->helper->getChatGptLanguage();
                    $type = $this->getRequest()->getParam('type');
                    $queryValue = $category->getName();
                    if ($queryValue) {
                        $data = $this->queryCompletion->makeRequest($queryValue, $type, $lang, $controller);
                        $response = ['error' => false, 'data' => $data['text']];
                    }
                }

            } catch (QueryException $e) {
                $response = ['error' => true, 'data' => $e->getMessage()];
            } catch (\Exception $e) {
                $response = ['error' => true, 'data' => $e->getMessage()];
            }
        }

        $resultJson = $this->resultJson->create();
        return $resultJson->setData($response);
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ADMIN_RESOURCE);
    }

}
