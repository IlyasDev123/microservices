<?php

namespace App\Messaging;

use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQPublisher
{
    public function __construct(
        protected RabbitMQConnection $connection
    ) {}

    public function publish(
        string $exchange,
        string $routingKey,
        array $payload
    ): void {

        $channel = $this->connection->channel();

        // Create exchange if it doesn't exist
        $channel->exchange_declare(
            exchange: $exchange,
            type: 'topic',
            passive: false,
            durable: true,
            auto_delete: false
        );

        $message = new AMQPMessage(
            json_encode($payload, JSON_THROW_ON_ERROR),
            [
                'content_type' => 'application/json',
                'delivery_mode' => 2, // Persistent
            ]
        );

        $channel->basic_publish(
            msg: $message,
            exchange: $exchange,
            routing_key: $routingKey
        );

        $this->connection->close();
    }
}
