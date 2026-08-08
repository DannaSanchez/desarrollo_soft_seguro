<?php
    ob_start();
    session_start();
    require_once "models/DataBase.php";

    // Lista blanca de controllers válidos
    $controllers_permitidos = [
        "Dashboard" => "controllers/Dashboard.php",
        "Landing" => "controllers/Landing.php",
        "Login"   => "controllers/Login.php",
        "Logout" => "controllers/Logout.php",
        "Users" => "controllers/Users.php",
    ];

    // Lista blanca de acciones permitidas POR CADA controller
    $actions_permitidas = [
        "Dashboard" => ["main"],
        "Landing"   => ["main"],
        "Login"     => ["main"],
        "Logout"    => ["main"],
        "Users"     => ["main", "rolCreate", "rolRead", "rolUpdate", "rolDelete",
                         "userCreate", "userRead", "userUpdate", "userDelete"],
    ];

    $controller = isset($_REQUEST['c']) ? $_REQUEST['c'] : "Landing";

    if (!array_key_exists($controller, $controllers_permitidos)) {
        $controller = "Landing";
    }

    $route_controller = $controllers_permitidos[$controller];

    if (file_exists($route_controller)) {
        $view = $controller;
        require_once $route_controller;
        $controllerObj = new $controller;

        // La acción solicitada solo se acepta si está en la lista blanca
        // definida para ESE controller específico
        $action = isset($_REQUEST['a']) ? $_REQUEST['a'] : 'main';
        if (!in_array($action, $actions_permitidas[$controller], true)) {
            $action = 'main';
        }

        if ($view === 'Landing' || $view === 'Login') {
            require_once "views/company/header.view.php";
            call_user_func(array($controllerObj, $action));
            require_once "views/company/footer.view.php";
        } elseif (!empty($_SESSION['session'])) {
            require_once "models/User.php";
            $profile = unserialize($_SESSION['profile']);
            $session = $_SESSION['session'];
            require_once "views/roles/".$session."/header.view.php";
            call_user_func(array($controllerObj, $action));
            require_once "views/roles/".$session."/footer.view.php";
        } else {
            header("Location:?");
        }
    } else {
        header("Location:?");
    }
    ob_end_flush();
?>