<?php

require_once 'config.php';
require_once 'database.php';
require_once 'product.php';
require_once 'category.php';

date_default_timezone_set('Etc/UTC');


function return_to_sender() {
    echo '<!DOCTYPE HTML>'."\n";
    echo '<html lang="en-US"><head><meta charset="UTF-8">'."\n";
    echo '<meta http-equiv="refresh" content="1;url=/admin.php">';
    echo '</head></html>';
}

/* Only POST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo 'Error: POST only';
    http_response_code(400);
    return_to_sender();
    return;
}

/* Open DB */
$db = db_connect();
if ($db && $db->handle === NULL) {
    http_response_code(500);
    return_to_sender();
    return;
}

/* Fetch POST info and generate a Category */
if (! isset($_POST['id'])) {
    echo 'No ID set to remove';
    http_response_code(400);
    return_to_sender();
    return;
}

if (! product_delete_by_id($db, $_POST['id'])) {
    http_response_code(500);
    var_dump($_POST);
    display_failure();
    return_to_sender();
    return;
}

return_to_sender();

?>
