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

use PM4Life\CmsInstaller\Data\CreateTemplateExport;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PM4Life\CmsInstaller\Helper\Config as ConfigHelper;

class ExportCmsContent extends Command
{
    private const IDENTIFIER = 'identifier';

    private const TYPE = 'type';

    private ConfigHelper $configHelper;

    private CreateTemplateExport $createTemplateExport;

    public function __construct(
        ConfigHelper $configHelper,
        CreateTemplateExport $createTemplateExport,
        string $name = null
    ) {
        $this->configHelper = $configHelper;
        $this->createTemplateExport = $createTemplateExport;
        parent::__construct($name);
    }

    /**
     * Initialization of the command.
     */
    protected function configure()
    {
        $this->setName('cms:installer:export');
        $this->addOption(
            self::IDENTIFIER,
            '-i',
            InputOption::VALUE_REQUIRED,
            'Identifier of cms entity to export',
            ''
        );
        $this->addOption(
            self::TYPE,
            '-t',
            InputOption::VALUE_REQUIRED,
            'Type of cms entity (block or page)',
            ''
        );

        $this->setDescription('Extract desired cms block or page to .html template');
        parent::configure();
    }

    /**
     * CLI command description.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        if ($this->configHelper->isEnabled()) {

            $type = $input->getOption(self::TYPE);
            $identifiers = $input->getOption(self::IDENTIFIER)
                ? explode(',', $input->getOption(self::IDENTIFIER))
                : [];

            try {
                $this->createTemplateExport->export($type, $identifiers);
                $output->writeln('<info>Finished, check files in app/design/frontend/cms_install/ folder.</info>');
            } catch (\Exception $exception) {
                $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
            }

        } else {
            $output->writeln('<error>This feature is not enabled, please check in admin.</error>');
        }
    }
}
