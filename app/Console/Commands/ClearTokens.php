<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Eliminar todos los registros de la tabla que guarda los tokens
        DB::table('jwt_tokens')->delete();

        $this->info('Todos los tokens han sido eliminados.');
    }
}
