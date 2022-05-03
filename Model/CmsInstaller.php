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

namespace PM4Life\CmsInstaller\Model;

use Magento\Framework\Model\AbstractModel;
use PM4Life\CmsInstaller\Api\Data\CmsInstallerInterface;
use PM4Life\CmsInstaller\Model\ResourceModel\CmsInstaller as ResourceModel;

class CmsInstaller extends AbstractModel implements CmsInstallerInterface
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'cms_installer_model';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritDoc
     */
    public function getVersionId(): ?int
    {
        return (int) $this->getData(self::VERSION_ID);
    }

    /**
     * @inheritDoc
     */
    public function setOriginModule(string $originModule): CmsInstallerInterface
    {
        return $this->setData(self::ORIGIN_MODULE, $originModule);
    }

    /**
     * @inheritDoc
     */
    public function getOriginModule(): ?string
    {
        return $this->getData(self::ORIGIN_MODULE);
    }

    /**
     * @inheritDoc
     */
    public function setTemplateType(string $type): CmsInstallerInterface
    {
        return $this->setData(self::TEMPLATE_TYPE, $type);
    }

    /**
     * @inheritDoc
     */
    public function getTemplateType(): ?string
    {
        return $this->getData(self::TEMPLATE_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setIdentifier(string $identifier): CmsInstallerInterface
    {
        return $this->setData(self::IDENTIFIER, $identifier);
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier(): ?string
    {
        return $this->getData(self::IDENTIFIER);
    }

    /**
     * @inheritDoc
     */
    public function setVersionHash(string $versionHash): CmsInstallerInterface
    {
        return $this->setData(self::VERSION_HASH, $versionHash);
    }

    /**
     * @inheritDoc
     */
    public function getVersionHash(): ?string
    {
        return $this->getData(self::VERSION_HASH);
    }
}
