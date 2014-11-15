<?php

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
        echo '    <meta name="viewport" content="width=device-width,initial-scale=1" />' .  "\n";
        echo '    <link rel="stylesheet" type="text/css" media="all" href="' . $this->stylesheet . '" />' . "\n";
        echo '  </head>' . "\n";
        echo '  <body>' . "\n";
    }
}

class Tail {
    function display() {
        echo '  </body>' . "\n";
        echo '</html>' . "\n";
    }
}


?>
