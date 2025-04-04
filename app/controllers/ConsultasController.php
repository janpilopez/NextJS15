<?php

namespace app\controllers;

use app\models\SysDetalleIndicador;
use app\models\SysEncabezadoIndicador;
use app\models\SysIndicadores;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosPermisosIngresosDet;
use Exception;
use Yii;
use app\models\SysAdmUsuariosDep;
use app\models\SysConfiguracion;
use app\models\SysProvincias;
use app\models\SysCantones;
use app\models\SysRrhhCuadrillasJornadasMov;
use app\models\SysRrhhEmpleadosMarcacionesReloj;
use app\models\SysRrhhJornadasCab;
use app\models\SysRrhhCuadrillasEmpleados;
use app\models\SysRrhhCuadrillas;
use app\models\SysRrhhHorarioCab;
use app\models\SysAdmDepartamentos;
use app\models\SysAdmAreas;
use app\models\SysSsooEPP;

class ConsultasController extends \yii\web\Controller
{
   /* public function actionIndex()
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
   public function actionListempleados(){
        
        
      $value = Yii::$app->request->get('q','');
              
      $datos = SysRrhhEmpleados::find()->select(['id_sys_rrhh_cedula as value', 'nombres'])
        ->where(['or',
            ['like','id_sys_rrhh_cedula',$value.'%',false],
            ['like','nombres',$value.'%',false]])
        ->andFilterWhere(["id_sys_empresa"=> "001"])
        ->andFilterWhere(['estado'=> 'A'])
         ->orderBy(['nombres'=>SORT_ASC])
        ->limit(5)
        ->asArray()
        ->all();
       
       
        
        
        return json_encode($datos);
    }
   public function actionEmpleadosrol(){        
        
        $value = Yii::$app->request->get('q','');
        
        $datos = SysRrhhEmpleados::find()->select(['id_sys_rrhh_cedula as value', 'nombres'])
        ->where(['or',
            ['like','id_sys_rrhh_cedula',$value.'%',false],
            ['like','nombres',$value.'%',false]])
            ->andFilterWhere(["id_sys_empresa"=> "001"])
            ->orderBy(['nombres'=>SORT_ASC])
            ->limit(5)
            ->asArray()
        ->all();
        
 
        return json_encode($datos);
    }
   public function actionListempleados2(){
        
        
        $value = Yii::$app->request->get('q','');
        
      
        $userdeparta = SysAdmUsuariosDep::find()->where(['id_usuario'=> Yii::$app->user->id])->andwhere(['estado'=> 'A'])->one();
        $areas = [];
        $departamentos =[];
        
        if($userdeparta):
        
            if(trim($userdeparta->area) != ''):
                  $areas =  SysAdmUsuariosDep::find()->select('area')->where(['id_usuario'=> Yii::$app->user->id])->andWhere(['estado'=> 'A'])->distinct()->asArray()->column();
            else:
                  $areas =  SysAdmAreas::find()->select('id_sys_adm_area')->asArray()->column();
            endif;
            
            if(trim($userdeparta->departamento) != ''):
            
             $departamentos =  SysAdmUsuariosDep::find()->select('departamento')->where(['id_usuario'=> Yii::$app->user->id])->andWhere(['estado'=> 'A'])->distinct()->asArray()->column();
            
            else:
                $departamentos =  SysAdmDepartamentos::find()->select('id_sys_adm_departamento')->asArray()->column();
            endif;
           
            
        endif;
        

        
        $datos = SysRrhhEmpleados::find()->select(['id_sys_rrhh_cedula as value', 'nombres'])
        ->join('join', 'sys_adm_cargos', 'sys_rrhh_empleados.id_sys_adm_cargo = sys_adm_cargos.id_sys_adm_cargo')
        ->join('join', 'sys_adm_departamentos', 'sys_adm_cargos.id_sys_adm_departamento =  sys_adm_departamentos.id_sys_adm_departamento')
        ->where(['or',
            ['like','id_sys_rrhh_cedula',$value.'%',false],
            ['like','nombres',$value.'%',false]])
            ->andWhere(["sys_rrhh_empleados.id_sys_empresa"=> "001"])
            ->andWhere(['sys_rrhh_empleados.estado'=> 'A'])
            ->andWhere(["sys_adm_departamentos.id_sys_adm_area"=> $areas])
            ->andWhere(["sys_adm_departamentos.id_sys_adm_departamento"=>$departamentos])
            ->orderBy(['nombres'=>SORT_ASC])
            ->asArray()
            ->limit(5)
            ->all();
        
    
        
        
        return json_encode($datos);
    }

    public function actionListpersonalvisitas(){
        
        
        $value = Yii::$app->request->get('q','');
        
        $datos = SysRrhhEmpleadosPermisosIngresosDet::find()->select(['id_sys_rrhh_cedula as value', 'nombres'])
        ->distinct()->where(['or',
            ['like','id_sys_rrhh_cedula',$value.'%',false],
            ['like','nombres',$value.'%',false]])
            ->orderBy(['nombres'=>SORT_ASC])
            ->asArray()
            ->limit(5)
            ->all();
        
    
        
        
        return json_encode($datos);
    }

    public function actionListempleadossueldo(){
        
        
        $value = Yii::$app->request->get('q','');
        
      
        $userdeparta = SysAdmUsuariosDep::find()->where(['id_usuario'=> Yii::$app->user->id])->andwhere(['estado'=> 'A'])->one();
        $areas = [];
        $departamentos =[];
        
        if($userdeparta):
        
            if(trim($userdeparta->area) != ''):
                  $areas =  SysAdmUsuariosDep::find()->select('area')->where(['id_usuario'=> Yii::$app->user->id])->andWhere(['estado'=> 'A'])->distinct()->asArray()->column();
            else:
                  $areas =  SysAdmAreas::find()->select('id_sys_adm_area')->asArray()->column();
            endif;
            
            if(trim($userdeparta->departamento) != ''):
            
             $departamentos =  SysAdmUsuariosDep::find()->select('departamento')->where(['id_usuario'=> Yii::$app->user->id])->andWhere(['estado'=> 'A'])->distinct()->asArray()->column();
            
            else:
                $departamentos =  SysAdmDepartamentos::find()->select('id_sys_adm_departamento')->asArray()->column();
            endif;
           
            
        endif;
        
        $db   = $_SESSION['db'];

        /*$datos =   Yii::$app->$db->createCommand("select sys_rrhh_empleados.id_sys_rrhh_cedula as value, nombres
        from sys_rrhh_empleados
        inner join sys_adm_cargos on sys_rrhh_empleados.id_sys_adm_cargo = sys_adm_cargos.id_sys_adm_cargo
        inner join sys_adm_departamentos on sys_adm_cargos.id_sys_adm_departamento =  sys_adm_departamentos.id_sys_adm_departamento
        inner join sys_rrhh_empleados_sueldos on sys_rrhh_empleados.id_sys_rrhh_cedula = sys_rrhh_empleados_sueldos.id_sys_rrhh_cedula
        where sys_rrhh_empleados.id_sys_empresa = '001'
        and sys_rrhh_empleados.estado = 'A'
        and sys_rrhh_empleados_sueldos.estado ='A'
        and sys_rrhh_empleados_sueldos.sueldo >= 991.80
        and sys_rrhh_empleados.id_sys_rrhh_cedula like '$value%' 
        order By nombres ASC")->queryAll();*/
        
        $datos = SysRrhhEmpleados::find()->select(['sys_rrhh_empleados.id_sys_rrhh_cedula as value', 'nombres'])
        ->join('join', 'sys_rrhh_empleados_sueldos', 'sys_rrhh_empleados.id_sys_rrhh_cedula = sys_rrhh_empleados_sueldos.id_sys_rrhh_cedula')
        ->join('join', 'sys_adm_cargos', 'sys_rrhh_empleados.id_sys_adm_cargo = sys_adm_cargos.id_sys_adm_cargo')
        ->join('join', 'sys_adm_departamentos', 'sys_adm_cargos.id_sys_adm_departamento =  sys_adm_departamentos.id_sys_adm_departamento')
        ->where(['or',
            ['like','sys_rrhh_empleados.id_sys_rrhh_cedula',$value.'%',false],
            ['like','sys_rrhh_empleados.nombres',$value.'%',false]])
            ->andWhere(["sys_rrhh_empleados.id_sys_empresa"=> "001"])
            ->andWhere(['sys_rrhh_empleados.estado'=> 'A'])
            ->andWhere(["sys_adm_departamentos.id_sys_adm_area"=> $areas])
            ->andWhere(["sys_adm_departamentos.id_sys_adm_departamento"=>$departamentos])
            ->andWhere(["sys_rrhh_empleados_sueldos.estado"=>'A'])
            ->andWhere("sys_rrhh_empleados_sueldos.sueldo >= 991.80")
            ->orderBy(['sys_rrhh_empleados.nombres'=>SORT_ASC])
            ->asArray()
            ->limit(5)
            ->all();
        
    
        
        
        return json_encode($datos);
    }

    public function actionListempleados3(){
        
        
        $value = Yii::$app->request->get('q','');
        
      
        $userdeparta = SysAdmUsuariosDep::find()->where(['id_usuario'=> Yii::$app->user->id])->andwhere(['estado'=> 'A'])->one();
        $areas = [];
        $departamentos =[];
        
        if($userdeparta):
        
            if(trim($userdeparta->area) != ''):
                  $areas =  SysAdmUsuariosDep::find()->select('area')->where(['id_usuario'=> Yii::$app->user->id])->andWhere(['estado'=> 'A'])->distinct()->asArray()->column();
            else:
                  $areas =  SysAdmAreas::find()->select('id_sys_adm_area')->asArray()->column();
            endif;
            
            if(trim($userdeparta->departamento) != ''):
            
             $departamentos =  SysAdmUsuariosDep::find()->select('departamento')->where(['id_usuario'=> Yii::$app->user->id])->andWhere(['estado'=> 'A'])->distinct()->asArray()->column();
            
            else:
                $departamentos =  SysAdmDepartamentos::find()->select('id_sys_adm_departamento')->asArray()->column();
            endif;
           
            
        endif;
        

        
        $datos = SysRrhhEmpleados::find()->select(['id_sys_rrhh_cedula as value', 'nombres'])
        ->join('join', 'sys_adm_cargos', 'sys_rrhh_empleados.id_sys_adm_cargo = sys_adm_cargos.id_sys_adm_cargo')
        ->join('join', 'sys_adm_departamentos', 'sys_adm_cargos.id_sys_adm_departamento =  sys_adm_departamentos.id_sys_adm_departamento')
        ->where(['or',
            ['like','id_sys_rrhh_cedula',$value.'%',false],
            ['like','nombres',$value.'%',false]])
            ->andWhere(["sys_rrhh_empleados.id_sys_empresa"=> "001"])
            ->andWhere(["sys_adm_departamentos.id_sys_adm_area"=> $areas])
            ->andWhere(["sys_adm_departamentos.id_sys_adm_departamento"=>$departamentos])
            ->orderBy(['nombres'=>SORT_ASC])
            ->asArray()
            ->limit(5)
            ->all();
        
    
        
        
        return json_encode($datos);
    }
   public function actionDepartamentos($area){        
        $datos = [];
        $datos = SysAdmDepartamentos::find()->select(['id_sys_adm_departamento', 'departamento'])
        ->FilterWhere(['like','id_sys_adm_area','%'.$area.'%', false])
        ->andWhere(["estado"=> "A"])
        ->andWhere(["id_sys_empresa"=> "001"])
         ->asArray()
         ->orderBy(['departamento'=> SORT_ASC])
        ->all();
   
        return json_encode($datos);
  
    }
    public function actionListepp(){
         
         
        $value = Yii::$app->request->get('q','');
        
        $datos = SysSsooEPP::find()->select(['nombre as value', 'id_sys_ssoo_epp', 'vida_util', 'estado'])
        ->distinct()->where(['or',
            ['like','um',$value.'%',false],
            ['like','nombre',$value.'%',false]])
            ->orderBy(['nombre'=>SORT_ASC])
            ->asArray()
            ->limit(5)
            ->all();
        
    
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $datos;
    }
   public function actionListeprovincias(){
        
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $pais = $parents[0];
               
                 $out = (new \yii\db\Query())->select(['id_sys_provincia as id', 'provincia as name'])
                ->from('sys_provincias')
                ->orderby('provincia')
                ->where("id_sys_pais = '{$pais}'")
                ->all(SysProvincias::getDb());
                
              
                
                // the getDefaultSubCat function will query the database
                // and return the default sub cat for the cat_id
                
                return ['output' => $out, 'selected' => ""];
            }
        }
        return ['output' => '', 'selected' => ''];
        
      
       
   }
   public function actionListcantones(){
       
       Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
       $out = [];
    
       if (isset($_POST['depdrop_parents'])) {
           $parents = $_POST['depdrop_parents'];
           if ($parents != null) {
               $provincia = $parents[0];
               
               $out = (new \yii\db\Query())->select(['id_sys_canton as id', 'canton as name'])
               ->from('sys_cantones')
               ->orderby('canton')
               ->where("id_sys_provincia ='{$provincia}'")
               ->all(SysCantones::getDb());
               
            
               
               return ['output'=>$out, 'selected'=>''];
           }
       }
       return ['output'=>'', 'selected'=>''];
   }
   public function actionListparroquias(){       
       
       Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
       $out = [];
     
       if (isset($_POST['depdrop_parents'])) {
           $parents = $_POST['depdrop_parents'];
           if ($parents != null) {
               $canton = $parents[0];
               
               $out = (new \yii\db\Query())->select(['id_sys_parroquia as id', 'parroquia as name'])
               ->from('sys_parroquias')
               ->orderby('parroquia')
               ->where("id_sys_canton ='{$canton}'")
               ->all(SysCantones::getDb());

               return ['output'=>$out, 'selected'=>''];
           }
       }
       return ['output'=>'', 'selected'=>''];
       
   }
   //Agendamientos//
   //Listado de Jornadas
   public function actionListarjornadas () {
       
     //  $jornadas = SysRrhhJornadasCab::find()->select('id_sys_rrhh_jornada, descripcion')->where(['estado'=> 'A'])->asArray()->all();
       $jornadas   = SysRrhhHorarioCab::find()->select('id_sys_rrhh_horario_cab, horario')->where(['estado'=> 'A'])->asArray()->all();
       return json_encode($jornadas);
       
   }
   //Listado de personas por Cuadrillas
   public function actionListargrupo (){
       
       $codcuadrilla = Yii::$app->request->get('codcuadrilla');
       
       $datos = [];
       
        
       $datos = (new \yii\db\Query())
       ->select([ "c.id_sys_rrhh_cedula as cedula", "nombres"])
       ->from("sys_rrhh_cuadrillas_empleados c")
       ->join("INNER JOIN",  "sys_rrhh_empleados e", "c.id_sys_rrhh_cedula  = e.id_sys_rrhh_cedula")
       ->where("c.id_sys_rrhh_cuadrilla = '{$codcuadrilla}'")
       ->orderBy('nombres asc')
       ->all(SysRrhhCuadrillas::getDb());
     
        return json_encode($datos);
         
   }
   //Fin Agendamientos/
   //Inicio  Listado departamento ///
   public function actionListadepartamento (){       
       
       $userdeparta = SysAdmUsuariosDep::find()->where(['id_usuario'=> Yii::$app->user->id])->one();
       
       Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
       $out = [];

       if (isset($_POST['depdrop_parents'])) {
           $parents = $_POST['depdrop_parents'];
           if ($parents != null) {
               $area = $parents[0];
               
               if($userdeparta):
               
                       if(trim($userdeparta->area) != ''):
                       
                           if(trim($userdeparta->departamento) != ''):
                           
                           $departamento     =  SysAdmUsuariosDep::find()->select('departamento')->where(['id_usuario'=> Yii::$app->user->id])->asArray()->column();
                           $out              =  SysAdmDepartamentos::find()->select(['id_sys_adm_departamento as id', 'departamento as name'])->where(['id_sys_adm_departamento'=> $departamento])->asArray()->all();
        
                           else:
                           
                             $out = (new \yii\db\Query())
                               ->select(["id_sys_adm_departamento as id", "departamento as name"])
                               ->from("sys_adm_departamentos")
                               ->where("id_sys_adm_area = '{$area}'")
                               ->andwhere("id_sys_empresa = '001'")
                               ->all(SysRrhhCuadrillas::getDb());     
                               
                           endif;
                       else:
                       
                           $out = (new \yii\db\Query())
                               ->select(["id_sys_adm_departamento as id", "departamento as name"])
                               ->from("sys_adm_departamentos")
                               ->where("id_sys_adm_area = '{$area}'")
                               ->andwhere("id_sys_empresa = '001'")
                               ->all(SysRrhhCuadrillas::getDb());
                        endif;
               else:
                   
                   $out = (new \yii\db\Query())
                   ->select(["id_sys_adm_departamento as id", "departamento as name"])
                   ->from("sys_adm_departamentos")
                   ->where("id_sys_adm_area = '{$area}'")
                   ->andwhere("id_sys_empresa = '001'")
                   ->all(SysRrhhCuadrillas::getDb());
                   
               endif;     
           
               
               return ['output'=>$out, 'selected'=>''];
           }
       }
       return ['output'=>'', 'selected'=>''];
       
   }
   //Fin Listado departamento//
   public function actionListardepartamentos(){       
       Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
       $out = [];
       
       if (isset($_POST['depdrop_parents'])) {
           $parents = $_POST['depdrop_parents'];
           if ($parents != null) {
               $area = $parents[0];
               
               $out = (new \yii\db\Query())->select(['id_sys_adm_departamento as id', 'departamento as name'])
               ->from('sys_adm_departamentos')
               ->orderby('departamento')
               ->where("id_sys_adm_area ='{$area}'")
               ->andwhere("id_sys_empresa= '001'")
               ->all(SysAdmDepartamentos::getDb());
               
           
               return ['output'=>$out, 'selected'=>''];
           }
       }
       return ['output'=>'', 'selected'=>''];
   }
 //Marcaciones ///
   public function actionCargar()
   {
       //insertamos el detalle de las marcaciones
       $marcaciones = [];
       
  
       
       // $this->getValidadaMarcacion('', '0603765231','2019-07-22 8:45:03.000');
       
       $marcaciones =  Yii::$app->dbreloj->createCommand("select userinfo.userid as codigo,userinfo.ssn as cedula,checkinout.checktime as marcacion  FROM userinfo,checkinout where checkinout.userid=userinfo.userid and validar=0 and  YEAR (CHECKTIME) >= 2019 and YEAR(CHECKTIME) <= YEAR(getdate()) and Validar = 0 and UserExtFmt = 1 and convert (date, CHECKTIME) >=  '2019-07-23'
	         order by userinfo.userid, checkinout.checktime")->queryall();
       
   
 
      if (count($marcaciones)> 0 ) {
                    
           foreach ($marcaciones as $marcacion){
              

                $empleados = SysRrhhEmpleados::find()
               ->where(['sys_rrhh_empleados.estado'=> 'A'])
               ->andWhere(['id_sys_rrhh_cedula'=> trim($marcacion['cedula'])])
               ->andwhere(['id_sys_empresa'=> '001'])->scalar();
               
               //verficamos si el empleador existe
               if ($empleados > 0){
                   
                 
                  // echo str_replace('-', '',  $marcacion['marcacion']).'<br>';
                  
                   //validamos la hora de marcacion
                   if($this->getValidadaMarcacion($marcacion['codigo'], trim($marcacion['cedula']),$marcacion['marcacion'])){
                       
                           $this->InsertaMarcacion($marcacion['codigo'], trim($marcacion['cedula']),$marcacion['marcacion']);
                       
                   };
                      
                     // Yii::$app->dbsysbase->createCommand("insert into sys_rrhh_empleados_marcaciones_reloj (id_sys_rrhh_cedula, fecha_marcacion, id_sys_empresa, tipo) values ('{$marcacion['cedula']}','{$marcacion['marcacion']}','001','S')")->execute();
                      
               
                        //$this->InsertaMarcacion($marcacion['codigo'], $marcacion['cedula'],$marcacion['marcacion']);
                        
                 
                   

               }
               
           }
           return count($marcaciones);
         
           
       }
       
       
   }
   public function getValidadaMarcacion($codigouser, $cedula, $fechamarcacion){
       
 
       //descartamos datos duplicados
       $minutos_omitir = SysConfiguracion::find()->select('parametro')->where(['id_sys_empresa'=> '001'])->andwhere(['id_sys_conf_cod'=> '201'])->scalar();
       
       //buscamas la fecha de la marcacion
        $marcacionesreloj =  SysRrhhEmpleadosMarcacionesReloj::find()
       ->where(['id_sys_empresa'=> '001'])
       ->andWhere(['id_sys_rrhh_cedula'=> $cedula])->andWhere(['fecha_jornada'=> date('Y-m-d', strtotime($fechamarcacion))])->orderBy(['fecha_marcacion'=>SORT_DESC])->all();
        
       if (count($marcacionesreloj) > 0){
           
               $segundos  =   (strtotime($fechamarcacion) - strtotime($marcacionesreloj[0]['fecha_marcacion']));
              
               if ((intval(abs($segundos)) >= intval($minutos_omitir*60))) {
                 
                   return true;
               }
               else{
                   
                   $fechamarcacion =   str_replace("-", "", $fechamarcacion);
                   Yii::$app->dbreloj->createCommand("update checkinout SET validar= 1  WHERE userid = {$codigouser} and checktime = '".$fechamarcacion."'")->execute();
               }
     
       }else{
           
           return true;
       }
       
       return false;
      
   }
   public function InsertaMarcacion($codigo, $cedula, $marcacion){       
         //descartamos datos duplicados
         $minutos_omitir = SysConfiguracion::find()->select('parametro')->where(['id_sys_empresa'=> '001'])->andwhere(['id_sys_conf_cod'=> '201'])->scalar();
       
          $coutmarcacions =  SysRrhhEmpleadosMarcacionesReloj::find()
           ->where(['id_sys_empresa'=> '001'])
           ->andWhere(['id_sys_rrhh_cedula'=> $cedula])->andWhere(['fecha_jornada'=> date('Y-m-d', strtotime($marcacion))])->count();
          
         
           if ($coutmarcacions == 1 || $coutmarcacions == 3){
                  
               
               
               
                $marcacions =  SysRrhhEmpleadosMarcacionesReloj::find()
               ->where(['id_sys_empresa'=> '001'])
               ->andWhere(['id_sys_rrhh_cedula'=> $cedula])->andWhere(['fecha_jornada'=> date('Y-m-d', strtotime($marcacion))])->orderBy(['fecha_marcacion'=>SORT_DESC])->one();
               
               
                  //revisar fecha anterior datos duplicados     
               //revisar datos duplicados
               $segundos  =   (strtotime($marcacion) - strtotime($marcacions['fecha_marcacion']));
               
               
               if ((intval(abs($segundos)) >= intval($minutos_omitir*60))) {
                   
                   $model =  new SysRrhhEmpleadosMarcacionesReloj();
                   $model->id_sys_rrhh_cedula =  $cedula;
                   $model->id_sys_empresa     = '001';
                   $model->fecha_marcacion    =  $marcacion;
                   $model->fecha_sistema      =  $marcacion;
                   $model->fecha_jornada      =  $marcacions->fecha_jornada;
                   $model->tipo               =  'S';
                   $model->save(false);
                   
                   $this->ValidaMarcacion($codigo, $marcacion);
                   
               }else{
                   
                   $this->ValidaMarcacion($codigo, $marcacion);
               }
   
               
           }else{
               
                    $marcacion_anterior  = date("Y-m-d",strtotime($marcacion."- 1 days"));
         
                    $coutmarcacions =  SysRrhhEmpleadosMarcacionesReloj::find()
                   ->where(['id_sys_empresa'=> '001'])
                   ->andWhere(['id_sys_rrhh_cedula'=> $cedula])->andWhere(['fecha_jornada'=> $marcacion_anterior])->count();
                   

                   if ($coutmarcacions == 1 || $coutmarcacions == 3){

                               $marcacions =  SysRrhhEmpleadosMarcacionesReloj::find()
                               ->where(['id_sys_empresa'=> '001'])
                               ->andWhere(['id_sys_rrhh_cedula'=> $cedula])->andWhere(['fecha_jornada'=> $marcacion_anterior])->orderBy(['fecha_marcacion'=>SORT_DESC])->one();
   
                               $arrayentrada =  explode(':', date('H:i:s', strtotime($marcacions->fecha_sistema)));
                               $arraysalida =  explode(':', date('H:i:s', strtotime($marcacion)));
                               
                               
                               $totalhoras = 0;
                               $totalmin   = 0;
                               
                               $horaentra  = $arrayentrada[0];
                               $minentrada = $arrayentrada[1];
                               $horasalida = $arraysalida[0];
                               $minsalida  = $arraysalida[1];
                               
                               
                               
                               $minentrada = 60 - $minentrada;
                               $horaentra++;
                               $horaentra = 24 - $horaentra;
                               $totalmin = $minentrada + $minsalida;
                               
                               
                               if ($totalmin >= 60):
                               
                                   $totalmin   = $totalmin - 60;
                                   $horasalida++;
                               
                               endif;
                               
                               $totalhoras = $horasalida + $horaentra;
                               
                               
                               
                               if ($totalhoras >= 8 and $totalhoras <= 12){
                                   
                                  
                                   //revisar datos duplicados 
                                   $segundos  =   (strtotime($marcacion) - strtotime($marcacions['fecha_marcacion']));
                                   
                                   
                                   if ((intval(abs($segundos)) >= intval($minutos_omitir*60))) {
                                       
                                       
                                       $model =  new SysRrhhEmpleadosMarcacionesReloj();
                                       $model->id_sys_rrhh_cedula =  $cedula;
                                       $model->id_sys_empresa     = '001';
                                       $model->fecha_marcacion    =  $marcacion;
                                       $model->fecha_sistema      =  $marcacion;
                                       $model->fecha_jornada      =  $marcacion_anterior;
                                       $model->validar            =  $coutmarcacions;
                                       $model->tipo               =  'S';
                                       $model->save(false);
                                       
                                       $this->ValidaMarcacion($codigo, $marcacion);
                                       
                                   }else{
                                       
                                       $this->ValidaMarcacion($codigo, $marcacion);
                                   }
                                   
                                   
                                   
             
                               }else{
                                   
                                   //revisar datos duplicados
                                   $segundos  =   (strtotime($marcacion) - strtotime($marcacions['fecha_marcacion']));
                                   
                                   
                                   if ((intval(abs($segundos)) >= intval($minutos_omitir*60))) {
                                       
                                       $model =  new SysRrhhEmpleadosMarcacionesReloj();
                                       $model->id_sys_rrhh_cedula =  $cedula;
                                       $model->id_sys_empresa     = '001';
                                       $model->fecha_marcacion    =  $marcacion;
                                       $model->fecha_sistema      =  $marcacion;
                                       $model->fecha_jornada      =  date("Y-m-d",strtotime($marcacion));
                                       $model->tipo               =  'E';
                                       $model->save(false);
                                       
                                       $this->ValidaMarcacion($codigo, $marcacion);
                                       
                                   }else{
                                       
                                       $this->ValidaMarcacion($codigo, $marcacion);
                                   }
                                   
                               }
                       
                       
                   }else{
                       
                    
                       if($this->getValidadaMarcacion($codigo,$cedula,$marcacion)){
                           
                           $model =  new SysRrhhEmpleadosMarcacionesReloj();
                           $model->id_sys_rrhh_cedula =  $cedula;
                           $model->id_sys_empresa     = '001';
                           $model->fecha_marcacion    =  $marcacion;
                           $model->fecha_sistema      =  $marcacion;
                           $model->fecha_jornada      =  date("Y-m-d",strtotime($marcacion));
                           $model->tipo               =  'E';
                           $model->save(false);
                           
                           $this->ValidaMarcacion($codigo, $marcacion);
                           
                       }else{
                           $this->ValidaMarcacion($codigo, $marcacion);
                       }
                   } 
               }
   }
   public function ValidaMarcacion($codigouser, $fechamarcacion){
      
    $fechamarcacion =   str_replace("-", "", $fechamarcacion);
    Yii::$app->dbreloj->createCommand("update checkinout SET validar= 1  WHERE userid = {$codigouser} and checktime = '".$fechamarcacion."'")->execute();
          
   }
   
   
   public function actionIndicadores($departamento){        
    $datos = [];
    $datos = SysAdmDepartamentos::find()->select(['id_sys_adm_departamento', 'departamento','indicadores'])
    ->andWhere(['id_sys_adm_departamento' => $departamento])
    ->asArray()
    ->one();

    return json_encode($datos);

    }

    public function actionArrayindicadores($indicadores){        
        $datos = [];
        $datos = SysIndicadores::find()
        ->andWhere('tipo_indicador <='.$indicadores)
        ->asArray()
        ->all();

        return json_encode($datos);

    }

    public function actionIndicador($indicador){        
        $datos = [];
        $datos = SysIndicadores::find()
        ->andWhere(['id_indicador'=>$indicador])
        ->asArray()
        ->one();

        return json_encode($datos);

    }

    public function actionDepartamento($departamento){        
        $datos = [];
        $datos = SysAdmDepartamentos::find()
        ->andWhere(['id_sys_adm_departamento'=>$departamento])
        ->asArray()
        ->one();

        return json_encode($datos);

    }

    public function actionEncabezadoindicador($indicador){        
        $datos = [];
        $datos = SysEncabezadoIndicador::find()
        ->andWhere(['id_encabezado_indicador'=>$indicador])
        ->asArray()
        ->one();

        return json_encode($datos);

    }

    public function actionDetalle($departamento,$fecha){        
        $datos = [];
        $datos = SysDetalleIndicador::find()
        ->andWhere(['imp_departamento'=>$departamento])
        ->andWhere(['fecha'=>$fecha])
        ->asArray()
        ->all();

        return json_encode($datos);

    }
   
   
      
}
