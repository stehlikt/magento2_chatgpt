<?php

namespace Railsformers\ChatGPT\Model\Query;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Railsformers\ChatGPT\Helper\Data as HelperData;

class Completions
{
    public const OPENAI_API_COMPLETION_ENDPOINT = 'https://api.openai.com/v1/chat/completions';

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var HelperData
     */
    protected $helper;

    /**
     * @param Curl $curl
     * @param Json $json
     * @param HelperData $helper
     */
    public function __construct(
        Curl $curl,
        Json $json,
        HelperData $helper
    ) {
        $this->curl = $curl;
        $this->helper = $helper;
        $this->json = $json;
    }

    /**
     * Get curl object
     *
     * @return Curl
     */
    public function getCurlClient()
    {
        return $this->curl;
    }

    /**
     * Call OpenAI API request
     *
     * @param string $prompt
     * @param string $type
     * @return string
     * @throws QueryException
     */
    public function makeRequest($prompt, $type , $lang, $controller)
    {
        $this->setHeaders();

        $this->getCurlClient()->post(
            self::OPENAI_API_COMPLETION_ENDPOINT,
            $this->getPayload($prompt, $type, $lang, $controller)
        );

        $response = $this->validateResponse();
        $this->createLog($response['text'],$response['tokens'],$controller,$type,$prompt);

        return $response;
    }

    /**
     * Set API header
     *
     * @return void
     * @throws QueryException
     */
    protected function setHeaders()
    {
        $token = $this->helper->getApiSecret();
        if (!$token) {
            throw new QueryException(__('API Secret not found. Please check configuration'));
        }
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->helper->getApiSecret()
        ];
        $this->getCurlClient()->setHeaders($headers);
    }

    /**
     * Get API payload
     *
     * @param string $prompt
     * @param string $type
     * @return string
     */
    protected function getPayload($prompt, $type, $lang, $controller)
    {
        $maxToken = $this->helper->getMaxToken($type);
        $input = '';
        if(str_contains($type,'meta'))
        {
            if($type == 'meta_description')
            {
                $input = "You are an ecommerce SEO expert and specialist. Create me a meta description for ".$controller." : %s in %s language. Don't exceed ".$maxToken." characters. Write me just the meta description in language that i gave you. Don't use quotation marks.";
                //$input = "Generate a SEO-optimized meta description for a ".$controller." : %s in %s language. Don't exceed ".$maxToken." characters.";
                //$input = "Create meta description in ".$maxToken." words for ".$controller." : %s in language %s";
            }
            elseif($type == 'meta_title')
            {
                $input = "You are an ecommerce SEO expert and specialist. Create me a meta title for ".$controller." : %s in %s language. Don't exceed ".$maxToken." characters. Write me just the meta title in language that i gave you. Don't use quotation marks.";
                //$input = "Generate a SEO-optimized meta title for a ".$controller." : %s in %s language. Don't exceed ".$maxToken." characters.";
                //$input = "Create meta title for ".$controller." : %s in language %s";
            }
            $payload = [
                'model' => 'gpt-3.5-turbo-0301',
                'messages' => [['role' => 'user', 'content' => sprintf($input, strip_tags($prompt), $lang)]],
                'max_tokens' => $maxToken,
                'temperature' => 0.5,
                'n' => 1
            ];

        }
        else
        {
            if($controller == 'product')
            {
                $input = "You are ecommerce merchendasing specialist and expert. Write me a unique HTML friendly product description for %s in %s language on an eshop. Don't exceed ".$maxToken." characters. Write me just description in language that i gave you, dont start with 'Product description: '";
                //$input = "Create a HTML description for a product : %s in %s language. Don't exceed ".$maxToken." characters.";
                //$input = "Create HTML description in ".$maxToken." words for product : %s in language %s";
            }
            elseif($controller == 'category')
            {
                $input = "Think like an ecommerce merchandising specialist and write a HTML friendly category description to list %s in %s language on an eshop. Don't exceed ".$maxToken." characters.";
                //$input = "Create a HTML description for a category : %s in %s language. Don't exceed ".$maxToken." characters.";
                //$input = "Create HTML description in ".$maxToken." words words for category page: %s in language %s";
            }
            $payload = [
                'model' => 'gpt-3.5-turbo-0301',
                'messages' => [['role' => 'user', 'content' => sprintf($input, strip_tags($prompt), $lang)]],
                'max_tokens' => $maxToken,
                'temperature' => 0.5,
                'n' => 1
            ];
        }

        return $this->json->serialize($payload);
    }

    /**
     * Verify API response
     *
     * @return array array
     * @throws QueryException
     */
    public function validateResponse()
    {
        if ($this->getCurlClient()->getStatus() == 401) {
            throw new QueryException(__('Unauthorized response. Please check token.'));
        }

        if ($this->getCurlClient()->getStatus() >= 500) {
            throw new QueryException(__('Server error'));
        }

        $response = $this->json->unserialize($this->getCurlClient()->getBody());

        if (isset($response['error'])) {
            throw new QueryException(__($response['error']['message'] ?? 'Unknown Error'));
        }

        if (!isset($response['choices'])) {
            throw new QueryException(__('No results found from API response'));
        }

        return ['text' => trim($response['choices'][0]['message']['content']), 'tokens' => $response['usage']['total_tokens']];
    }

    public function createLog($text,$tokens_count,$controller,$type,$prompt)
    {
        $log_text = '';
        if($controller == 'product')
        {
            $log_text = 'Vygenerovalo se ' . $type . ' pro produkt :' . $prompt . ' Text:' . $text;
        }
        elseif($controller == 'category')
        {
            $log_text = 'Vygenerovalo se ' . $type . ' pro kategorii :' . $prompt . ' Text:' . $text;
        }
        $message = '';
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $log_model = $objectManager->create('Railsformers\ChatGPT\Model\ChatgptLog');
        $data = [
            'log_text' => $log_text,
            'tokens' => $tokens_count
        ];
        $log_model->setData($data)->save();
    }
}
