<?php
class Role{
    private $dbh;
    private $rol_code;
    private $rol_name;

    public function __construct(){
        try {
            $this->dbh = DataBase::connection();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function setRolCode($rol_code){ $this->rol_code = $rol_code; }
    public function getRolCode(){ return $this->rol_code; }
    public function setRolName($rol_name){ $this->rol_name = $rol_name; }
    public function getRolName(){ return $this->rol_name; }

    # RF03_CU03 - Registrar Rol
    public function create_rol(){
        try {
            $sql = 'INSERT INTO ROLES VALUES (:rolCode,:rolName)';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('rolCode', $this->getRolCode());
            $stmt->bindValue('rolName', $this->getRolName());
            $stmt->execute();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    # RF04_CU04 - Consultar Roles
    public function read_roles(){
        try {
            $rolList = [];
            $sql = 'SELECT * FROM ROLES';
            $stmt = $this->dbh->query($sql);
            foreach ($stmt->fetchAll() as $rol) {
                $rolObj = new Role();
                $rolObj->setRolCode($rol['rol_code']);
                $rolObj->setRolName($rol['rol_name']);
                array_push($rolList, $rolObj);
            }
            return $rolList;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    # RF05_CU05 - Obtener el Rol por el código
    public function getrol_bycode($rolCode){
        try {
            $sql = "SELECT * FROM ROLES WHERE rol_code=:rolCode";
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('rolCode', $rolCode);
            $stmt->execute();
            $rolDb = $stmt->fetch();
            $rol = new Role();
            $rol->setRolCode($rolDb['rol_code']);
            $rol->setRolName($rolDb['rol_name']);
            return $rol;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    # RF06_CU06 - Actualizar Rol
    public function update_rol(){
        try {
            $sql = 'UPDATE ROLES SET rol_code = :rolCode, rol_name = :rolName WHERE rol_code = :rolCode';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('rolCode', $this->getRolCode());
            $stmt->bindValue('rolName', $this->getRolName());
            $stmt->execute();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    # RF07_CU07 - Eliminar Rol
    public function delete_rol($rolCode){
        try {
            $sql = 'DELETE FROM ROLES WHERE rol_code = :rolCode';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('rolCode', $rolCode);
            $stmt->execute();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
?>