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

use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\Exception\InvalidArgumentException;
use Magento\Framework\Exception\LocalizedException;
use PM4Life\CmsInstaller\Api\Data\CmsInstallerInterface;
use PM4Life\CmsInstaller\Model\CmsInstaller;
use PM4Life\CmsInstaller\Model\CmsInstallerFactory;
use PM4Life\CmsInstaller\Model\Command\CmsInstaller\GetCmsInstallerList;
use PM4Life\CmsInstaller\Sources\Template\TemplateVariables;
use PM4Life\CmsInstaller\Sources\Template\VariablesInterface;

class ProcessModuleCmsInstaller
{
    private TemplateVariables $templateVariables;

    private SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory;

    private CmsInstallerFactory $cmsInstallerFactory;

    private GetCmsInstallerList $getCmsInstallerList;

    private ApplyCmsInstaller $applyCmsInstaller;

    public function __construct(
        TemplateVariables $templateVariables,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        CmsInstallerFactory $cmsInstallerFactory,
        GetCmsInstallerList $getCmsInstallerList,
        ApplyCmsInstaller $applyCmsInstaller
    ) {
        $this->templateVariables = $templateVariables;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->cmsInstallerFactory = $cmsInstallerFactory;
        $this->getCmsInstallerList = $getCmsInstallerList;
        $this->applyCmsInstaller = $applyCmsInstaller;
    }

    /**
     * Apply changes from templates to cms block or page
     *
     * @param array $moduleCmsData
     * @return void
     * @throws InvalidArgumentException
     * @throws LocalizedException
     */
    public function execute(array $moduleCmsData)
    {
        $cmsFiles = $moduleCmsData[VariablesInterface::CMS_DATA];
        foreach ($cmsFiles as $fileName => $fileContent) {

            $this->templateVariables->init($fileName, $fileContent);
            [$variables, $content, $uniqueId] = $this->templateVariables->getTemplateData();

            $cmsInstaller = $this->getCmsInstaller(
                $moduleCmsData[VariablesInterface::MODULE_NAME],
                $variables[VariablesInterface::TEMPLATE_TYPE],
                $fileName
            );

            if ($cmsInstaller->getVersionHash() === $uniqueId) {
                continue;
            }

            $cmsInstaller->setVersionHash($uniqueId);
            $this->applyCmsInstaller->save($cmsInstaller, $variables, $content);
        }
    }

    /**
     * Get cms installer instance
     *
     * @param string $module
     * @param string $templateType
     * @param string $identifier
     * @return CmsInstaller
     * @throws InvalidArgumentException
     */
    private function getCmsInstaller(
        string $module,
        string $templateType,
        string $identifier
    ): CmsInstaller {

        $cmsInstaller = $this->getExistingCmsInstaller($module, $templateType, $identifier);
        $newData = [
            CmsInstallerInterface::ORIGIN_MODULE => $module,
            CmsInstallerInterface::TEMPLATE_TYPE => $templateType,
            CmsInstallerInterface::IDENTIFIER => $identifier
        ];

        if ($cmsInstaller->getTemplateType() !== $newData[CmsInstallerInterface::TEMPLATE_TYPE]
            && $cmsInstaller->getVersionId()
        ) {
                throw new InvalidArgumentException(__('Template type is not changeable'));
        }

        return $cmsInstaller->addData($newData);
    }

    /**
     * Get existing cms installer
     *
     * @param string $module
     * @param string $templateType
     * @param string $identifier
     * @return CmsInstallerInterface|null
     */
    private function getExistingCmsInstaller(
        string $module,
        string $templateType,
        string $identifier
    ): ?CmsInstallerInterface {

        $searchCriteria = $this->searchCriteriaBuilderFactory->create()
            ->addFilter(CmsInstallerInterface::ORIGIN_MODULE, $module)
            ->addFilter(CmsInstallerInterface::TEMPLATE_TYPE, $templateType)
            ->addFilter(CmsInstallerInterface::IDENTIFIER, $identifier)
            ->create();

        /** @var CmsInstallerInterface[] $list */
        $list = $this->getCmsInstallerList->execute($searchCriteria)->getItems();

        foreach ($list as $item) {
            return $item;
        }

        return $this->cmsInstallerFactory->create();
    }
}
