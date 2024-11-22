<?php

declare(strict_types=1);

namespace Tests\Mail;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\DBAL\Contracts\Query\SelectQueryBuilderContract;
use DJWeb\Framework\Mail\Content;
use DJWeb\Framework\Web\Application;
use Tests\BaseTestCase;

class ContentTest extends BaseTestCase
{
    public function setUp(): void
    {
        Application::withInstance(null);
    }
    public function testBuild()
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
        $content = new Content('mail/test.blade.php', ['name' => 'test']);
        $this->assertEquals('test email test', $content->render());
    }
}