<?php

class Article {
    public $id;
    public $subject;
    public $body;

    function fillFromRow($row) {
        $this->id      = $row['id'];
        $this->subject = $row['subject'];
        $this->body    = $row['body'];
    }
}

function article_load($id) {
    $db = $GLOBALS['db'];

    $sql = 'SELECT id, subject, body'.
           '  FROM articles'.
           ' WHERE id = :id';

    $sth = $db->handle->prepare($sql);
    if (! $sth->execute(array(':id'=>$id))) {
        return NULL;
    }
    /* $rs = $sth->fetchAll(PDO::FETCH_ASSOC);  */
    $rs = db_cast_query_results($sth);

    foreach($rs as $row) {
        $article = new Article();
        $article->fillFromRow($row);
    }
    return $article;
}

?>
