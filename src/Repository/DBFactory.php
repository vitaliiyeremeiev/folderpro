<?php

namespace App\Repository;

class DBFactory
{
    private static string $host = 'db';
    private static string $db = 'folderpro';
    private static string $user = 'root';
    private static string $password = 'root';
    private static int $port = 3306;
    private static string $charset = 'utf8mb4';

    /**
     * @return \mysqli
     * @throws \Exception
     */
    public static function get(): \mysqli
    {
        try {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $db = new \mysqli(self::$host, self::$user, self::$password, self::$db, self::$port);
            $db->set_charset(self::$charset);
            $db->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);
        } catch (\Throwable $e){
            throw new \Exception('MySqli Error: ' . $e->getMessage());
        }
        return $db;
    }
}