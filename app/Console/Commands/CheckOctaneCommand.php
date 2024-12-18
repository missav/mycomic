<?php

namespace App\Console\Commands;

use App\Ploi\Ploi;
use App\Shell\Shell;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Laravel\Octane\FrankenPhp\ServerProcessInspector as FrankenPhpServerProcessInspector;
use Laravel\Octane\RoadRunner\ServerProcessInspector as RoadRunnerServerProcessInspector;
use Laravel\Octane\Swoole\ServerProcessInspector as SwooleServerProcessInspector;

class CheckOctaneCommand extends Command
{
    protected $signature = 'octane:check';

    protected $description = 'Check Laravel Octane command';

    public function handle(): void
    {
        $isRunning = match (config('octane.server')) {
            'swoole' => $this->isSwooleServerRunning(),
            'roadrunner' => $this->isRoadRunnerServerRunning(),
            'frankenphp' => $this->isFrankenPhpServerRunning(),
        };
        if ($isRunning) {
            return;
        }

        $ploi = app(Ploi::class);
        $server = app(Ploi::class)->servers()->firstWhere('name', gethostname());
        if (! $server) {
            return;
        }

        $daemon = collect($ploi->api("servers/{$server['id']}/daemons"))->first(fn (array $daemon) =>
        Str::contains($daemon['command'], 'octane:start')
        );
        if (! $daemon) {
            return;
        }

        $port = 8000;
        Shell::exec("fuser -n tcp -k {$port}");
        $this->info("Kill processes consuming port {$port}");

        sleep(3);

        $ploi->api("servers/{$server['id']}/daemons/{$daemon['id']}/restart", method: 'POST');
        $this->info('Restarted Laravel Octane daemon');
    }

    protected function isSwooleServerRunning(): bool
    {
        return app(SwooleServerProcessInspector::class)
            ->serverIsRunning();
    }

    protected function isRoadRunnerServerRunning(): bool
    {
        return app(RoadRunnerServerProcessInspector::class)
            ->serverIsRunning();
    }

    protected function isFrankenPhpServerRunning(): bool
    {
        return app(FrankenPhpServerProcessInspector::class)
            ->serverIsRunning();
    }
}
