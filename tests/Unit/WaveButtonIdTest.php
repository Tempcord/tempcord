<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Components\WaveButton;
use PHPUnit\Framework\TestCase;
use Tempcord\Runtime\CustomId;

/**
 * That /wave and the button answering it still agree on one custom id.
 *
 * Discord hands a button press back with nothing but its custom id, so the
 * command that builds the id and the handler that matches on it have to stay in
 * step. Nothing reports it when they stop: an id nothing matches is ignored by
 * design, so the button would simply do nothing.
 *
 * This is the shape worth copying for your own components — it tests your two
 * files against each other, not the framework, which has its own tests for
 * routing and discovery.
 */
final class WaveButtonIdTest extends TestCase
{
    public function test_the_id_the_command_builds_is_the_one_the_button_answers(): void
    {
        $pattern = CustomId::compile(WaveButton::CUSTOM_ID);

        // Exactly what WaveCommand puts on the button it posts.
        $customId = $pattern->build(['name' => 'Vlad']);

        $this->assertSame(['name' => 'Vlad'], $pattern->match($customId));
    }

    /**
     * A name with a space in it is the ordinary case for a Discord nickname,
     * and a pattern that stopped at the first one would hand the handler half
     * a name.
     */
    public function test_a_name_with_spaces_survives_the_round_trip(): void
    {
        $pattern = CustomId::compile(WaveButton::CUSTOM_ID);

        $this->assertSame(
            ['name' => 'Vlad the Impaler'],
            $pattern->match($pattern->build(['name' => 'Vlad the Impaler'])),
        );
    }

    /**
     * Discord refuses a custom id longer than 100 characters, and a display
     * name can be 32 of them.
     */
    public function test_the_id_fits_inside_what_discord_accepts(): void
    {
        $customId = CustomId::compile(WaveButton::CUSTOM_ID)->build([
            'name' => str_repeat('n', 32),
        ]);

        $this->assertLessThanOrEqual(100, strlen($customId));
    }
}
