<?php
echo "PHP Version: " . phpversion() . "<br>";
echo "Intl Extension Loaded: " . (extension_loaded('intl') ? 'YES' : 'NO') . "<br>";
echo "Intl Classes exist: " . (class_exists('NumberFormatter') ? 'YES' : 'NO') . "<br>";
echo "PHP INI Path: " . php_ini_loaded_file() . "<br>";
