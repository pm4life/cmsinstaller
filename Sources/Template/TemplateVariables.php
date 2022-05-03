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

use Magento\Framework\Exception\InvalidArgumentException;
use Magento\Framework\Exception\LocalizedException;

class TemplateVariables implements VariablesInterface
{
    public const VARIABLES_BLOCK_START = 'f216878f6f91a56593fbc6017a3eced4';

    public const VARIABLES_BLOCK_END = 'be2d6ad322374001d09b50693d4add18';

    private ?array $templateVariables;

    private ?string $templateContent;

    private ?array $cmsTemplateRequiredVariables;

    public function __construct(
        array $cmsTemplateRequiredVariables
    ) {
        $this->cmsTemplateRequiredVariables = $cmsTemplateRequiredVariables;
    }

    private const SUPPORTED_TYPES = [
        self::TYPE_BLOCK,
        self::TYPE_PAGE
    ];

    private const CMS_BLOCK_VARIABLES = [
        self::TEMPLATE_TYPE,
        self::IS_ACTIVE,
        self::TITLE,
        self::STORES
    ];

    private const CMS_PAGE_VARIABLES = [
        self::TEMPLATE_TYPE,
        self::IS_ACTIVE,
        self::TITLE,
        self::CONTENT_HEADING,
        self::META_TITLE,
        self::META_KEYWORDS,
        self::META_DESCRIPTION,
        self::STORES,
        self::LAYOUT_UPDATE,
        self::CUSTOM_THEME,
        self::CUSTOM_ROOT_TEMPLATE,
        self::CUSTOM_LAYOUT_UPDATE_XML,
        self::PAGE_LAYOUT,
        self::LAYOUT_UPDATE_SELECTED,
        self::CUSTOM_THEME_FROM,
        self::CUSTOM_THEME_TO,
        self::WEBSITE_ROOT
    ];

    /**
     * Get cms block variables
     *
     * @return array
     */
    public function getCmsBlockVariables(): array
    {
        return self::CMS_BLOCK_VARIABLES;
    }

    /**
     * Get cms page variables
     *
     * @return array
     */
    public function getCmsPageVariables(): array
    {
        return self::CMS_PAGE_VARIABLES;
    }

    /**
     * @param string $fileName
     * @param string $fileContent
     * @return void
     * @throws InvalidArgumentException
     */
    public function init(string $fileName, string $fileContent)
    {
        $templateVariables = $this->getTemplateVariables($fileName, $fileContent);
        $templateContent = $this->getTemplateContent($fileName, $fileContent);
        $this->validate($templateVariables);

        $this->templateVariables = $templateVariables;
        $this->templateContent = $templateContent;
    }

    /**
     * Return initialized template data
     *
     * @return array
     * @throws LocalizedException
     */
    public function getTemplateData(): array
    {
        if (!$this->templateVariables) {
            throw new LocalizedException(__('Template not initialized.'));
        }

        // used just to generate unique hash, not for cryptography
        // phpcs:ignore Magento2.Security.InsecureFunction
        $uniqueContentAndVariableHash = md5(
            json_encode($this->templateVariables) . $this->templateContent
        );

        return [$this->templateVariables, $this->templateContent, $uniqueContentAndVariableHash];
    }

    /**
     * Create variables block for template export
     *
     * @param string $type
     * @param array $variables
     * @return string
     */
    public function createExportVariablesBlock(string $type, array $variables): string
    {
        $typeVariables = $type === VariablesInterface::TYPE_PAGE
            ? $this->getCmsPageVariables()
            : $this->getCmsBlockVariables();

        $data = [];
        $variablesBlock = self::VARIABLES_BLOCK_START . "\n";
        foreach ($typeVariables as $key) {
            if (isset($variables[$key]) && !empty($variables[$key])) {
                $data[$key] = $variables[$key];
            }
        }
        $data[VariablesInterface::TEMPLATE_TYPE] = $type;

        $variablesBlock .= json_encode($data, JSON_PRETTY_PRINT) . "\n";
        $variablesBlock .= self::VARIABLES_BLOCK_END . "\n";

        return $variablesBlock;
    }

    /**
     * Get template name
     *
     * @param array $data
     * @return string|null
     */
    public function getTemplateName(array $data): ?string
    {
        if (isset($data[self::IDENTIFIER])) {
            return $data[self::IDENTIFIER] . '.html';
        }
        return null;
    }

    /**
     * Get template content
     *
     * @param array $data
     * @return string|null
     */
    public function getContentData(array $data): ?string
    {
        if (isset($data[self::CONTENT])) {
            return $data[self::CONTENT];
        }
        return null;
    }

    /**
     * Get template variables array
     *
     * @param string $fileName
     * @param string $fileContent
     * @return array
     * @throws InvalidArgumentException
     */
    private function getTemplateVariables(string $fileName, string $fileContent): array
    {
        $regex = sprintf(
            '/(?:%s)(.+?)(?:%s)/s',
            TemplateVariables::VARIABLES_BLOCK_START,
            TemplateVariables::VARIABLES_BLOCK_END
        );

        preg_match($regex, $fileContent, $matches);
        $variablesBlock = $matches[1] ?? false;

        if ($variablesBlock === false) {
            throw new InvalidArgumentException(
                __(
                    'No variables block found in: %file',
                    ['file' => $fileName]
                )
            );
        }

        try {
            $templateVariables =
                json_decode(
                    $variablesBlock,
                    true,
                    6,
                    JSON_THROW_ON_ERROR
                );
        } catch (\Exception $exception) {
            throw new InvalidArgumentException(
                __(
                    'Template variable json decode failed: %message in template: %template',
                    ['message' => $exception->getMessage(), 'template' => $fileName]
                )
            );
        }

        return $templateVariables;
    }

    /**
     * Get template content
     *
     * @param string $fileName
     * @param string $fileContent
     * @return string
     * @throws InvalidArgumentException
     */
    private function getTemplateContent(string $fileName, string $fileContent): string
    {
        $regex = sprintf(
            '/(?:%s)(.+?)($)/s',
            TemplateVariables::VARIABLES_BLOCK_END
        );

        preg_match($regex, $fileContent, $matches);
        $cmsEntityContent = $matches[1] ?? false;

        if ($cmsEntityContent === false) {
            throw new InvalidArgumentException(
                __(
                    'Cms entity content is empty, please check file %file',
                    ['file' => $fileName]
                )
            );
        }

        return trim($cmsEntityContent);
    }

    /**
     * Validate variables for template
     *
     * @param array $templateVars
     * @return void
     * @throws InvalidArgumentException
     */
    private function validate(array $templateVars): void
    {
        $templateVarsKeys = array_keys($templateVars);

        if (!isset($templateVars[self::TEMPLATE_TYPE])) {
            throw new InvalidArgumentException(__('%field has to be set.', ['field' => self::TEMPLATE_TYPE]));
        }

        if (!in_array($templateVars[self::TEMPLATE_TYPE], self::SUPPORTED_TYPES)) {
            throw new InvalidArgumentException(
                __('Unknown cms entity template type "%type".', ['type' => $templateVars[self::TEMPLATE_TYPE]])
            );
        }

        $missingRequiredVariables = array_diff(
            $this->cmsTemplateRequiredVariables[$templateVars[self::TEMPLATE_TYPE]],
            $templateVarsKeys
        );

        if (!empty($missingRequiredVariables)) {
            throw new InvalidArgumentException(
                __(
                    'Missing required values: %values for cms %type type.',
                    [
                        'values' => implode(',', $missingRequiredVariables),
                        'type' => $templateVars[self::TEMPLATE_TYPE]
                    ]
                )
            );
        }

        foreach ($templateVars as $key => $value) {

            $allowedVariables = $templateVars[self::TEMPLATE_TYPE] === self::TYPE_BLOCK
                ? $this->getCmsBlockVariables()
                : $this->getCmsPageVariables();

            if (!in_array($key, $allowedVariables)) {
                throw new InvalidArgumentException(
                    __(
                        'Variable "%variable" is not allowed for template type: %type',
                        ['variable' => $key, 'type' => $templateVars[self::TEMPLATE_TYPE]]
                    )
                );
            }

            if (empty($value)) {
                throw new InvalidArgumentException(
                    __(
                        'Value for template variable: %variable cannot be empty',
                        ['variable' => $key]
                    )
                );
            }
        }
    }
}
