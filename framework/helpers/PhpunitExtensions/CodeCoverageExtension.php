<?php

declare(strict_types=1);

namespace DJWeb\Helpers\PhpunitExtensions;

use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\FinishedSubscriber;
use PHPUnit\Event\TestRunner\Finished as TestRunnerFinished;
use PHPUnit\Event\TestRunner\FinishedSubscriber as TestRunnerFinishedSubscriber;
use PHPUnit\Event\TestRunner\Started;
use PHPUnit\Event\TestRunner\StartedSubscriber;
use PHPUnit\Runner\CodeCoverage;
use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\CodeCoverageFilterRegistry;
use PHPUnit\TextUI\Configuration\Configuration;
use RuntimeException;
use SebastianBergmann\CodeCoverage\Report\Text as TextReport;
use SebastianBergmann\CodeCoverage\Report\Thresholds;

class CodeCoverageExtension implements Extension
{
    private $details = [];
    private $startTime;
    public function __construct(private string $outputFile = 'coverage_report.json')
    {
    }
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        //$params = ParameterCollection::fromArray(['my_file' => 'output file']);
        //$outputFile = $params->has('my_file') ? $params->get('my_file') : 'dupa';
        $facade->registerSubscriber(new class($this) implements StartedSubscriber {
            private $extension;
            public function __construct($extension)
            {
                $this->extension = $extension;
            }
            public function notify(Started $event): void
            {
                $this->extension->onStarted($event);
            }
        });
        $facade->registerSubscriber(new class($this) implements FinishedSubscriber {
            private $extension;
            public function __construct($extension)
            {
                $this->extension = $extension;
            }
            public function notify(Finished $event): void
            {
                $this->extension->onTestFinished($event);
            }
        });
        $facade->registerSubscriber(new class($this) implements TestRunnerFinishedSubscriber {
            private $extension;
            public function __construct($extension)
            {
                $this->extension = $extension;
            }
            public function notify(TestRunnerFinished $event): void
            {
                $this->extension->onTestRunnerFinished($event);
            }
        });
        $codeCoverageFilterRegistry = new CodeCoverageFilterRegistry();
        $codeCoverageFilterRegistry->init($configuration, true);
        $codeCoverage = CodeCoverage::instance();
        $codeCoverage->init($configuration, $codeCoverageFilterRegistry, true);
        if (! $codeCoverage->isActive()) {
            throw new RuntimeException('Code Coverage could not be activated.');
        }
    }

    public function onStarted(Started $event): void
    {
        $this->startTime = microtime(true);
    }

    public function onTestRunnerFinished(TestRunnerFinished $event): void
    {
        $this->printFinalReport();
    }

    public function onTestFinished(Finished $event): void
    {
        $this->details[] = [
            'class' => $event->test()->className(),
            'name' => $event->test()->name(),
            'time_ms' => $event->telemetryInfo()->durationSincePrevious()->nanoseconds() / 1000000,
        ];
    }

    private function printFinalReport(): void
    {
        $codeCoverage = CodeCoverage::instance();
        $executionTime = microtime(true) - $this->startTime;
        $result = [
            'execution_time' => round($executionTime, 2),
        ];
        $testResult = \PHPUnit\TestRunner\TestResult\Facade::result();
        $resultJsonData = [
            'tests' => $testResult->numberOfTestsRun(),
            'failed' => $testResult->numberOfTestFailedEvents(),
            'assertions' => $testResult->numberOfAssertions(),
            'errors' => $testResult->numberOfTestErroredEvents(),
            'warnings' => $testResult->numberOfWarnings(),
            'deprecations' => $testResult->numberOfDeprecations(),
            'notices' => $testResult->numberOfNotices(),
            'success' => $testResult->numberOfTestsRun() - $testResult->numberOfTestErroredEvents(),
            'incomplete' => $testResult->numberOfTestMarkedIncompleteEvents(),
            'risky' => $testResult->numberOfTestsWithTestConsideredRiskyEvents(),
            'skipped' => $testResult->numberOfTestSuiteSkippedEvents() + $testResult->numberOfTestSkippedEvents(),
        ];
        if ($codeCoverage->isActive()) {
            $processor = new TextReport(
                Thresholds::default(),
                true,
                false
            );
//            $report = $codeCoverage->codeCoverage()->getReport();
//            dd($report->numberOfTestedClassesAndTraits());
            $coverageReport = $processor->process($codeCoverage->codeCoverage());
            $result['code_coverage'] = $this->parseCoverageReport($codeCoverage->codeCoverage());
        } else {
            $result['code_coverage'] = 'Code coverage is not active.';
        }

        $result['tests'] = $resultJsonData;
        // $result['details'] = $this->details;

        file_put_contents($this->outputFile, json_encode($result, JSON_PRETTY_PRINT));
    }

    private function parseCoverageReport(\SebastianBergmann\CodeCoverage\CodeCoverage $coverage): array
    {
        $report = $coverage->getReport();
        return [
            'classes' => [
                'total' => $report->numberOfClassesAndTraits(),
                'tested' => $report->numberOfTestedClassesAndTraits(),
            ],
            'methods' => [
                'total' => $report->numberOfMethods(),
                'tested' => $report->numberOfTestedMethods(),
            ],
            'lines' => [
                'total' => $report->numberOfExecutableLines(),
                'tested' => $report->numberOfExecutedLines(),
            ],
        ];
//        $lines = explode("\n", $report);
//        $coverage = [];
//
//        foreach ($lines as $line) {
//            if (preg_match('/^\s*Methods:\s*(\d+\.\d+)%/', $line, $matches)) {
//                $coverage['lines_percent'] = (float) $matches[1];
//            } elseif (preg_match('/^\s*Functions:\s*(\d+\.\d+)%/', $line, $matches)) {
//                $coverage['functions_percent'] = (float) $matches[1];
//            } elseif (preg_match('/^\s*Classes:\s*(\d+\.\d+)%/', $line, $matches)) {
//                $coverage['classes_percent'] = (float) $matches[1];
//            }
//        }
//
//        return $coverage;
    }
}
