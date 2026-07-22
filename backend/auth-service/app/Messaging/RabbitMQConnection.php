<?php

namespace App\Messaging;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Channel\AMQPChannel;

class RabbitMQConnection
{
    private ?AMQPStreamConnection $connection = null;
    private ?AMQPChannel $channel = null;

    public function channel(): AMQPChannel
    {
        if ($this->channel instanceof AMQPChannel) {
            return $this->channel;
        }

        $this->connection = new AMQPStreamConnection(
            host: env('RABBITMQ_HOST', 'rabbitmq'),
            port: (int) env('RABBITMQ_PORT', 5672),
            user: env('RABBITMQ_USER', 'guest'),
            password: env('RABBITMQ_PASSWORD', 'guest'),
            vhost: env('RABBITMQ_VHOST', '/')
        );

        $this->channel = $this->connection->channel();

        return $this->channel;
    }

    public function close(): void
    {
        if ($this->channel) {
            $this->channel->close();
        }

        if ($this->connection) {
            $this->connection->close();
        }
    }
}
