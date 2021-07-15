<?php

namespace Wefabric\FormCache\Console\Commands;

use Illuminate\Console\Command;

class FormCacheGarbageCollection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'form-cache:garbage-collection {--now}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes old form caches';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if($this->option('now')) {
            \Wefabric\FormCache\Jobs\FormCacheGarbageCollection::dispatchNow();
        } else {
            \Wefabric\FormCache\Jobs\FormCacheGarbageCollection::dispatch();
        }
        $this->info('Success');
    }
}
