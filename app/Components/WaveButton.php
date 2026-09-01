<?php

declare(strict_types=1);

namespace App\Components;

use Tempcord\Discord\Interaction\ButtonInteraction;
use Tempcord\Attributes\Button;

/**
 * Answers the button /wave posts.
 *
 * The {name} in the custom id is matched out of the id Discord sends back and
 * handed to the parameter of the same name.
 */
#[Button(id: WaveButton::CUSTOM_ID)]
final readonly class WaveButton
{
    public const string CUSTOM_ID = 'wave.back.{name}';

    public function __invoke(ButtonInteraction $interaction, string $name): void
    {
        $interaction->update($name . ' waved back 👋');
    }
}
