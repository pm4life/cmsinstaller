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

namespace PM4Life\CmsInstaller\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CmsInstaller extends AbstractDb
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'cms_installer_resource_model';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('cms_installer', 'version_id');
        $this->_useIsObjectNew = true;
    }
}
