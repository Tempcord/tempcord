<?php

declare(strict_types=1);

namespace App\Commands;

use App\Components\WaveButton;
use Tempcord\Discord\Component\Button\PrimaryButton;
use Tempcord\Discord\Interaction\CommandInteraction;
use Tempcord\Discord\Interaction\Response;
use Tempcord\Attributes\Command;
use Tempcord\Attributes\Option;
use Tempcord\Runtime\CustomId;

/**
 * Posts a button for someone to press.
 *
 * Who it was meant for travels inside the button's custom id, because Discord
 * hands back nothing else when it is pressed.
 */
#[Command(description: 'Wave at someone')]
final readonly class WaveCommand
{
    public function __invoke(
        CommandInteraction $interaction,
        #[Option(description: 'Who to wave at')]
        string $name,
    ): void {
        $button = new PrimaryButton(
            customId: CustomId::compile(WaveButton::CUSTOM_ID)->build(['name' => $name]),
            label: 'Wave back',
        );

        $interaction->reply(
            Response::message('Waving at ' . $name . ' 👋')->addButton($button),
        );
    }
}
