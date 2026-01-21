<?php
/**
 * Database Configuration
 * Part 1: Legacy PHP Refactoring
 */

// Main database
$servername = "localhost";
$username = "root"; // Laragon default
$password = "7605";     // Laragon default
$dbname = "main_db";

// Create main database connection
$con = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

/**
 * Get site-specific database configuration
 * @param int $site_id
 * @return array|false
 */
function getSiteDbConfig($site_id) {
    $configs = [
        1 => [
            'host' => 'localhost',
            'username' => 'root',
            'password' => '7605',
            'dbname' => 'site1_db'
        ],
        2 => [
            'host' => 'localhost',
            'username' => 'root',
            'password' => '7605',
            'dbname' => 'site2_db'
        ],
    ];

    return isset($configs[$site_id]) ? $configs[$site_id] : false;
}
