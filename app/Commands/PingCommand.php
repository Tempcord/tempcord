<?php

declare(strict_types=1);

namespace App\Commands;

use CyberWolf\Discord\Interaction\CommandInteraction;
use Tempcord\Attributes\Command;
use Tempcord\Attributes\Option;
use Tempcord\AutoCompletes\ArrayAutocomplete;

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
        $interaction->replyEphemeral(
            trim(str_repeat('Ping, ' . ($user ?? 'world') . '! ', $times)),
        );
    }
}
