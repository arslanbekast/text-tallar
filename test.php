<?php
include ("backend/utils/ba_chars.php");
include ("backend/utils/convert_word.php");
include ("backend/utils/clean_html.php");

// $word = "(=====абоде}}}}).";
// [$before_chars, $after_chars] = ba_chars($word);
// print_r($before_chars);
// echo "    ";
// print_r($after_chars);

echo clean_html('<p>абоде</p> ')

?>