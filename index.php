<?php
    ob_start();
    session_start();
    require_once "models/DataBase.php";

    $controller_solicitado = isset($_REQUEST['c']) ? $_REQUEST['c'] : "Landing";
    $action_solicitada = isset($_REQUEST['a']) ? $_REQUEST['a'] : "main";

    switch ($controller_solicitado) {
        case "Dashboard":
            require_once "controllers/Dashboard.php";
            $controllerObj = new Dashboard();
            $view = "Dashboard";
            break;
        case "Login":
            require_once "controllers/Login.php";
            $controllerObj = new Login();
            $view = "Login";
            break;
        case "Logout":
            require_once "controllers/Logout.php";
            $controllerObj = new Logout();
            $view = "Logout";
            break;
        case "Users":
            require_once "controllers/Users.php";
            $controllerObj = new Users();
            $view = "Users";
            break;
        case "Landing":
        default:
            require_once "controllers/Landing.php";
            $controllerObj = new Landing();
            $view = "Landing";
            break;
    }

    if ($view === 'Landing' || $view === 'Login') {
        require_once "views/company/header.view.php";
        ejecutar_accion($controllerObj, $view, $action_solicitada);
        require_once "views/company/footer.view.php";
    } elseif (!empty($_SESSION['session'])) {
        require_once "models/User.php";
        $profile = unserialize($_SESSION['profile']);
        $session = $_SESSION['session'];
        require_once "views/roles/".$session."/header.view.php";
        ejecutar_accion($controllerObj, $view, $action_solicitada);
        require_once "views/roles/".$session."/footer.view.php";
    } else {
        header("Location:?");
    }
    ob_end_flush();

    function ejecutar_accion($controllerObj, $view, $action){
        switch ($view) {
            case "Dashboard":
                $controllerObj->main();
                break;

            case "Landing":
                $controllerObj->main();
                break;

            case "Login":
                $controllerObj->main();
                break;

            case "Logout":
                $controllerObj->main();
                break;

            case "Users":
                switch ($action) {
                    case "rolCreate":  $controllerObj->rolCreate();  break;
                    case "rolRead":    $controllerObj->rolRead();    break;
                    case "rolUpdate":  $controllerObj->rolUpdate();  break;
                    case "rolDelete":  $controllerObj->rolDelete();  break;
                    case "userCreate": $controllerObj->userCreate(); break;
                    case "userRead":   $controllerObj->userRead();   break;
                    case "userUpdate": $controllerObj->userUpdate(); break;
                    case "userDelete": $controllerObj->userDelete(); break;
                    case "main":
                    default:
                        $controllerObj->main();
                        break;
                }
                break;

            default:
                // No debería ocurrir, ya que $view viene validado desde el switch anterior,
                // pero se maneja explícitamente por seguridad y buenas prácticas.
                header("Location:?");
                break;
        }
    }
