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

namespace PM4Life\CmsInstaller\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Cms\Model\ResourceModel\Page\CollectionFactory as CmsPageCollectionFactory;
use Magento\CmsUrlRewrite\Model\CmsPageUrlRewriteGenerator;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\Cms\Api\Data\PageInterface;
use Psr\Log\LoggerInterface;

class RegenerateCmsPageUrls extends Command
{
    private CmsPageCollectionFactory $cmsPageCollectionFactory;

    private CmsPageUrlRewriteGenerator $cmsPageUrlRewriteGenerator;

    private UrlPersistInterface $urlPersist;

    private LoggerInterface $logger;

    public function __construct(
        CmsPageCollectionFactory $cmsPageCollectionFactory,
        CmsPageUrlRewriteGenerator $cmsPageUrlRewriteGenerator,
        UrlPersistInterface $urlPersist,
        LoggerInterface $logger,
        string $name = null
    ) {
        $this->cmsPageCollectionFactory = $cmsPageCollectionFactory;
        $this->cmsPageUrlRewriteGenerator = $cmsPageUrlRewriteGenerator;
        $this->urlPersist = $urlPersist;
        $this->logger = $logger;
        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('cms:installer:rebuild-url-rewrites');
        $this->setDescription('Performs rebuild of any applicable url rewrite for active cms pages');
        parent::configure();
    }

    /**
     * Rebuild cms pages rewrites
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $cmsPageCollection = $this->cmsPageCollectionFactory->create()
            ->addFieldToFilter(PageInterface::IS_ACTIVE, 1);

        $output->writeln(
            __('Started url rewrite rebuild, %pages pages found', ['pages' => $cmsPageCollection->count()])
        );

        foreach ($cmsPageCollection as $cmsPage) {
            $urls = $this->cmsPageUrlRewriteGenerator->generate($cmsPage);
            try {
                $this->urlPersist->deleteByData([
                    UrlRewrite::ENTITY_ID => $cmsPage->getId(),
                    UrlRewrite::ENTITY_TYPE => CmsPageUrlRewriteGenerator::ENTITY_TYPE,
                ]);
                $this->urlPersist->replace($urls);
            } catch (\Exception $exception) {
                $this->logger->warning($exception->getMessage());
                $output->writeln($exception->getMessage());
            }
        }

        $output->writeln(
            __('Cms page url rewrite rebuild complete')
        );
    }
}
