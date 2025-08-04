<?php
$target = realpath(__DIR__ . '/../storage/app/public'); 
$shortcut = __DIR__ . '/storage'; 

if ($target === false) {
    echo "Error: Target directory does not exist.";
    exit;
}

if (file_exists($shortcut)) {
    echo "Symlink already exists. Removing it...\n";
    unlink($shortcut);  
}

if (symlink($target, $shortcut)) {
    echo "Symlink created successfully.";
} else {
    echo "Error: Failed to create the symlink.";
}

?>
