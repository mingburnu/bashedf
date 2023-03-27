<?php

namespace App\Console\Commands;

use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use Illuminate\Console\Command;
use Workerman\Worker;

class GatewayWorkerServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gateway-worker:server {action} {--daemon}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a GatewayWorker Server.';

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

        $argv[0] = 'gateway-worker:server';
        $argv[1] = $action;
        $argv[2] = $this->option('daemon') ? '-d' : '';

        $this->start();
    }

    private function start()
    {
        $event = new class {
            public static function onWorkerStart(BusinessWorker $businessWorker)
            {
            }

            public static function onConnect($client_id)
            {
            }

            public static function onWebSocketConnect($client_id, $data)
            {
                var_dump($client_id);
                var_dump($data);
            }

            public static function onMessage($client_id, $message)
            {
                $data = json_decode($message, true) ?? [];

                echo $message . PHP_EOL;

                \GatewayWorker\Lib\Gateway::setSession($client_id, collect(\GatewayWorker\Lib\Gateway::getSession($client_id) ?? [])->merge($data)->toArray());
                \GatewayWorker\Lib\Gateway::sendToClient($client_id, 'connect!' . ' ' . now()->toDateTimeString());

                echo json_encode(\GatewayWorker\Lib\Gateway::getSession($client_id)) . PHP_EOL;
            }

            public static function onClose($client_id)
            {
                echo json_encode($_SESSION) . PHP_EOL;
                echo "$client_id disconnect!" . PHP_EOL;
            }
        };

        $ssl = [
            'local_cert' => config('ssl.cert'),
            'local_pk' => config('ssl.key'),
            'verify_peer' => false
        ];

        $gateway = new Gateway("websocket://0.0.0.0:23460", compact('ssl'));
        $gateway->transport = is_file(config('ssl.cert')) && is_file(config('ssl.key')) ? 'ssl' : 'tcp';
        $gateway->name = 'Gateway';
        $gateway->count = 1;

        $worker = new BusinessWorker();
        $worker->name = 'BusinessWorker';
        $worker->count = 1;
        $worker->eventHandler = get_class($event);

        new Register('text://0.0.0.0:1236');

        Worker::runAll();
    }

}