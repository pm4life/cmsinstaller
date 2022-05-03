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

namespace PM4Life\CmsInstaller\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    private const BASE_DESIGN_DIR = '/app/design/frontend/';

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag('cms/installer/is_enabled');
    }

    /**
     * Base determines directory where .html templates will be stored
     * default location is app/design/frontend/cms_install
     *
     * @return bool
     */
    public function getBase(): bool
    {
        return (bool) $this->scopeConfig->getValue('cms/installer/base');
    }

    /**
     * Base design directory to contain install templates
     *
     * @return string
     */
    public function getDesignDir(): string
    {
        return self::BASE_DESIGN_DIR;
    }

    /**
     * Get array with modules that are enabled to use cms_install directory for .html installer templates
     *
     * @return array
     */
    public function getAllowedModules(): array
    {
        return explode(',', $this->scopeConfig->getValue('cms/installer/allowed_modules') ?? '');
    }
}
