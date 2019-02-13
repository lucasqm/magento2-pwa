<?php 

namespace Resultate\PWA\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const GENERAL_CONFIG_PATH = "resultate_pwa_configs/general/";
    const MANIFEST_CONFIG_PATH = "resultate_pwa_configs/manifest/";
    const SERVICE_WORKER_CONFIG_PATH = "resultate_pwa_configs/service_worker/";
    const ONE_SIGNAL_CONFIG_PATH = "resultate_pwa_configs/one_signal/";

    /**
     * @var \Magento\Framework\App\View\Deployment\Version
     */
    private $deploymentVersion;

    /**
     * @param \Magento\Framework\App\View\Deployment\Version $deploymentVersion
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\View\Deployment\Version $deploymentVersion
    ) {
        parent::__construct($context);
        $this->deploymentVersion = $deploymentVersion;
    }

    public function getGeneralConfig($config_path)
    {
        return $this->getConfig(
            self::GENERAL_CONFIG_PATH . $config_path
        );
    }

    public function getManifestConfig($config_path)
    {
        return $this->getConfig(
            self::MANIFEST_CONFIG_PATH . $config_path
        );
    }

    public function getSWConfig($config_path)
    {
        return $this->getConfig(
            self::SERVICE_WORKER_CONFIG_PATH . $config_path
        );
    }

    public function getSWSuffix()
    {
        $configSuffix = $this->getSWConfig('sw_cache_suffix');

        return $configSuffix ? $configSuffix : $this->deploymentVersion->getValue();
    }

    public function getOneSignalConfig($config_path)
    {
        return $this->getConfig(
            self::ONE_SIGNAL_CONFIG_PATH . $config_path
        );
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
