<?php
    class User{
        // 1ra Parte: Atributos
        private $dbh;
        private $rolCode;
        private $rolName;
        private $userCode;
        private $userName;
        private $userLastName;
        private $userId;
        private $userEmail;
        private $userPass;
        private $userState;

        // 2da Parte: Sobrecarga Constructores
        public function __construct(){
            try {
                $this->dbh = DataBase::connection();
                $a = func_get_args();
                $i = func_num_args();
                if (method_exists($this, $f = '__construct' . $i)) {
                    call_user_func_array(array($this, $f), $a);
                }
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }

        # Constructor: Objeto 00 parámetros
        public function __construct0(){}

        # Constructor: Objeto 02 parámetros
        public function __construct2($userEmail,$userPass){
            $this->userEmail = $userEmail;
            $this->userPass = $userPass;
        }

        # Constructor: Objeto 08 parámetros
        public function __construct8($rolCode,$userCode,$userName,$userLastName,$userId,$userEmail,$userPass,$userState){
            $this->rolCode = $rolCode;
            $this->userCode = $userCode;
            $this->userName = $userName;
            $this->userLastName = $userLastName;
            $this->userId = $userId;
            $this->userEmail = $userEmail;
            $this->userPass = $userPass;
            $this->userState = $userState;
        }

        # Constructor: Objeto 09 parámetros
        public function __construct9($rolCode,$rolName,$userCode,$userName,$userLastName,$userId,$userEmail,$userPass,$userState){
            unset($this->dbh);
            $this->rolCode = $rolCode;
            $this->rolName = $rolName;
            $this->userCode = $userCode;
            $this->userName = $userName;
            $this->userLastName = $userLastName;
            $this->userId = $userId;
            $this->userEmail = $userEmail;
            $this->userPass = $userPass;
            $this->userState = $userState;
        }

        // 3ra Parte: Setter y Getters
        # Código Rol (se conserva solo como dato informativo del usuario, no gestiona roles)
        public function setRolCode($rolCode){
            $this->rolCode = $rolCode;
        }
        public function getRolCode(){
            return $this->rolCode;
        }
        # Nombre Rol (idem, solo lectura para mostrar junto al usuario)
        public function setRolName($rolName){
            $this->rolName = $rolName;
        }
        public function getRolName(){
            return $this->rolName;
        }
        # Código Usuario
        public function setUserCode($userCode){
            $this->userCode = $userCode;
        }
        public function getUserCode(){
            return $this->userCode;
        }
        # Nombre Usuario
        public function setUserName($userName){
            $this->userName = $userName;
        }
        public function getUserName(){
            return $this->userName;
        }
        # Apellido Usuario
        public function setUserLastName($userLastName){
            $this->userLastName = $userLastName;
        }
        public function getUserLastName(){
            return $this->userLastName;
        }
        # Identificación Usuario
        public function setUserId($userId){
            $this->userId = $userId;
        }
        public function getUserId(){
            return $this->userId;
        }
        # Email Usuario
        public function setUserEmail($userEmail){
            $this->userEmail = $userEmail;
        }
        public function getUserEmail(){
            return $this->userEmail;
        }
        # Contraseña Usuario
        public function setUserPass($userPass){
            $this->userPass = $userPass;
        }
        public function getUserPass(){
            return $this->userPass;
        }
        # Estado Usuario
        public function setUserState($userState){
            $this->userState = $userState;
        }
        public function getUserState(){
            return $this->userState;
        }

        // 4ta Parte: Persistencia a la Base de Datos

        # RF01_CU01 - Iniciar Sesión
        public function login(){
            try {
                $sql = 'SELECT
                            r.rolCode,
                            r.rolName,
                            userCode,
                            userName,
                            userLastName,
                            userId,
                            userEmail,
                            userPass,
                            userState
                        FROM ROLES AS r
                        INNER JOIN USERS AS u
                        on r.rolCode = u.rolCode
                        WHERE userEmail = :userEmail';
                $stmt = $this->dbh->prepare($sql);
                $stmt->bindValue('userEmail', $this->getUserEmail());
                $stmt->execute();
                $userDb = $stmt->fetch();

                if ($userDb && password_verify($this->getUserPass(), $userDb['userPass'])) {
                    $user = new User(
                        $userDb['rolCode'],
                        $userDb['rolName'],
                        $userDb['userCode'],
                        $userDb['userName'],
                        $userDb['userLastName'],
                        $userDb['userId'],
                        $userDb['userEmail'],
                        $userDb['userPass'],
                        $userDb['userState']
                    );
                    return $user;
                } else {
                    return false;
                }
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }

        # RF08_CU08 - Registrar Usuario
        public function create_user(){
            try {
                $sql = 'INSERT INTO USERS VALUES (
                            :rolCode,
                            :userCode,
                            :userName,
                            :userLastName,
                            :userId,
                            :userEmail,
                            :userPass,
                            :userState
                        )';
                $stmt = $this->dbh->prepare($sql);
                $stmt->bindValue('rolCode', $this->getRolCode());
                $stmt->bindValue('userCode', $this->getUserCode());
                $stmt->bindValue('userName', $this->getUserName());
                $stmt->bindValue('userLastName', $this->getUserLastName());
                $stmt->bindValue('userId', $this->getUserId());
                $stmt->bindValue('userEmail', $this->getUserEmail());
                $stmt->bindValue('userPass', password_hash($this->getUserPass(), PASSWORD_DEFAULT));
                $stmt->bindValue('userState', $this->getUserState());
                $stmt->execute();
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }

        # RF09_CU09 - Consultar Usuarios
        public function read_users(){
            try {
                $userList = [];
                $sql = 'SELECT
                            r.rolCode,
                            r.rolName,
                            userCode,
                            userName,
                            userLastName,
                            userId,
                            userEmail,
                            userPass,
                            userState
                        FROM ROLES AS r
                        INNER JOIN USERS AS u
                        on r.rolCode = u.rolCode';
                $stmt = $this->dbh->query($sql);
                foreach ($stmt->fetchAll() as $user) {
                    $userObj = new User(
                        $user['rolCode'],
                        $user['rolName'],
                        $user['userCode'],
                        $user['userName'],
                        $user['userLastName'],
                        $user['userId'],
                        $user['userEmail'],
                        $user['userPass'],
                        $user['userState']
                    );
                    array_push($userList, $userObj);
                }
                return $userList;
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }

        # RF10_CU10 - Obtener el Usuario por el código
        public function getuser_bycode($userCode){
            try {
                $sql = 'SELECT
                            r.rolCode,
                            r.rolName,
                            userCode,
                            userName,
                            userLastName,
                            userId,
                            userEmail,
                            userPass,
                            userState
                        FROM ROLES AS r
                        INNER JOIN USERS AS u
                        on r.rolCode = u.rolCode
                        WHERE userCode=:userCode';
                $stmt = $this->dbh->prepare($sql);
                $stmt->bindValue('userCode', $userCode);
                $stmt->execute();
                $userDb = $stmt->fetch();
                $user = new User(
                    $userDb['rolCode'],
                    $userDb['rolName'],
                    $userDb['userCode'],
                    $userDb['userName'],
                    $userDb['userLastName'],
                    $userDb['userId'],
                    $userDb['userEmail'],
                    $userDb['userPass'],
                    $userDb['userState']
                );
                return $user;
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }

        # RF11_CU11 - Actualizar usuario
        public function update_user(){
            try {
                $sql = 'UPDATE USERS SET
                            rolCode = :rolCode,
                            userCode = :userCode,
                            userName = :userName,
                            userLastName = :userLastName,
                            userId = :userId,
                            userEmail = :userEmail,
                            userPass = :userPass,
                            userState = :userState
                        WHERE userCode = :userCode';
                $stmt = $this->dbh->prepare($sql);
                $stmt->bindValue('rolCode', $this->getRolCode());
                $stmt->bindValue('userCode', $this->getUserCode());
                $stmt->bindValue('userName', $this->getUserName());
                $stmt->bindValue('userLastName', $this->getUserLastName());
                $stmt->bindValue('userId', $this->getUserId());
                $stmt->bindValue('userEmail', $this->getUserEmail());
                $stmt->bindValue('userPass', password_hash($this->getUserPass(), PASSWORD_DEFAULT));
                $stmt->bindValue('userState', $this->getUserState());
                $stmt->execute();
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }

        # RF12_CU12 - Eliminar Usuario
        public function delete_user($userCode){
            try {
                $sql = 'DELETE FROM USERS WHERE userCode = :userCode';
                $stmt = $this->dbh->prepare($sql);
                $stmt->bindValue('userCode', $userCode);
                $stmt->execute();
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }

    }
