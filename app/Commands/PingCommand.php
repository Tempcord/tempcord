<?php

namespace Bot\Commands;

use Ragnarok\Fenrir\Bitwise\Bitwise;
use Ragnarok\Fenrir\Enums\InteractionCallbackType;
use Ragnarok\Fenrir\Enums\MessageFlag;
use Ragnarok\Fenrir\Interaction\CommandInteraction;
use Ragnarok\Fenrir\Interaction\Helpers\InteractionCallbackBuilder;
use Tempcord\Attributes\Command;
use Tempcord\Attributes\Option;
use Tempcord\AutoCompletes\ArrayAutocomplete;

#[Command(description: "Ping? Pong!")]
final readonly class PingCommand
{
    public function __invoke(
        CommandInteraction $interaction,
        #[Option(description: 'Whom to ping?', autocomplete: new ArrayAutocomplete(items: ['Vlad', 'Mikield']))]
        string|null        $user = null,
    ): void {
        $interaction->createInteractionResponse(
            InteractionCallbackBuilder::new()
                ->setContent('Ping, ' . $user)
                ->setType(InteractionCallbackType::CHANNEL_MESSAGE_WITH_SOURCE)
                ->setFlags(Bitwise::from(MessageFlag::EPHEMERAL)->getBitSet())
        );
    }
}
