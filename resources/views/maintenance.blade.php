<!doctype html>
<html lang="ru">

<head>
    <meta charset=" utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0A0C10">

    <title>
        Сайт на техническом обслуживании | ФБКИ ИГУ
    </title>

    <link href="{{ mix('fonts/fonts.css') }}" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            width: 100vw;
            height: 100vh;
            box-sizing: border-box;
            padding-bottom: 10vh;

            overflow: hidden;

            background: #0A0C10;
        }

        p {
            all: unset;
        }

        .maintenance-message {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            width: 100%;
            height: 100%;

            pointer-events: none;
        }

        .maintenance-message>p {
            color: #5D6268;
            font-family: "Golos Text", "Inter", "Manrope", "Segoe UI", "Arial", sans-serif;
            font-size: 20px;
            font-style: normal;
            font-weight: 300;
            text-align: center;
            margin-top: 8px;
        }

        .maintenance-message>.logo-fbki-mini {
            zoom: 4;
            width: 20vw;
            height: fit-content;
        }

        .maintenance-message>.logo-fbki-mini path {
            stroke: #5D6268;
            stroke-width: 0.25px;
            fill: transparent;
        }

    </style>

</html>


<body>
    <div class="maintenance-message">
        @svg('/assets/icons/home/header/logo-fbki-mini.svg', 'logo-fbki-mini')
        <p>
            Ведутся технические работы
        </p>
        <p>
            попробуйте зайти позже
        </p>
    </div>
</body>

</html>
