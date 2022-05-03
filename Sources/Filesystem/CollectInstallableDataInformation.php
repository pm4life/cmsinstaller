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

namespace PM4Life\CmsInstaller\Sources\Filesystem;

use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use PM4Life\CmsInstaller\Helper\Config as ConfigHelper;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use PM4Life\CmsInstaller\Api\Data\CmsInstallerInterface;
use Psr\Log\LoggerInterface;

class CollectInstallableDataInformation
{
    private const FILE_IDENTIFIER_EXTENSION = '.html';

    private ConfigHelper $configHelper;

    private DirectoryList $directoryList;

    private ComponentRegistrarInterface $componentRegistrar;

    private ReadFactory $readFactory;

    private LoggerInterface $logger;

    public function __construct(
        ConfigHelper $configHelper,
        DirectoryList $directoryList,
        ComponentRegistrarInterface $componentRegistrar,
        ReadFactory $directoryRead,
        LoggerInterface $logger
    ) {
        $this->configHelper = $configHelper;
        $this->directoryList = $directoryList;
        $this->componentRegistrar = $componentRegistrar;
        $this->readFactory = $directoryRead;
        $this->logger = $logger;
    }

    /**
     * Collect installable data from enabled source directory
     *
     * @return \Generator
     */
    public function execute(): \Generator
    {
        if ($this->configHelper->getBase()) {
            foreach ($this->configHelper->getAllowedModules() as $module) {
                $modulePath = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, $module);
                return $this->yieldContents($modulePath, $module);
            }
        } else {
            $designDir = $this->directoryList->getRoot() . $this->configHelper->getDesignDir();
            return $this->yieldContents($designDir, ucwords(CmsInstallerInterface::INSTALL_DIR, '_'));
        }
    }

    /**
     * Get contents from directory
     *
     * @param string $path
     * @param string $moduleName
     * @return \Generator
     */
    private function yieldContents(string $path, string  $moduleName): \Generator
    {
        $directoryRead = $this->readFactory->create($path);
        $filePath = sprintf('%s/%s/', $path, CmsInstallerInterface::INSTALL_DIR);

        try {
            if ($directoryRead->isExist($filePath) && $this->hasCmsInstallDataFolder($directoryRead, $filePath)) {
                yield [
                    'module_name' => $moduleName,
                    'cms_data' => $this->getCmsInstallData($directoryRead, $filePath)
                ];
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    /**
     * Check if module has cms_install directory
     *
     * @param Read $directoryRead
     * @param string $path
     * @return bool
     */
    private function hasCmsInstallDataFolder(Read $directoryRead, string $path): bool
    {
        try {
            return $directoryRead->isExist($path) && !empty($directoryRead->read($path));
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Collect all install data for given module path
     *
     * @param Read $directoryRead
     * @param string $path
     * @return array|null
     */
    private function getCmsInstallData(Read $directoryRead, string $path): ?array
    {
        $cmsInstallData = [];
        try {
            $allHtmlFiles = $directoryRead->search(CmsInstallerInterface::FILE_EXTENSION_PATTERN, $path);
            foreach ($allHtmlFiles as $filePath) {
                $cmsInstallData[$this->getIdentifier($filePath)] = $directoryRead->openFile($filePath)->readAll();
            }
        } catch (\Exception $exception) {
            return null;
        }

        return $cmsInstallData;
    }

    /**
     * Get file name from short path
     *
     * @param string $path
     * @return string
     */
    private function getIdentifier(string $path): string
    {
        return str_replace([CmsInstallerInterface::INSTALL_DIR . '/', self::FILE_IDENTIFIER_EXTENSION], '', $path);
    }
}
