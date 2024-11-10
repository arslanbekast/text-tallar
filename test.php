<?php
include ("backend/utils/ba_chars.php");

$word = "(=====абоде}}}}).";
[$before_chars, $after_chars] = ba_chars($word);
print_r($before_chars);
echo "    ";
print_r($after_chars);

?>