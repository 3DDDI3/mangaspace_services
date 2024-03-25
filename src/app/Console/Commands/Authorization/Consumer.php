<?php

namespace App\Console\Commands\Authorization;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class Consumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consumer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $connection = new AMQPStreamConnection(config('rabbitmq.host'), config('rabbitmq.port'), config('rabbitmq.user'), config('rabbitmq.password'));
        $channel = $connection->channel();
        echo " [*] Waiting for messages. To exit press CTRL+C\n";
        // $channel->exchange_declare('auth', 'direct');
        // $channel->queue_declare('request', false, false, false, false);
        // $channel->queue_declare('response', false, false, false, false);
        // $channel->queue_bind('request', 'auth', 'request', );
        // $channel->queue_bind('request', 'auth', 'register');
        // $channel->queue_bind('response', 'auth', 'response');

        // echo " [*] Waiting for messages. To exit press CTRL+C\n";

        // $callback = function (AMQPMessage $msg) use ($channel) {
        //     echo ' [x] Received ', var_dump($msg->get('application_headers')->getNativeData()), "\n";
        //     $channel->basic_consume('response', 'amq.direct', false, false, false, false, function ($message) {
        //         echo ' [x] Received ', $message->body, "\n";
        //     });
        // };

        // $channel->basic_consume('request', 'auth', false, false, false, false, $callback);



        // try {
        //     $channel->consume();
        // } catch (\Throwable $exception) {
        //     echo $exception->getMessage();
        // }

        $channel->queue_declare('result_queue', false, true, false, false);
        $channel->queue_declare('another_queue', false, true, false, false);

        // Установка коллбэка для обработки сообщений из 'result_queue'
        $channel->basic_consume('result_queue', '', false, false, false, false, function ($msg) use ($channel) {
            $headers = $msg->get('application_headers')->getNativeData();
            

            // $msg = new AMQPMessage('Hello World!');
            // $headers = new AMQPTable(array('key' => '213'));
            // $msg->set('application_headers', $headers);
            // $channel->basic_publish($msg, '', 'another_queue');

            // $channel->basic_publish(new AMQPMessage('heko'), '', 'another_queue');
            // Обработка сообщения
            // ...
            // Прослушивание другой очереди 'another_queue'
            $channel->basic_consume('another_queue', '', false, false, false, false, function ($msg) use ($channel) {
                echo ' [x] Received from another_queue: ', $msg->body, "\n";
                // echo $msg->get('application_headers')->getNativeData()['key'];
                echo $msg->delivery_info['delivery_tag'];
                // Обработка сообщения из другой очереди
                // ...
            });
        });

        while (count($channel->callbacks)) {
            $channel->wait();
        }

        // Закрытие канала и соединения
        $channel->close();
        $connection->close();

        return Command::SUCCESS;
    }
}
