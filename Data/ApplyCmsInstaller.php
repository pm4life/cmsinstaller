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

use Magento\Framework\Exception\LocalizedException;
use PM4Life\CmsInstaller\Model\CmsInstaller;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\ResourceModel\Page as PageResource;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Model\ResourceModel\Block as BlockResource;
use PM4Life\CmsInstaller\Model\Command\CmsInstaller\SaveVersion as SaveCmsInstallerVersion;
use PM4Life\CmsInstaller\Sources\Template\VariablesInterface;
use Psr\Log\LoggerInterface;

class ApplyCmsInstaller
{
    private array $modelAndResource;

    private SaveCmsInstallerVersion $saveCmsInstallerVersion;

    private LoggerInterface $logger;

    public function __construct(
        BlockFactory $blockFactory,
        BlockResource $blockResource,
        PageFactory $pageFactory,
        PageResource $pageResource,
        SaveCmsInstallerVersion $saveCmsInstallerVersion,
        LoggerInterface $logger
    ) {
        $this->modelAndResource = [
            VariablesInterface::TYPE_BLOCK => [$blockFactory, $blockResource],
            VariablesInterface::TYPE_PAGE => [$pageFactory, $pageResource]
        ];
        $this->saveCmsInstallerVersion = $saveCmsInstallerVersion;
        $this->logger = $logger;
    }

    /**
     * Pièce de résistance
     *
     * @param CmsInstaller $cmsInstaller
     * @param array $variables
     * @param string $content
     * @return void
     * @throws LocalizedException
     */
    public function save(CmsInstaller $cmsInstaller, array $variables, string $content)
    {
        $type = $cmsInstaller->getTemplateType();
        $identifier = $cmsInstaller->getIdentifier();

        if (!isset($this->modelAndResource[$type])) {
            throw new LocalizedException(
                __('No such type: %type defined in: %class', ['type' => $type, 'class' => self::class])
            );
        }

        try {
            [$objectFactory, $objectResource] = $this->modelAndResource[$type];

            $object = $objectFactory->create();
            $objectResource->load($object, $identifier, VariablesInterface::IDENTIFIER);
            $variables[VariablesInterface::CONTENT] = $content;
            $variables[VariablesInterface::IDENTIFIER] = $identifier;
            $variables['update_time'] = date('Y-m-d H:i:s');
            $object->addData($variables);

            $objectResource->save($object);
            $this->saveCmsInstallerVersion->execute($cmsInstaller);

        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }
}
