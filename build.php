<?php

require_once 'config.php';
require_once 'navigation.php';

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
        echo '    <link rel="stylesheet" type="text/css" media="all" href="lightbox/css/lightbox.css" />' . "\n";

        echo '    <script type="text/javascript" src="lightbox/js/jquery-1.11.0.min.js"></script>' . "\n";
        echo '    <script type="text/javascript" src="lightbox/js/lightbox.min.js"></script>' . "\n";

        echo '  </head>' . "\n";
        echo '  <body>' . "\n";

        echo '<div id="fb-root"></div><script>(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0];       if (d.getElementById(id)) return; js = d.createElement(s); js.id = id; js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0"; fjs.parentNode.insertBefore(js, fjs); }(document, \'script\', \'facebook-jssdk\'));</script>';

        echo '    <div class="page">' . "\n";
        echo '      <div class="header">' . "\n";
        echo '        <h1>All kids love</h1>' . "\n";
        echo '      </div>' . "\n";

        /* Navigation bar */
        navigation_display(navigation_load());

        /* Begin of article */
        echo '      <div class="section">';
        echo '          <p>'."\n";

    }
}

class Tail {
    function display() {
        /* End of article */
        echo '          </p>'."\n";
        echo '      </div>' . "\n";

        /* Footer */
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


function form_field_text($name, $text, $default_value, $width_chars, $max_chars,
                         $placeholder, $autofocus, $required, $autocomplete) {
    echo '<label for="'.$name.'">'.$text.'</label>';
    echo '<input type="text" name="'.$name.'" id="'.$name.'"';
    echo ' value="'.$default_value.'" size="'.$width_chars.
         '  " maxlength="'.$max_chars.'" placeholder="'.$placeholder.'"';
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

function form_field_email($name, $text, $default_value, $width_chars, $max_chars,
                          $placeholder, $autofocus, $required, $autocomplete) {
    echo '<label for="'.$name.'">'.$text.'</label>';
    echo '<input type="email" name="'.$name.'" id="'.$name.'"';
    echo ' value="'.$default_value.'" size="'.$width_chars.
         '  " maxlength="'.$max_chars.'" placeholder="'.$placeholder.'"';
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
    echo '<label for="'.$name.'">'.$text.'</label>';
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
    echo '<label for="'.$name.'">'.$text.' </label>';

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

function form_field_radio($name, $text, $list, $selected_value, $autofocus, $required) {
    if (! empty($text)) {
        echo '<label for="'.$name.'">'.$text.' </label>';
    }

    foreach ($list as $row) {
        echo '<input class="radio" type="radio" id="'.$name.'"';

        if ($autofocus === True) {
            echo ' autofocus';
        }
        if ($required === True) {
            echo ' required';
        }

        echo ' name="'.$name.'" ';
        echo ' value="'.$row['value'].'" ';

        if ($selected_value === $row['value']) {
            echo ' checked="checked" ';
        }
        echo '>';
        echo $row['text'];

        echo '</input>';
    }
}

function form_field_checkbox($name, $text, $checked, $autofocus, $required) {
    if (! empty($text)) {
        echo '<label for="'.$name.'">'.$text.' </label>';
    }

    echo '<input class="checkbox" type="checkbox" id="'.$name.'"';

    if ($checked === True) {
        echo ' checked';
    }
    if ($autofocus === True) {
        echo ' autofocus';
    }
    if ($required === True) {
        echo ' required';
    }

    echo ' name="'.$name.'" ';
    echo '>';
    echo '</input>';
}

?>
