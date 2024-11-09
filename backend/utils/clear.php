<?php
function clear($var) {
    // $var = mb_strtolower($var, "UTF-8");
    $var = trim($var);
    // $var = strip_tags($var);
    // $var = htmlentities($var, ENT_IGNORE, "UTF-8");
    $var = stripcslashes($var);
    $var = str_replace('ӏ','Ӏ',$var);
    $var = str_replace('І','Ӏ',$var);
    $var = str_replace('I','Ӏ',$var);
    $var = str_replace('i','Ӏ',$var);
    $var = str_replace('1','Ӏ',$var);
    return $var;
    // return mysqli_real_escape_string($db, $var);
}
?>