<?php
namespace App\Config;

use MongoDB\Client;
use MongoDB\Database;

final class DatabaseConnection
{
    private static ?Database $database = null;

    public static function getDatabase(): Database
    {
        if (self::$database === null) {
            $uri = $_ENV['MONGODB_URI'] ?? getenv('MONGODB_URI') ?: 'mongodb://localhost:27017';
            $dbName = $_ENV['MONGODB_DATABASE'] ?? getenv('MONGODB_DATABASE') ?: 'local_bon_plan';

            self::$database = (new Client($uri))->selectDatabase($dbName);
        }
        return self::$database;
    }
}
