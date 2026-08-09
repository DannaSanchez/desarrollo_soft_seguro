<?php
class Role{
    private $dbh;
    private $rolCode;
    private $rolName;

    public function __construct(){
        try {
            $this->dbh = DataBase::connection();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function setRolCode($rolCode){ $this->rolCode = $rolCode; }
    public function getRolCode(){ return $this->rolCode; }
    public function setRolName($rolName){ $this->rolName = $rolName; }
    public function getRolName(){ return $this->rolName; }

    # RF03_CU03 - Registrar Rol
    public function createRol(){
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
    public function readRoles(){
        try {
            $rolList = [];
            $sql = 'SELECT * FROM ROLES';
            $stmt = $this->dbh->query($sql);
            foreach ($stmt->fetchAll() as $rol) {
                $rolObj = new Role();
                $rolObj->setRolCode($rol['rolCode']);
                $rolObj->setRolName($rol['rolName']);
                array_push($rolList, $rolObj);
            }
            return $rolList;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    # RF05_CU05 - Obtener el Rol por el código
    public function getrolByCode($rolCode){
        try {
            $sql = "SELECT * FROM ROLES WHERE rolCode=:rolCode";
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('rolCode', $rolCode);
            $stmt->execute();
            $rolDb = $stmt->fetch();
            $rol = new Role();
            $rol->setRolCode($rolDb['rolCode']);
            $rol->setRolName($rolDb['rolName']);
            return $rol;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    # RF06_CU06 - Actualizar Rol
    public function updateRol(){
        try {
            $sql = 'UPDATE ROLES SET rolCode = :rolCode, rolName = :rolName WHERE rolCode = :rolCode';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('rolCode', $this->getRolCode());
            $stmt->bindValue('rolName', $this->getRolName());
            $stmt->execute();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    # RF07_CU07 - Eliminar Rol
    public function deleteRol($rolCode){
        try {
            $sql = 'DELETE FROM ROLES WHERE rolCode = :rolCode';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue('rolCode', $rolCode);
            $stmt->execute();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
}
