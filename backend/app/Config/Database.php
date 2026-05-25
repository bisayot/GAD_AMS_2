<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;
    public string $defaultGroup = 'default';

    public array $default = [
        'DSN'      => '', // Left completely blank here
        'hostname' => '',
        'username' => '',
        'password' => '',
        'database' => '',
        'DBDriver' => '',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug'  => true,
        'charset'  => '',
        'DBCollat' => '',
        'swapPre'  => '',
        'encrypt'  => [],
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => '',
    ];

    public array $tests = [
        'DSN'         => '',
        'hostname'    => '',
        'username'    => '',
        'password'    => '',
        'database'    => '',
        'DBDriver'    => '',
        'DBPrefix'    => '',
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => '',
        'DBCollat'    => '',
        'swapPre'     => '',
        'encrypt'     => [],
        'compress'    => false,
        'strictOn'    => false,
        'failover'    => [],
        'port'        => '',
        'foreignKeys' => true,
        'busyTimeout' => 1000,
    ];

    public function __construct()
    {
        parent::__construct();

        // Dynamically inject the Render environment variable when the class runs
        $this->default['DSN'] = getenv('DB_DSN') ?: '';

        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }
}