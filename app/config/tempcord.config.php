<?php

declare(strict_types=1);

use Ragnarok\Fenrir\Bitwise\Bitwise;
use Ragnarok\Fenrir\Enums\Intent;
use Tempcord\TempcordConfig;

use function Tempest\env;

return new TempcordConfig(
    token: env('DISCORD_TOKEN') ?? throw new RuntimeException(
        'DISCORD_TOKEN is not set. Copy .env.example to .env and add your bot token.',
    ),

    /**
     * Discord only sends the events you subscribe to. Add the intents your bot
     * needs, and enable the privileged ones — MESSAGE_CONTENT, GUILD_MEMBERS
     * and GUILD_PRESENCES — in the Discord developer portal as well.
     */
    intents: Bitwise::from(
        Intent::GUILDS,
        Intent::GUILD_MESSAGES,
        Intent::DIRECT_MESSAGES,
        Intent::MESSAGE_CONTENT,
    ),
);
