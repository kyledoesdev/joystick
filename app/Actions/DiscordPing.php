<?php

namespace App\Actions;

use App\Models\Group;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use MarvinLabs\DiscordLogger\Discord\Exceptions\MessageCouldNotBeSent;
use MarvinLabs\DiscordLogger\Logger;

final class DiscordPing
{
    public function handle(Group $group, string $message, string $level = 'info'): void
    {
        if ($this->isNotValidWebHook($group)) {
            return;
        }

        $originalConfig = config('logging.channels.discord');

        try {
            config(['logging.channels.discord' => [
                'driver' => 'custom',
                'via' => Logger::class,
                'level' => 'info',
                'url' => $group->discord_webhook_url,
            ]]);

            Log::channel('discord')->{$level}($message);
            Log::channel('single')->info($message);
        } catch (MessageCouldNotBeSent $e) {
            Log::warning('Invalid Discord webhook URL provided.');
        } catch (Exception $e) {
            Log::error('An error occurred while logging to Discord.');
        } finally {
            config(['logging.channels.discord' => $originalConfig]);
            # TODO - audit all sent statements for group owners with a status & timestamp.
        }
    }

    private function isNotValidWebHook(Group $group): bool
    {
        return is_null($group->discord_webhook_url);
    }
}