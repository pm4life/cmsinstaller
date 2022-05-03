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

namespace PM4Life\CmsInstaller\Model\Config\Service;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Config\ConfigOptionsListConstants;

class LocalModuleLoader
{
    private const APPLICABLE_PATH = '/app/code/';

    private ComponentRegistrarInterface $componentRegistrar;

    private DeploymentConfig $deploymentConfig;

    public function __construct(
        ComponentRegistrarInterface $componentRegistrar,
        DeploymentConfig $deploymentConfig
    ) {
        $this->componentRegistrar = $componentRegistrar;
        $this->deploymentConfig = $deploymentConfig;
    }

    /**
     * Get list of local modules that are enabled
     *
     * @return array
     */
    public function getList(): array
    {
        $localModules = [];

        $modulePaths = $this->componentRegistrar->getPaths(ComponentRegistrar::MODULE);
        foreach ($modulePaths as $moduleName => $modulePath) {
            if (strpos($modulePath, self::APPLICABLE_PATH) !== false) {
                $localModules[$moduleName] = $moduleName;
            }
        }
        try {
            $modulesList = $this->deploymentConfig->get(ConfigOptionsListConstants::KEY_MODULES);
        } catch (\Exception $exception) {
            $modulesList = [];
        }

        foreach ($localModules as $moduleName) {
            if (isset($modulesList[$moduleName]) && $modulesList[$moduleName] === 0) {
                unset($localModules[$moduleName]);
            }
        }

        return array_values($localModules);
    }
}
