<?php
include ("settings/db.php");
include ("utils/clear.php");

function cleanHtml($text) {
    // Удаляем все теги <span> и </span>
    $text = preg_replace('/<\/?span[^>]*>/', '', $text);

    // Заменяем </div> на пустую строку и <div>, <br>, <br /> на \n
    $text = preg_replace(['/<\/div>/i','/<div>/i', '/<br\s*\/?>/i'], ['', "\n", "\n"], $text);

    return $text;
}

if (isset($_POST['text'])) {
    $text = cleanHtml($_POST['text']);
    $text = preg_replace("/\n+/u","\n",$text);
    $text = clear($text);
    $text = trim(str_replace('&nbsp;', ' ', $text));
    
    // Делим текст на абзацы по знаку абзаца
    $paragraphs = explode("\n", $text);

    foreach ($paragraphs as &$paragraph) {

        $words = preg_split('/\s+/', $paragraph);

        // Проверка каждого слова
        foreach ($words as &$word) {

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

            $stmt = $pdo->prepare("SELECT COUNT(*) FROM words WHERE word = :word");
            $stmt->execute(['word' => $word]);
            
            if ($stmt->fetchColumn() == 0) { // Если слово не найдено в базе данных
                $word = "$before_chars<span class='spell-error'>$word</span>$after_chars"; // Оборачиваем ошибочные слова
            }
        }

        $paragraph = "<p>".implode(' ', $words)."</p>"; // Возвращаем текст с подчёркнутыми ошибками
    }
    echo implode(' ', $paragraphs);
    
}

?>