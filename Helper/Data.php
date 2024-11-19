<?php

namespace Railsformers\ChatGPT\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * Config XML paths
     */
    public const XML_PATH_IS_ENABLED = 'chatgpt/general/enabled';
    public const XML_PATH_API_KEY = 'chatgpt/general/api_secret';
    public const XML_PATH_DESCRIPTION_WORD_COUNT = 'chatgpt/general/description_words_count';
    public const XML_PATH_SHORT_DESCRIPTION_WORD_COUNT = 'chatgpt/general/short_description_words_count';
    public const XML_PATH_PRODUCT_ATTRIBUTE = 'chatgpt/general/attribute';
    public const XML_PATH_CHATGPT_LANGUAGE = 'chatgpt/general/chatgpt_language';
    public const XML_PATH_META_DESCRIPTION_WORD_COUNT = 'chatgpt/general/meta_description_words_count';
    public const XML_PATH_META_TITLE_WORD_COUNT = 'chatgpt/general/meta_title_words_count';

    /**
     * Get config value
     *
     * @param string $path
     * @return mixed
     */
    public function getConfig($path)
    {
        return $this->scopeConfig->getValue($path);
    }

    /**
     * Check is extension is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_IS_ENABLED);
    }

    /**
     * Get API secret
     *
     * @return string
     */
    public function getApiSecret()
    {
        return $this->getConfig(self::XML_PATH_API_KEY);
    }

    /**
     * Get ChatGPT language
     *
     * @return string
     */
    public function getChatGptLanguage()
    {
        return $this->getConfig(self::XML_PATH_CHATGPT_LANGUAGE);
    }

    /**
     * Get number of description words
     *
     * @return int
     */
    public function getDescriptionWordCount()
    {
        return (int) $this->getConfig(self::XML_PATH_DESCRIPTION_WORD_COUNT);
    }

    /**
     * Get number of short description words
     *
     * @return int
     */
    public function getShortDescriptionWordCount()
    {
        return (int) $this->getConfig(self::XML_PATH_SHORT_DESCRIPTION_WORD_COUNT);
    }

    /**
     * Get number of meta title words
     *
     * @retrun int
     */
    public function getMetaTitleWordCount()
    {
        return (int) $this->getConfig(self::XML_PATH_META_TITLE_WORD_COUNT);
    }

    /**
     * Get number of meta description words
     *
     * @return int
     */
    public function getMetaDescripptionWordCount()
    {
        return (int) $this->getConfig(self::XML_PATH_META_DESCRIPTION_WORD_COUNT);
    }

    /**
     * Get max token
     *
     * @param string $type
     * @return int
     */
    public function getMaxToken($type)
    {
        if ($type == 'short') {
            $maxToken = $this->getShortDescriptionWordCount();
        } elseif($type == 'full') {
            $maxToken = $this->getDescriptionWordCount();
        }   elseif ($type == 'meta_description'){
            $maxToken = $this->getMetaDescripptionWordCount();
        } elseif($type == 'meta_title'){
            $maxToken = $this->getMetaTitleWordCount();
        }
        return $maxToken;
    }

    /**
     * Get product attribute code
     *
     * @return mixed
     */
    public function getProductAttribute()
    {
        return $this->getConfig(self::XML_PATH_PRODUCT_ATTRIBUTE);
    }
}
