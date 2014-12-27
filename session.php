<?php

require_once 'config.php';


function secure_random_sha256() {
    $i = 256;
    $cstrong = True;

    /* Always use SHA256, in the session_mngt() func
     * there is a length check based on the 64 bytes 
     * output here. */
    return hash('sha256',
                openssl_random_pseudo_bytes($i, $cstrong),
                False);
}


function session_cookie_new() {
    $site = new Site;

    /* $sessiontoken = generate_session($dbh, $cookie_hours * 3600); */
    $sessiontoken = secure_random_sha256();

    setcookie($site->cookie_name,
              $sessiontoken,
              time()+(3600 * $site->cookie_hours),
              '/',
              $site->cookie_scope,
              TRUE);
    return;
}


function session_is_cookie_valid($db, $cookie) {
    return True;
}


function session_mngt($db) {
    $site = new Site;

    /* Already have a cookie? 
     *  If yes, authenticate it. On failure, generate a new key.
     *  If no, generate new cookie */


    /* Length is 64 bytes, because of the SHA256 output */
    if (isset($_COOKIE[$site->cookie_name]) &&
        strlen($_COOKIE[$site->cookie_name]) == 64) {

        if (session_is_cookie_valid($db,
                        $_COOKIE[$site->cookie_name])) {

            /* TODO Session is known and valid, thus good */
            echo "got cookie: " . $_COOKIE[$site->cookie_name];

            /* TODO Check if cookie will expire in the next 30 minutes */
        } else {
            /* Session went bad, create new session.
             * Note: All passed logon cookies are mute */
            session_cookie_new();
        }
    } else {
        /* New session to be created */
        session_cookie_new();
        echo "set cookie";
        echo $site->cookie_name;
    }


    /* Generate session key, 
       by generating a random ID, 
       storing the ID + IP in the DB, 
       set the cookie. 
       Later, renew it regularly, */

}

?>
