<?php
function ba_chars($word) {
    // Отделяем до и после слова лишние знаки, если есть
    $before_chars = '';
    $after_chars = '';
    $before_chars_pattern = '/^[^А-яӀ]+/u';
    $after_chars_pattern = '/[^А-яӀ]+$/u';
    // Получаем знаки перед словом 
    if ( preg_match($before_chars_pattern, $word, $matches) ) {
        $before_chars = $matches[0];
        $word = preg_replace($before_chars_pattern, '', $word);
    }
    // Получаем знаки после слова        
    if ( preg_match($after_chars_pattern, $word, $matches) ) {
        $after_chars = $matches[0];
        $word = preg_replace($after_chars_pattern, '', $word);
    }

    return [$word, $before_chars, $after_chars];
}
?>