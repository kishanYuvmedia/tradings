<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Intraday; // Replace with your model
class WebsocketAPICall extends Command
{
    protected $signature = 'websocket:api-call';
    protected $description = 'Call API and insert data into database via WebSocket';
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {   
        $getcurrentstrike = 'http://127.0.0.1:8000/api/getcurrentstrike/NIFTY';
        $result = Http::get($getcurrentstrike)->json();
        Intraday::create($getcurrentstrike[0]);
        $this->info('API data inserted into the database.');
    }

}
