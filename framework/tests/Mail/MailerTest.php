<?php

declare(strict_types=1);

namespace Tests\Mail;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use DJWeb\Framework\Mail\Mailer;
use DJWeb\Framework\Mail\MailHistoryLogger;
use DJWeb\Framework\Mail\MailHistoryLoggerContract;
use DJWeb\Framework\Web\Application;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Mailer\MailerInterface;
use Tests\BaseTestCase;
use Tests\Helpers\ExampleMailable;

class MailerTest extends BaseTestCase
{
    private string $dsn = 'smtp://test:test@localhost:25';
    private MockObject $symfonyMailerMock;
    private MockObject $loggerMock;
    private ExampleMailable $email;
    private Mailer $mailer;

    protected function setUp(): void
    {
        $this->symfonyMailerMock = $this->createMock(MailerInterface::class);

        $this->loggerMock = $this->createMock(MailHistoryLoggerContract::class);

        $this->email = new ExampleMailable();
        $this->mailer = new Mailer($this->dsn, $this->symfonyMailerMock, $this->loggerMock);

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
    }

    public function testSuccessfulMailSending(): void
    {
        $this->symfonyMailerMock
            ->expects($this->once())
            ->method('send')
            ->with($this->email->build());

        $this->mailer->send($this->email);
    }

    public function testFailedMailSending(): void
    {
        $errorMessage = 'SMTP connection failed';

        $this->symfonyMailerMock
            ->expects($this->once())
            ->method('send')
            ->with($this->email->build())
            ->willThrowException(new \Exception($errorMessage));

        $this->loggerMock
            ->expects($this->once())
            ->method('logMail')
            ->with(
                $this->email->build(),
                'failed',
                $errorMessage
            );

        $this->mailer->send($this->email);
    }
}