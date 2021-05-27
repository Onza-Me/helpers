<?php


namespace OnzaMe\Helpers\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearLogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all logs';

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
        Artisan::command('logs:clear', function() {

            exec('rm -f ' . storage_path('logs/*.log'));

            exec('rm -f ' . base_path('*.log'));

            $this->comment('Logs have been cleared!');

        })->describe('Clear log files');

        return 0;
    }
}

