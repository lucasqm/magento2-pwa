<?php 

namespace Resultate\PWA\Helper;


use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const GENERAL_CONFIG_PATH = "resultate_pwa_configs/general/";
    const MANIFEST_CONFIG_PATH = "resultate_pwa_configs/manifest/";
    const SERVICE_WORKER_CONFIG_PATH = "resultate_pwa_configs/service_worker/";
    const ONE_SIGNAL_CONFIG_PATH = "resultate_pwa_configs/one_signal/";
    /**
     * Template of signature component of URL, parametrized with the deployment version of static files
     */
    const SIGNATURE_TEMPLATE = 'version%s';

    const MERGED_PATH = '_cache/merged/';

    /**
     * @var \Magento\Framework\App\View\Deployment\Version
     */
    private $deploymentVersion;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $_filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $_staticDirectory;
    
    /**
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    private $_fileDrive;

    /**
     * @var String
     */
    private $_staticPath;


    /**
     * @param \Magento\Framework\App\View\Deployment\Version $deploymentVersion
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\View\Deployment\Version $deploymentVersion,
        \Magento\Framework\Filesystem\Driver\File $fileDrive,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct($context);
        $this->deploymentVersion = $deploymentVersion;

        $this->_filesystem = $filesystem;
        $this->_fileDrive = $fileDrive;
        $this->_staticDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::STATIC_VIEW);
        $this->_staticPath = $this->_staticDirectory->getAbsolutePath();
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

    public function getCacheAssets()
    {
        try {
            $path = $this->_staticPath . self::MERGED_PATH;
            $files = $this->_fileDrive->readDirectoryRecursively($path);
            return $this->_nomalizeFilesArray($files);
        } catch (\Exception $e) {
            return [];
        }
    }

    private function _nomalizeFilesArray($files)
    {
        return array_map(
            function($file){
                return str_replace(
                    $this->_staticPath,
                    "/static/". $this->_renderUrlSignature() . "/",
                    $file);
            }, 
            $files);
    }

    /**
     * Render URL signature from the template
     *
     * @return string
     */
    private function _renderUrlSignature()
    {
        return sprintf(self::SIGNATURE_TEMPLATE, $this->deploymentVersion->getValue());
    }
}
