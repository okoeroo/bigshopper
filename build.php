<?php

require_once 'config.php';

class Head {
    public $title;
    public $keywords;
    public $description;
    public $viewport;
    public $stylesheet;

    function __construct() {
        $this->setup();
    }

    function setup() {
        $site = new Site;
        $this->title = $site->title;
        $this->keywords = $site->keywords;
        $this->description = $site->description;
        $this->stylesheet = $site->stylesheet;
    }

    function display() {
        echo '<!DOCTYPE html>' . "\n";
        echo '<html>' . "\n";
        echo '  <head>' . "\n";
        echo '    <title>' . $this->title . '</title>' . "\n";
        echo '    <meta charset="utf-8" />' . "\n";
        echo '    <meta name="keywords" content="';
        $y = count($this->keywords);
        for ($x=0; $x<$y; $x++) {
            if ($x !== 0) {
                echo ', ';
            }
            echo $this->keywords[$x];
        }
        echo '" />' .  "\n";
        echo '    <meta name="description" content="' . $this->description . '" />' .  "\n";
        /* echo '    <meta name="viewport" content="width=device-width,initial-scale=1" />' .  "\n"; */
        echo '    <link rel="stylesheet" type="text/css" media="all" href="' . $this->stylesheet . '" />' . "\n";

        echo '  </head>' . "\n";
        echo '  <body>' . "\n";

        echo '<div id="fb-root"></div><script>(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0];       if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0"; fjs.parentNode.insertBefore(js, fjs); }(document, \'script\', \'facebook-jssdk\'));</script>';

        echo '    <div class="page">' . "\n";
        echo '      <div class="header">' . "\n";
        echo '        <h1>All kids love</h1>' . "\n";
        echo '      </div>' . "\n";
    }
}

class Tail {
    function display() {
        echo '      <div class="footer">' . "\n";
        echo '          All kids love' . "\n";
        echo '          <div class="fb-like" data-href="https://www.facebook.com/pages/All-kids-love/1485746685040904" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true"></div>';
        echo '      </div>' . "\n";
        echo '    </div>' . "\n";
        echo '  </body>' . "\n";
        echo '</html>' . "\n";
    }
}


/***********************/

function article_display($article) {
        echo '      <div class="section">';
        echo '          <h1>'.$article->subject.'</h1>' . "\n";
        echo '          <p>'."\n";
        echo '          '.$article->body. "\n";
        echo '          </p>'."\n";
        echo '      </div>' . "\n";
}

function section_display($article_id) {
    $article = article_load($article_id);
    article_display($article);
}


function form_field_text($name, $text, $default_value, $max_chars, $placeholder, $autofocus, $required, $autocomplete) {
    echo '<label for="'.$name.'">'.$text.':</label>';
    echo '<input type="text" name="'.$name.'" id="'.$name.'"';
    echo ' value="'.$default_value.'" size="'.$max_chars.'" maxlength="'.$max_chars.'" placeholder="'.$placeholder.'"';
    if ($autofocus === True) {
        echo ' autofocus';
    }
    if ($required === True) {
        echo ' required';
    }
    if ($autocomplete === True) {
        echo 'autocomplete="on"';
    }
    echo ' />';
    echo "\n";
}

function form_field_file($name, $text, $autofocus, $required) {
    echo '<label for="'.$name.'">'.$text.':</label>';
    echo '<input type="file" name="'.$name.'" id="'.$name.'"';
    if ($autofocus === True) {
        echo ' autofocus';
    }
    if ($required === True) {
        echo ' required';
    }
    echo ' />';
    echo "\n";
}

function form_field_dropdown($name, $text, $list, $selected_value, $autofocus, $required) {
    echo '<label for="'.$name.'">'.$text.': </label>';

    echo '<select name="'.$name.'" id="'.$name.'"';
    if ($autofocus === True) {
        echo ' autofocus';
    }
    if ($required === True) {
        echo ' required';
    }
    echo '>';
    foreach ($list as $row) {
        echo '<option value="'.$row['value'].'" ';
        if ($selected_value === $row['value']) {
            echo 'selected';
        }
        echo '>'.$row['name'].'</option>';
    }
    echo '</select>';
}

/*
function form_field_radio($name, $text, $max, $placeholder, $autofocus, $required, $autocomplete) {
                        <label for="faction">Speler of observator?</label>
                        <ul>
                            <li>
                                <div class="tip">
                                <label for="rd_speler">Paintball speler (12,50 euro)</label>
                                <input id="rd_speler" class="radio" type="radio"
                                    name="gotgame" value="speler"
                                    checked="checked"></input>
                                </div>
                            </li>
                            <li>
                                <div class="tip">
                                <label for="rd_nonspeler">Observator (5,- euro)</label>
                                <input id="rd_nonspeler" class="radio" type="radio"
                                    name="gotgame"
                                    value="observator"></input>
                                </div>
                            </li>
                        </ul>
*/

?>
