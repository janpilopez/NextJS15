<?php

use yii\bootstrap\Html;
use app\assets\EmpleadosCredencialAsset;
use app\models\SysRrhhEmpleados;
EmpleadosCredencialAsset::register($this);
$generator = new Picqer\Barcode\BarcodeGeneratorHTML();
?>
 <div class="principal">
 
     <?php foreach ($empleados as $emp):
 
        $datos =  (new \yii\db\Query())
        ->select(["sys_rrhh_empleados.id_sys_rrhh_cedula","nombre","apellidos","tipo_sangre","departamento","color","color_fuente","codigo_temp as barra", "sys_adm_cargos.id_sys_adm_cargo"])
        ->from("sys_rrhh_empleados")
        ->innerJoin("sys_adm_cargos","sys_rrhh_empleados.id_sys_adm_cargo = sys_adm_cargos.id_sys_adm_cargo")
        ->innerJoin("sys_adm_departamentos","sys_adm_cargos.id_sys_adm_departamento = sys_adm_departamentos.id_sys_adm_departamento")
        ->leftJoin("sys_rrhh_empleados_foto", "sys_rrhh_empleados.id_sys_rrhh_cedula = sys_rrhh_empleados_foto.id_sys_rrhh_cedula")
        ->andwhere("sys_rrhh_empleados.id_sys_rrhh_cedula  = '{$emp->id_sys_rrhh_cedula}'")
        ->one(SysRrhhEmpleados::getDb());
       ?>
     
      <?= $this->render('_credencialformato', ['datos'=> $datos])?>
     
     
     <?php endforeach;?>
      
 </div>