<?php

declare(strict_types=1);

namespace KejawenLab\ApiSkeleton\Command;

use Cron\Cron;
use Cron\Report\ReportInterface;
use Cron\Resolver\ArrayResolver;
use Cron\Schedule\CrontabSchedule;
use KejawenLab\ApiSkeleton\Cron\CronBuilder;
use KejawenLab\ApiSkeleton\Cron\CronReportService;
use KejawenLab\ApiSkeleton\Cron\CronService;
use KejawenLab\ApiSkeleton\Cron\Executor;
use KejawenLab\ApiSkeleton\Cron\Model\CronInterface;
use KejawenLab\ApiSkeleton\Cron\Model\CronReportInterface;
use KejawenLab\ApiSkeleton\Cron\ShellJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Muhamad Surya Iksanudin<surya.kejawen@gmail.com>
 */
final class CronRunCommand extends Command
{
    private CronService $cronService;

    private CronReportService $reportService;

    private CronBuilder $builder;

    private Executor $executor;

    private string $reportClass;

    public function __construct(
        CronService $cronService,
        CronReportService $reportService,
        CronBuilder $builder,
        Executor $executor,
        string $reportClass
    ) {
        $this->cronService = $cronService;
        $this->reportService = $reportService;
        $this->builder = $builder;
        $this->executor = $executor;
        $this->reportClass = $reportClass;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('semart:cron:run')
            ->setDescription('Runs any currently schedule cron jobs')
            ->addArgument('job', InputArgument::OPTIONAL, 'Run only this job (if enabled)')
            ->addOption('force', null, InputOption::VALUE_NONE, 'Force schedule the current job.')
            ->addOption('schedule_now', null, InputOption::VALUE_NONE, 'Temporary set the job schedule to now.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cron = new Cron();
        $cron->setExecutor($this->executor);
        if ($input->getArgument('job')) {
            $resolver = $this->getResolver($input->getArgument('job'), false !== $input->getParameterOption('--force'), false !== $input->getParameterOption('--schedule_now'));
        } else {
            $resolver = $this->cronService;
        }

        $cron->setResolver($resolver);
        $time = microtime(true);
        /** @var ReportInterface $outputs */
        $outputs = $cron->run();

        while ($cron->isRunning()) {
        }

        $output->writeln(sprintf('time: %s', (microtime(true) - $time)));
        foreach ($outputs->getReports() as $value) {
            /** @var CronInterface $cron */
            $cron = $value->getJob()->getCron();
            $cron->setRunning(false);

            $this->cronService->save($cron);

            /** @var CronReportInterface $report */
            $report = new $this->reportClass();
            $report->setCron($cron);
            $report->setOutput(implode("\n", (array) $value->getOutput()));
            $report->setExitCode($value->getJob()->getProcess()->getExitCode());
            $report->setRunAt(\DateTime::createFromFormat('U.u', number_format($value->getStartTime(), 6, '.', '')));
            $report->setRunTime($value->getEndTime() - $value->getStartTime());

            $this->reportService->save($report);
        }

        return 0;
    }

    private function getResolver(string $id, $force = false, $schedule_now = false)
    {
        $cron = $this->cronService->get($id);
        if (!$cron) {
            throw new \InvalidArgumentException('Unknown job.');
        }

        if (!$cron->isEnabled() && !$force) {
            throw new \InvalidArgumentException('Job is disabled, run with --force to force schedule it.');
        }

        $job = new ShellJob($cron);
        $job->setCommand($this->builder->build($cron));
        $job->setSchedule(new CrontabSchedule(!$schedule_now ? $cron->getSchedule() : '* * * * *'));

        $resolver = new ArrayResolver();
        $resolver->addJob($job);

        return $resolver;
    }
}
