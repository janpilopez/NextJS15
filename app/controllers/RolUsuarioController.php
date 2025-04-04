<?php

namespace app\controllers;

use Yii;
use app\models\SysEmpresa;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosNovedades;
use Mpdf\Mpdf;
use app\models\SysRrhhEmpleadosRolCab;
use kartik\mpdf\Pdf;
class RolUsuarioController extends \yii\web\Controller
{
    /*public function actionIndex()
    {
        return $this->render('index');
    }*/
    
    public function behaviors()
    {
        return [
            'ghost-access'=> [
                'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
            ],
        ];
    }
    
    public function actionIndex(){
        
        ini_set("pcre.backtrack_limit", "5000000");
            
        $meses        =  Yii::$app->params['meses'];
        //$periodos     =  Yii::$app->params['periodos'];
        $mes          =  date('n');
        $periodo      =  '';
        $datos        =  [];
        //$departamento =  '';
        //$area         =  '';
        $anio         = date('Y');
        $cedula       = '';


        if(Yii::$app->request->post()){
            
            $anio         =  $_POST['anio'];
            $mes          =  $_POST['mes'];
            $periodo      =  '2';
            //$departamento =  $_POST['departamento']== null ?  '': $_POST['departamento'];
            //$area         =  $_POST['area']== null ? '': $_POST['area'];
            $cedula       =  $_POST['cedula'] == null ? '': $_POST['cedula'];
           
            
            $consepto = $this->getConcepto($periodo);
            
            
            if($periodo != '' && $consepto != ''):
            
            
                 if($cedula == ''):
            
                
                        $datos = $this->getRoles($anio, $mes, $periodo, /*$area, $departamento,*/ $consepto);
               
                        
                   else:
                           
                        $datos = $this->getRolIndividual($anio, $mes, $periodo, /*$area, $departamento,*/ $consepto, $cedula);
                   
                   endif;
                   
                   

              endif;
                
        }
        return $this->render('index', ['meses'=> $meses, 'mes'=> $mes, 'periodo'=> $periodo, /*'area'=> $area, 'departamento' => $departamento,*/ 'datos'=> $datos, 'anio'=> $anio]);
        
    }

    private function getConcepto ($periodo){
          
        $concepto = '';
        
        switch ($periodo) {
            
            case 2:    
                $concepto = "SUELDO";
                break;
        }
        
      
        return $concepto;
        
    }

    private  function getRolIndividual($anio, $mes, $periodo, /*$area, $departamento,*/ $concepto, $cedula){
          
          
          
        if($anio == '2019' && $mes == '10'):
        
        
            return  (new \yii\db\Query())->select(
                [
                    "area.id_sys_adm_area",
                    "area.area",
                    "departamento.id_sys_adm_departamento",
                    "departamento.departamento",
                    "emp.nombres",
                    "emp.id_sys_rrhh_cedula",
                    "rol_mov.cantidad",
                    "emp.email"
                    
                ])
                ->from("sys_rrhh_empleados_rol_mov as rol_mov")
                ->innerJoin('sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
                ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
                ->innerJoin("sys_adm_departamentos as departamento","cargo.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
                ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
                ->where("rol_mov.anio = '{$anio}'")
                ->andwhere("rol_mov.mes=  '{$mes}'")
                ->andwhere("rol_mov.periodo=  {$periodo}")
                ->andwhere("rol_mov.id_sys_empresa= '001'")
                ->andwhere("rol_mov.id_sys_rrhh_cedula = '{$cedula}'")
                ->andwhere("id_sys_rrhh_concepto = '$concepto'")
                ->all(SysRrhhEmpleadosNovedades::getDb());
            
         else:
         
             return  (new \yii\db\Query())->select(
                 [
                     "area.id_sys_adm_area",
                     "area.area",
                     "departamento.id_sys_adm_departamento",
                     "departamento.departamento",
                     "emp.nombres",
                     "emp.id_sys_rrhh_cedula",
                     "rol_mov.cantidad",
                     "emp.email"
                     
                 ])
                 ->from("sys_rrhh_empleados_rol_mov as rol_mov")
                 ->innerJoin('sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
                 ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
                 ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
                 ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
                 ->where("rol_mov.anio = '{$anio}'")
                 ->andwhere("rol_mov.mes=  '{$mes}'")
                 ->andwhere("rol_mov.periodo=  {$periodo}")
                 ->andwhere("rol_mov.id_sys_empresa= '001'")
                 ->andwhere("rol_mov.id_sys_rrhh_cedula = '{$cedula}'")
                 ->andwhere("id_sys_rrhh_concepto = '$concepto'")
                 ->all(SysRrhhEmpleadosNovedades::getDb());
                
            
        endif;
    }
}