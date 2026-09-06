<?php
require_once 'classes/PasswordGenerator.php';

// Example matching task requirement: 9 chars (2 lower, 3 upper, 2 special, 2 numbers)
$generator = new PasswordGenerator(9, 3, 2, 2, 2);
echo "Generated Password: " . $generator->generate();