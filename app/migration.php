<?php
use Classes\DbConnection;

include_once 'autoloader.php';

$db = DbConnection::getInstance();

try{
    $db->multi_query("
        DROP TABLE IF EXISTS `users`;
        DROP TABLE IF EXISTS `roles`;
        DROP TABLE IF EXISTS `leagues`;
        
        CREATE TABLE IF NOT EXISTS `roles` (
            `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `key` VARCHAR(12) NOT NULL UNIQUE,
            `name` VARCHAR(16) NOT NULL UNIQUE
        );
        
        CREATE TABLE IF NOT EXISTS `leagues` (
            `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(12) NOT NULL,
            `description` VARCHAR(30)
        );
        
        CREATE TABLE IF NOT EXISTS `users` (
            `id` int(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(12) NOT NULL UNIQUE,
            `role_id` int(10) UNSIGNED DEFAULT NULL,
            `league_id` int(10) UNSIGNED DEFAULT NULL,
            FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
            FOREIGN KEY (`league_id`) REFERENCES `leagues` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        );
        
        INSERT INTO `roles` (`key`, `name`)
            VALUES ('leader', 'Leader'), ('alternate', 'Alternate'), ('solder', 'Solder');
        
        INSERT INTO `users` (`name`, `role_id`, `league_id`)
            VALUES ('Bill', NULL, NULL), ('Alex', NULL, NULL), ('Elen', NULL, NULL), ('Bob', NULL, NULL), 
            ('Jon', NULL, NULL), ('Kleo', NULL, NULL), ('Harry', NULL, NULL), ('James', NULL, NULL), 
            ('Harper', NULL, NULL), ('Avery', NULL, NULL), ('William', NULL, NULL);    
            
    ");
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode($e->getMessage());
    exit;
}

echo json_encode('Done');
