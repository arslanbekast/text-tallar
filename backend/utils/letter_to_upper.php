<?php
// Функция определения является ли первая буква ЗАГЛАВНОЙ
function fl_is_upper($word) {
    $first_letter = mb_substr($word, 0, 1);
    if (mb_strtolower($first_letter) !== $first_letter) return True;
    else return False;
}

// Функция для перевода первой буквы строки в верхний регистр
function fl_to_upper($str) {
    return mb_strtoupper(mb_substr($str, 0, 1)) . mb_substr($str, 1);
}

// Функция проверяет на наличие в слове буквы в верхнем регистре, если есть переводит нужную букву в верхний регистр.
function letter_to_upper($word, $new_word) {
    if ( preg_match('/[-_–]/u', $new_word, $matches) ) {
        $defis = $matches[0];
        $defis_pos = mb_strpos($word, $defis);
        $word1 = mb_substr($word, 0, $defis_pos);
        $word2 = mb_substr($word, $defis_pos+1);

        $word_new1 = mb_substr($new_word, 0, $defis_pos);
        $word_new2 = mb_substr($new_word, $defis_pos+1);

        if ( fl_is_upper($word1) ) $word_new1 = fl_to_upper($word_new1);
        if ( fl_is_upper($word2) ) $word_new2 = fl_to_upper($word_new2);
        
        return $word_new1 . $defis . $word_new2;
    }

    if ( fl_is_upper($word) ) return fl_to_upper($new_word);
    return $new_word;
}