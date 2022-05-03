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

class BaseCmsInstallerDirOptions implements OptionSourceInterface
{
    private const BASE_IN_DESIGN = 0;

    private const BASE_PER_ALLOWED_MODULE = 1;

    /**
     * @return array|void
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::BASE_IN_DESIGN, 'label' => __('Design Directory')],
            ['value' => self::BASE_PER_ALLOWED_MODULE, 'label' => __('Allowed Module(s) Directory')]
        ];
    }
}
