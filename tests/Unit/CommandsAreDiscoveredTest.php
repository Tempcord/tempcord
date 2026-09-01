<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Components\WaveButton;
use PHPUnit\Framework\TestCase;
use Tempcord\Enums\ComponentKind;
use Tempcord\Registries\CommandsRegistry;
use Tempcord\Registries\ComponentsRegistry;
use Tempest\Container\Container;
use Tempest\Core\Tempest;

/**
 * That the bot's handlers actually reach Discord.
 *
 * A command whose attribute is wrong is not an error anywhere — it simply never
 * gets registered, and a component nothing matches is ignored by design. Both
 * are silent, so this is what turns either into a failing test.
 */
final class CommandsAreDiscoveredTest extends TestCase
{
    private static ?Container $container = null;

    private function container(): Container
    {
        if (self::$container === null) {
            self::$container = Tempest::boot(dirname(__DIR__, 2));

            // Booting installs the framework's handlers; the test run wants its own.
            restore_error_handler();
            restore_exception_handler();
        }

        return self::$container;
    }

    public static function tearDownAfterClass(): void
    {
        self::$container = null;
    }

    public function test_the_example_commands_are_registered(): void
    {
        $names = array_keys($this->container()->get(CommandsRegistry::class)->all());
        sort($names);

        $this->assertSame(['ping', 'wave'], $names);
    }

    public function test_the_example_button_answers_the_id_the_command_builds(): void
    {
        $match = $this->container()->get(ComponentsRegistry::class)
            ->match(ComponentKind::Button, 'wave.back.Vlad');

        $this->assertNotNull($match);
        $this->assertSame(WaveButton::class, $match->definition->handler);
        $this->assertSame(['name' => 'Vlad'], $match->parameters);
    }
}
