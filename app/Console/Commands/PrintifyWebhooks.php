<?php

namespace App\Console\Commands;

use App\Services\PrintifyService;
use Illuminate\Console\Command;

class PrintifyWebhooks extends Command
{
    protected $signature = 'printify:webhooks
                            {--url= : Public webhook URL (defaults to APP_URL/printify/webhook)}
                            {--list : Only list registered webhooks}
                            {--shops : List shops available to the API token}';

    protected $description = 'Register the Printify product and order webhooks for this site';

    // Topics handled by PrintifyWebhookController.
    protected const TOPICS = [
        'product:publish:started',
        'product:deleted',
        'order:updated',
        'order:sent-to-production',
        'order:shipment:created',
        'order:shipment:delivered',
    ];

    public function handle(PrintifyService $printify): int
    {
        if ($this->option('shops')) {
            if (empty(config('services.printify.token'))) {
                $this->error('Set PRINTIFY_API_TOKEN in .env first.');

                return self::FAILURE;
            }

            $this->table(['ID', 'Title', 'Sales channel'], collect($printify->shops())
                ->map(fn ($s) => [$s['id'], $s['title'], $s['sales_channel'] ?? ''])->all());

            return self::SUCCESS;
        }

        if (! $printify->enabled()) {
            $this->error('Printify is not configured. Set PRINTIFY_API_TOKEN and PRINTIFY_SHOP_ID in .env.');

            return self::FAILURE;
        }

        $existing = collect($printify->webhooks());

        if ($this->option('list')) {
            $this->showWebhooks($existing->all());

            return self::SUCCESS;
        }

        $url = $this->option('url') ?: rtrim(config('app.url'), '/').'/printify/webhook';

        if (! str_starts_with($url, 'https://')) {
            $this->error("Webhook URL must be public HTTPS, got: {$url}");
            $this->line('Set APP_URL to your live domain, or pass --url=https://your-tunnel/printify/webhook');

            return self::FAILURE;
        }

        $secret = config('services.printify.webhook_secret') ?: null;
        if (! $secret) {
            $this->warn('PRINTIFY_WEBHOOK_SECRET is empty; webhook signatures will not be verified.');
        }

        foreach (self::TOPICS as $topic) {
            // Replace any existing webhook for this topic so the URL and secret stay in sync.
            foreach ($existing->where('topic', $topic) as $hook) {
                $printify->deleteWebhook($hook['id'], $hook['url']);
                $this->line("Removed old {$topic} webhook ({$hook['url']})");
            }

            $printify->createWebhook($topic, $url, $secret);
            $this->info("Registered {$topic} -> {$url}");
        }

        $this->showWebhooks($printify->webhooks());

        return self::SUCCESS;
    }

    protected function showWebhooks(array $hooks): void
    {
        if (empty($hooks)) {
            $this->line('No webhooks registered.');

            return;
        }

        $this->table(['ID', 'Topic', 'URL'], collect($hooks)
            ->map(fn ($h) => [$h['id'], $h['topic'], $h['url']])->all());
    }
}
