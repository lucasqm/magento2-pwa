<?php 

namespace Resultate\PWA\Block\System\Config\Form;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

use Magento\Framework\Data\Form\Element\AbstractElement;

class FileList extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;
    
    /**
     * @var Filesystem
     */
    protected $_fileDrive;
    protected $_mediaPath;
    


    /**
     * @param \Magento\Framework\Model\Context $context
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Filesystem\Driver\File $fileDrive
    ) {
        $this->_fileDrive = $fileDrive;
        parent::__construct($context);
        $this->_mediaDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $this->_mediaPath = $this->_mediaDirectory->getAbsolutePath();
    }

    /**
     * @return void
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        try{
            $path = $this->_getUploadDir($element);
            $images = $this->_fileDrive->readDirectoryRecursively($path);
            $imagesPath = $this->_getMediaPath($images);
            $html = $this->_getImageHtml($imagesPath);
            $element->setValue($html);
        }catch (\Exception $e){
            $element->setValue("Não foi encontrado arquivos.");
        }
        return $element->getValue();
    }

    protected function _getImageHtml($imagesPath)
    {
        $html = "<ul style='list-style: none;'>";
        foreach ($imagesPath as $imagePath)
        {
            $html .= "<li>" . $imagePath . "</li>";
        }
        $html .= "</ul>";
        return $html;
    }

    protected function _getMediaPath($images)
    {
        $response = array_map(
            function($image){
                return str_replace($this->_mediaPath, "/media/", $image);
            }, 
            $images);
        return $response;
    }

    /**
     * Return path to directory for upload file
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getUploadDir($element)
    {
        $fieldConfig = $element->getFieldConfig();

        if (!array_key_exists('upload_dir', $fieldConfig)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The base directory to upload file is not specified.')
            );
        }

        if (is_array($fieldConfig['upload_dir'])) {
            $uploadDir = $fieldConfig['upload_dir']['value'];
            if (
                array_key_exists('scope_info', $fieldConfig['upload_dir'])
                && $fieldConfig['upload_dir']['scope_info']
            ) {
                $uploadDir = $this->_appendScopeInfo($uploadDir, $element);
            }

            if (array_key_exists('config', $fieldConfig['upload_dir'])) {
                $uploadDir = $this->getUploadDirPath($uploadDir);
            }
        } else {
            $uploadDir = (string)$fieldConfig['upload_dir'];
        }

        return $uploadDir;
    }

    /**
     * Retrieve upload directory path
     *
     * @param string $uploadDir
     * @return string
     */
    protected function getUploadDirPath($uploadDir)
    {
        return $this->_mediaDirectory->getAbsolutePath($uploadDir);
    }

    /**
     * Add scope info to path
     *
     * E.g. 'path/stores/2' , 'path/websites/3', 'path/default'
     *
     * @param string $path
     * @return string
     */
    protected function _appendScopeInfo($path, $element)
    {
        $path .= '/' . $element->getScope();
        if (ScopeConfigInterface::SCOPE_TYPE_DEFAULT != $element->getScope()) {
            $path .= '/' . $element->getScopeId();
        }
        return $path;
    }
}
