<?php

declare(strict_types=1);

namespace App\Commands;

use CyberWolf\Discord\Discord;
use Tempest\Console\Console;
use Tempest\Console\ConsoleArgument;
use Tempest\Console\ConsoleCommand;
use Tempest\Console\ExitCode;
use Throwable;

use function React\Async\await;

/**
 * First-run setup wizard: collects the bot token, verifies it against Discord,
 * shows a ready-to-use invite link, and offers to launch the bot.
 */
final readonly class InitCommand
{
    /**
     * A friendly default permission set for a fresh bot:
     * View Channels + Send Messages + Embed Links + Read Message History.
     */
    private const int INVITE_PERMISSIONS = 84992;

    public function __construct(
        private Console $console,
    ) {}

    #[ConsoleCommand(name: 'init', description: 'Interactive first-run setup for your Tempcord bot')]
    public function __invoke(
        #[ConsoleArgument(description: 'Reconfigure even if a token is already set', aliases: ['f'])]
        bool $force = false,
    ): ExitCode|int {
        $this->console->writeln();
        $this->console->writeln('  <style="fg-blue bold">🐺  Tempcord</style>  <style="fg-gray">— first-run setup</style>');
        $this->console->writeln('  <style="fg-gray dim">Let\'s get your first bot online.</style>');
        $this->console->writeln();

        if (! $this->console->supportsPrompting()) {
            $this->console->error(
                'This command is interactive and needs a terminal. '
                . 'Copy .env.example to .env and set DISCORD_TOKEN manually, then run: php tempcord boot --register',
            );

            return ExitCode::ERROR;
        }

        $root = getcwd();
        $envPath = $root . '/.env';
        $examplePath = $root . '/.env.example';

        // Bring an .env into existence from the example if needed.
        if (! is_file($envPath) && is_file($examplePath)) {
            copy($examplePath, $envPath);
        }

        $existing = $this->readEnv($envPath, 'DISCORD_TOKEN');
        if ($existing !== null && $existing !== '' && ! $force) {
            $this->console->info('A Discord token is already configured in .env.');
            if (! $this->console->confirm('Do you want to replace it?', default: false)) {
                $this->console->writeln('Keeping the existing token.');
                $this->showNextSteps();

                return ExitCode::SUCCESS;
            }
        }

        // 1. Token
        $this->console->writeln('  <style="bold">Step 1 — Bot token</style>');
        $this->console->instructions([
            'Discord Developer Portal → Applications → your app → <style="bold">Bot</style>',
            'Press <style="bold">Reset Token</style> and copy the value.',
            'https://discord.com/developers/applications',
        ]);

        $token = null;
        while ($token === null || trim($token) === '') {
            $token = $this->console->password(label: 'Paste your bot token');

            if ($token === null || trim($token) === '') {
                $this->console->warning('A token is required to continue.');
            }
        }
        $token = trim($token);

        // 2. Verify against Discord
        $this->console->writeln();
        $this->console->writeln('  <style="bold">Step 2 — Verifying with Discord</style>');

        $identity = $this->verifyToken($token);

        if ($identity === null) {
            $this->console->error('Discord rejected that token (or the network is unavailable).');

            if (! $this->console->confirm('Save it to .env anyway?', default: false)) {
                $this->console->writeln('Nothing was written. Run <style="bold">php tempcord init</style> again when ready.');

                return ExitCode::ERROR;
            }
        } else {
            $tag = $identity['name'];
            $this->console->success('Authenticated as ' . $tag);
        }

        // 3. Persist
        $this->setEnv($envPath, 'DISCORD_TOKEN', $token);
        $this->console->writeln('  Saved <style="bold">DISCORD_TOKEN</style> to .env');

        // 4. Invite link
        if ($identity !== null) {
            $invite = sprintf(
                'https://discord.com/oauth2/authorize?client_id=%s&permissions=%d&scope=bot+applications.commands',
                $identity['id'],
                self::INVITE_PERMISSIONS,
            );

            $this->console->writeln();
            $this->console->writeln('  <style="bold">Step 3 — Invite your bot</style>');
            $this->console->writeln('  Open this link and add the bot to a server:');
            $this->console->writeln('  <style="fg-cyan underline">' . $invite . '</style>');
        }

        // 5. Launch
        $this->console->writeln();
        $this->console->writeln('  <style="bold">Step 4 — Go live</style>');

        if ($this->console->confirm('Register slash commands and start the bot now?', default: true)) {
            $this->console->writeln();

            return $this->console->call('boot', ['--register']);
        }

        $this->showNextSteps();

        return ExitCode::SUCCESS;
    }

    /**
     * @return array{id: string, name: string}|null
     */
    private function verifyToken(string $token): ?array
    {
        try {
            $discord = new Discord($token);
            $discord->withRest();

            $user = await($discord->rest->user->getCurrent());

            return [
                'id' => $user->id,
                'name' => $user->global_name ?? $user->username,
            ];
        } catch (Throwable) {
            return null;
        }
    }

    private function showNextSteps(): void
    {
        $this->console->writeln();
        $this->console->info('You\'re all set. Start your bot any time with:');
        $this->console->writeln('  <style="bold">php tempcord boot --register</style>   (register commands + run)');
        $this->console->writeln('  <style="bold">php tempcord boot</style>              (run only)');
        $this->console->writeln();
        $this->console->writeln('  Then try <style="bold">/ping</style> in your server. Happy building! 🐺');
    }

    private function readEnv(string $path, string $key): ?string
    {
        if (! is_file($path)) {
            return null;
        }

        $content = (string) file_get_contents($path);

        if (preg_match('/^' . preg_quote($key, '/') . '=(.*)$/m', $content, $m) === 1) {
            return trim($m[1]);
        }

        return null;
    }

    private function setEnv(string $path, string $key, string $value): void
    {
        $line = $key . '=' . $value;

        if (is_file($path)) {
            $content = (string) file_get_contents($path);

            if (preg_match('/^' . preg_quote($key, '/') . '=.*$/m', $content) === 1) {
                $content = (string) preg_replace('/^' . preg_quote($key, '/') . '=.*$/m', $line, $content);
            } else {
                $content = rtrim($content, "\n") . "\n" . $line . "\n";
            }
        } else {
            $content = $line . "\n";
        }

        file_put_contents($path, $content);
    }
}
