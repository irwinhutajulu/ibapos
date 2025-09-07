<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Models\User;

try {
    $user = User::first();
    if ($user) {
        echo "First User Found:\n";
        echo "Email: " . $user->email . "\n";
        echo "Name: " . $user->name . "\n";
        echo "ID: " . $user->id . "\n";
    } else {
        echo "No users found in database\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
