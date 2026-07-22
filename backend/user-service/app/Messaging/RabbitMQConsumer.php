<?php

namespace App\Messaging;

use App\Actions\User\SyncUserAction;
use App\DTOs\User\CreateUserData;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

class RabbitMQConsumer
{
    public function __construct(
        protected RabbitMQConnection $connection,
        protected SyncUserAction $action,
    ) {}

    public function consume(): void
    {
        $channel = $this->connection->channel();

        /*
        |--------------------------------------------------------------------------
        | Exchange
        |--------------------------------------------------------------------------
        */
        $channel->exchange_declare(
            'user.events',
            'topic',
            false,
            true,
            false
        );

        /*
        |--------------------------------------------------------------------------
        | Queue
        |--------------------------------------------------------------------------
        */
        $channel->queue_declare(
            'user.created.queue',
            false,
            true,
            false,
            false
        );

        /*
        |--------------------------------------------------------------------------
        | Binding
        |--------------------------------------------------------------------------
        */
        $channel->queue_bind(
            'user.created.queue',
            'user.events',
            'user.created'
        );

        /*
        |--------------------------------------------------------------------------
        | Fair Dispatch
        |--------------------------------------------------------------------------
        */
        $channel->basic_qos(null, 1, null);

        echo "Waiting for user.created events...\n";

        $channel->basic_consume(
            queue: 'user.created.queue',
            consumer_tag: '',
            no_local: false,
            no_ack: false,
            exclusive: false,
            nowait: false,
            callback: function (AMQPMessage $message) {

                try {

                    $payload = json_decode(
                        $message->getBody(),
                        true,
                        JSON_THROW_ON_ERROR
                    );

                    $dto = new CreateUserData(
                        id: $payload['id'],
                        name: $payload['name'],
                        email: $payload['email'],
                        password: $payload['password'],
                    );
                    $this->action->execute($dto);

                    $message->ack();

                    echo "User Synced\n";
                } catch (Throwable $e) {

                    report($e);

                    $message->nack(false, true);

                    echo "Failed: {$e->getMessage()}\n";
                }
            }
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }
    }
}
