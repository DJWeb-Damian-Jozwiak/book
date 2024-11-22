<?php

declare(strict_types=1);

namespace Mail;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\Web\Application;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mime\Email;
use Tests\BaseTestCase;
use Tests\Helpers\ExampleMailable;

class MailableTest extends BaseTestCase
{
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
        $app->bind('base_path', dirname(__DIR__));
        $config = $this->createMock(ConfigContract::class);
        $config->expects($this->any())->method('get')->willReturn($returnedConfig);
        $app->set(ConfigContract::class, $config);
        $mail = new ExampleMailable();
        $build = $mail->build();
        $this->assertInstanceOf(Email::class, $build);
    }
}