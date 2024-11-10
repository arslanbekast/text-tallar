<?php
include ("settings/db.php");
include ("utils/clear.php");
include ("utils/letter_to_upper.php");

if (isset($_POST['word'])) {
    $incorrectWord = clear($_POST['word']);
    $suggestions = [];

    // Определяем первую букву ошибочного слова
    $firstLetter = mb_substr($incorrectWord, 0, 1);

    // Подготавливаем запрос для получения слов, начинающихся на ту же букву
    $stmt = $pdo->prepare("SELECT word FROM words WHERE word LIKE LOWER(:firstLetter)");
    $stmt->execute(['firstLetter' => $firstLetter . '%']);

    $words = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($words as $word) {
        // Рассчитываем расстояние Левенштейна
        $distance = levenshtein($incorrectWord, $word);
        
        // Добавляем слова, отличающиеся на 1-2 буквы
        if ($distance > 0 && $distance <= 2) {
            $suggestions[] = letter_to_upper($incorrectWord, $word);
        }
    }
    
    // Возвращаем варианты в формате JSON
    echo json_encode($suggestions);
}
?>