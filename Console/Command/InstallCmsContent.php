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

use PM4Life\CmsInstaller\Helper\Config as ConfigHelper;
use PM4Life\CmsInstaller\Data\ProcessModuleCmsInstaller;
use PM4Life\CmsInstaller\Sources\Filesystem\CollectInstallableDataInformation;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCmsContent extends Command
{
    private ConfigHelper $configHelper;

    private CollectInstallableDataInformation $collectInstallableDataInformation;

    private ProcessModuleCmsInstaller $processModuleCmsInstaller;

    public function __construct(
        ConfigHelper $configHelper,
        CollectInstallableDataInformation $collectInstallableDataInformation,
        ProcessModuleCmsInstaller $processModuleCmsInstaller,
        string $name = null
    ) {
        $this->configHelper = $configHelper;
        $this->collectInstallableDataInformation = $collectInstallableDataInformation;
        $this->processModuleCmsInstaller = $processModuleCmsInstaller;
        parent::__construct($name);
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('cms:installer:apply');
        $this->setDescription('Creates or updates any cms page or block for which there is defined .html file');
        parent::configure();
    }

    /**
     * CLI command description
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        if ($this->configHelper->isEnabled()) {
            $modulesCmsData = $this->collectInstallableDataInformation->execute();
            try {
                foreach ($modulesCmsData as $moduleCmsData) {
                    $this->processModuleCmsInstaller->execute($moduleCmsData);
                }
            } catch (\Exception $exception) {
                $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
            }
        } else {
            $output->writeln('<error>This feature is not enabled, please check in admin.</error>');
        }
    }
}
