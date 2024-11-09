<?php
// Функция для очистки переменной
function clear($var) {
    // $var = mb_strtolower($var, "UTF-8");
    $var = trim($var);
    // $var = strip_tags($var);
    // $var = htmlentities($var, ENT_IGNORE, "UTF-8");
    $var = stripcslashes($var);
    $var = replace_lat_to_kir($var);
    $var = replace_I($var);
    return $var;
    // return mysqli_real_escape_string($db, $var);
}

// Функция замены чеченской буквы I
function replace_I($string) {
    if ( preg_match_all('/[А-я][1Ii]|[1Ii][А-я]/u', $string, $matches) ) {
        
        $search_array = Array('1','I','i','І','і','ӏ');
        $match_array = $matches[0];

        $match_replaced_array = str_replace($search_array, "Ӏ", $match_array);
        $string = str_replace($match_array, $match_replaced_array, $string);
    }
    return $string;
}

// Функция замены латинских символов на кириллические
function replace_lat_to_kir($text) {
    if ( preg_match('/[AaBCcEeHKMOoPpTXxYy]/u',$text) ){
        $lat_array = ['A','a','B','C','c','E','e','H','K','M','O','o','P','p','T','X','x','Y','y'];
        $kir_array = ['А','а','В','С','с','Е','е','Н','К','М','О','о','Р','р','Т','Х','х','У','у'];

        // Делим текст на абзацы по знаку абзаца
        $paragraphs_array = explode("\n", $text);
        $new_paragraphs_array = Array();

        // Перебираем абзацы
        foreach ($paragraphs_array as $key => $paragraph) {
            // Делим абзацы на слова по пробелам
            $words_array = mb_split('\s+', $paragraph);
        
            // Перебираем слова абзаца
            foreach ($words_array as $key => $word) {

                if ( preg_match_all('/[AaBCcEeHKMOoPpTXxYy]/u', $word, $matches) && preg_match('/[А-я]/u', $word) ) {
                    $word = str_replace($lat_array, $kir_array, $word);
                }

                // Обратно собираем слова в массив
                $words_array[$key] = $word;
            }

            // Обратно собираем абзацы в массив
            $new_paragraph = implode(' ', $words_array);
            $new_paragraphs_array[] = $new_paragraph;
        }

        // Обратно собираем весь текст
        $text = implode("\n", $new_paragraphs_array);
    
    }
    return $text;
}
?>