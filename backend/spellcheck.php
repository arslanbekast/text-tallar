<?php
include ("settings/db.php");
include ("utils/clear.php");
include ("utils/clean_html.php");
include ("utils/ba_chars.php");

if (isset($_POST['text'])) {
    $text = clean_html($_POST['text']);
    $text = clear($text);
    
    // Делим текст на абзацы по знаку абзаца
    $paragraphs = explode("||", $text);

    // print_r($paragraphs);
    // exit();

    foreach ($paragraphs as &$paragraph) {

        if (!$paragraph) {
            continue;
        }

        $words = preg_split('/\s+/', $paragraph);

        // Проверка каждого слова
        foreach ($words as &$word) {

            // Получаем символы до и после слова
            [$before_chars, $after_chars] = ba_chars($word);

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