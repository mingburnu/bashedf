<?php

namespace App\Console\Commands;

use App\Entities\Payment;
use Channel\Client;
use Channel\Server;
use Illuminate\Console\Command;
use Workerman\Connection\TcpConnection;
use Workerman\Timer;
use Workerman\Worker;

class ChannelWorkerServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'channel-worker:server {action} {--daemon}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a ChannelWorker Server.';

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        global $argv;

        if (!in_array($action = $this->argument('action'), ['start', 'stop', 'restart'])) {
            $this->error('Error Arguments');
            exit;
        }

        $argv[0] = 'channel-worker:server';
        $argv[1] = $action;
        $argv[2] = $this->option('daemon') ? '-d' : '';

        $this->start();
    }

    private function start()
    {
        $ws_worker = new Worker("websocket://0.0.0.0:2346");
        $ws_worker->name = 'Payment channel';
        $ws_worker->count = 1;

        $ws_worker->onWorkerStart = function (Worker $worker) {
            echo "Worker opened\n";

            Client::connect();
            Client::on('broadcast', function (string $event_data) use ($worker) {
                collect($worker->connections)->shuffle()->shuffle()->shuffle()
                    ->each(function (TcpConnection $connection) use ($event_data) {
                        echo $connection->getRemoteAddress() . ' ' . date('Y-m-d H:i:s') . PHP_EOL;
                        $connection->send($event_data);
                        return false;
                    });
            });

            Timer::add(5, function () use ($worker) {
                $p = Payment::whereStatus(0)->inRandomOrder()->first();
                if (!is_null($p)) {
                    Client::publish('broadcast', $p->toJson());
                }
            });
        };

        $ws_worker->onConnect = function (TcpConnection $connection) {
            echo $connection->getRemoteAddress() . ' connect' . PHP_EOL;
        };

        $ws_worker->onClose = function (TcpConnection $connection) {
            echo $connection->getRemoteAddress() . " closed\n";
        };

        $ws_worker->onMessage = function (TcpConnection $connection, $message) {
            echo $connection->getRemoteAddress() . ' : ' . $message . PHP_EOL;
        };

        new Server();

        Worker::runAll();
    }

}