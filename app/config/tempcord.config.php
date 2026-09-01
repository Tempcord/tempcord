<?php

declare(strict_types=1);

use Tempcord\Discord\Bitwise\Bitwise;
use Tempcord\Discord\Enums\Intent;
use Tempcord\TempcordConfig;

use function Tempest\env;

return new TempcordConfig(
    /**
     * Your bot token. Leave it in .env (never commit it). Run `php tempcord init`
     * for a guided setup, or copy .env.example to .env and fill it in yourself.
     * The bot only fails to boot — not to load — when this is empty.
     */
    token: env('DISCORD_TOKEN') ?? '',

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
