<?php

namespace Resultate\PWA\Controller\Fallback;

class Index extends \Magento\Framework\App\Action\Action
{

    protected $logger;
    protected $_pageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\View\Result\PageFactory  $pageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
		\Magento\Framework\View\Result\PageFactory $pageFactory
    ) {
        $this->logger = $logger;
		$this->_pageFactory = $pageFactory;
        parent::__construct($context);
    }

    /**
     * Cria o manifesto com base nas configurações
     * 
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        return $this->_pageFactory->create();
    }

}