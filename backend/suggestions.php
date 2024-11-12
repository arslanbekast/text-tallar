<?php
include ("settings/db.php");
include ("utils/clear.php");
include ("utils/letter_to_upper.php");
include ("utils/convert_word.php");

if (isset($_POST['word'])) {
    $incorrectWord = clear($_POST['word']);
    $suggestions = [];
    $newSpellingWords = convert_word($incorrectWord);

    // Определяем первую букву ошибочного слова
    // $firstLetter = mb_substr($incorrectWord, 0, 1);
    
    // // Подготавливаем запрос для получения слов, начинающихся на ту же букву
    // $stmt = $pdo->prepare("SELECT word FROM words WHERE word LIKE :firstLetter");
    // $stmt->execute(['firstLetter' => $firstLetter . '%']);
    // $words = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $pdo->query("SELECT word FROM words");
    $words = $stmt->fetchAll(PDO::FETCH_COLUMN);

    

    foreach ($words as $word) {
        // Рассчитываем расстояние Левенштейна
        $distance = levenshtein(mb_strtolower($incorrectWord), mb_strtolower($word));
        
        // Добавляем слова, отличающиеся на 1-2 буквы
        if ($distance > 0 && $distance <= 2) {
            $suggestions[] = letter_to_upper($incorrectWord, $word);
        }
    }

    // Получаем из базы слова по новой орфографии
    foreach ($newSpellingWords as $word) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM words WHERE word = :word");
        $stmt->execute(['word' => $word]);

        if ($stmt->fetchColumn() > 0) {
            $suggestions[] = letter_to_upper($incorrectWord, $word);
        }
    }
    
    // Возвращаем варианты в формате JSON
    echo json_encode($suggestions);
}
?>