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
        'DSN'      => '', 
        'hostname' => '',
        'username' => '',
        'password' => '',
        'database' => '',
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug'  => true,
        'charset'  => 'utf8mb4',
        'DBCollat' => 'utf8mb4_general_ci',
        'swapPre'  => '',
        'encrypt'  => [
            'ssl_verify' => false, 
        ],
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 26685, 
    ];

    public array $tests = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => '',
        'password'    => '',
        'database'    => ':memory:',
        'DBDriver'    => 'SQLite3',
        'DBPrefix'    => 'db_',
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => 'utf8',
        'DBCollat'    => 'utf8_general_ci',
        'swapPre'     => '',
        'encrypt'     => false,
        'compress'    => false,
        'strictOn'    => false,
        'failover'    => [],
        'port'        => 3306,
        'foreignKeys' => true,
        'busyTimeout' => 1000,
    ];

    public function __construct()
    {
        parent::__construct();

        // 1. Grab the dynamic Render environment string
        $dsnString = getenv('DB_DSN') ?: '';

        // 2. Manually crack the string open to bypass CodeIgniter's buggy DSN parser
        if (!empty($dsnString)) {
            $parsed = parse_url($dsnString);

            if ($parsed) {
                $this->default['hostname'] = $parsed['host'] ?? '';
                $this->default['username'] = $parsed['user'] ?? '';
                $this->default['password'] = $parsed['pass'] ?? '';
                
                // Remove the leading slash from the database name
                $this->default['database'] = ltrim($parsed['path'] ?? '', '/');
                
                // FIX: Force the port to be an exact integer using (int)
                $this->default['port']     = isset($parsed['port']) ? (int) $parsed['port'] : 26685;
            }
        }

        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }
}