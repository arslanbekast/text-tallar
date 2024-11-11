$(document).ready(main);

function main() {

    let timer;
    // Переменная для хранения позиции курсора
    let savedCursorPosition = null;

    // Функция для сохранения текущей позиции курсора
    function saveCursorPosition() {
        const selection = quill.getSelection();
        if (selection) {
            savedCursorPosition = selection.index; // Сохраняем позицию курсора
        }
    }

    // Функция для восстановления позиции курсора
    function restoreCursorPosition() {
        if (savedCursorPosition !== null) {
            quill.setSelection(savedCursorPosition); // Восстанавливаем позицию курсора
        }
    }

    const quill = new Quill('#editor', {
        modules: {
            toolbar: true    // Snow includes toolbar by default
          },
        placeholder: 'Введите текст...',
        theme: 'snow'
    });

    quill.on('text-change', (delta, oldDelta, source) => {
        if (source === 'user') {
            clearTimeout(timer);
            timer = setTimeout(checkSpelling, 1000);
        }
    });

    // Функция для отправки текста на сервер
    function checkSpelling() {
        const text = quill.getSemanticHTML();
        // const text = quill.getContents();
        saveCursorPosition();

        if (!text) return;
        
        $.ajax({
            url: "backend/spellcheck.php", // файл PHP для проверки
            type: "POST",
            data: { text: text },
            success: function(response) {
                quill.clipboard.dangerouslyPasteHTML(response);
                restoreCursorPosition();
            }
        });
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

                const screenWidth = $(window).width();
                const contextMenuWidth = $('#context-menu').width();
                const contextMenuTop = e.pageY + 10 + 'px';
                let contextMenuLeft = e.pageX + 'px';
                const contextMenuLeftAndWidth = e.pageX + contextMenuWidth; 
                
                // Если контекстное меню выходит за пределы экрана
                // выводим меню чуть левее
                if (contextMenuLeftAndWidth > screenWidth) {
                    contextMenuLeft = e.pageX - (contextMenuLeftAndWidth - screenWidth) - 20 + 'px';
                }

                // Позиционируем и показываем меню
                menu.css({ top: contextMenuTop, left: contextMenuLeft}).fadeIn();
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