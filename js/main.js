

$(document).ready(main);

function main() {

    const quill = new Quill('#editor', {
        placeholder: 'Введите текст...',
        theme: 'snow'
    });

    let timer;

    quill.on('text-change', (delta, oldDelta, source) => {
        if (source === 'user') {
            clearTimeout(timer);
            // if (e.key === " ") {  // Если нажата клавиша пробела
            //     timer = setTimeout(checkSpelling, 500); // Отправляем запрос с задержкой в 700 мс
            // }
            timer = setTimeout(checkSpelling, 500);
        }
        
    });

    

    let savedRange = null; // Переменная для хранения позиции курсора
    
    // Функция для сохранения текущей позиции курсора
    // function saveCursorPosition() {
    //     const selection = window.getSelection();
    //     if (selection.rangeCount > 0) {
    //         savedRange = selection.getRangeAt(0);
    //     }
    // }

    // Функция для восстановления позиции курсора
    // function restoreCursorPosition() {
    //     if (savedRange) {
    //         const selection = window.getSelection();
    //         selection.removeAllRanges();
    //         selection.addRange(savedRange);
    //     }
    // }

    // Функция для отправки текста на сервер
    function checkSpelling() {
        // const text = $("#editor").html();
        const text = quill.getSemanticHTML();
        console.log(text);
        if (!text) return;
        
        $.ajax({
            url: "backend/spellcheck.php", // файл PHP для проверки
            type: "POST",
            data: { text: text },
            success: function(response) {
                quill.clipboard.dangerouslyPasteHTML(response);
                console.log(response);
                // $("#editor").html(response); // Обновляем содержимое с проверенными словами
                // $("#editor p:last").append('<span>&nbsp;</span>');
                // restoreCursorPosition();
                // moveCursorToEnd($("#editor")[0]); // Перемещаем курсор в конец
            }
        });
    }

    // $("#editor").on("input", function() {
    //     $(this).html($(this).html().replace(/<\/?u>/g, ""));
    //     moveCursorToEnd($("#editor")[0]);
    // });
    

    // Отслеживаем изменения в contenteditable
    // $("#editor").on("keydown", function(e) {
    //     saveCursorPosition();
    //     clearTimeout(timer);
    //     // timer = setTimeout(checkSpelling, 1000); // Отправляем запрос с задержкой в 700 мс
    //     if (e.key === " ") {  // Если нажата клавиша пробела
    //         timer = setTimeout(checkSpelling, 500); // Отправляем запрос с задержкой в 700 мс
    //     }
        
    // });

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