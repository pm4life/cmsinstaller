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

namespace PM4Life\CmsInstaller\Sources\Template;

interface VariablesInterface
{
    const MODULE_NAME = 'module_name';
    const CMS_DATA = 'cms_data';

    const TEMPLATE_TYPE = 'template_type';

    const TYPE_BLOCK = 'block';
    const TYPE_PAGE = 'page';

    const IS_ACTIVE = 'is_active';
    const TITLE = 'title';
    const IDENTIFIER = 'identifier';
    const CONTENT_HEADING = 'content_heading';
    const CONTENT = 'content';
    const META_TITLE = 'meta_title';
    const META_KEYWORDS = 'meta_keywords';
    const META_DESCRIPTION = 'meta_description';
    const STORES = 'store_id';
    const LAYOUT_UPDATE = 'layout_update_xml';
    const CUSTOM_THEME = 'custom_theme';
    const CUSTOM_ROOT_TEMPLATE = 'custom_root_template';
    const CUSTOM_LAYOUT_UPDATE_XML = 'custom_layout_update_xml';
    const PAGE_LAYOUT = 'page_layout';
    const LAYOUT_UPDATE_SELECTED = 'layout_update_selected';
    const CUSTOM_THEME_FROM = 'custom_theme_from';
    const CUSTOM_THEME_TO = 'custom_theme_to';
    const WEBSITE_ROOT = 'website_root';
}
