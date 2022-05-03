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

namespace PM4Life\CmsInstaller\Data;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\DriverPool;
use PM4Life\CmsInstaller\Api\Data\CmsInstallerInterface;
use PM4Life\CmsInstaller\Helper\Config as ConfigHelper;
use Magento\Framework\Exception\InvalidArgumentException;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory as BlockCollectionFactory;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as PageCollectionFactory;
use PM4Life\CmsInstaller\Sources\Template\TemplateVariables;
use PM4Life\CmsInstaller\Sources\Template\VariablesInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\WriteFactory;
use Magento\Framework\Filesystem\File\WriteFactory as FileWriteFactory;

class CreateTemplateExport
{
    private ConfigHelper $configHelper;

    private DirectoryList $directoryList;

    private TemplateVariables $templateVariables;

    private WriteFactory $writeFactory;

    private FileWriteFactory $fileWriteFactory;

    private array $collectionTypes;

    public function __construct(
        ConfigHelper $configHelper,
        DirectoryList $directoryList,
        TemplateVariables $templateVariables,
        WriteFactory $writeFactory,
        FileWriteFactory $fileWriteFactory,
        BlockCollectionFactory $blockCollectionFactory,
        PageCollectionFactory $pageCollectionFactory
    ) {
        $this->configHelper = $configHelper;
        $this->directoryList = $directoryList;
        $this->templateVariables = $templateVariables;
        $this->writeFactory = $writeFactory;
        $this->fileWriteFactory = $fileWriteFactory;
        $this->collectionTypes = [
            VariablesInterface::TYPE_BLOCK => $blockCollectionFactory,
            VariablesInterface::TYPE_PAGE => $pageCollectionFactory
        ];
    }

    /**
     * Create export for given cms entity type identifiers,
     * exported files are either placed in module cms_install folder or in app/design/frontend/cms_installer
     *
     * @param string $cmsEntityType
     * @param array $identifiers
     * @return void
     * @throws InvalidArgumentException
     * @throws FileSystemException
     */
    public function export(string $cmsEntityType, array $identifiers)
    {

        if (!empty($cmsEntityType) && !isset($this->collectionTypes[$cmsEntityType])) {
            throw new InvalidArgumentException(__('Type: %1 is not allowed', $cmsEntityType));
        }

        $entityTypes = $cmsEntityType
            ? [$cmsEntityType]
            : [VariablesInterface::TYPE_BLOCK, VariablesInterface::TYPE_PAGE];

        foreach ($entityTypes as $type) {
            $collection = $this->collectionTypes[$type]->create();

            if (!empty($identifiers)) {
                $collection->addFieldToFilter('identifier', ['in' => $identifiers]);
            }

            foreach ($collection as $item) {
                $this->exportTemplate($type, $item->getData());
            }
        }
    }

    /**
     * Export data to template
     *
     * @param string $type
     * @param array $data
     * @return void
     * @throws FileSystemException
     */
    private function exportTemplate(string $type, array $data)
    {
        $destinationDir = sprintf(
            '%s%s%s/',
            $this->directoryList->getRoot(),
            $this->configHelper->getDesignDir(),
            CmsInstallerInterface::INSTALL_DIR
        );

        $directoryWrite = $this->writeFactory->create($destinationDir);

        try {
            if (!$directoryWrite->isDirectory($destinationDir)) {
                $directoryWrite->create($destinationDir);
            }

            if ($directoryWrite->isWritable($destinationDir)) {
                $fileName = $this->templateVariables->getTemplateName($data);
                $contents = $this->templateVariables->createExportVariablesBlock($type, $data);
                $contents .= $this->templateVariables->getContentData($data);

                $fileWrite = $this->fileWriteFactory->create(
                    $destinationDir . $fileName,
                    DriverPool::FILE,
                    'w'
                );
                $fileWrite->write($contents);
            }
        } catch (\Exception $exception) {
            throw new FileSystemException(
                __(
                    'Failed exporting template, error: %message',
                    ['message' => $exception->getMessage()]
                )
            );
        }
    }
}
