<?php

namespace L91\Bundle\SeoBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to crawl a specific url.
 */
class CrawlCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('l91:seo:crawl')
            ->setDescription('Will crawl a specific url')
            ->addArgument('url', InputArgument::REQUIRED, 'Add a valid url e.g. https://example.org')
            ->addOption('depth', 'd', InputOption::VALUE_REQUIRED, 'How depth it should crawl for urls')
            ->addOption('external', 'x', null, 'Check http status code of external urls');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        return $this->getContainer()->get('l91_seo.crawler.crawler')->crawl(
            $input->getArgument('url'),
            (int) $input->getOption('depth'),
            (bool) $input->getOption('external')
        );
    }
}
