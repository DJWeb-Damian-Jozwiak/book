<?php

declare(strict_types=1);

namespace DJWeb\Helpers\Formatters;

use NunoMaduro\PhpInsights\Application\Console\Contracts\Formatter;
use NunoMaduro\PhpInsights\Domain\Insights\InsightCollection;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Classes as ArchitectureClasses;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Files;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Globally as ArchitectureGlobally;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Interfaces as ArchitectureInterfaces;
use NunoMaduro\PhpInsights\Domain\Metrics\Architecture\Traits as ArchitectureTraits;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Classes;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Code;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Comments;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Functions;
use NunoMaduro\PhpInsights\Domain\Metrics\Code\Globally;
use NunoMaduro\PhpInsights\Domain\Metrics\Complexity\Complexity;
use NunoMaduro\PhpInsights\Domain\Results;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FullJsonFormatter implements Formatter
{
    private const CODE_METRIC_CLASSES = [
        Comments::class,
        Classes::class,
        Functions::class,
        Globally::class,
    ];
    private const ARCHITECTURE_METRIC_CLASSES = [
        ArchitectureClasses::class,
        ArchitectureInterfaces::class,
        ArchitectureGlobally::class,
        ArchitectureTraits::class,
    ];
    public function __construct(
    protected InputInterface $input,
    protected OutputInterface $output,
) {
    }

    public function format(InsightCollection $insightCollection, array $metrics): void
    {
        $results = $insightCollection->results();
        $data = [
            ...$this->summary($results),
            ...$this->complexity($insightCollection),
            ...$this->code($insightCollection, $results),
            ...$this->architecture($insightCollection, $results),
        ];
        echo json_encode($data, JSON_PRETTY_PRINT);
//        dd($data);
    }
    public function summary(Results $results): array
    {
        return [
            'summary' => [
                'date' => date('Y-m-d H:i:s'),
                'quality' => $results->getCodeQuality(),
                'complexity' => $results->getComplexity(),
                'structure' => $results->getStructure(),
                'style' => $results->getStyle(),
            ],
        ];
    }
    private function code(InsightCollection $insightCollection, Results $results): array
    {
        $details = [];
        foreach (self::CODE_METRIC_CLASSES as $metric) {
            $name = explode('\\', $metric);
            $details[end($name)] = round(new $metric()->getPercentage($insightCollection->getCollector()));
        }

        return [
            'code' => [
                'lines' => new Code()->getValue($insightCollection->getCollector()),
                'details' => $details,
            ],
        ];
    }

    private function complexity(InsightCollection $insightCollection): array
    {
        return [
            'complexity' => new Complexity()->getAvg($insightCollection->getCollector()),
        ];
    }

    private function architecture(InsightCollection $insightCollection, Results $results): array
    {
        $details = [];
        foreach (self::ARCHITECTURE_METRIC_CLASSES as $metric) {
            $name = explode('\\', $metric);
            $details[end($name)] = round(new $metric()->getPercentage($insightCollection->getCollector()));
        }

        return [
            'architecture' => [
                'files' => new Files()->getValue($insightCollection->getCollector()),
                'details' => $details,
            ],
        ];
    }
}
