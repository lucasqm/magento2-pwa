<?php
namespace Resultate\PWA\Block\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;


class Generate extends Field
{
	/**
	 * @var string
	 */
	protected $_template = 'Resultate_PWA::system/config/generate.phtml';


	/**
	 * @param Context $context
	 * @param array $data
	 */
	public function __construct(
		Context $context,
		array $data = []
	) {
		parent::__construct($context, $data);
	}

	/**
	 * Remove scope label
	 *
	 * @param  AbstractElement $element
	 * @return string
	 */
	public function render(AbstractElement $element)
	{
		$element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
		return parent::render($element);
	}

	/**
	 * Return element html
	 *
	 * @param  AbstractElement $element
	 * @return string
	 */
	protected function _getElementHtml(AbstractElement $element)
	{
		return $this->_toHtml();
	}

	/**
	 * Generate collect button html
	 *
	 * @return string
	 */
	public function getButtonHtml()
	{
		$button = $this->getLayout()->createBlock(
			'Magento\Backend\Block\Widget\Button'
		)->setData(
			[
				'id' => 'manifest_generate_button',
				'label' => __('Gerar manifest.json'),
			]
		);

		return $button->toHtml();
	}

	/**
	 * Return ajax url for collect button
	 *
	 * @return string
	 */
	public function getAjaxUrl()
	{
		return $this->getUrl('pwa/system_config/generate');
	}
}
?>