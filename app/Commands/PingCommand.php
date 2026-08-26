<?php

declare(strict_types=1);

namespace App\Commands;

use CyberWolf\Discord\Bitwise\Bitwise;
use CyberWolf\Discord\Enums\InteractionCallbackType;
use CyberWolf\Discord\Enums\MessageFlag;
use CyberWolf\Discord\Interaction\CommandInteraction;
use CyberWolf\Discord\Interaction\Helpers\InteractionCallbackBuilder;
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
        $interaction->createInteractionResponse(
            InteractionCallbackBuilder::new()
                ->setContent(trim(str_repeat('Ping, ' . ($user ?? 'world') . '! ', $times)))
                ->setType(InteractionCallbackType::CHANNEL_MESSAGE_WITH_SOURCE)
                ->setFlags(Bitwise::from(MessageFlag::EPHEMERAL)->get()),
        );
    }
}
