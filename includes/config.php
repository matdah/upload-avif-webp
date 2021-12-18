<?php
/**
 * Examle code for uploading images and creating miniatures.
 * Converts and outputs to: JPEG, Webp, AVIF
 * @author Mattias Dahlgren, 2021 <mattias.dahlgren@miun.se>
 * @version 1.0
 */

$devMode = true;

if($devMode) {
    // Activate error reporting
    error_reporting(-1);
    ini_set("display_errors", 1);
}

// Auto load classes
spl_autoload_register(function ($class_name) {
    include 'classes/' . $class_name . '.class.php';
});