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

You can create a new Tempcord project using Composer:

```bash
composer create-project tempcord/tempcord my-discord-bot
cd my-discord-bot
```

Or install it in an existing project:

```bash
composer require tempcord/tempcord
```

## Quick Start

### 1. Environment Setup

Copy the example environment file and configure your Discord bot token:

```bash
cp .env.example .env
```

Edit `.env` and add your Discord bot token:

```env
DISCORD_TOKEN=your_bot_token_here
```

### 2. Create Your First Command

Create a simple ping command in `app/Commands/PingCommand.php`:

```php
<?php

namespace App\Commands;

use Tempcord\Attributes\Command;
use Tempcord\Attributes\Description;
use Tempcord\Discord\Interaction;

#[Command('ping')]
class PingCommand
{
    public function handle(Interaction $interaction): void
    {
        $interaction->reply('🏓 Pong!');
    }
}
```

### 3. Run Your Bot

```bash
php tempcord boot
```

## Usage Examples

### Slash Commands

```php
<?php

namespace App\Commands;

use Tempcord\Attributes\Command;
use Tempcord\Attributes\Description;
use Tempcord\Attributes\Option;
use Tempcord\Discord\Interaction;
use Tempcord\Enums\OptionType;

class GreetCommand
{
    #[Command('greet')]
    #[Description('Greet a user')]
    #[Option('user', OptionType::USER, 'The user to greet', required: true)]
    #[Option('message', OptionType::STRING, 'Custom greeting message')]
    public function handle(Interaction $interaction): void
    {
        $user = $interaction->getOption('user');
        $message = $interaction->getOption('message') ?? 'Hello';
        
        $interaction->reply("{$message}, {$user->mention()}!");
    }
}
```

### Event Listeners

```php
<?php

namespace App\Listeners;

use Tempcord\Attributes\EventListener;
use Tempcord\Events\MessageCreate;

class MessageListener
{
    #[EventListener]
    public function onMessageCreate(MessageCreate $event): void
    {
        $message = $event->message;
        
        if ($message->content === '!hello') {
            $message->reply('Hello there!');
        }
    }
}
```

### Middleware

```php
<?php

namespace App\Middleware;

use Tempcord\Attributes\Middleware;
use Tempcord\Discord\Interaction;
use Tempcord\Middleware\MiddlewareInterface;

#[Middleware]
class AdminOnlyMiddleware implements MiddlewareInterface
{
    public function handle(Interaction $interaction, callable $next): mixed
    {
        if (!$interaction->member->hasPermission('ADMINISTRATOR')) {
            $interaction->reply('❌ This command requires administrator permissions.');
            return;
        }
        
        return $next($interaction);
    }
}
```

## Configuration

Tempcord uses a simple configuration system. You can customize your bot's behavior in `app/config/tempcord.config.php`:

```php
<?php

use Tempcord\Config\TempcordConfig;

return new TempcordConfig(
    token: env('DISCORD_TOKEN'),
    clientId: env('DISCORD_CLIENT_ID'),
    intents: [
        'GUILDS',
        'GUILD_MESSAGES',
        'MESSAGE_CONTENT',
    ],
    commandPrefix: '!',
    autoRegisterCommands: true,
);
```

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