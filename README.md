# Tempcord

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tempcord/tempcord.svg?style=flat-square)](https://packagist.org/packages/tempcord/tempcord)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/tempcord/tempcord/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/tempcord/tempcord/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/tempcord/tempcord/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/tempcord/tempcord/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/tempcord/tempcord.svg?style=flat-square)](https://packagist.org/packages/tempcord/tempcord)

A modern Discord bot framework for PHP built on top of [Tempest](https://tempestphp.com). Tempcord provides a clean, expressive API for building Discord bots with PHP 8.5+.

## Features

- 🚀 **Modern PHP**: Built for PHP 8.5+ with full type safety
- ⚡ **Tempest Integration**: Leverages the powerful Tempest framework
- 🎯 **Discord API**: Full Discord API v10 support
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

use CyberWolf\Discord\Interaction\CommandInteraction;
use CyberWolf\Discord\Interaction\Helpers\InteractionCallbackBuilder;
use CyberWolf\Discord\Enums\InteractionCallbackType;
use Tempcord\Attributes\Command;

#[Command(description: 'Ping? Pong!')]
final readonly class PingCommand
{
    public function __invoke(CommandInteraction $interaction): void
    {
        $interaction->createInteractionResponse(
            InteractionCallbackBuilder::new()
                ->setContent('Pong!')
                ->setType(InteractionCallbackType::CHANNEL_MESSAGE_WITH_SOURCE),
        );
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

use CyberWolf\Discord\Interaction\CommandInteraction;
use CyberWolf\Discord\Interaction\Helpers\InteractionCallbackBuilder;
use CyberWolf\Discord\Enums\InteractionCallbackType;
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
        $interaction->createInteractionResponse(
            InteractionCallbackBuilder::new()
                ->setContent('Hello, ' . ($name ?? 'world') . '!')
                ->setType(InteractionCallbackType::CHANNEL_MESSAGE_WITH_SOURCE),
        );
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

use CyberWolf\Discord\Constants\Events;
use CyberWolf\Discord\Gateway\Events\MessageCreate;
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

## Configuration

Your bot is configured in `app/config/tempcord.config.php`:

```php
<?php

use CyberWolf\Discord\Bitwise\Bitwise;
use CyberWolf\Discord\Enums\Intent;
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