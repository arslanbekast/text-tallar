<?php
function clean_html($text) {
    $text = preg_replace('/<(p|div|br)(\s[^>]*)?>/i', "||", $text);
    $text = strip_tags($text);
    return $text;
}
?>