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

use Magento\Framework\Exception\CouldNotDeleteException;
use PM4Life\CmsInstaller\Model\CmsInstallerFactory;
use PM4Life\CmsInstaller\Model\ResourceModel\CmsInstaller as CmsInstallerResource;

class DeleteByVersionHash
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
     * @param string $versionHash
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function execute(string $versionHash): bool
    {
        $cmsInstaller = $this->cmsInstallerFactory->create();
        $this->cmsInstallerResource->load($cmsInstaller, $versionHash, 'version_hash');

        if ($cmsInstaller->getVersionId()) {
            try {
                $this->cmsInstallerResource->delete($cmsInstaller);
                return true;
            } catch (\Exception $exception) {
                throw new CouldNotDeleteException(
                    __(
                        'Could not delete cms installer module: %module, identifier: %identifier',
                        [
                            'module' => $cmsInstaller->getOriginModule(),
                            'identifier' => $cmsInstaller->getIdentifier()
                        ]
                    )
                );
            }
        }

        return false;
    }
}
