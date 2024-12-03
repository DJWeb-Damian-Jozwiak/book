<?php

declare(strict_types=1);

namespace DJWeb\Framework\WebSockets\Listeners;

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\WebSockets\EventDispatcher;
use DJWeb\Framework\WebSockets\ListenerContract;
use OpenAI;
use React\Socket\Connection;

class MessageListener implements ListenerContract
{
    public function listen(array $data, Connection $connection, EventDispatcher $dispatcher): void
    {
        $userMessage = $data['message'];
        $yourApiKey = Config::get('openai.api_key');
        $client = OpenAI::client($yourApiKey);
        $system = 'You are a friendly assistant who always answers questions briefly and concisely';
        $result = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $userMessage],
            ],
        ]);
        $content = $result->choices[0]->message->content;
        $dispatcher->send('message', $content, $connection);
    }
}
