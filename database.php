<?php

date_default_timezone_set('Etc/UTC');

function db_connect() {
    try {
        $db = new Database;
        $db->handle = new PDO($db->dsn, $db->user, $db->pass);

        if ($db->debug) {
            $db->handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    } catch (Exception $e) {
        return NULL;
    }

    return $db;
}
