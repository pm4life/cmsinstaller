<?php
declare(strict_types=1);

/**
 * PM4Life
 *
 * @category  Leisure & Lifestyle
 * @package   PM4Life_CmsInstaller
 * @author    Aleksa Zivkovic <aleksa.zivkovic.mga@gmail.com>
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 * @link      https://github.com/pm4life/
 */

namespace PM4Life\CmsInstaller\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\ResourceModel\Block as BlockResource;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\ResourceModel\Page as PageResource;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use PM4Life\CmsInstaller\Sources\Template\TemplateVariables;
use Magento\Framework\App\Response\Http\FileFactory;
use PM4Life\CmsInstaller\Sources\Template\VariablesInterface;

class Export extends Action implements HttpGetActionInterface
{
    private BlockFactory $blockFactory;

    private BlockResource $blockResource;

    private PageFactory $pageFactory;

    private PageResource $pageResource;

    private TemplateVariables $templateVariables;

    private FileFactory $fileFactory;

    public function __construct(
        Context $context,
        BlockFactory $blockFactory,
        BlockResource $blockResource,
        PageFactory $pageFactory,
        PageResource $pageResource,
        TemplateVariables $templateVariables,
        FileFactory $fileFactory
    ) {
        $this->blockFactory = $blockFactory;
        $this->blockResource = $blockResource;
        $this->pageFactory = $pageFactory;
        $this->pageResource = $pageResource;
        $this->templateVariables = $templateVariables;
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * Export template to browser output
     *
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $type = $this->getRequest()->getParam('type');
        $id = $this->getRequest()->getParam('id');

        if (empty($type) || empty($id)) {
            $this->messageManager->addErrorMessage(__('Missing required parameters'));
            return $this->_redirect($this->_redirect->getRefererUrl());
        }

        if ($type === VariablesInterface::TYPE_PAGE) {
            $entity = $this->pageFactory->create();
            $this->pageResource->load($entity, $id);
        } else {
            $entity = $this->blockFactory->create();
            $this->blockResource->load($entity, $id);
        }

        if (empty($entity->getId())) {
            $this->messageManager->addErrorMessage(
                __('No such %entity with id: %id found', ['entity' => $type, 'id' => $id])
            );
            return $this->_redirect($this->_redirect->getRefererUrl());
        }

        $fileName = $this->templateVariables->getTemplateName($entity->getData());
        $fileContents = $this->templateVariables->createExportVariablesBlock($type, $entity->getData());
        $fileContents .= $entity->getContent();
        $content = [
            'type' => 'string',
            'value' => $fileContents,
            'rm' => true
        ];

        try {
            return $this->fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
            return $this->_redirect($this->_redirect->getRefererUrl());
        }
    }
}
