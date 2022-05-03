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

namespace PM4Life\CmsInstaller\Block\Adminhtml\Cms\Button;

use Magento\Backend\Block\Context;
use Magento\Framework\App\State;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ExportTemplate implements ButtonProviderInterface
{
    private Context $context;

    private State $appState;

    public function __construct(
        Context $context,
        State $appState
    ) {
        $this->context = $context;
        $this->appState = $appState;
    }

    /**
     * @inheritDoc
     */
    public function getButtonData(): array
    {
        if (in_array($this->appState->getMode(), [State::MODE_DEVELOPER, State::MODE_DEFAULT])) {
            [$entityId, $type] = $this->context->getRequest()->getParam('page_id')
                ? [$this->context->getRequest()->getParam('page_id'), 'page']
                : [$this->context->getRequest()->getParam('block_id'), 'block'];

            if (!empty($entityId)) {
                return [
                    'label' => __('Export Template'),
                    'class' => 'secondary',
                    'on_click' => sprintf(
                        "location.href = '%s';",
                        $this->context->getUrlBuilder()->getUrl(
                            'cms/template/export',
                            ['type' => $type, 'id' => $entityId]
                        )
                    ),
                    'sort_order' => 7,
                ];
            }
        }
        return [];
    }
}
