<?php

namespace App\Actions;

use App\Models\Group;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use MarvinLabs\DiscordLogger\Discord\Exceptions\MessageCouldNotBeSent;

final class DiscordPing
{
    public function handle(Group $group, string $message, string $level = 'info'): void
    {
        if (! $this->validateWebhookUrl($group)) {
            return;
        }

        $originalConfig = config('logging.channels.discord');

        try {
            config(['logging.channels.discord' => [
                'url' => $group->discord_webhook_url,
            ]]);

            Log::channel('discord')->{$level}($message);
            Log::info($message);
        } catch (MessageCouldNotBeSent $e) {
            Log::warning('Invalid Discord webhook URL provided.');
        } catch (Exception $e) {
            Log::error('An error occurred while logging to Discord.');
        } finally {
            config(['logging.channels.discord' => $originalConfig]);
            # TODO - audit all sent statements for group owners with a status & timestamp.
        }
    }

    private function validateWebhookUrl(Group $group): bool
    {
        return is_null($group->discord_webhook_url);
    }
}