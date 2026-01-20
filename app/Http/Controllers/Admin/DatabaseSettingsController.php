<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Doctrine\DBAL\DriverManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\DB;

class DatabaseSettingsController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function databaseIndex(): View|Factory|Application
    {
        $laravelConnection = DB::connection();

        // Create Doctrine DBAL connection with credentials
        $connection = DriverManager::getConnection([
            'dbname'   => $laravelConnection->getDatabaseName(),
            'user'     => $laravelConnection->getConfig('username'),
            'password' => $laravelConnection->getConfig('password'),
            'host'     => $laravelConnection->getConfig('host'),
            'port'     => $laravelConnection->getConfig('port'),
            'driver'   => 'pdo_mysql',
            'pdo'      => $laravelConnection->getPdo(), // reuse Laravel PDO
        ]);

        // Get tables from Doctrine
        $schemaManager = $connection->createSchemaManager();
        $tables = $schemaManager->listTableNames();

        // Exclude unwanted tables
        $filterTables = [
            'admins', 'branches', 'business_settings', 'email_verifications', 'failed_jobs', 'migrations',
            'oauth_access_tokens', 'oauth_auth_codes', 'oauth_clients', 'oauth_personal_access_clients',
            'oauth_refresh_tokens', 'password_resets', 'phone_verifications', 'soft_credentials',
            'users', 'currencies', 'colors'
        ];

        $tables = array_values(array_diff($tables, $filterTables));

        // Build one variable: [ ['name' => 'table', 'rows' => count], ... ]
        $tables = collect($tables)->map(function ($table) {
            return [
                'name' => $table,
                'rows' => DB::table($table)->count(), // exact row count
            ];
        });

        return view('admin-views.business-settings.db-index', compact('tables'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function cleanDatabase(Request $request): RedirectResponse
    {
        $tables = (array)$request->tables;

        if(count($tables) == 0) {
            Toastr::error(translate('No Table Updated'));
            return back();
        }

        try {
            DB::transaction(function () use ($tables) {
                foreach ($tables as $table) {
                    DB::table($table)->delete();
                }
            });
        } catch (\Exception $exception) {
            Toastr::error(translate('Failed to update!'));
            return back();
        }

        Toastr::success(translate('Updated successfully!'));
        return back();
    }
}
