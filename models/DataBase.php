<?php
class DataBase{
    # Conexión Local
    public static function connection(){
        $hostname = "localhost";
        $port = "3307";
        $database = "db_inventory";
        $username = getenv('DB_USER') ?: "app_user";
        $password = getenv('DB_PASSWORD');
        $pdo = new PDO("mysql:host=$hostname;port=$port;dbname=$database;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }
}
