<?php

namespace App\Console\Commands;

use App\Messaging\RabbitMQConsumer;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:consume-user-created')]
#[Description('Command description')]
class ConsumeUserCreated extends Command
{
    protected $signature = 'rabbitmq:consume-users';

    protected $description = 'Consume user events';

    public function __construct(
        protected RabbitMQConsumer $consumer
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Consumer Started');

        $this->consumer->consume();

        return self::SUCCESS;
    }
}
