<?php

namespace Kanakku\Http\Controllers\V1\Onboarding;

use Kanakku\Http\Controllers\Controller;
use Kanakku\Space\EnvironmentManager;
use Kanakku\Http\Requests\DatabaseEnvironmentRequest;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class DatabaseConfigurationController extends Controller
{
    /**
     * @var EnvironmentManager
     */
    protected $EnvironmentManager;

    /**
     * @param EnvironmentManager $environmentManager
     */
    public function __construct(EnvironmentManager $environmentManager)
    {
        $this->environmentManager = $environmentManager;
    }

    /**
     *
     * @param DatabaseEnvironmentRequest $request
     */
    public function saveDatabaseEnvironment(DatabaseEnvironmentRequest $request)
    {
        ini_set('max_execution_time', 0);
        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        $results = $this->environmentManager->saveDatabaseVariables($request);

        if (array_key_exists("success", $results)) {
            Artisan::call('key:generate --force');
            Artisan::call('config:clear');
            Artisan::call('cache:clear');
            Artisan::call('storage:link');
            Artisan::call('migrate --seed --force');
        }

        return response()->json($results);
    }

    public function getDatabaseEnvironment(Request $request)
    {
        $databaseData = [];

        switch ($request->connection) {
            case 'sqlite':
                $databaseData = [
                    'database_connection' => 'sqlite',
                    'database_name' => database_path('database.sqlite'),
                ];
                break;

            case 'pgsql':
                $databaseData = [
                    'database_connection' => 'pgsql',
                    'database_host' => '127.0.0.1',
                    'database_port' => 5432,
                ];
                break;

            case 'mysql':
                $databaseData = [
                    'database_connection' => 'mysql',
                    'database_host' => '127.0.0.1',
                    'database_port' => 3306,
                ];
                break;

            case 'sqlsrv':
                $databaseData = [
                    'driver' => 'sqlsrv',
                    'host' => '127.0.0.1',
                    'port' => 1433,
                ];
                break;
        }


        return response()->json([
            'config' => $databaseData,
            'success' => true
        ]);
    }
}
