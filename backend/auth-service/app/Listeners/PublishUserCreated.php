<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Messaging\RabbitMQPublisher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PublishUserCreated
{
    public function __construct(
        protected RabbitMQPublisher $publisher
    ) {}

    public function handle(UserCreated $event): void
    {
        $this->publisher->publish(
            exchange: 'user.events',
            routingKey: 'user.created',
            payload: [
                'id' => $event->user->id,
                'name' => $event->user->name,
                'email' => $event->user->email,
                'password' => $event->user->password,
            ]
        );
    }
}
