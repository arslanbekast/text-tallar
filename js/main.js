$(document).ready(main);

function main() {
    let timer;

    // Функция для отправки текста на сервер
    function checkSpelling() {
        const text = $("#editor").html();

        if (!text) return;
        
        $.ajax({
            url: "backend/spellcheck.php", // файл PHP для проверки
            type: "POST",
            data: { text: text },
            success: function(response) {
                $("#editor").html(response + '<span>&nbsp;</span>'); // Обновляем содержимое с проверенными словами
                moveCursorToEnd($("#editor")[0]); // Перемещаем курсор в конец
            }
        });
    }

    $("#editor").on("input", function() {
        $(this).html($(this).html().replace(/<\/?u>/g, ""));
        moveCursorToEnd($("#editor")[0]);
    });
    

    // Отслеживаем изменения в contenteditable
    $("#editor").on("keydown", function(e) {
        clearTimeout(timer);
        if (e.key === " ") {  // Если нажата клавиша пробела
            timer = setTimeout(checkSpelling, 500); // Отправляем запрос с задержкой в 700 мс
        }
        
    });

    // Функция для перемещения курсора в конец contenteditable
    function moveCursorToEnd(element) {
        const range = document.createRange();
        const selection = window.getSelection();
        range.selectNodeContents(element);
        range.collapse(false); // Устанавливаем курсор в конец
        selection.removeAllRanges();
        selection.addRange(range);
    }

    // Обработчик для правого клика по ошибочному слову
    $(document).on("contextmenu", ".spell-error", function(e) {
        e.preventDefault();
        const currentWord = $(this);
        const incorrectWord = currentWord.text();
        $('.spell-error').removeClass('current');
        currentWord.addClass('current');

        // Отправляем запрос для получения вариантов исправления
        $.ajax({
            url: "backend/suggestions.php",
            type: "POST",
            data: { word: incorrectWord },
            success: function(response) {
                const menu = $("#context-menu");
                const suggestionsList = $("#suggestions");
                const suggestions = JSON.parse(response);

                // Очищаем меню и добавляем новые варианты
                suggestionsList.empty();
                if (suggestions.length) {
                    suggestions.forEach(suggestion => {
                        suggestionsList.append(`<li>${suggestion}</li>`);
                    });
                } else {
                    suggestionsList.append("<span class='no-options'>Нет вариатов</span>")
                }
                

                // Позиционируем и показываем меню
                menu.css({ top: e.pageY + 5, left: e.pageX}).fadeIn();
            }
        });
    });

    // Обработчик для клика по варианту исправления
    $(document).on("click", "#suggestions li", function() {
        const newWord = $(this).text();
        const $errorWord = $(".spell-error.current");
        
        $errorWord.text(newWord).removeClass("spell-error current");

        $("#context-menu").hide();
    });

    // Закрываем контекстное меню при клике вне его
    $(document).click(function(e) {
        if (!$(e.target).closest("#context-menu").length) {
            $("#context-menu").hide();
        }
    });


}