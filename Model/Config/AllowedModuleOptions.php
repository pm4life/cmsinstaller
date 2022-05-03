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

namespace PM4Life\CmsInstaller\Model\Config;

use Magento\Framework\Data\OptionSourceInterface;
use PM4Life\CmsInstaller\Model\Config\Service\LocalModuleLoader;

class AllowedModuleOptions implements OptionSourceInterface
{
    private LocalModuleLoader $localModuleLoader;

    public function __construct(LocalModuleLoader $localModuleLoader)
    {
        $this->localModuleLoader = $localModuleLoader;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        $options = [];
        $modules = $this->localModuleLoader->getList();

        foreach ($modules as $moduleName) {
            $options[] = [
                'value' => $moduleName,
                'label' => $moduleName
            ];
        }

        return $options;
    }
}
