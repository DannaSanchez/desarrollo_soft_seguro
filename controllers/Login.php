<?php
    require_once "models/User.php";

    // Ruta de la vista de login, definida una sola vez
    const LOGIN_VIEW = "views/company/login.view.php";

    class Login{
        // Controlador Principal
        public function main(){
            if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                if (empty($_SESSION['session'])) {
                    $message = "";
                    require_once LOGIN_VIEW;
                } else {
                    header("Location:?c=Dashboard");
                }
            }
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $profile = new User(
                    $_POST['user_email'],
                    $_POST['user_pass']
                );
                $profile = $profile->login();
                if ($profile) {
                    $active = $profile->getUserState();
                    if ($active != 0) {
                        $_SESSION['session'] = $profile->getRolName();
                        $_SESSION['profile'] = serialize($profile);
                        header("Location:?c=Dashboard");
                    } else {
                        $message = "El Usuario NO está activo";
                        require_once LOGIN_VIEW;
                    }
                } else {
                    $message = "Credenciales incorrectas ó el Usuario NO existe";
                    require_once LOGIN_VIEW;
                }
            }

        }
    }
