<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="css/style.css?v=<?php echo filectime('css/style.css'); ?>">
    <title>Текст таллар</title>
</head>
<body>
    <main>
        <div id='editor' class="editor" contenteditable="true" spellcheck="false"></div>
        <div id="context-menu">
            <ul id="suggestions"></ul>
        </div>
    </main>

<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script src="js/jquery-3.7.1.min.js"></script>
<script type="module" src="js/main.js?v=<?php echo filectime('js/main.js'); ?>"></script>
</body>
</html>