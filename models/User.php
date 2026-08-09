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

        // 2da Parte: Constructor único, recibe un arreglo asociativo con los datos disponibles
        public function __construct($data = []){
            try {
                $this->dbh = DataBase::connection();
                $this->rolCode      = $data['rol_code']      ?? null;
                $this->rolName      = $data['rol_name']      ?? null;
                $this->userCode     = $data['user_code']     ?? null;
                $this->userName     = $data['user_name']     ?? null;
                $this->userLastName = $data['user_lastname']  ?? null;
                $this->userId       = $data['user_id']       ?? null;
                $this->userEmail    = $data['user_email']    ?? null;
                $this->userPass     = $data['user_pass']     ?? null;
                $this->userState    = $data['user_state']    ?? null;
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }

        // Le indica a PHP qué propiedades SÍ debe guardar al serializar el objeto.
        // $dbh (la conexión PDO) se excluye porque PHP no permite serializar
        // recursos de conexión a base de datos.
        public function __sleep(){
            return ['rolCode', 'rolName', 'userCode', 'userName', 'userLastName',
                    'userId', 'userEmail', 'userPass', 'userState'];
        }

        // 3ra Parte: Getters
        public function getRolCode(){ return $this->rolCode; }
        public function getRolName(){ return $this->rolName; }
        public function getUserCode(){ return $this->userCode; }
        public function getUserName(){ return $this->userName; }
        public function getUserLastName(){ return $this->userLastName; }
        public function getUserId(){ return $this->userId; }
        public function getUserEmail(){ return $this->userEmail; }
        public function getUserPass(){ return $this->userPass; }
        public function getUserState(){ return $this->userState; }

        // 4ta Parte: Persistencia a la Base de Datos

        # RF01_CU01 - Iniciar Sesión
        public function login(){
            try {
                $sql = 'SELECT
                            r.rol_code,
                            r.rol_name,
                            user_code,
                            user_name,
                            user_lastname,
                            user_id,
                            user_email,
                            user_pass,
                            user_state
                        FROM ROLES AS r
                        INNER JOIN USERS AS u
                        on r.rol_code = u.rol_code
                        WHERE user_email = :userEmail';
                $stmt = $this->dbh->prepare($sql);
                $stmt->bindValue('userEmail', $this->getUserEmail());
                $stmt->execute();
                $userDb = $stmt->fetch();

                if ($userDb && password_verify($this->getUserPass(), $userDb['user_pass'])) {
                    return new User($userDb);
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
                            r.rol_code,
                            r.rol_name,
                            user_code,
                            user_name,
                            user_lastname,
                            user_id,
                            user_email,
                            user_pass,
                            user_state
                        FROM ROLES AS r
                        INNER JOIN USERS AS u
                        on r.rol_code = u.rol_code';
                $stmt = $this->dbh->query($sql);
                foreach ($stmt->fetchAll() as $user) {
                    array_push($userList, new User($user));
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
                            r.rol_code,
                            r.rol_name,
                            user_code,
                            user_name,
                            user_lastname,
                            user_id,
                            user_email,
                            user_pass,
                            user_state
                        FROM ROLES AS r
                        INNER JOIN USERS AS u
                        on r.rol_code = u.rol_code
                        WHERE user_code=:userCode';
                $stmt = $this->dbh->prepare($sql);
                $stmt->bindValue('userCode', $userCode);
                $stmt->execute();
                $userDb = $stmt->fetch();
                return new User($userDb);
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }

        # RF11_CU11 - Actualizar usuario
        public function update_user(){
            try {
                $sql = 'UPDATE USERS SET
                            rol_code = :rolCode,
                            user_code = :userCode,
                            user_name = :userName,
                            user_lastname = :userLastName,
                            user_id = :userId,
                            user_email = :userEmail,
                            user_pass = :userPass,
                            user_state = :userState
                        WHERE user_code = :userCode';
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
                $sql = 'DELETE FROM USERS WHERE user_code = :userCode';
                $stmt = $this->dbh->prepare($sql);
                $stmt->bindValue('userCode', $userCode);
                $stmt->execute();
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }

    }