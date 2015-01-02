<?php

require_once 'config.php';

class Session {
    public $id;
    public $token;
    public $valid_for_seconds;
    public $created_on;

    function fillFromRow($row) {
        $this->id                = $row['id'];
        $this->token             = $row['token'];
        $this->valid_for_seconds = $row['valid_for_seconds'];
        $this->created_on_unix   = $row['created_on_unix'];
    }

    function is_valid() {
        $site = new Site;

        /* Both the cookie lifetime and the max cookie lifetime template must be valid */
        if ((time() < ($this->created_on_unix + $this->valid_for_seconds)) &&
            (time() < ($this->created_on_unix + $site->cookie_seconds))) {
            return True;
        }
        return False;
    }
}

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

function session_search_by_token($token) {
    $db = $GLOBALS['db'];

    $sql = 'SELECT id, token, ' .
           '       valid_for_seconds, ' .
           '       created_on_unix ' .
           '  FROM sessions '.
           ' WHERE sessions.token = :token';

    $sth = $db->handle->prepare($sql);
    if (! $sth->execute(array(
        ':token'=>$token))) {
        return NULL;
    }
    $rs = $sth->fetchAll(PDO::FETCH_ASSOC); 

    foreach($rs as $row) {
        $session = new Session();
        $session ->fillFromRow($row);
        return $session;
    }
    return NULL;
}

function session_insert_token($token, $valid_for_seconds) {
    $db = $GLOBALS['db'];

    $sql = 'INSERT INTO sessions' .
           '            (token, valid_for_seconds, created_on_unix) '.
           '     VALUES (:token, :valid_for_seconds, :created_on_unix)';

    try {
        $sth = $db->handle->prepare($sql);
        $sth->execute(array(
            ':token'=>$token,
            ':valid_for_seconds'=>$valid_for_seconds,
            ':created_on_unix'=>time()));

    } catch (Exception $e) {
        if ($db->debug === True) {
            var_dump($e);
        }
        return False;
    }
    return True;
}

function session_update_token($token, $valid_for_seconds) {
    $db = $GLOBALS['db'];

    $sql = 'UPDATE sessions '.
           '   SET valid_for_seconds = :valid_for_seconds, '.
           '       created_on_unix = :created_on_unix '.
           ' WHERE token = :token';

    try {
        $sth = $db->handle->prepare($sql);
        $sth->execute(array(
            ':token'=>$token,
            ':valid_for_seconds'=>$valid_for_seconds,
            ':created_on_unix'=>time()));

    } catch (Exception $e) {
        if ($db->debug === True) {
            var_dump($e);
        }
        return False;
    }
    return True;
}

function session_cookie_new() {
    $db = $GLOBALS['db'];
    $site = new Site;

    $sessiontoken = secure_random_sha256();
    $valid_until_for_seconds = time() + $site->cookie_seconds;

    setcookie($site->cookie_name,
              $sessiontoken,
              $valid_until_for_seconds,
              time() + $site->cookie_seconds,
              '/',
              $site->cookie_scope,
              TRUE);

    session_insert_token($sessiontoken, $site->cookie_seconds);
    return;
}


function session_is_cookie_valid($cookie) {
    $db = $GLOBALS['db'];

    /* Fetch session data */
    $session = session_search_by_token($cookie);
    if ($session === NULL) {
        return False;
    } else {
        /* Check if cookie will expire or site has shortened 
         * the maxlifetime of cookies */
        return $session->is_valid();
    }

    /* TODO: IP check */
    return True;
}

function session_mngt() {
    $db = $GLOBALS['db'];
    $site = new Site;

    /* Is cookie set and if set a candidate session cookie?
     * Length is 64 bytes, because of the SHA256 output */
    if (isset($_COOKIE[$site->cookie_name]) &&
        strlen($_COOKIE[$site->cookie_name]) == 64) {

        /* Is the session cookie valid */
        if (session_is_cookie_valid($_COOKIE[$site->cookie_name])) {

            /* Update the cookie lifetime */
            session_update_token($_COOKIE[$site->cookie_name], $site->cookie_seconds);

        } else {
            /* Session went bad, create new session.
             * Note: All passed logon cookies are mute */
            session_cookie_new();
            return;
        }
    } else {
        /* New session to be created */
        session_cookie_new();
        return;
    }

    return;
}

/* clean up:
SELECT id, token, valid_for_seconds, created_on_unix, (valid_for_seconds + created_on_unix) as total from sessions group by id, token, total having  UNIX_TIMESTAMP(now()) > total; 
*/
?>
