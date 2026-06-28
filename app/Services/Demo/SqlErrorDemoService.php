<?php

namespace App\Services\Demo;

use Illuminate\Support\Facades\DB;

class SqlErrorDemoService
{
    /**
     * Execute invalid SQL so Scout captures a database QueryException.
     */
    public function execute(): void
    {
        DB::select('SELECT * FROM scout_poc_nonexistent_table WHERE id = ?', [1]);
    }
}
