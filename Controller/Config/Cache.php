<?php

namespace Resultate\PWA\Controller\Config;

class Cache extends \Magento\Framework\App\Action\Action
{
    const SERVICE_WORKER_CONFIG_PATH = "resultate_pwa_configs/service_worker/";

    protected $logger;
    protected $_jsonFactory;
    protected $_helper;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Resultate\PWA\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Psr\Log\LoggerInterface $logger,
        \Resultate\PWA\Helper\Data $helper
    ) {
        $this->logger = $logger;
        $this->_jsonFactory = $jsonFactory;
        $this->_helper = $helper;
        parent::__construct($context);
    }

    /**
     * Cria o manifesto com base nas configurações
     * 
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        return $this->_getResponse();
    }

    private function _getResponse()
    {
        $data = $this->_getConfigData();
        return $this->_jsonFactory->create()->setData($data);
    }

    private function _getConfigData()
    {
        $config = new \stdClass;
        $config->cacheMaxAgeSeconds = $this->_getMaxAge();
        $config->cachePrefix = $this->_getConfig('sw_cache_prefix');
        $config->cacheSuffix = $this->_helper->getSWSuffix();
        $config->cacheName = $this->_getCacheNames();
        $config->staticAssets = $this->_getStaticAssets();
        return $config;
    }

    private function _getStaticAssets()
    {
        $staticAssets = new \stdClass;
        $staticAssets->offline = "/pwa/fallback";
        $staticAssets->default = $this->_getDefaultAssets();
        return $staticAssets;
    }

    private function _getDefaultAssets()
    {
        $confRoutes = $this->_getConfig('sw_cache_routes');
        $confRoutes = unserialize($confRoutes);
        $response = [];
        foreach ($confRoutes as $data) {
            $response[] = $data['route'];
        }

        return array_merge($response, $this->_helper->getCacheAssets());
    }

    private function _getCacheNames()
    {
        $cachePrefix = $this->_getConfig('sw_cache_prefix');
        $cacheSuffix = $this->_helper->getSWSuffix();

        $cacheNames = new \stdClass;
        $cacheNames->default = $cachePrefix . '-' . $cacheSuffix;
        $cacheNames->offline = $cachePrefix . '-off-' . $cacheSuffix;
        return $cacheNames;
    }

    private function _getMaxAge()
    {
        return $this->_getConfig('sw_cache_max_age') * 24 * 60 * 60;
    }

    /**
     * Get config from system
     */
    protected function _getConfig($config_path)
    {
        return $this->_helper->getSWConfig($config_path);
    }
}