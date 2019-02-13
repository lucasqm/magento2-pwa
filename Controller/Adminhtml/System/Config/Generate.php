<?php

namespace Resultate\PWA\Controller\Adminhtml\System\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Generate extends Action
{
	const MANIFES_PATH = 'pwa/manifest/manifest.json';
	/**
	 * @type \Magento\Framework\Controller\Result\JsonFactory
	 */
	protected $resultJsonFactory;
	protected $_filesystem;
	protected $_media;
    protected $helper;

	/**
	 * @param Context $context
	 * @param JsonFactory $resultJsonFactory
	 */
	public function __construct(
		Context $context,
		JsonFactory $resultJsonFactory,
		Filesystem $filesystem,
        \Resultate\PWA\Helper\Data $helper
	) {
		$this->resultJsonFactory = $resultJsonFactory;
		$this->_filesystem = $filesystem;
		$this->_media = $this->_filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->helper = $helper;
		parent::__construct($context);
	}


	public function execute()
	{
		$status=false;
		try {
			$content = $this->_generateContent();

			$this->_media->writeFile(self::MANIFES_PATH, $content);
			$status = true;
			$message = __("manifest.json criado com sucesso!");
		} catch (\Exception $e) {
			echo $e->getMessage();
			$message = __("Erro ao criar manifest.json!");
		}

		/** @var \Magento\Framework\Controller\Result\Json $result */
		$result = $this->resultJsonFactory->create();
		return $result->setData(['success' => $status, 'message' => $message]);
	}

	protected function _generateContent()
	{
        $content = new \stdClass;
        $tags = $this->_getConfig('manifest_tags');
        $tags = unserialize($tags);

        foreach ($tags as $data)
        {
        	$content->{$data['tag']} = $data['value'];
        }

        $content->icons = $this->_getIcons();
        return \json_encode($content, JSON_PRETTY_PRINT);
	}

	protected function _getIcons()
	{
		$iconsResp = [];
        $icons = $this->_getConfig('manifest_icons');
        $icons = unserialize($icons);

		foreach ($icons as $data)
        {
        	$icon = new \stdClass;
        	$icon->src = $data['src'];
        	$icon->type = $data['type'];
        	$icon->sizes = $data['sizes'];
        	$iconsResp[] = $icon;
        }
        return $iconsResp;
	}

    /**
     * Get config from system
     */
    protected function _getConfig($config_path)
    {
        return $this->helper->getManifestConfig($config_path);
    }
}

?>