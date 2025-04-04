<?php

namespace app\controllers;

use app\models\SysRrhhEmpleados;
use Yii;

class DashboardController extends \yii\web\Controller
{
    public function actionIndex()
    {
        $this->layout = '@app/views/layouts/main_emplados';
        $empActivos = $this->getEmpleadosActivos();
        $empXAreas = $this->getEmpleadosAreas();
        $generoXArea = $this->getGeneroXArea();
        $edadMujeres = $this->getEdadMujeres();
        $edadHombres = $this->getEdadHombres();
        return $this->render('index', [
            'empActivos'=> $empActivos, 
            'empXAreas'=> $empXAreas, 
            'generoXArea' => $generoXArea,
            'edadMujeres' => $edadMujeres,
            'edadHombres' => $edadHombres
        ]);
    }
    public function actionRotacionempleado(){
        
        $this->layout = '@app/views/layouts/main_emplados';
        $empIngresos  = $this->IngresoPersonalAnioActualVsAnioAnterior();
        $empSalida  = $this->SalidaPersonalAnioActualVsAnioAnterior();
        return $this->render('rotacion', ['empIngresos' => $empIngresos,'empSalida' => $empSalida]);
    }

    public function actionPersonalplanta(){
        
        $this->layout = '@app/views/layouts/main_emplados';
        $empActivosPlanta = $this->getEmpleadosActivosPlanta();
        $empActivosPlantaVisitas = $this->getEmpleadosActivosPlantaVisitas();
        $entHombre = $this->getMarcacionesHombresDetalle();
        $entMujer = $this->getMarcacionesMujeresDetalle();
        $entVisitas = $this->getVisitasDetalle();
        return $this->render('personalplanta', ['empActivosPlanta'=> $empActivosPlanta,'empActivosPlantaVisitas' => $empActivosPlantaVisitas,'entHombre' => $entHombre,'entMujer'=> $entMujer,'entVisitas'=>$entVisitas]);

    }
    public function actionPersonaldiscapacidad(){        
        $this->layout = '@app/views/layouts/main_emplados';
        $empXAreas = $this->getEmpleadosAreasDiscapacidad();
        $emp = $this->getEmpleadosDiscapacidad();
        $detalle = $this->getEmpleadosDiscapacidadDetalle();   
        return $this->render('discapacidad_empleado', ['empXAreas'=> $empXAreas, 'emp'=> $emp, 'detalle'=> $detalle]);
    }

    public function actionPersonalmaternidad(){        
        $this->layout = '@app/views/layouts/main_emplados';
        $empXAreas = $this->getEmpleadosAreasMaternidad();
        $detalle = $this->getEmpleadosMaternidadDetalle();   
        return $this->render('personalmaternidad', ['empXAreas'=> $empXAreas, 'detalle'=> $detalle]);
    } 
    public function actionEdadproduccion(){        
        $this->layout = '@app/views/layouts/main_emplados';
        $datosHombres = $this->getEdadHombresProducion();
        $datosMujeres = $this->getEdadMujeresProduccion();
        $datosHombresDetalle = $this->getEdadHombresProduccionDetalle();
        $datosMujeresDetalle = $this->getEdadMujeresProduccionDetalle();
        return $this->render('produccion_edad', [
            'datosHombres' => $datosHombres,
            'datosMujeres' => $datosMujeres, 
            'datosHombresDetalle' => $datosHombresDetalle,
            'datosMujeresDetalle' => $datosMujeresDetalle
        ]);
    }
    public function actionTipocontrato(){        
        $this->layout = '@app/views/layouts/main_emplados';
        $totalContrato = $this->getTotalContrato();
        $tipoContrato = $this->getTipodeContrato();
        $datosIndefinido = $this->getTipodeContratoIndefinidoDetalle();
        $datosEventual = $this->getTipodeContratoEventualDetalle();
        $datosPasantia = $this->getTipodeContratoPasantiaDetalle();
        $datosEmergente = $this->getTipodeContratoEmergenteDetalle();
        $datosTemporada = $this->getTipodeContratoTemporadaDetalle();
        $datosProduccion = $this->getTipodeContratoProduccionDetalle();
        return $this->render('tipocontrato', [
            'totalContrato' => $totalContrato,
            'tipoContrato' => $tipoContrato,
            'datosIndefinido' => $datosIndefinido,
            'datosEventual' => $datosEventual,
            'datosPasantia' => $datosPasantia,
            'datosEmergente' => $datosEmergente,
            'datosTemporada' => $datosTemporada,
            'datosProduccion' => $datosProduccion
        ]);
    }
    public function actionHorasextras(){        
        $this->layout = '@app/views/layouts/main_emplados';
       
        return $this->render('horasextras', [
        ]);
    }
    public function actionVacaciones(){        
        $this->layout = '@app/views/layouts/main_emplados';
       
        return $this->render('vacaciones', [
        ]);
    }
    public function actionPermisos(){        
        $this->layout = '@app/views/layouts/main_emplados';
       
        return $this->render('permisos', [
        ]);
    }
    public function actionCobus(){        
        $this->layout = '@app/views/layouts/main_emplados';
       
        return $this->render('cobus', [
        ]);
    }
    public function actionFinanciero(){        
        $this->layout = '@app/views/layouts/main_emplados';
       
        return $this->render('financiero', [
        ]);
    }
    private function getEmpleadosActivos(){
        
        $db =  $_SESSION['db'];
        $arraydata = [];
        
        $datos =  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerEmpleadosActivos]")->queryAll();
            
            foreach ( $datos as $data):
                 array_push($arraydata,  ["name"=> $data["name"]== "M"? "HOMBRES": "MUJERES", "y" => floatval($data["y"])]);
            endforeach;
        
            return $arraydata;
    }
    
    private function getTotalContrato(){
        
        $arraydata = [];
    
        $datos =  (new \yii\db\Query())
        ->select(["contrato as name", "COUNT(*) as y"])
        ->from("sys_rrhh_contratos")
        ->innerJoin("sys_rrhh_empleados","sys_rrhh_contratos.id_sys_rrhh_contrato = sys_rrhh_empleados.id_sys_rrhh_contrato")
        ->Where("sys_rrhh_empleados.estado = 'A'")
        ->groupBy("contrato")
        ->all (SysRrhhEmpleados::getDb());
        
        foreach ( $datos as $data):
             array_push($arraydata,  ["name"=> $data["name"], "y" => floatval($data["y"])]);
        endforeach;
    
        return $arraydata;
    }   //CAMBIO NC
    private function getEmpleadosLaborando(){
        
        $arraydata = [];
        
        /*
        $datos =  (new \yii\db\Query())
        ->select(["genero as name", "COUNT(*) as y"])
        ->from("sys_rrhh_empleados_marcaciones_reloj mar")
        ->Where("estado = 'A'")
        ->groupBy("genero")
        ->all (SysRrhhEmpleados::getDb());
        
        foreach ( $datos as $data):
            array_push($arraydata,  ["name"=> $data["name"]== "M"? "HOMBRES": "MUJERES", "y" => floatval($data["y"])]);
        endforeach;
        */
        
        return $arraydata;
    }
    private function getEmpleadosAreas(){
        
            $arraydata = [];
        
            $datos = (new \yii\db\Query())->select(
                [
                    "area.area as name",
                    "count(*) as y",
                    
                ])
                ->from("sys_rrhh_empleados emp")
                ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
                ->innerJoin("sys_adm_departamentos as departamento","cargo.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
                ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
                ->andWhere("emp.estado = 'A'")
                ->groupBy("area.area")
                ->all(SysRrhhEmpleados::getDb());
            
            
            foreach ( $datos as $data):
                 array_push($arraydata,  ["name"=> $data["name"], "y" => floatval($data["y"])]);
            endforeach;
            
            return $arraydata;
            
    }
    private function getGeneroXArea(){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerGeneroxArea]")->queryAll();
            
    }
    private function IngresoPersonalAnioActualVsAnioAnterior(){
        
        $arrayData = [];
        $data1 = [];
        $data2 = [];
        $data3 = [];
        $data4 = [];
        $monthNames = [1,2,3,4,5,6,7,8,9,10,11,12];
        
        $anioAct = date('Y');
        $anioAnt = date('Y') - 1;
        
        //Ingreso Personal Año Anterior
        $ingresoAnt  = (new \yii\db\Query())->select(
            [
                "month(fecha_ingreso) as mes",
                "count(*) as ingreso"
                
            ])
            ->from("dbo.sys_rrhh_empleados_contratos")
            ->andWhere("year(fecha_ingreso) = {$anioAnt}")
            ->groupBy("month(fecha_ingreso)")
            ->all(SysRrhhEmpleados::getDb());
            
 
            //Ingreso Personal Año Actual
            $ingresoAct  = (new \yii\db\Query())->select(
                [
                    "month(fecha_ingreso) as mes",
                    "count(*) as ingreso"
                    
                ])
                ->from("dbo.sys_rrhh_empleados_contratos")
                ->andWhere("year(fecha_ingreso) = {$anioAct}")
                ->groupBy("month(fecha_ingreso)")
                ->all(SysRrhhEmpleados::getDb());


                foreach ($ingresoAnt as $i):
                
                    $data4[] = $i['mes'];
                
                endforeach;
                
                foreach ($monthNames as $mes):
                     
                    if(in_array($mes, $data4)):
                        foreach ($ingresoAnt as $i):

                            if($mes == $i['mes']):
                
                                $data1[]= floatval($i['ingreso']);
                                
                            endif;
                            
                        endforeach;
                            
                    else:
                        $data1[] = 0;
                    endif;

                endforeach;
                
                    array_push($arrayData, ["name" => $anioAnt, "data"=> $data1]);
                
                foreach ($ingresoAct as $i):
                
                    $data3[] = $i['mes'];
                
                endforeach;
                
                foreach ($monthNames as $mes):
                     
                    if(in_array($mes, $data3)):
                        foreach ($ingresoAct as $i):

                            if($mes == $i['mes']):
                
                                $data2[] = floatval($i['ingreso']);
                                
                            endif;
                            
                        endforeach;
                            
                    else:
                        $data2[] = 0;
                    endif;

                endforeach;

                    array_push($arrayData, ["name" => $anioAct, "data"=> $data2]);
                
                return $arrayData;
                
    }
    private function SalidaPersonalAnioActualVsAnioAnterior(){
        
        $arrayData = [];
        $data1 = [];
        $data2 = [];
        $data3 = [];
        $data4 = [];
        $monthNames = [1,2,3,4,5,6,7,8,9,10,11,12];

        $anioAct = date('Y');
        $anioAnt = date('Y') - 1;
        
        //Ingreso Personal Año Anterior
        $ingresoAnt  = (new \yii\db\Query())->select(
            [
                "month(fecha_salida) as mes",
                "count(*) as salida"
                
            ])
            ->from("dbo.sys_rrhh_empleados_contratos")
            ->andWhere("year(fecha_salida) = {$anioAnt}")
            ->groupBy("month(fecha_salida)")
            ->all(SysRrhhEmpleados::getDb());
            
            
            
            //Ingreso Personal Año Actual
            $ingresoAct  = (new \yii\db\Query())->select(
                [
                    "month(fecha_salida) as mes",
                    "count(*) as salida"
                    
                ])
                ->from("dbo.sys_rrhh_empleados_contratos")
                ->andWhere("year(fecha_salida) = {$anioAct}")
                ->groupBy("month(fecha_salida)")
                ->all(SysRrhhEmpleados::getDb());
                
                
                foreach ($ingresoAnt as $i):
                
                    $data3[] = $i['mes'];
                
                endforeach;
                
                foreach ($monthNames as $mes):
                     
                    if(in_array($mes, $data3)):
                        foreach ($ingresoAnt as $i):

                            if($mes == $i['mes']):
                
                                $data1[]= floatval($i['salida']);
                                
                            endif;
                            
                        endforeach;
                            
                    else:
                        $data1[] = 0;
                    endif;

                endforeach;
                
                array_push($arrayData, ["name" => $anioAnt, "data"=> $data1]);
                
                foreach ($ingresoAct as $i):
                
                    $data4[] = $i['mes'];
                
                endforeach;
                
                foreach ($monthNames as $mes):
                     
                    if(in_array($mes, $data4)):
                        foreach ($ingresoAct as $i):

                            if($mes == $i['mes']):
                
                                $data2[]= floatval($i['salida']);
                                
                            endif;
                            
                        endforeach;
                            
                    else:
                        $data2[] = 0;
                    endif;

                endforeach;
                
                array_push($arrayData, ["name" => $anioAct, "data"=> $data2]);
                
                return $arrayData;
                
    }
    private function getEmpleadosAreasDiscapacidad(){
        
        
        $arraydata = [];
        
        $datos = (new \yii\db\Query())->select(
            [
                "area.area as name",
                "count(*) as y",
                
            ])
            ->from("sys_rrhh_empleados emp")
            ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
            ->innerJoin("sys_adm_departamentos as departamento","cargo.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->Where("emp.estado = 'A'")
            ->andWhere("emp.discapacidad = 'S'")
            ->groupBy("area.area")
            ->all(SysRrhhEmpleados::getDb());
            
            
            foreach ( $datos as $data):
                array_push($arraydata,  ["name"=> $data["name"], "y" => floatval($data["y"])]);
            endforeach;
            
            return $arraydata;
        
    }
    private function getEmpleadosAreasMaternidad(){
        
        $db =  $_SESSION['db'];
        $arraydata = [];
        
        $datos = Yii::$app->$db->createCommand("EXEC [dbo].[PersonalAreaPersonalMaternidad]")->queryAll(); 
            
            
        foreach ( $datos as $data):
            array_push($arraydata,  ["name"=> $data["area"], "y" => floatval($data["y"])]);
        endforeach;
            
        return $arraydata;
        
    }
    private function getEmpleadosDiscapacidad(){        
            $db =  $_SESSION['db'];
            $arraydata = [];
            
            $datos = Yii::$app->$db->createCommand("EXEC [dbo].[PersonalDispacacidad]")->queryAll();
           
            foreach ( $datos as $data):
               array_push($arraydata,  ["name"=> $data["name"], "y" => floatval($data["y"])]);
            endforeach;
            
            return $arraydata;
            
    }

    private function getTipodeContrato(){        
        $db =  $_SESSION['db'];
        $arraydata = [];
        
        $datos = Yii::$app->$db->createCommand("EXEC [dbo].[PersonalContrato]")->queryAll();
       
        foreach ( $datos as $data):
           array_push($arraydata,  ["name"=> $data["name"], "y" => floatval($data["y"])]);
        endforeach;
        
        return $arraydata;
        
    }
    private function getEmpleadosDiscapacidadDetalle(){
       
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[PersonalDispacacidadDetalle]")->queryAll();
    

    }
    private function getEmpleadosMaternidadDetalle(){
       
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[PersonalDetalleMaternidad]")->queryAll();
    

    }
    private function getEdadHombres(){
        
        $arraydata = [];
        
        $datos =  (new \yii\db\Query())
        ->select(["(cast(datediff(dd,fecha_nacimiento,GETDATE()) / 365.25 as int)) as edad",  "count(id_sys_rrhh_cedula) as total"])
        ->from("sys_rrhh_empleados")
        ->Where("estado = 'A'")
        ->andWhere("genero = 'M'")
        ->groupBy("(cast(datediff(dd,fecha_nacimiento,GETDATE()) / 365.25 as int))")
        ->all(SysRrhhEmpleados::getDb());
        
        foreach ( $datos as $data):
            array_push($arraydata,  [$data["edad"], floatval($data["total"])]);
        endforeach;
        
        return $arraydata;
        
    }
    private function getEdadMujeres(){
        
        
        $arraydata = [];
        
        $datos =  (new \yii\db\Query())
        ->select(["(cast(datediff(dd,fecha_nacimiento,GETDATE()) / 365.25 as int)) as edad",  "count(id_sys_rrhh_cedula) as total"])
        ->from("sys_rrhh_empleados")
        ->Where("estado = 'A'")
        ->andWhere("genero = 'F'")
        ->groupBy("(cast(datediff(dd,fecha_nacimiento,GETDATE()) / 365.25 as int))")
        ->all(SysRrhhEmpleados::getDb());
        
        foreach ( $datos as $data):
            array_push($arraydata,  [$data["edad"], floatval($data["total"])]);
        endforeach;
        
        return $arraydata;
    }
    private function getEdadHombresProducion(){
      
        $db    =  $_SESSION['db'];
        $arraydata = [];
        $datos =  Yii::$app->$db->createCommand("exec dbo.ObtenerEdadXGeneroProduccion @genero = 'M'")->queryAll();
        
        foreach ($datos as $data):
              array_push($arraydata, [$data["edad"], floatval($data["total"])]);
        endforeach;
        
        return $arraydata;
        
    }

    private function getEmpleadosActivosPlanta(){
      
        $db    =  $_SESSION['db'];
        $arraydata = [];
        $datos =  Yii::$app->$db->createCommand("exec dbo.ObtenerEmpleadosActivosPlanta")->queryAll();
        
        foreach ($datos as $data):
            $total = $data['totalE'] - $data['totalS'];
            array_push($arraydata,  ["name"=> $data["genero"]== "M"? "HOMBRES": "MUJERES", "y" => floatval($total)]);
        endforeach;

        return $arraydata;
        
    }

    private function getEmpleadosActivosPlantaVisitas(){
      
        $db    =  $_SESSION['db'];
        $arraydata = [];

        $datos =  Yii::$app->$db->createCommand("exec dbo.[ObtenerVisitasActivosPlantaXDepartamento]")->queryAll();
        
        foreach ($datos as $data):
            array_push($arraydata,  ["name"=> $data["departamento"], "y" => floatval($data['total'])]);
        endforeach;

        return $arraydata;
        
    }

    private function getMarcacionesHombresDetalle(){
        
        $db = $_SESSION['db']; 
        return Yii::$app->$db->createCommand("exec dbo.ObtenerMarcacionesIngresosXGenero @genero = 'M'")->queryAll();
        
    }

    private function getMarcacionesMujeresDetalle(){
        
        $db = $_SESSION['db']; 
        return Yii::$app->$db->createCommand("exec dbo.ObtenerMarcacionesIngresosXGenero @genero = 'F'")->queryAll();
        
    }

    private function getVisitasDetalle(){
        
        $db = $_SESSION['db']; 
        return Yii::$app->$db->createCommand("exec dbo.ObtenerHoraVisitasActivosPlanta")->queryAll();
        
    }

    private function getEdadHombresProduccionDetalle(){
        
        $db = $_SESSION['db']; 
        return Yii::$app->$db->createCommand("exec dbo.ObtenerEdadXGeneroProduccionDetalle @genero = 'M'")->queryAll();
        
    }

    private function getEdadMujeresProduccionDetalle(){        
        
        $db = $_SESSION['db'];
        return Yii::$app->$db->createCommand("exec dbo.ObtenerEdadXGeneroProduccionDetalle @genero = 'F'")->queryAll();
        
     }

    private function getTipodeContratoIndefinidoDetalle(){
        
        $db = $_SESSION['db']; 
        return Yii::$app->$db->createCommand("exec dbo.ObtenerTipodeContratoDetalle @tipocontrato = '1'")->queryAll();
        
    }

    private function getTipodeContratoEventualDetalle(){
        
        $db = $_SESSION['db']; 
        return Yii::$app->$db->createCommand("exec dbo.ObtenerTipodeContratoDetalle @tipocontrato = '2'")->queryAll();
        
    }

    private function getTipodeContratoPasantiaDetalle(){
        
        $db = $_SESSION['db']; 
        return Yii::$app->$db->createCommand("exec dbo.ObtenerTipodeContratoDetalle @tipocontrato = '3'")->queryAll();
        
    }

    private function getTipodeContratoEmergenteDetalle(){
        
        $db = $_SESSION['db']; 
        return Yii::$app->$db->createCommand("exec dbo.ObtenerTipodeContratoDetalle @tipocontrato = '4'")->queryAll();
        
    }

    private function getTipodeContratoTemporadaDetalle(){
        
        $db = $_SESSION['db']; 
        return Yii::$app->$db->createCommand("exec dbo.ObtenerTipodeContratoDetalle @tipocontrato = '5'")->queryAll();
        
    }

    private function getTipodeContratoProduccionDetalle(){
        
        $db = $_SESSION['db']; 
        return Yii::$app->$db->createCommand("exec dbo.ObtenerTipodeContratoDetalle @tipocontrato = '6'")->queryAll();
        
    }

    private function getEdadMujeresProduccion(){    
        $db    =  $_SESSION['db'];
        $arraydata = [];
        $datos =  Yii::$app->$db->createCommand("exec dbo.ObtenerEdadXGeneroProduccion @genero = 'F'")->queryAll();
        
        foreach ($datos as $data):
           array_push($arraydata, [$data["edad"], floatval($data["total"])]);
        endforeach;
        
        return $arraydata;
        
    }

}

