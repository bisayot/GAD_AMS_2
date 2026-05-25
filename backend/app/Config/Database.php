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

        // 1. Grab the dynamic DSN connection string from Render
        $dsn = getenv('DB_DSN') ?: '';

        // 2. If it starts with lowercase "mysql://", switch it to "MySQLi://"
        if (str_starts_with($dsn, 'mysql://')) {
            $dsn = 'MySQLi' . substr($dsn, 5);
        }

        // 3. Inject the corrected string into your default group
        $this->default['DSN'] = $dsn;

        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }
}