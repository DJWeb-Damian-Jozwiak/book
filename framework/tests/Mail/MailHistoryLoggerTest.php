<?php

declare(strict_types=1);

namespace Tests\Mail;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\DBAL\Contracts\Query\InsertQueryBuilderContract;
use DJWeb\Framework\Mail\MailHistoryLogger;
use DJWeb\Framework\Web\Application;
use PDOStatement;
use Tests\BaseTestCase;
use Tests\Helpers\ExampleMailable;

class MailHistoryLoggerTest extends BaseTestCase
{
    public function testLog()
    {
        $returnedConfig = [
            'paths' => [
                'template_path' => __DIR__ . '/../resources/views/blade',
                'cache_path' => __DIR__ . '/../storage/cache/blade',
            ],
            'components' => [
                'namespace' => '\\Tests\\Helpers\\View\\Components\\',
            ]
        ];
        $app = Application::getInstance();
        $config = $this->createMock(ConfigContract::class);
        $app->set(ConfigContract::class, $config);
        $config->expects($this->any())->method('get')->willReturn($returnedConfig);
        $builder = $this->createMock(InsertQueryBuilderContract::class);
        $stmt = $this->createMock(PDOStatement::class);
        $builder->expects($this->once())->method('table')->willReturnSelf();
        $builder->expects($this->once())->method('values')->willReturnSelf();
        $builder->expects($this->once())->method('execute')->willReturn($stmt);
        $builder->expects($this->once())->method('getInsertId')->willReturn('1');
        $app->set(InsertQueryBuilderContract::class, $builder);

        $email = new ExampleMailable();
        new MailHistoryLogger()->logMail($email->build(), 'success');
    }
}