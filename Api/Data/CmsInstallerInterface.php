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

namespace PM4Life\CmsInstaller\Api\Data;

interface CmsInstallerInterface
{
    public const FILE_EXTENSION_PATTERN = '*.html';

    public const INSTALL_DIR = 'cms_install';

    public const VERSION_ID = 'version_id';
    public const ORIGIN_MODULE = 'origin_module';
    public const TEMPLATE_TYPE = 'template_type';
    public const IDENTIFIER = 'identifier';
    public const VERSION_HASH = 'version_hash';

    /**
     * Get version id
     *
     * @return int|null
     */
    public function getVersionId(): ?int;

    /**
     * Set origin module of cms block/page
     *
     * @param string $originModule
     * @return $this
     */
    public function setOriginModule(string $originModule): self;

    /**
     * Get origin module
     *
     * @return string|null
     */
    public function getOriginModule(): ?string;

    /**
     * Set cms content type (block, page, maybe something else some day...)
     *
     * @param string $type
     * @return $this
     */
    public function setTemplateType(string $type): self;

    /**
     * Get cms content type
     *
     * @return string|null
     */
    public function getTemplateType(): ?string;

    /**
     * Set content identifier
     *
     * @param string $identifier
     * @return $this
     */
    public function setIdentifier(string $identifier): self;

    /**
     * Get content identifier
     *
     * @return string|null
     */
    public function getIdentifier(): ?string;

    /**
     * Set version hash
     *
     * @param string $versionHash
     * @return $this
     */
    public function setVersionHash(string $versionHash): self;

    /**
     * Get version hash
     *
     * @return string|null
     */
    public function getVersionHash(): ?string;
}
