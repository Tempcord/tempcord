# Tempcord

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tempcord/tempcord.svg?style=flat-square)](https://packagist.org/packages/tempcord/tempcord)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/tempcord/tempcord/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/tempcord/tempcord/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/tempcord/tempcord/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/tempcord/tempcord/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/tempcord/tempcord.svg?style=flat-square)](https://packagist.org/packages/tempcord/tempcord)

A modern Discord bot framework for PHP built on top of [Tempest](https://tempestphp.com). Tempcord provides a clean, expressive API for building Discord bots with PHP 8.5+.

## Features

- 🚀 **Modern PHP**: Built for PHP 8.5+ with full type safety
- 🧩 **Components**: Buttons, select menus and modals routed by custom id
- ⚡ **Tempest Integration**: Leverages the powerful Tempest framework
- 🎯 **Discord API**: Full Discord API v10 support
- 🗃️ **Gateway cache**: Guilds, channels, roles, members and voice states, read without a round trip
- 🔧 **Developer Friendly**: Intuitive API with excellent IDE support
- 📦 **Composer Ready**: Easy installation and dependency management
- 🧪 **Testing**: Built-in testing support with PHPUnit and Pest
- 📚 **Documented**: [Guides and an API reference](https://github.com/tempcord/framework/blob/master/docs/README.md) generated from the framework source

## Installation

Create a new Tempcord project with Composer:

```bash
composer create-project tempcord/tempcord my-discord-bot
cd my-discord-bot
```

## Quick Start

### 1. Run the setup wizard

```bash
php tempcord init
```

`init` walks you through everything a first bot needs:

- 🔑 asks for your **bot token** (and writes it to `.env` for you)
- ✅ **verifies** it against Discord and greets you by your bot's name
- 🔗 prints a ready-to-use **invite link** so you can add the bot to a server
- 🚀 offers to **register slash commands and boot** right away

> No token yet? Create an application at the
> [Discord Developer Portal](https://discord.com/developers/applications),
> open **Bot → Reset Token**, and paste it into the wizard.

### 2. Try it

The starter project ships with a `/ping` command in `app/Commands/PingCommand.php`:

```php
<?php

namespace App\Commands;

use Tempcord\Discord\Interaction\CommandInteraction;
use Tempcord\Attributes\Command;

#[Command(description: 'Ping? Pong!')]
final readonly class PingCommand
{
    public function __invoke(CommandInteraction $interaction): void
    {
        $interaction->reply('Pong!');
    }
}
```

Invite the bot, then type `/ping` in your server.

### 3. Start the bot any time

```bash
php tempcord boot --register   # register slash commands, then run
php tempcord boot              # run only
```

Prefer to skip the wizard? Copy `.env.example` to `.env`, set `DISCORD_TOKEN`,
and run `php tempcord boot --register`.

## Usage Examples

### Slash commands

Options are declared as typed parameters. Add `#[Option]` for a description and
constraints; Discord builds the picker from them.

```php
<?php

namespace App\Commands;

use Tempcord\Discord\Interaction\CommandInteraction;
use Tempcord\Attributes\Command;
use Tempcord\Attributes\Option;

#[Command(description: 'Greet a user')]
final readonly class GreetCommand
{
    public function __invoke(
        CommandInteraction $interaction,
        #[Option(description: 'Who to greet')]
        ?string $name = null,
    ): void {
        $interaction->reply('Hello, ' . ($name ?? 'world') . '!');
    }
}
```

### Event listeners

An invokable class with `#[Event]` listens to a gateway event. The payload arrives
as the single argument, and the class is resolved from the container — so it can take
constructor dependencies such as the Discord client.

```php
<?php

namespace App\Listeners;

use Tempcord\Discord\Constants\Events;
use Tempcord\Discord\Gateway\Events\MessageCreate;
use Tempcord\Attributes\Event;

#[Event(name: Events::MESSAGE_CREATE)]
final readonly class MessageLogger
{
    public function __invoke(MessageCreate $message): void
    {
        if ($message->content === '!hello') {
            // handle the message
        }
    }
}
```

> A listener only fires for events your bot has the intent for — set intents in
> `app/config/tempcord.config.php`. `MESSAGE_CONTENT` is privileged and must also
> be enabled in the Discord developer portal.

### Buttons, select menus and modals

`#[Button]`, `#[SelectMenu]` and `#[ModalSubmit]` route a component back to the code
that answers it. Discord hands back nothing but the custom id, so anything the handler
needs to know travels inside it as a `{placeholder}`:

```php
<?php

namespace App\Components;

use Tempcord\Discord\Interaction\ButtonInteraction;
use Tempcord\Attributes\Button;

#[Button(id: WaveButton::CUSTOM_ID)]
final readonly class WaveButton
{
    public const string CUSTOM_ID = 'wave.back.{name}';

    public function __invoke(ButtonInteraction $interaction, string $name): void
    {
        $interaction->update($name . ' waved back 👋');
    }
}
```

Build a matching id from the same pattern when you create the button, so the two cannot
drift apart:

```php
use Tempcord\Runtime\CustomId;

new PrimaryButton(
    customId: CustomId::compile(WaveButton::CUSTOM_ID)->build(['name' => $name]),
    label: 'Wave back',
);
```

The starter ships both halves in `app/Commands/WaveCommand.php` and
`app/Components/WaveButton.php` — invite the bot and run `/wave`.

### Answering an interaction

Every interaction takes a reply directly. Text, an embed, or a fully built response when
you need more:

```php
$interaction->reply('Pong!');
$interaction->reply($embed, ephemeral: true);  // only the person who triggered it sees it
$interaction->update($embed);                  // replaces the message a component sits on
$interaction->showModal($modal);
$interaction->defer();                         // then editReply() once the work is done
$interaction->followUp('and one more thing');
```

Components go on without building the tree by hand — `addButton()` fills a row five at a
time, `addRow()` groups deliberately:

```php
$interaction->reply(
    Response::message('Pick one')->addButton($accept)->addButton($reject),
);
```

`Response::message()`, `::ephemeral()`, `::update()`, `::modal()` and `::defer()` return
the underlying builder for anything they do not cover.

### Suggesting values

An option can suggest values while the user is still typing. The lightest way is a method
on the command itself:

```php
#[Autocomplete(option: 'track')]
public function completeTrack(string $typed): array
{
    return $this->tracks->matching($typed);
}
```

For suggestions more than one command wants, name a class implementing `Autocomplete` —
it is built by the container, so it may take dependencies of its own.

### Reading what the gateway knows

The Discord library keeps no state, so without a cache every role check is an HTTP round
trip. Ask the container for `Tempcord\Cache\Cache` and read guilds, channels, roles,
members and voice states straight out of memory:

```php
$member = $this->cache->member($guildId, $userId);
$blocked = in_array($blockedRoleId, $member?->roles ?? [], true);
```

Reads never touch the network, so a miss returns null rather than quietly becoming a
rate-limited request inside a loop.

## Configuration

Your bot is configured in `app/config/tempcord.config.php`:

```php
<?php

use Tempcord\Discord\Bitwise\Bitwise;
use Tempcord\Discord\Enums\Intent;
use Tempcord\TempcordConfig;

use function Tempest\env;

return new TempcordConfig(
    token: env('DISCORD_TOKEN') ?? '',
    intents: Bitwise::from(
        Intent::GUILDS,
        Intent::GUILD_MESSAGES,
        Intent::MESSAGE_CONTENT,
    ),
);
```

More advanced topics — subcommands, autocomplete, localization and plugins — are
covered in the [framework guides](https://github.com/tempcord/framework/blob/master/docs/README.md).

## Testing

Run the test suite:

```bash
composer test
```

Run tests with coverage:

```bash
composer test-coverage
```

Run static analysis:

```bash
composer analyse
```

Format code:

```bash
composer format
```

## Security Vulnerabilities

If you discover a security vulnerability within Tempcord, please send an e-mail to the maintainers via [mikield@icloud.com](mailto:mikield@icloud.com). All security vulnerabilities will be promptly addressed.

## License

Tempcord is open-sourced software licensed under the [MIT license](LICENSE).

## Credits

- [Vladyslav G.](https://github.com/mikield)
- [All Contributors](../../contributors)