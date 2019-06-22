<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1">
    <meta charset="UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Генератор QR-кодов</title>

    <link rel="stylesheet" type="text/css" href="components/assets/css/main.css">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {packages: ['corechart']});
    </script>
    <script type="text/javascript" charset="UTF-8" src="https://www.gstatic.com/charts/46.2/loader.js"></script>

    <script type="text/javascript" src="components/js/require-config.js"></script>
    <script type="text/javascript" src="components/js/libs/require.js"></script>
    <script type="text/javascript" src="components/js/main-bundle.js"></script>
    <script type="text/javascript" charset="utf-8" async="" data-requirecontext="_" data-requiremodule="user"
            src="components/js/user.js"></script>
    <link id="load-css-0" rel="stylesheet" type="text/css"
          href="https://www.gstatic.com/charts/46.2/css/core/tooltip.css">
    <link id="load-css-1" rel="stylesheet" type="text/css" href="https://www.gstatic.com/charts/46.2/css/util/util.css">
    <script type="text/javascript" charset="UTF-8"
            src="https://www.gstatic.com/charts/46.2/js/jsapi_compiled_format_module.js"></script>
    <script type="text/javascript" charset="UTF-8"
            src="https://www.gstatic.com/charts/46.2/js/jsapi_compiled_default_module.js"></script>
    <script type="text/javascript" charset="UTF-8"
            src="https://www.gstatic.com/charts/46.2/js/jsapi_compiled_ui_module.js"></script>
    <script type="text/javascript" charset="UTF-8"
            src="https://www.gstatic.com/charts/46.2/js/jsapi_compiled_corechart_module.js"></script>

</head>


<body id="pgpage-category" class="sidebar-desktop-active" data-page-entry="list" data-inactivity-timeout="0">
<nav id="navbar" class="navbar navbar-default navbar-fixed-top">

    <div class="toggle-sidebar pull-left" title="Засветить/заховать сайдбар (Ctrl+[, Ctrl+])">
        <button class="icon-toggle-sidebar"></button>
    </div>

    <div class="container-fluid">
        <div class="navbar-header">

            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navnav"
                    aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>

        <div class="navbar-collapse collapse" id="navnav">

            <ul id="nav-menu" class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">
                        <i class="icon-user"></i>
                        Admin
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="login.php?operation=logout">Выход</a></li>
                    </ul>
                </li>
            </ul>

        </div>
    </div>
</nav>


<div class="container-fluid">

    <div class="row sidebar-owner">


        <div class="sidebar">
            <div class="content">

                <div class="sidebar-nav">

                    <ul class="nav nav-pills nav-stacked">


                        <li class="active" title="Генерировать QR-код">
                            <span class="sidebar-nav-item">
                                Генерировать QR-код
                            </span>
                        </li>

                        <li>
                            <a class="sidebar-nav-item" href="qr-read.html" title="Читать QR-код">
                                Читать QR-код
                            </a>
                        </li>

                        <li>
                            <a class="sidebar-nav-item" href="category.php" title="Категории">
                                Категории
                            </a>
                        </li>

                        <li>
                            <a class="sidebar-nav-item" href="employee.php" title="Сотрудники">
                                Сотрудники
                            </a>
                        </li>


                        <li>
                            <a class="sidebar-nav-item" href="material.php" title="Материалы">
                                Материалы
                            </a>
                        </li>


                        <li>
                            <a class="sidebar-nav-item" href="position.php" title="Должности">
                                Должности
                            </a>
                        </li>

                        <li>
                            <a class="sidebar-nav-item" href="revision.php" title="Инвентаризация">
                                Инвентаризация
                            </a>
                        </li>
                        <li>
                            <a class="sidebar-nav-item" href="storage.php" title="Место хранения">
                                Место хранения
                            </a>
                        </li>


                    </ul>

                </div>

            </div>
        </div>
        <div class="sidebar-backdrop"></div>

        <div class="col-md-12">
            <div class="sidebar-outer">
                <div class="container-padding">
                    <div class="page-header">
                        <ol class="breadcrumb pgui-breadcrumb">
                            <li><a href="index.php"><i class="icon-home"></i></a></li>

                            <li class="dropdown">
                                Генерация QR-кодов


                            </li>
                        </ol>
                        <h1>Генерация QR-кодов</h1>
                    </div>
                    <?php


                    //set it to writable location, a place for temp generated PNG files
                    $PNG_TEMP_DIR = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;

                    //html PNG location prefix
                    $PNG_WEB_DIR = 'temp/';

                    include "qr-gen/qrlib.php";

                    //ofcourse we need rights to create temp dir
                    if (!file_exists($PNG_TEMP_DIR))
                        mkdir($PNG_TEMP_DIR);


                    $filename = $PNG_TEMP_DIR . 'test.png';

                    //processing form input
                    //remember to sanitize user input in real-life solution !!!
                    $errorCorrectionLevel = 'H';
                    if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L', 'M', 'Q', 'H')))
                        $errorCorrectionLevel = $_REQUEST['level'];

                    $matrixPointSize = 10;
                    if (isset($_REQUEST['size']))
                        $matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);


                    if (isset($_REQUEST['data'])) {

                        //it's very important!
                        if (trim($_REQUEST['data']) == '')
                            die('data cannot be empty! <a href="?">back</a>');

                        // user data
                        $filename = $PNG_TEMP_DIR . 'test' . md5($_REQUEST['data'] . '|' . $errorCorrectionLevel . '|' . $matrixPointSize) . '.png';
                        QRcode::png($_REQUEST['data'], $filename, $errorCorrectionLevel, $matrixPointSize, 2);

                    } else {

                        //default data
                       // echo 'Данные можно отправлять в GET параметрах: <a href="?data=данные из гет-параметров">данные из гет-параметров</a><hr/>';
                       // QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2);

                    }

                    //display generated file
                    echo '<img src="' . $PNG_WEB_DIR . basename($filename) . '" /><hr/>';

                    //config form
                    echo '<form action="qr-gen.php" method="post">
        Инвентарный номер:&nbsp;<input name="data" value="' . (isset($_REQUEST['data']) ? htmlspecialchars($_REQUEST['data']) : 'Введите инвентарный номер') . '" />&nbsp;
        Качество:&nbsp;<select name="level">
            <option value="L" ' . (($errorCorrectionLevel == 'L') ? ' selected' : '') . '> низкое</option>
            <option value="M" ' . (($errorCorrectionLevel == 'M') ? ' selected' : '') . '> среднее</option>
            <option value="Q" ' . (($errorCorrectionLevel == 'Q') ? ' selected' : '') . '> высокое</option>
            <option value="H" ' . (($errorCorrectionLevel == 'H') ? ' selected' : '') . '> высшее</option>
        </select>&nbsp;
        Размер:&nbsp;<select name="size">';

                    for ($i = 1; $i <= 10; $i++)
                        echo '<option value="' . $i . ' " ' . (($matrixPointSize == $i) ? ' selected' : '') . ' > ' . $i . '</option>';

                    echo '</select>&nbsp;
        <input type="submit" value="Создать QR-код"></form><hr/>';

                    // benchmark
                    // QRtools::timeBenchmark();


                    ?>

                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>