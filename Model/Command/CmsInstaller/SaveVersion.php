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

use Magento\Framework\Exception\CouldNotSaveException;
use PM4Life\CmsInstaller\Model\CmsInstaller;
use PM4Life\CmsInstaller\Model\CmsInstallerFactory;
use PM4Life\CmsInstaller\Model\ResourceModel\CmsInstaller as CmsInstallerResource;
use Psr\Log\LoggerInterface;

class SaveVersion
{
    private CmsInstallerFactory $cmsInstallerFactory;

    private CmsInstallerResource $cmsInstallerResource;

    private LoggerInterface $logger;

    public function __construct(
        CmsInstallerFactory $cmsInstallerFactory,
        CmsInstallerResource $cmsInstaller,
        LoggerInterface $logger
    ) {
        $this->cmsInstallerFactory = $cmsInstallerFactory;
        $this->cmsInstallerResource = $cmsInstaller;
        $this->logger = $logger;
    }

    /**
     * Save cms installer
     *
     * @param CmsInstaller $cmsInstaller
     * @return CmsInstaller
     * @throws CouldNotSaveException
     */
    public function execute(CmsInstaller $cmsInstaller): CmsInstaller
    {
        try {
            $cmsInstallerModel = $this->cmsInstallerFactory->create();
            $cmsInstallerModel->addData($cmsInstaller->getData());
            $cmsInstallerModel->hasDataChanges(true);

            if (!$cmsInstallerModel->getVersionId()) {
                $cmsInstallerModel->isObjectNew(true);
            }

            $this->cmsInstallerResource->save($cmsInstaller);
            return $cmsInstaller;
        } catch (\Exception $exception) {

            $this->logger->error(
                __(
                    'Could not save cms installer, message: %message',
                    [
                        'message' => $exception->getMessage(),
                        'exception' => $exception
                    ]
                )
            );

            throw new CouldNotSaveException(
                __(
                    'Could not save cms installer, origin module: %module, identifier: %identifier',
                    [
                        'module' => $cmsInstaller->getOriginModule(),
                        'identifier' => $cmsInstaller->getIdentifier()
                    ]
                )
            );
        }
    }
}
