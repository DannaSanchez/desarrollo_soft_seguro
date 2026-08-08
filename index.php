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

    // Lista blanca de acciones permitidas por cada controller
    $actions_permitidas = [
        "Dashboard" => ["main"],
        "Landing"   => ["main"],
        "Login"     => ["main"],
        "Logout"    => ["main"],
        "Users"     => ["main", "rolCreate", "rolRead", "rolUpdate", "rolDelete",
                         "userCreate", "userRead", "userUpdate", "userDelete"],
    ];

    // Valor crudo, sin validar (solo se usa para buscar en la lista blanca)
    $controller_solicitado = isset($_REQUEST['c']) ? $_REQUEST['c'] : "Landing";

    if (!array_key_exists($controller_solicitado, $controllers_permitidos)) {
        $controller_solicitado = "Landing";
    }

    // A partir de aquí, $nombre_controller es un valor 100% controlado por el código,
    // nunca directamente por el usuario
    $nombre_controller = $controller_solicitado;
    $route_controller = $controllers_permitidos[$nombre_controller];

    if (file_exists($route_controller)) {
        $view = $nombre_controller;
        require_once $route_controller;
        $controllerObj = new $nombre_controller();

        // Valor crudo, sin validar (solo se usa para buscar en la lista blanca)
        $action_solicitada = isset($_REQUEST['a']) ? $_REQUEST['a'] : 'main';
        if (!in_array($action_solicitada, $actions_permitidas[$nombre_controller], true)) {
            $action_solicitada = 'main';
        }
        $accion = $action_solicitada;

        if ($view === 'Landing' || $view === 'Login') {
            require_once "views/company/header.view.php";
            call_user_func(array($controllerObj, $accion));
            require_once "views/company/footer.view.php";
        } elseif (!empty($_SESSION['session'])) {
            require_once "models/User.php";
            $profile = unserialize($_SESSION['profile']);
            $session = $_SESSION['session'];
            require_once "views/roles/".$session."/header.view.php";
            call_user_func(array($controllerObj, $accion));
            require_once "views/roles/".$session."/footer.view.php";
        } else {
            header("Location:?");
        }
    } else {
        header("Location:?");
    }
    ob_end_flush();
?>