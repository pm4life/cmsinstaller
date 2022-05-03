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

namespace PM4Life\CmsInstaller\Model\Command\CmsInstaller;

use PM4Life\CmsInstaller\Model\CmsInstaller;
use PM4Life\CmsInstaller\Model\CmsInstallerFactory;
use PM4Life\CmsInstaller\Model\ResourceModel\CmsInstaller as CmsInstallerResource;

class GetByVersionHash
{
    private CmsInstallerFactory $cmsInstallerFactory;

    private CmsInstallerResource $cmsInstallerResource;

    public function __construct(
        CmsInstallerFactory $cmsInstallerFactory,
        CmsInstallerResource $cmsInstaller
    ) {
        $this->cmsInstallerFactory = $cmsInstallerFactory;
        $this->cmsInstallerResource = $cmsInstaller;
    }

    /**
     * Load CmsInstaller by version hash id
     *
     * @param string $versionHash
     * @return CmsInstaller
     */
    public function execute(string $versionHash): CmsInstaller
    {
        $cmsInstaller = $this->cmsInstallerFactory->create();
        $this->cmsInstallerResource->load($cmsInstaller, $versionHash, 'version_hash');

        return $cmsInstaller;
    }
}
