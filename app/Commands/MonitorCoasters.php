<?php

namespace App\Commands;

use App\Models\Coaster;
use Clue\React\Redis\RedisClient;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\CLI\Commands;
use Psr\Log\LoggerInterface;
use React\EventLoop\Loop;
use React\Promise\PromiseInterface;
use function React\Promise\all;

class MonitorCoasters extends BaseCommand
{
    protected $group       = 'Monitoring';
    protected $name        = 'monitor:coasters';
    protected $description = 'Monitor coaster stats in realtime.';

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

        // Regularne pobieranie statystyk co 2 sekundy
        $loop->addPeriodicTimer(2, function () {
            $dateTime = new \DateTimeImmutable();

            $this->fetchCoasters()->then(function ($stats) use ($dateTime) {
                CLI::write(lang('Messages.time', ['time' => $dateTime->format('H:i')]), 'green');
                CLI::write("------------------\n");

                /**
                 * @var Coaster $coasterModel
                 */
                foreach ($stats as $coasterModel) {
                    CLI::write(lang('Messages.coaster.header',
                        ['name' => $coasterModel->uuid])
                    );
                    CLI::write(lang('Messages.coaster.opening_hours',
                        ['from' => $coasterModel->hours_from, 'to' => $coasterModel->hours_to])
                    );
                    CLI::write(lang('Messages.coaster.wagons',
                        ['wagons' => $coasterModel->getWagons()->count(), 'expected' => $coasterModel->getExpectedNumberOfWagons() ])
                    );
                    CLI::write(lang('Messages.coaster.staff',
                        ['staff' => $coasterModel->number_of_staff, 'required_staff' => $coasterModel->getRequiredStaff()])
                    );
                    CLI::write(lang('Messages.coaster.clients', ['clients' => $coasterModel->number_of_clients]));
                    $coasterStatus = $coasterModel->getStatus();
                    if($coasterStatus->isOk()) {
                        CLI::write($coasterStatus->getOkMessage(), 'green');
                    } else {
                        CLI::error($coasterStatus->getErrorMessage());
                        $this->logger->debug($coasterStatus->getErrorMessage());
                        $this->redisClient->publish('coaster_errors', json_encode([
                            'dateTime' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                            'coasterId' => $coasterModel->uuid,
                            'message' => $coasterStatus->getErrorMessage(),
                        ]));
                    }
                    CLI::write("------------------\n");
                }
            }, function ($error) {
                CLI::error("Błąd podczas pobierania statystyk: $error");
            });
        });

        $loop->run();
    }

    private function fetchCoasters(): PromiseInterface
    {
        return $this->redisClient->keys('coasters_*')->then(function ($keys) {
            $stats = [];
            $promises = [];
            foreach ($keys as $key) {
                $promises[] = $this->redisClient->get($key)->then(function ($value) use (&$stats, $key) {
                    $stats[$key]  = Coaster::fromSerialized(unserialize($value));
                });
            }

            return all($promises)->then(function () use (&$stats) {
                return $stats;
            });
        });
    }
}