<?php

namespace App\Commands;

use Clue\React\Redis\RedisClient;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\CLI\Commands;
use Psr\Log\LoggerInterface;
use React\EventLoop\Loop;

class SubscribeCoasterErrors extends BaseCommand
{
    protected $group       = 'Subscribers';
    protected $name        = 'subscribe:coaster_errors';
    protected $description = 'Subscribe to coaster errors.';

    private RedisClient $redisClient;

    public function __construct(LoggerInterface $logger, Commands $commands)
    {
        parent::__construct($logger, $commands);

        /** @var RedisClient $redisClient */
        $redisClient = service('redisClient');
        $this->redisClient = $redisClient;
    }

    public function run(array $params)
    {
        $loop = Loop::get();

        $this->redisClient->subscribe('coaster_errors')->then(function ($subscription) {
            CLI::write("Subscribed to 'coaster_errors' channel.\n");

            $this->redisClient->on('message', function ($channel, $data) {
                $data = json_decode($data);
                $message = lang('Messages.coaster.error_log', [
                        'dateTime' => $data->dateTime,
                        'coasterId' => $data->coasterId,
                        'message' => $data->message
                    ]);
                CLI::error($message);

                $this->logger->error($message);

            });
        });

        $loop->run();
    }
}