<?php
$devMode = true;

if($devMode) {
    // Aktivera felrapportering
    error_reporting(-1);
    ini_set("display_errors", 1);
}

// Ladda klasser
spl_autoload_register(function ($class_name) {
    include 'classes/' . $class_name . '.class.php';
});