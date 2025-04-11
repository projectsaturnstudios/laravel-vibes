<?php

namespace ProjectSaturnStudios\Vibes\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

abstract class LoudCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description;

    protected $slug;

    /**
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle() : void
    {
        $this->fireOffEvent('command-executed', ['command' => $this->slug]);
        $this->start();
        //$this->fireOffEvent('command-finished', ['command' => $this->slug]);
    }

    /**
     * @param string $name
     * @param array|null $payload
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function fireOffEvent(string $name, ?array $payload = null) : void
    {
        if(!empty($this->option('session')))
        {
            if(!is_array($payload))
            {
                $payload = [];
            }

            foreach($this->option('session') as $session)
            {
                $payload['session'] = $session;
                $payload['request_id'] = $this->option('request_id')[0] ?? 0;
                vibe_activity($name, $payload);
            }
        }

    }
}
