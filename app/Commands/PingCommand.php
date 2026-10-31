<?php

declare(strict_types=1);

namespace App\Commands;

use Tempcord\Discord\Interaction\CommandInteraction;
use Tempcord\Declare\Command\Command;
use Tempcord\Declare\Command\Option;
use Tempcord\Support\Autocomplete\ArrayAutocomplete;

#[Command(description: 'Ping? Pong!')]
final readonly class PingCommand
{
    public function __invoke(
        CommandInteraction $interaction,
        #[Option(
            description: 'Whom to ping?',
            autocomplete: new ArrayAutocomplete(items: ['Vlad', 'Mikield']),
        )]
        ?string $user = null,
        #[Option(description: 'How many times?', minValue: 1, maxValue: 5)]
        int $times = 1,
    ): void {
        $interaction->reply(
            trim(str_repeat('Ping, ' . ($user ?? 'world') . '! ', $times)),
            ephemeral: true,
        );
    }
}
