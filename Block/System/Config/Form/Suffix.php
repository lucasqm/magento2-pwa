<?php

namespace Resultate\PWA\Block\System\Config\Form;


class Suffix extends \Magento\Config\Block\System\Config\Form\Field
{
	/**
     * @var \Magento\Framework\App\View\Deployment\Version
     */
    private $deploymentVersion;

    /**
     * @param \Magento\Framework\Model\Context $context
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\View\Deployment\Version $deploymentVersion
    ) {
        $this->deploymentVersion = $deploymentVersion;
        parent::__construct($context);
    }

	/**
     * Retrieve element HTML markup
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
    	if(!$element->getValue())
    	{
    		$element->setValue($this->deploymentVersion->getValue());
    	}
        return $element->getElementHtml();
    }
}
