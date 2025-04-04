<?php
namespace app\controllers;

use app\models\SysRrhhEmpleadosActualizacionDatos;
use app\models\SysRrhhEventosEmpleados;
use Yii;
use app\models\SysEmpresa;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosNovedades;
use Mpdf\Mpdf;
use app\models\SysRrhhEmpleadosRolCab;
use kartik\mpdf\Pdf;
use GuzzleHttp\Client;

class FuncionesController extends \yii\web\Controller
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
    
    public function actionCargarnovedades(){
        
        return $this->render('carganovedades');
    }

    public function actionActualizaciondatos(){

        $datos        =   [];
        $anio         =   date('Y');
        $area         =   '';
        $departamento =   '';

        if (Yii::$app->request->post()){
            
            $anio    =  $_POST['anio'] == null ? date('Y'): $_POST['anio'];
            $area    =  $_POST['area'] ==  null ? '': $_POST['area'];
            $departamento =  $_POST['departamento'] == null ? '' : $_POST['departamento'];

            $db   = $_SESSION['db'];

            if($area != '' && $departamento != ''):

                $datos =   Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerEmpleadosActivosParaActualizacionDatosXAreayDepartamento] @anio = '{$anio}', @area = '{$area}' , @departamento = '{$departamento}'")->queryAll(); 

            elseif($area != ''):
                
                $datos =   Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerEmpleadosActivosParaActualizacionDatosXArea] @anio = '{$anio}',@area = '{$area}'")->queryAll(); 
            
            else:

                $datos =   Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerEmpleadosActivosParaActualizacionDatos] @anio = '{$anio}'")->queryAll(); 
            
            endif;
           
        }
        
        return $this->render('actualizaciondatos',['datos'=> $datos,'anio'=> $anio, 'area' =>$area, 'departamento' =>$departamento]);
    }

    public function actionActualizardatos(){
        
        if (Yii::$app->request->isAjax && Yii::$app->request->post()) {
            
            $obj         =  json_decode(Yii::$app->request->post('datos'));
            $empleados   =  $obj->{'empleados'};
            $error       =  [];  
                   
            foreach($empleados as $data){
                    
                $newactualizacion = new SysRrhhEmpleadosActualizacionDatos();

                $newactualizacion->id_sys_rrhh_cedula = $data->id_sys_rrhh_cedula;
                $newactualizacion->anio = $data->anio;
                $newactualizacion->recibido = true;

                $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $data->id_sys_rrhh_cedula])->one();

                $empleado->transporte = $data->transporte;
                $empleado->id_sys_adm_ruta = $data->ruta;

                $empleado->save(false);
                $newactualizacion->save(false);
                    
            }
                   
            return  json_encode(['data'=> ['estado'=>  true , 'mjs'=> 'Datos Actualizados']]);
        } 

    }

    public function actionDatosanual(){

        $datos        =   [];
        $anio         =   date('Y');

        if (Yii::$app->request->post()){
            
            $anio    =  $_POST['anio'] == null ? date('Y'): $_POST['anio'];

            $db   = $_SESSION['db'];

            $datos =   Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerEmpleadosActivosParaActualizacionDatosAnual] @anio = '{$anio}'")->queryAll(); 
           
        }
        
        return $this->render('datosanual',['datos'=> $datos,'anio'=> $anio]);
    }

    public function actionDatosanualxls($anio){
        
        $db   = $_SESSION['db'];

        $datos =   Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerEmpleadosActivosParaActualizacionDatosAnual] @anio = '{$anio}'")->queryAll(); 

        return  $this->renderAjax('_datosanualxls', ['datos'=> $datos, 'anio'=> $anio]);

    }

    public function actionEventos(){

        $evento = "";
       
        return $this->render('_eventos', ['evento'=> $evento]);
    }

    public function actionRegistrarevento(){
        
        
        $codempleado    =  trim(Yii::$app->request->get('codempleado'));
        $idEvento       =  trim(Yii::$app->request->get('idEvento'));
        
        $img            =  file_get_contents('img/sin_foto.jpg');
        $fotodefault    = 'data:image/jpeg;base64, '.base64_encode($img);
        $foto           =  '';
        $mensaje        =  '';
        $estado         =  false;
        $count          =  0;
       
        $empleado  = (new \yii\db\Query())
        ->select(["*"])
        ->from("sys_rrhh_empleados")
        ->where("estado = 'A'")
        ->andwhere("(codigo_temp = '{$codempleado}' or id_sys_rrhh_cedula = '{$codempleado}')")
        ->one(SysRrhhEmpleados::getDb());
        
        $count   =  (new \yii\db\Query())->select(["count(*)"])
        ->from("sys_rrhh_eventos_empleados")
        ->where("year(fecha) = year(getdate())")
        ->andWhere("idEvento = '{$idEvento}'")
        ->Scalar(SysRrhhEmpleados::getDb());
        
        if($empleado):
        
                $fotoemp  = (new \yii\db\Query())
                ->select(["foto","baze64"])
                ->from("sys_rrhh_empleados_foto cross apply (select foto as '*' for xml path('')) T (baze64)")
                ->where("id_sys_rrhh_cedula = '{$empleado['id_sys_rrhh_cedula']}'")
                ->one(SysRrhhEmpleados::getDb());
            
                if($fotoemp['baze64'] != null):
                    
                      $foto =  'data:image/jpeg;base64, '.$fotoemp['baze64'];
                
                endif;
             
                $model = (new \yii\db\Query())->select(["*"])
                ->from("sys_rrhh_eventos_empleados")
                ->where("year(fecha) = year(getdate())")
                ->andwhere("id_sys_rrhh_cedula = '{$empleado['id_sys_rrhh_cedula']}'")
                ->andWhere("idEvento = '{$idEvento}'")
                ->one(SysRrhhEmpleados::getDb()); 
             
                if(!$model):
             
                    $newmodel = new SysRrhhEventosEmpleados();
                    $newmodel->id_sys_rrhh_cedula          = $empleado['id_sys_rrhh_cedula'];
                    $newmodel->fecha                       = date('Y-m-d');
                    $newmodel->idEvento                    = $idEvento;
                    $newmodel->fecha_registro              = date('Ymd H:i:s');
                    $newmodel->usuario_registro            = Yii::$app->user->username;
                         
                        if($newmodel->save(false)):
                         
                            $count   =  (new \yii\db\Query())->select(["count(*)"])
                            ->from("sys_rrhh_eventos_empleados")
                            ->where("year(fecha) = year(getdate())")
                            ->andWhere("idEvento = '{$idEvento}'")
                            ->Scalar(SysRrhhEmpleados::getDb());
                         
                            $mensaje =  'Registro Exitoso!!';
                            $estado   = true;
                         
                        else:
                      
                            $mensaje =  'Ha Ocurrido un Error!!';
    
                        endif;
                                                 
                else:

                    $mensaje = 'El empleador ya registra en el evento';
       
                endif;
          
         
        else:

            $mensaje = 'El empleador no se encuentra registrado, Comuniquese con el departamento de DDOO!!';

        endif;
            
        
        return json_encode( [ 'data' => [ 'estado'=> $estado, 'mjs'=> $mensaje, 'foto'=> $foto, 'fotodefault' => $fotodefault, 'count'=> $count]]);
    }

    public function actionContadorevento(){
        
        $idEvento       =  trim(Yii::$app->request->get('idEvento'));
        
        $img            =  file_get_contents('img/sin_foto.jpg');
        $fotodefault    = 'data:image/jpeg;base64, '.base64_encode($img);
        $estado         =  true;
        $count          =  0;
       
        $count   =  (new \yii\db\Query())->select(["count(*)"])
        ->from("sys_rrhh_eventos_empleados")
        ->where("year(fecha) = year(getdate())")
        ->andWhere("idEvento = '{$idEvento}'")
        ->Scalar(SysRrhhEmpleados::getDb());
        
        return json_encode( [ 'data' => [ 'estado'=> $estado, 'fotodefault' => $fotodefault, 'count'=> $count]]);
    }

    public function actionInformeeventos(){

        $datos        =   [];
        $evento      =   "";

        if (Yii::$app->request->post()){
            
            $evento    =  $_POST['nombreEvento'] == null ? "": $_POST['nombreEvento'];

            $db   = $_SESSION['db'];

            $datos =   Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerEmpleadoXEvento] @evento = '{$evento}'")->queryAll(); 
           
        }
        
        return $this->render('informecapacitaciones',['datos'=> $datos,'evento'=> $evento]);
    }

    public function actionInformecapacitacionesxls($evento){
        
        $db   = $_SESSION['db'];

        $datos =   Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerEmpleadoXEvento] @evento = '{$evento}'")->queryAll();  

        return  $this->renderAjax('_informecapacitacionesxls', ['datos'=> $datos, 'evento'=> $evento]);

    }
    
    public function actionGuardarnovedades(){
        
        if (Yii::$app->request->isAjax && Yii::$app->request->post()) {
        
                  $obj         =  json_decode(Yii::$app->request->post('cadena'));
                
                  $novedades   =  $obj->{'novedades'};
                  
                  
                 if($obj->{'tiponovedad'} == '1'):
                  
                    $concepto = 'DESC_PRES_QUIRO';
                  
                 elseif ($obj->{'tiponovedad'} == '2'):
                   
                    $concepto = 'DESC_PRES_HIPO';
                  
                 elseif ($obj->{'tiponovedad'} == '3'):
                  
                    $concepto = 'DESC_FAR';
                  
                 elseif($obj->{'tiponovedad'} == '4'):
                  
                    $concepto = 'VENTA_FILET';
                
                 elseif($obj->{'tiponovedad'} == '5'):
                  
                    $concepto = 'CRD-PAPEL';
                    
                 endif;
                  
                 // $concepto    =  $obj->{'tiponovedad'} == '1' ? 'DESC_PRES_QUIRO' : $obj->{'tiponovedad'} == '2' ? 'DESC_PRES_HIPO' : 'DESC_FAR';
                  
                  $db          = $_SESSION['db'];
                  
                  $transaction = \Yii::$app->$db->beginTransaction();
                  
                  $flag = true;
                  
                  foreach ($novedades as $data ){
                      
                      //verificamos el  empleador 
                      $empleado  = SysRrhhEmpleados::find()->where(['like', 'id_sys_rrhh_cedula' , '%'.$data->cedula.'%', false])->andWhere(['id_sys_empresa'=> '001'])->one();
                      
                       if ($empleado){
                           
                           $codnovedad =  SysRrhhEmpleadosNovedades::find()->select(['max(CAST(id_sys_rrhh_empleados_novedad AS INT))'])->Where(['id_sys_empresa'=> '001'])->scalar();
                           
                           $newnovedad = new SysRrhhEmpleadosNovedades();
                           
                           $newnovedad->id_sys_rrhh_empleados_novedad = $codnovedad + 1 ;
                           $newnovedad->id_sys_rrhh_cedula            = $empleado->id_sys_rrhh_cedula;
                           $newnovedad->id_sys_empresa                = '001';
                           $newnovedad->id_sys_rrhh_concepto          = $concepto;
                           $newnovedad->fecha                         = $obj->{'fecha'};
                           $newnovedad->cantidad                      = floatval($data->valor);
                           $newnovedad->comentario                    = $data->descripcion;
                           $newnovedad->transaccion_usuario           = Yii::$app->user->username;  
                           if(!$flag = $newnovedad->save(false)){
                               
                               break;
                               
                           }   
                       }            
                 }
                 
                 if($flag){
                     $transaction->commit();
                     echo  json_encode(['data' => [ 'estado' => false,'mensaje' => 'Los datos se ha registrado con exito!']]);
                     
                 }else{
                     $transaction->rollBack();
                     echo  json_encode(['data' => ['estado' => false, 'mensaje' => 'Ha ocurrido un error al guardar la noveadad!']]);
               } 
              
        }else{
            
             echo  json_encode(['data' => ['estado' => false, 'mensaje' => 'Ha ocurrido un error!']]);
        }
    }
    
   
    public function actionGeneratxt(){
        
        $meses        =  Yii::$app->params['meses'];
        $periodos     =  Yii::$app->params['periodos'];  
        $mes          =  date('n');
        $periodo      =  '';
        $contenido    =  '';
        $datos        =  []; 
        $anio         =  date('Y');
        $mjs          =  "";
        $tipo         =  '';
        $area         =  '';
        $departamento =  '';
       
        
        $archivo = "Arhivobanco.txt";
        
        if (Yii::$app->request->post()){
            
             $anio    =  $_POST['anio'];
             $mes     =  $_POST['mes'];
             $periodo =  $_POST['periodo'] == null ?  '': $_POST['periodo']; 
             $area    =  $_POST['area'] ==  null ? '': $_POST['area'];
             $departamento =  $_POST['departamento'] == null ? '' : $_POST['departamento'];
           
             if($periodo == 72 or $periodo == 73 ):
             
                   $db   = $_SESSION['db'];
             
                   if($periodo == 72):
                        
                         //Utilidades activos
                         $datos =   Yii::$app->$db->createCommand("EXEC [dbo].[ArchivoUtilidadesBanco] @anio = '{$anio}', @estado= 'A'")->queryAll();
                         
                         $contenido = "";
                         $cont = 0;
                         
                         foreach ($datos as $data):
                             
                             $cont++;
                             $valor1 = number_format($data['Total'], 2 , '.', '');
                             $array  = explode('.',  $valor1);
                             
                             $valor =  $array[0].''. $array[1];
                             
                             $tipoCuenta = "AHO";
                             
                             if($data["id_sys_rrhh_forma_pago"] == 'R'):
                             
                                $tipoCuenta = "CTE";
                             
                             endif;
                             
                             if( intval($data['cuenta']) > 0):
                             
                                 $contenido.="PA\t".$data['cuenta']."\t".$cont."\t\t".trim($data['id_sys_rrhh_cedula'])."\tUSD\t".str_pad($valor, 13, "0", STR_PAD_LEFT)."\tCTA\t0036\t".$tipoCuenta."\t".$data['cta_banco']."\tC\t".trim($data['id_sys_rrhh_cedula'])."\t".trim($data['nombres'])."\t\t\t\t\tUTILIDADES ".$anio."\t".date('Y-m-d');
                                 $contenido.= "\n";
                             
                             endif;
                             
                         endforeach;
                         
                         if (count($datos) > 0 ):
                             
                             $contenido = utf8_encode($contenido);
                             $f=fopen($archivo,"web");
                             fwrite($f,$contenido);
                             fclose($f);
                             
                             $enlace = $archivo;
                             header ("Content-Disposition: attachment; filename=".$enlace);
                             header ("Content-Type: application/octet-stream");
                             header ("Content-Length: ".filesize($enlace));
                             readfile($enlace);
                               
                         endif;
                           
                   elseif($periodo == 73):
                   
                       //Empleados Inactivos 
                       $datos =   Yii::$app->$db->createCommand("EXEC [dbo].[ArchivoUtilidadesBanco] @anio = '{$anio}', @estado= 'I'")->queryAll();
                       
                       $contenido = "";
                       $cont = 0;
                       
                       foreach ($datos as $data):
                           
                           $cont++;
                           $valor1 = number_format($data['Total'], 2 , '.', '');
                           $array = explode('.',  $valor1);
                           
                           $valor =  $array[0].''.$array[1];
                           
                          $contenido.="PA\t".$data['cuenta']."\t".$cont."\t\t".trim($data['id_sys_rrhh_cedula'])."\tUSD\t".str_pad($valor, 13, "0", STR_PAD_LEFT)."\tEFE\t36\t\t\tC\t".trim($data['id_sys_rrhh_cedula'])."\t".trim($data['nombres'])."\t\t\t\t\tUTILIDADES ".$anio."\t".date('Y-m-d');
                          $contenido.= "\n";
                       
                           
                       endforeach;
                       
                       if (count($datos) > 0 ) :
                           
                           $contenido = utf8_encode($contenido);
                           $f=fopen($archivo,"web");
                           fwrite($f,$contenido);
                           fclose($f);
                           
                           $enlace = $archivo;
                           header ("Content-Disposition: attachment; filename=".$enlace);
                           header ("Content-Type: application/octet-stream");
                           header ("Content-Length: ".filesize($enlace));
                           readfile($enlace);
                           
                       endif;
                       
                   endif;
             
             else:
             
             
             $rol = SysRrhhEmpleadosRolCab::find()->where(['anio'=> $anio])->andWhere(['mes'=> $mes])->andWhere(['periodo'=> $periodo])->one();
             
                     if($rol):    
                     
                          if($rol->estado == 'P'):
                     
                                 if ($periodo != null){
                                    
                                          $consepto = $this->getConcepto($periodo);
                             
                                          switch ($periodo) {
                                              case 1:
                                                   $tipo = 'QUICENAL';
                                                  break;
                                              case 2:
                                                  $tipo  = 'MENSUAL';
                                                  break;
                                              case 70:
                                                  $tipo  = 'DECIMO TERCER SUELDO';
                                                  break;
                                              case 71:
                                                  $tipo  = 'DECIMO CUARTO SUELDO';
                                                  break;
                                          }
                                          
                                          
                                          
                                          $datos =  (new \yii\db\Query())->select(
                                              [
                                               "emp.id_sys_rrhh_cedula",
                                               "emp.nombres", 
                                               "emp.cta_banco",
                                               "banco.cuenta",
                                               "emp.id_sys_rrhh_forma_pago",
                                               "(select isnull(sum(mov.valor), 0) from sys_rrhh_empleados_rol_mov as mov  
                                                  inner join sys_rrhh_conceptos as conceptos on (conceptos.id_sys_empresa=mov.id_sys_empresa and conceptos.id_sys_rrhh_concepto= mov.id_sys_rrhh_concepto)
                                                   where mov.id_sys_rrhh_cedula = rol_mov.id_sys_rrhh_cedula and  mov.anio= rol_mov.anio and mov.mes= rol_mov.mes  and mov.periodo= rol_mov.periodo  and mov.id_sys_empresa= rol_mov.id_sys_empresa and tipo = 'I') 
                                                - (select isnull(sum(mov.valor), 0) from sys_rrhh_empleados_rol_mov as mov 
                                                   inner join sys_rrhh_conceptos as conceptos on (conceptos.id_sys_empresa=mov.id_sys_empresa and conceptos.id_sys_rrhh_concepto= mov.id_sys_rrhh_concepto)
                                                   where mov.id_sys_rrhh_cedula = rol_mov.id_sys_rrhh_cedula and  mov.anio= rol_mov.anio and mov.mes= rol_mov.mes  and mov.periodo= rol_mov.periodo  and mov.id_sys_empresa= rol_mov.id_sys_empresa and tipo = 'E') Total",
                                            
                                              ])
                                             ->from("sys_rrhh_empleados_rol_mov as rol_mov")
                                             ->join("INNER JOIN", "sys_rrhh_empleados as emp","rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula")
                                             ->join("INNER JOIN", "sys_rrhh_bancos as banco","emp.id_sys_rrhh_banco = banco.id_sys_rrhh_banco and banco.estado = 'A'")
                                             ->where("rol_mov.anio = '{$anio}'")
                                             ->andwhere("rol_mov.mes=  '{$mes}'")
                                             ->andwhere("rol_mov.periodo=  {$periodo}")
                                             ->andwhere("rol_mov.id_sys_empresa= '001'")
                                             ->andwhere("id_sys_rrhh_concepto = '{$consepto}'")
                                             ->andwhere("emp.id_sys_empresa  = '001'")
                                             ->andwhere("emp.id_sys_rrhh_forma_pago in('A','R')") 
                                             ->andwhere("id_sys_adm_departamento in (select id_sys_adm_departamento from sys_adm_departamentos where id_sys_adm_area like '%{$area}%' and id_sys_adm_departamento like '%{$departamento}%')")
                                             ->orderBy("nombres")  
                                             ->all(SysRrhhEmpleadosNovedades::getDb());
                                             
                                             $contenido = "";
                                             $cont = 0;
                                             
                                             foreach ($datos as $data){
                                                 
                                                     
                                                     $array = explode('.',  $data['Total']);
                                                     
                                                     $valor =  $array[0].''.$array[1]; 
                                                    
                                                      $tipoCuenta = "AHO";
                                                     
                                                     if($data["id_sys_rrhh_forma_pago"] == 'R'):
                                                     
                                                      $tipoCuenta = "CTE";
                                                     
                                                     endif;
                                                     
                                                     
                                                     if( intval($data['cuenta']) > 0):
                                                     
                                                        if($valor > 0):
                                                            $cont++;
                                                            $contenido.="PA\t".$data['cuenta']."\t".$cont."\t\t".trim($data['id_sys_rrhh_cedula'])."\tUSD\t".str_pad($valor, 13, "0", STR_PAD_LEFT)."\tCTA\t0036\t".$tipoCuenta."\t".$data['cta_banco']."\tC\t".trim($data['id_sys_rrhh_cedula'])."\t".trim($data['nombres'])."\t\t\t\t\tROL DE PAGOS ".$tipo."\t".date('Y-m-d');
                                                            $contenido.= "\n"; 
                                                        endif;

                                                     endif;
                                                 }
                                             
                                                 if (count($datos) > 0 ) {
                                                     
                                                     
                                                        $contenido = utf8_encode($contenido);
                                                        $f=fopen($archivo,"web");
                                                        fwrite($f,$contenido);
                                                        fclose($f);
                                                         
                                                        $enlace = $archivo;
                                                        header ("Content-Disposition: attachment; filename=".$enlace);
                                                        header ("Content-Type: application/octet-stream");
                                                        header ("Content-Length: ".filesize($enlace));
                                                        ob_clean(); // Limpia el búfer de salida
                                                        flush();    // Vacía el búfer del sistema
                                                        readfile($enlace);
                                                       
                                                     
                                                 }
                                     
                                          };

                                else:
                                
                                         Yii::$app->getSession()->setFlash('info', [
                                        'type' => 'warning','duration' => 1500,
                                        'icon' => 'glyphicons glyphicons-robot','message' => 'El perido no se encuentra procesado!',
                                        'positonY' => 'top','positonX' => 'right']);
                                         return $this->render('generatxt', ['meses'=> $meses, 'mes'=> $mes, 'periodos'=> $periodos, 'periodo'=> $periodo, 'anio'=> $anio, 'area'=> $area, 'departamento'=> $departamento]);
                                         
                                endif;
                         else:
                         
                              Yii::$app->getSession()->setFlash('info', [
                             'type' => 'warning','duration' => 1500,
                             'icon' => 'glyphicons glyphicons-robot','message' => 'El periodo no existe!',
                             'positonY' => 'top','positonX' => 'right']);
                              return $this->render('generatxt', ['meses'=> $meses, 'mes'=> $mes, 'periodos'=> $periodos, 'periodo'=> $periodo, 'anio'=> $anio, 'area'=> $area, 'departamento'=> $departamento]);
                         
                         endif;
                endif;
                         
        }
        return $this->render('generatxt', ['meses'=> $meses, 'mes'=> $mes, 'periodos'=> $periodos, 'periodo'=> $periodo, 'anio'=> $anio, 'area'=> $area, 'departamento'=> $departamento]);
        
    }
    
    public function actionInfotipopago(){
        
        $meses        =  Yii::$app->params['meses'];
        $periodos     =  Yii::$app->params['periodos'];
        $mes          =  date('n');
        $tipospagos   =  ['C' => 'Cheques',  'A' => 'Transferencia', 'F' => 'Finiquito'];
        $tipopago     =  '';
        $periodo      =  1; 
        $datos        =  [];
        $anio         = date('Y');
        
        if (Yii::$app->request->post()){
            
            $anio     =  $_POST['anio'];
            $mes      =  $_POST['mes'];
            $periodo  =  $_POST['periodo'] == null ?  '': $_POST['periodo'];
            $tipopago =  $_POST['tipopago'] == null ? '': $_POST['tipopago'];
            
            
            if($periodo != '' && $tipopago != ''):
            
                 $datos  = $this->getSueldosporpagar('001', $anio, $mes, $periodo, $tipopago);
    
            endif;
            
            
            
        }
        
        return $this->render('informetipopago', ['meses'=> $meses, 'mes'=>$mes, 'periodos'=> $periodos, 'periodo' => $periodo, 'tipopago'=> $tipopago, 'tipospagos'=> $tipospagos, 'datos'=> $datos, 'anio'=> $anio]);
       
    }
    
    public function actionInfotipopagopdf($anio, $mes, $periodo, $tipopago){
        
        $datos =[];
        
        $datos = $this->getSueldosporpagar('001', $anio, $mes, $periodo, $tipopago);
        
        $html =  $this->renderPartial('informetipopagopdf',['anio'=> $anio, 'mes'=> $mes, 'periodo'=> $periodo, 'tipopago'=> $tipopago,  'datos'=> $datos]);
        
        
        /*$mpdf = new Mpdf([
            'format' => 'A4',
        ]);
        
        $mpdf->WriteHTML($html);
        $mpdf->Output('Sueldos_Apagar.pdf', 'I');
        exit();
        */
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            
            // your html content input
            'content' => $html,
            'marginTop' => 20,
            'marginBottom' => 19,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px} .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 { border:0; padding:0;margin-left:-0.00001; } th, td { padding:5px; height:40px;}',
            
            // set mPDF properties on the fly
            //'options' => ['title' => 'Solicitud de Vacaciones'],
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' =>  'ANTICIPOS A PAGAR',
                'SetHeader'=>['Sistema Gestión de Nómina - Anticipos por Pagar||'], 
                'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}']
            ]
        ]);
        
        // return the pdf output as per the destination setting
        return $pdf->render(); 
        
    }
  
    public function actionEnviarroles(){
        
        $meses        =  Yii::$app->params['meses'];
        $periodos     =  Yii::$app->params['periodos'];
        $mes          =  date('n');
        $periodo      =  '';
        $datos        =  [];
        $departamento =  '';
        $area         =  '';
        $anio         = date('Y');
        
        if(Yii::$app->request->post()){
            
            $anio         =  $_POST['anio'];
            $mes          =  $_POST['mes'];
            $periodo      =  $_POST['periodo'] == null ?  '': $_POST['periodo'];
            $departamento =  $_POST['departamento']== null ?  '': $_POST['departamento'];
            $area         =  $_POST['area']== null ? '': $_POST['area'];
            
           $concepto      = $this->getConcepto($periodo);
            
            if($periodo != '' && $concepto != ''):
        
                $datos =  (new \yii\db\Query())->select(
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
                    ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
                    ->andfilterWhere(['like', "area.id_sys_adm_area",$area])
                    ->andwhere("id_sys_rrhh_concepto = '$concepto'")
                    ->orderBy("area, departamento, nombres")
                    ->all(SysRrhhEmpleadosNovedades::getDb());
    
            endif;
   
        }
        return $this->render('enviaroles', ['meses'=> $meses, 'mes'=> $mes, 'periodos'=> $periodos, 'periodo'=> $periodo, 'area'=> $area, 'departamento' => $departamento, 'datos'=> $datos, 'anio'=> $anio]);
        
        
    }
  
    public function actionImprimirroles(){
        
        ini_set("pcre.backtrack_limit", "5000000");
            
        $meses        =  Yii::$app->params['meses'];
        $periodos     =  Yii::$app->params['periodos'];
        $mes          =  date('n');
        $periodo      =  '';
        $datos        =  [];
        $departamento =  '';
        $area         =  '';
        $anio         = date('Y');
        $cedula       = '';
        
        if(Yii::$app->request->post()){
            
            $anio         =  $_POST['anio'];
            $mes          =  $_POST['mes'];
            $periodo      =  $_POST['periodo'] == null ?  '': $_POST['periodo'];
            $departamento =  $_POST['departamento']== null ?  '': $_POST['departamento'];
            $area         =  $_POST['area']== null ? '': $_POST['area'];
            $cedula       =  $_POST['cedula'] == null ? '': $_POST['cedula'];
           
            
            $consepto = $this->getConcepto($periodo);
            
            
            if($periodo != '' && $consepto != ''):
            
            
                 if($cedula == ''):
            
                
                        $datos = $this->getRoles($anio, $mes, $periodo, $area, $departamento, $consepto);
               
                        
                   else:
                           
                        $datos = $this->getRolIndividual($anio, $mes, $periodo, $area, $departamento, $consepto, $cedula);
                   
                   endif;
                   
                   

              endif;
                
        }
        return $this->render('imprimeroles', ['meses'=> $meses, 'mes'=> $mes, 'periodos'=> $periodos, 'periodo'=> $periodo, 'area'=> $area, 'departamento' => $departamento, 'datos'=> $datos, 'anio'=> $anio]);
        
        
    }
    
    public function actionRoldetalle(){
        
        $this->layout = '@app/views/layouts/main_emplados';
        $meses        =  Yii::$app->params['meses'];
        $periodos     =  Yii::$app->params['periodos'];
        $mes          =  date('n');
        $anio         =  date('Y');
        $periodo      =   '';
        $datos        =   [];
        $anio         =   '';
        $departamento =   '';
        $area         =   '';

     
        if (Yii::$app->request->post()){
            
            $anio    =  $_POST['anio'] == null ? date('Y'): $_POST['anio'];
            $mes     =  $_POST['mes'];
            $periodo =  $_POST['periodo'] == null ?  '': $_POST['periodo'];
            $departamento = $_POST['departamento']== null ? '': $_POST['departamento'];
            $area         = $_POST['area']== null ? '':$_POST['area'];
            
            $concepto = $this->getConcepto($periodo);
           
            
            if ($periodo != null && $concepto != ''){
               
                $datos = $this->getRoles($anio, $mes, $periodo, $area, $departamento, $concepto); 
            }
        }
        return $this->render('informeroldetalle', ['meses'=> $meses, 'mes'=> $mes, 'periodos'=> $periodos, 'periodo'=> $periodo, 'area'=> $area, 'departamento' => $departamento, 'datos'=> $datos, 'anio'=> $anio]);
        
    
    }

    public function actionAsientocontable(){
        
        $this->layout = '@app/views/layouts/main_emplados';
        $meses        =  Yii::$app->params['meses'];
        $mes          =  date('n');
        $anio         =  date('Y');
        $periodo      =   2;
        $datos        =   [];
        $anio         =   '';
        $area         =   '';
        $db           = $_SESSION['db'];

     
        if (Yii::$app->request->post()){
            
            $anio    =  $_POST['anio'] == null ? date('Y'): $_POST['anio'];
            $mes     =  $_POST['mes'];
            $periodo =  $_POST['periodo'] == null ?  '': $_POST['periodo'];
            $area         = $_POST['area']== null ? '':$_POST['area'];
            
            $concepto = $this->getConcepto($periodo);
           
            
            if ($periodo != null && $concepto != ''){

                if($area == 1):
               
                    $datos =  Yii::$app->$db->createCommand("exec dbo.[ObtenerRolXAreaAdministracion] {$anio}, {$mes}, {$periodo}, '{$concepto}'")->queryAll();

                elseif($area == 2):

                    $datos =  Yii::$app->$db->createCommand("exec dbo.[ObtenerRolXAreaProduccion] {$anio}, {$mes}, {$periodo}, '{$concepto}'")->queryAll();

                elseif($area == 3):

                    $datos =  Yii::$app->$db->createCommand("exec dbo.[ObtenerRolXAreaMantenimiento] {$anio}, {$mes}, {$periodo}, '{$concepto}'")->queryAll();       

                elseif($area == 4):

                    $datos =  Yii::$app->$db->createCommand("exec dbo.[ObtenerRolXAreaUnidadHigiene] {$anio}, {$mes}, {$periodo}, '{$concepto}'")->queryAll();
                
                elseif($area == 5):

                    $datos =  Yii::$app->$db->createCommand("exec dbo.[ObtenerRolXAreaMateriaPrima] {$anio}, {$mes}, {$periodo}, '{$concepto}'")->queryAll();
                else:

                    $datos =  Yii::$app->$db->createCommand("exec dbo.[ObtenerRolXAreaGestionIntegral] {$anio}, {$mes}, {$periodo}, '{$concepto}'")->queryAll();
                
                endif;
            }
        }
        return $this->render('informerasientocontable', ['meses'=> $meses, 'mes'=> $mes, 'periodo'=> $periodo, 'area'=> $area, 'datos'=> $datos, 'anio'=> $anio]);
        
    
    }

    public function actionCrearasiento(){

        $db          =  $_SESSION['db'];
        $datos       =  Yii::$app->request->post('datos');
        $obj         =  json_decode($datos,true);
        $empresa     =  SysEmpresa::find()->where(['db_name'=> $db])->one();

        $client = new Client(['verify' => false]);

        $req = $client->post($empresa->url.'sap/asiento/phpcreate',[

            'header' => ['content-type' => 'application/json'],
            //'header' => ['Accept'=> 'application/json'],
            'json' => $obj,
        ]);

        $status_code = $req->getStatusCode();
        $response_body = $req->getBody()->getContents();

        if($status_code == 200){
            echo  json_encode(['data' => [ 'estado' => false,'mensaje' => $response_body]]);
        }else{
            echo  json_encode(['data' => [ 'estado' => false,'mensaje' => 'Ha ocurrido un error al generar el asiento!']]);
        }
        
    }
    public function actionInformeasientocontablexls($anio, $mes, $periodo, $area){
        
        $concepto = $this->getConcepto($periodo);
   
        if ($periodo != null && $concepto != ''){

            if($area == 1):
           
                $datos =  (new \yii\db\Query())->select(
                    [
                        "area.id_sys_adm_area",
                        "area.area",
                        "emp.id_sys_adm_ccosto", 
                        "co.centro_costo",
                        "count(emp.id_sys_adm_ccosto) as numero",
                        
                    ])
                    ->from("sys_rrhh_empleados_rol_mov as rol_mov")
                    ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
                    ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
                    ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
                    ->innerJoin("sys_adm_ccostos co","co.id_sys_adm_ccosto = emp.id_sys_adm_ccosto")
                    ->where("rol_mov.anio = '{$anio}'")
                    ->andwhere("rol_mov.mes=  '{$mes}'")
                    ->andwhere("rol_mov.periodo=  {$periodo}")
                    ->andwhere("rol_mov.id_sys_empresa= '001'")
                    ->andWhere(["area.id_sys_adm_area" => [1,8]])
                    ->andwhere("id_sys_rrhh_concepto = '$concepto'")
                    ->groupBy(["area.id_sys_adm_area", "area.area", "emp.id_sys_adm_ccosto","co.centro_costo"])
                    ->orderBy("emp.id_sys_adm_ccosto")
                    ->all(SysRrhhEmpleadosNovedades::getDb());

            elseif($area == 4):

                $datos =  (new \yii\db\Query())->select(
                    [
                        "area.id_sys_adm_area",
                        "area.area",
                        "emp.id_sys_adm_ccosto", 
                        "co.centro_costo",
                        "count(emp.id_sys_adm_ccosto) as numero",
                        
                    ])
                    ->from("sys_rrhh_empleados_rol_mov as rol_mov")
                    ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
                    ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
                    ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
                    ->innerJoin("sys_adm_ccostos co","co.id_sys_adm_ccosto = emp.id_sys_adm_ccosto")
                    ->where("rol_mov.anio = '{$anio}'")
                    ->andwhere("rol_mov.mes=  '{$mes}'")
                    ->andwhere("rol_mov.periodo=  {$periodo}")
                    ->andwhere("rol_mov.id_sys_empresa= '001'")
                    ->andWhere(["area.id_sys_adm_area" => [4,6]])
                    ->andwhere("id_sys_rrhh_concepto = '$concepto'")
                    ->groupBy(["area.id_sys_adm_area", "area.area", "emp.id_sys_adm_ccosto","co.centro_costo"])
                    ->orderBy("emp.id_sys_adm_ccosto")
                    ->all(SysRrhhEmpleadosNovedades::getDb());

            elseif($area == 3):

                $datos =  (new \yii\db\Query())->select(
                    [
                        "area.id_sys_adm_area",
                        "area.area",
                        "emp.id_sys_adm_ccosto", 
                        "co.centro_costo",
                        "count(emp.id_sys_adm_ccosto) as numero",
                                
                    ])
                    ->from("sys_rrhh_empleados_rol_mov as rol_mov")
                    ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
                    ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
                    ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
                    ->innerJoin("sys_adm_ccostos co","co.id_sys_adm_ccosto = emp.id_sys_adm_ccosto")
                    ->where("rol_mov.anio = '{$anio}'")
                    ->andwhere("rol_mov.mes=  '{$mes}'")
                    ->andwhere("rol_mov.periodo=  {$periodo}")
                    ->andwhere("rol_mov.id_sys_empresa= '001'")
                    ->andWhere(["area.id_sys_adm_area" => [3,9]])
                    ->andWhere("co.id_sys_adm_ccosto <> 'CC0202'")
                    ->andwhere("id_sys_rrhh_concepto = '$concepto'")
                    ->groupBy(["area.id_sys_adm_area", "area.area", "emp.id_sys_adm_ccosto","co.centro_costo"])
                    ->orderBy("emp.id_sys_adm_ccosto")
                    ->all(SysRrhhEmpleadosNovedades::getDb());        

            else:

                $datos =  (new \yii\db\Query())->select(
                    [
                        "area.id_sys_adm_area",
                        "area.area",
                        "emp.id_sys_adm_ccosto", 
                        "co.centro_costo",
                        "count(emp.id_sys_adm_ccosto) as numero",
                        
                    ])
                    ->from("sys_rrhh_empleados_rol_mov as rol_mov")
                    ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
                    ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
                    ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
                    ->innerJoin("sys_adm_ccostos co","co.id_sys_adm_ccosto = emp.id_sys_adm_ccosto")
                    ->where("rol_mov.anio = '{$anio}'")
                    ->andwhere("rol_mov.mes=  '{$mes}'")
                    ->andwhere("rol_mov.periodo=  {$periodo}")
                    ->andwhere("rol_mov.id_sys_empresa= '001'")
                    ->andfilterWhere(['like', "area.id_sys_adm_area",$area])
                    ->andwhere("id_sys_rrhh_concepto = '$concepto'")
                    ->groupBy(["area.id_sys_adm_area", "area.area", "emp.id_sys_adm_ccosto","co.centro_costo"])
                    ->orderBy("emp.id_sys_adm_ccosto")
                    ->all(SysRrhhEmpleadosNovedades::getDb());

            endif;

        }

        return  $this->renderAjax('_asientocontablexls', ['mes'=> $mes, 'periodo'=> $periodo, 'area'=> $area, 'datos'=> $datos, 'anio'=> $anio]);

    } 
    
    public function actionRoldetallepdf($anio, $mes, $periodo, $area, $departamento){
        
        ini_set("pcre.backtrack_limit", "5000000");
        
        $concepto = $this->getConcepto($periodo);
       
        if ($periodo != null && $concepto != ''){
            
            $datos = $this->getRoles($anio, $mes, $periodo, $area, $departamento, $concepto);
                
        }
        
        $html =  $this->renderPartial('_informeroldetallepdf', ['mes'=> $mes, 'periodo'=> $periodo, 'area'=> $area, 'departamento' => $departamento, 'datos'=> $datos, 'anio'=> $anio]);
        
       /* $mpdf = new Mpdf([
            'format' => 'A4',
            'orientation' => 'L'
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('RolPago.pdf', 'D');
     
        exit();
        */
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            
            // your html content input
            'content' => $html,
            'marginTop' => 20,
            'marginBottom' => 19,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px} .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {border:0;	padding:0;	margin-left:-0.00001;} th,td{padding:5px;}',
            
            // set mPDF properties on the fly
            //'options' => ['title' => 'Solicitud de Vacaciones'],
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' =>  'Rol Detalle',
                'SetHeader'=>['Sistema Gestión de Nómina - Rol de Pago Detallado||'],
                'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}']
            ]
        ]);
        return $pdf->render();

    }
   
    public function actionRoldetallexlsx($anio, $mes, $periodo, $area, $departamento){
        
        $meses = [1 => 'Enero',  2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre' ];
        
        header("Pragma: public");
        header("Expires: 0");
        $filename = "ROL_PAGO_".$anio."_".$meses[$mes]."_PERIODO_".$periodo.".xls";
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=$filename");
   
        
        $concepto = $this->getConcepto($periodo);        
        
        if ($concepto != ''){
            
            $datos =   $this->getRoles($anio, $mes, $periodo, $area, $departamento, $concepto);
                
        }
        
         return   $this->renderAjax('_tableroldetalle2', ['mes'=> $mes, 'periodo'=> $periodo, 'area'=> $area, 'departamento' => $departamento, 'datos'=> $datos, 'anio'=> $anio]);

    }
    
    public function actionConsolidadoxlsx($anio, $mes, $periodo, $area, $departamento){
        
        
        $consepto = $this->getConcepto($periodo);
        
            
        if ($periodo != null && $consepto != ''){
            
            $datos =  (new \yii\db\Query())->select(
                [
                    "area.id_sys_adm_area",
                    "area.area",
                    "id_sys_adm_ccosto", 
                    "count(id_sys_adm_ccosto) as numero"
                    
                ])
                ->from("sys_rrhh_empleados_rol_mov as rol_mov")
                ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
                ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
                ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
                ->where("rol_mov.anio = '{$anio}'")
                ->andwhere("rol_mov.mes=  '{$mes}'")
                ->andwhere("rol_mov.periodo=  {$periodo}")
                ->andwhere("rol_mov.id_sys_empresa= '001'")
                ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
                ->andfilterWhere(['like', "area.id_sys_adm_area",$area])
                ->andwhere("id_sys_rrhh_concepto = '$consepto'")
                ->groupBy(["area.id_sys_adm_area", "area.area", "id_sys_adm_ccosto"])
                ->orderBy("id_sys_adm_ccosto")
                ->all(SysRrhhEmpleadosNovedades::getDb());
                
        }
        
       return   $this->render('_consolidadoxlsx', ['mes'=> $mes, 'periodo'=> $periodo, 'area'=> $area, 'departamento' => $departamento, 'datos'=> $datos, 'anio'=> $anio]);
        
    }
    
    public function actionHorasextrasxlsx($anio, $mes, $periodo, $area, $departamento){
        
       
        if ($periodo == 2){
            
            $datos =  (new \yii\db\Query())->select(
                [
                    "area",
                    "departamento",
                    "rol_mov.id_sys_rrhh_cedula",
                    "emp.nombres",
                    "cantidad",
                    "(SELECT isnull(valor, 0)  FROM sys_rrhh_empleados_rol_mov mov
                     where mov.periodo =  rol_mov.periodo and mov.id_sys_rrhh_concepto = 'PAGO_HORAS_25' and mov.anio = rol_mov.anio and mov.mes = rol_mov.mes and mov.id_sys_rrhh_cedula = rol_mov.id_sys_rrhh_cedula) as h25",
                    "(SELECT isnull(valor, 0)  FROM sys_rrhh_empleados_rol_mov mov
                     where mov.periodo =  rol_mov.periodo and mov.id_sys_rrhh_concepto = 'PAGO_HORAS_50' and mov.anio = rol_mov.anio and mov.mes = rol_mov.mes and mov.id_sys_rrhh_cedula = rol_mov.id_sys_rrhh_cedula) as h50",
                    "(SELECT isnull(valor, 0)  FROM sys_rrhh_empleados_rol_mov mov
                     where mov.periodo =  rol_mov.periodo and mov.id_sys_rrhh_concepto = 'PAGO_HORAS_100' and mov.anio = rol_mov.anio and mov.mes = rol_mov.mes and mov.id_sys_rrhh_cedula = rol_mov.id_sys_rrhh_cedula) as h100"
                ])
                ->from("sys_rrhh_empleados_rol_mov as rol_mov")
                ->join('INNER JOIN', 'sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
                ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
                ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
                ->where("rol_mov.anio = '{$anio}'")
                ->andwhere("rol_mov.mes=  '{$mes}'")
                ->andwhere("rol_mov.periodo=  {$periodo}")
                ->andwhere("rol_mov.id_sys_empresa= '001'")
                ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
                ->andfilterWhere(['like', "area.id_sys_adm_area",$area])
                ->andwhere("id_sys_rrhh_concepto = 'SUELDO'")
                ->all(SysRrhhEmpleadosNovedades::getDb());
                
                
                return   $this->render('_horasextrasxlsx', ['mes'=> $mes, 'periodo'=> $periodo, 'area'=> $area, 'departamento' => $departamento, 'datos'=> $datos, 'anio'=> $anio]);
                
                
              
        }
    
        
        
    }
    
    public function actionSalidapersonal(){
        
        $anio   =  date('Y');
        $meses  =  Yii::$app->params['meses'];
        $mesini =  date('n');
        $mesfin =  date('n');
        $datos  =  [];
        $id_sys_rrhh_causa_salida = '';
        
        if (Yii::$app->request->post()){
        
            $anio   = $_POST['anio'];
            $mesini = $_POST['mesini'];
            $mesfin = $_POST['mesfin'];
            $id_sys_rrhh_causa_salida = $_POST['id_sys_rrhh_causa_salida'];
            $datos   =    $this->getSalidapersonal($anio, $mesini, $mesfin, $id_sys_rrhh_causa_salida);
        
        }
        
        return $this->render('informesalidapersonal', ['mesini'=> $mesini, 'mesfin' => $mesfin, 'id_sys_rrhh_causa_salida' => $id_sys_rrhh_causa_salida, 'anio'=> $anio, 'meses'=> $meses, 'datos'=> $datos]); 
        
    }

    public function actionAjustesalarial(){
        
        $anio   =  date('Y');
        $datos  =  [];
        
        if (Yii::$app->request->post()){
        
            $anio   = $_POST['anio'];

            $datos   =    $this->getDatosAjusteSalarial($anio);
        
        }
        
        return $this->render('informeajustesalarial', [ 'anio'=> $anio, 'datos'=> $datos]); 
        
    }

    public function actionAjustesalarialpdf($anio){
        
        
        $datos =  $this->getDatosAjusteSalarial($anio);
                
        $html =  $this->renderPartial('informeajustesalarialpdf',['anio'=> $anio,  'datos'=> $datos]);
        
        /* $mpdf = new Mpdf([
            'format' => 'A4',
             'orientation' => 'L'
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('Salida_Personal.pdf', 'I');
        
        exit();
        */
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            
            // your html content input
            'content' => $html,
            'marginTop' => 20,
            'marginBottom' => 19,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px} .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 { border:0; padding:0;margin-left:-0.00001; } th, td { padding:1px;}',
            
            // set mPDF properties on the fly
            //'options' => ['title' => 'Solicitud de Vacaciones'],
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' =>  'Ajuste Salarial',
                'SetHeader'=>['Sistema Gestión de Nómina - Reporte Ajuste Salarial||'],
                'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}']
            ]
        ]);
        
        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    public function actionAjustesalarialxls($anio){
        
        ini_set("pcre.backtrack_limit", "50000000");
        
        $datos =   $this->getDatosAjusteSalarial($anio);

        if (count($datos) > 0) :
             
            return   $this->render('_ajustesalarialxls', ['datos'=> $datos,'anio'=>$anio]);

        endif;
    }
    
    public function actionSalidapersonalpdf($anio, $mesini, $mesfin, $causa_salida){
        
        
        $datos =  $this->getSalidapersonal($anio, $mesini, $mesfin, $causa_salida);
                
        $html =  $this->renderPartial('informesalidapersonalpdf',['mesini'=> $mesini, 'mesfin'=> $mesfin, 'id_sys_rrhh_causa_salida'=>$causa_salida,'anio'=> $anio,  'datos'=> $datos]);
        
        /* $mpdf = new Mpdf([
            'format' => 'A4',
             'orientation' => 'L'
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('Salida_Personal.pdf', 'I');
        
        exit();
        */
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            
            // your html content input
            'content' => $html,
            'marginTop' => 20,
            'marginBottom' => 19,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px} .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 { border:0; padding:0;margin-left:-0.00001; } th, td { padding:1px;}',
            
            // set mPDF properties on the fly
            //'options' => ['title' => 'Solicitud de Vacaciones'],
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' =>  'Salida de Personal',
                'SetHeader'=>['Sistema Gestión de Nómina - Reporte Salida de Personal||'],
                'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}']
            ]
        ]);
        
        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    public function actionSalidapersonalxls($anio, $mesini, $mesfin, $causa_salida){
        
        ini_set("pcre.backtrack_limit", "50000000");
        
        $datos =   $this->getSalidapersonal($anio,$mesini,$mesfin,$causa_salida);

        if (count($datos) > 0) :
             
            return   $this->render('informesalidapersonalxls', ['datos'=> $datos,'anio'=>$anio,'mesini'=>$mesini,'mesfin'=>$mesfin]);

        endif;
    }
    
    public function actionCumpleanios(){
        
        
        $datos = [];
        $meses        =  Yii::$app->params['meses'];
        $mes          =  date('n');
        
        if (Yii::$app->request->post()){
            
               $mes = $_POST['mes'];
               
               $datos = $this->getCumpleaños($mes);
            
        }
        return $this->render('infocumpleanios', ['datos'=> $datos, 'meses'=> $meses, 'mes'=> $mes]);
        
    }

    public function actionInfoempleados(){
         
        $datos      = [];
        $tipo       =  "";
            
        if (Yii::$app->request->post()){
            
            $tipo = $_POST['tipo'];
               
            if($tipo == 'E'):

                $datos = $this->getEstadoCivilesEmpleados();

            else:

                $datos = $this->getCargasEmpleados();

            endif;
            
        }
        return $this->render('infoempleados', ['datos'=> $datos, 'tipo'=> $tipo]);
        
    }

    public function actionInfoestadocivilpdf(){
        
        $datos =  $this->getEstadoCivilesEmpleados();
        
        $html =  $this->renderPartial('infoestadocivilpdf',['datos'=> $datos]);
        
       /* $mpdf = new Mpdf([
            'format' => 'A4',
           // 'orientation' => 'L'
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('Listada_cumpleanios.pdf', 'I');
        exit;
        */
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            
            // your html content input
            'content' => $html,
            'marginTop' => 20,
            'marginBottom' => 19,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px} .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 { border:0; padding:0;margin-left:-0.00001; } th, td { padding:5px; height:40px;}',
            
            // set mPDF properties on the fly
            //'options' => ['title' => 'Solicitud de Vacaciones'],
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' =>  'Informe Estado Civil',
                'SetHeader'=>['Sistema Gestión de Nómina - Informe Estado Civil||'],
                'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}']
            ]
        ]);
        // return the pdf output as per the destination setting
        return $pdf->render();
        
    }

    public function actionInfoestadocivilxls(){
        
      
        header("Pragma: public");
        header("Expires: 0");
        $filename = "INFORME DE ESTADO CIVIL EMPLEADOS..xls";
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=$filename");
        
        $datos =   $this->getEstadoCivilesEmpleados();
             
        return   $this->renderAjax('_tableinfoestadocivil', ['datos'=> $datos]);
    }

    public function actionInfonumerocargaspdf(){
        
        $datos =  $this->getCargasEmpleados();
        
        $html =  $this->renderPartial('infonumerocargaspdf',['datos'=> $datos]);
        
       /* $mpdf = new Mpdf([
            'format' => 'A4',
           // 'orientation' => 'L'
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('Listada_cumpleanios.pdf', 'I');
        exit;
        */
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            
            // your html content input
            'content' => $html,
            'marginTop' => 20,
            'marginBottom' => 19,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px} .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 { border:0; padding:0;margin-left:-0.00001; } th, td { padding:5px; height:40px;}',
            
            // set mPDF properties on the fly
            //'options' => ['title' => 'Solicitud de Vacaciones'],
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' =>  'Informe Estado Civil',
                'SetHeader'=>['Sistema Gestión de Nómina - Informe Estado Civil||'],
                'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}']
            ]
        ]);
        // return the pdf output as per the destination setting
        return $pdf->render();
        
    }

    public function actionInfonumerocargasxls(){
        
      
        header("Pragma: public");
        header("Expires: 0");
        $filename = "INFORME DE NUMERO CARGAS EMPLEADOS..xls";
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=$filename");
        
        $datos =   $this->getCargasEmpleados();
             
        return   $this->renderAjax('_tablenumerocargas', ['datos'=> $datos]);
    }

    public function actionLaborados(){
        
        
        $datos = [];
        $meses        =  Yii::$app->params['meses'];
        $mes          =  date('n');
        
        if (Yii::$app->request->post()){
            
               $mes = $_POST['mes'];
               
               $datos = $this->getCumpleañosLaborando($mes);
            
        }
        return $this->render('infoaniotrabajos', ['datos'=> $datos, 'meses'=> $meses, 'mes'=> $mes]);
        
    }
    
    
    public function actionCumpleaniospdf($mes){
        
        $datos =  $this->getCumpleaños($mes);
        
        $html =  $this->renderPartial('infocumpleaniospdf',['mes'=> $mes, 'datos'=> $datos]);
        
       /* $mpdf = new Mpdf([
            'format' => 'A4',
           // 'orientation' => 'L'
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('Listada_cumpleanios.pdf', 'I');
        exit;
        */
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            
            // your html content input
            'content' => $html,
            'marginTop' => 20,
            'marginBottom' => 19,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px} .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 { border:0; padding:0;margin-left:-0.00001; } th, td { padding:5px; height:40px;}',
            
            // set mPDF properties on the fly
            //'options' => ['title' => 'Solicitud de Vacaciones'],
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' =>  'Listado de Cumpleaños',
                'SetHeader'=>['Sistema Gestión de Nómina - Lista de cumpleaños||'],
                'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}']
            ]
        ]);
        // return the pdf output as per the destination setting
        return $pdf->render();
        
    }

    public function actionLaboradospdf($mes){
        
        $datos =  $this->getCumpleañosLaborando($mes);
        
        $html =  $this->renderPartial('infoaniotrabajospdf',['mes'=> $mes, 'datos'=> $datos]);
        
       /* $mpdf = new Mpdf([
            'format' => 'A4',
           // 'orientation' => 'L'
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('Listada_cumpleanios.pdf', 'I');
        exit;
        */
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            
            // your html content input
            'content' => $html,
            'marginTop' => 20,
            'marginBottom' => 19,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px} .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 { border:0; padding:0;margin-left:-0.00001; } th, td { padding:5px; height:40px;}',
            
            // set mPDF properties on the fly
            //'options' => ['title' => 'Solicitud de Vacaciones'],
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' =>  'Listado de Cumpleaños',
                'SetHeader'=>['Sistema Gestión de Nómina - Lista de cumpleaños empresarial||'],
                'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}']
            ]
        ]);
        // return the pdf output as per the destination setting
        return $pdf->render();
        
    }
   
    public function actionLaboradosxlsx($mes){
        
      
        header("Pragma: public");
        header("Expires: 0");
        $filename = "LISTADO_CUMPLEAÑOS_EMPRESARIAL..xls";
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=$filename");
        
        $datos =   $this->getCumpleañosLaborando($mes);
             
        return   $this->renderAjax('_tableaniotrabajos', ['datos'=> $datos]);
    }

    public function actionCumpleaniosxlsx($mes){
        
      
        header("Pragma: public");
        header("Expires: 0");
        $filename = "LISTADO_CUMPLEAÑOS..xls";
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=$filename");
        
        $datos =   $this->getCumpleaños($mes);
             
        return   $this->renderAjax('_tablecumpleanios', ['datos'=> $datos]);
    }
    
    public function actionEmpleadosactivos(){
        
        
        $datos = $this->getPersonalActivo();
        
        return $this->render('infopersonalactivo', ['datos'=> $datos]);
        
        
        
    }

    /*public function actionEmpleadosactivosmesanio(){
        
        
        $datos = $this->getPersonalActivoMesAnio();
        
        return $this->render('infopersonalactivomesanio', ['datos'=> $datos]);
        
        
        
    }*/
    
    public function actionEmpleadosactivospdf(){
        
        $datos = $this->getPersonalActivo();
        
        $html =  $this->renderAjax('infopersonalactivopdf',['datos'=> $datos]);
        
        $mpdf = new Mpdf([
            'format' => 'A4',
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('Personal_Activo.pdf', 'I');
        
        exit();
        
        
    }

    public function actionEmpleadosactivoscredencial(){
        
        $datos = $this->getDatosCredencial();
        
        $html =  $this->renderAjax('infopersonalactivocredencial',['datos'=> $datos]);
        
        $mpdf = new Mpdf([
            'format' => 'A4',
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('Datos_Credencial.pdf', 'I');
        
        exit();
        
        
    }

    /*public function actionEmpleadosactivosmesaniopdf(){
        
        $datos = $this->getPersonalActivo();
        
        $html =  $this->renderAjax('infopersonalactivomesaniopdf',['datos'=> $datos]);
        
        $mpdf = new Mpdf([
            'format' => 'A4',
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('Personal_Activo.pdf', 'I');
        
        exit();
        
        
    }*/
    
    private function getSalidapersonal($anio, $mesini, $mesfin, $id_sys_rrhh_causa_salida){
        
        $db    = $_SESSION['db'];
        
        if ($id_sys_rrhh_causa_salida != null or $id_sys_rrhh_causa_salida != ""):  
        
           return   Yii::$app->$db->createCommand("exec dbo.[ListadoSalidaPersonal] @anio = {$anio}, @mesini= {$mesini}, @mesfin = {$mesfin}, @id_sys_rrhh_causa_salida = '{$id_sys_rrhh_causa_salida}'")->queryAll();
        
        else:
        
           return   Yii::$app->$db->createCommand("exec dbo.[ListadoSalidaPersonal] @anio = {$anio}, @mesini= {$mesini}, @mesfin = {$mesfin}")->queryAll();

        endif;
    }

    private function getDatosAjusteSalarial($anio){
        
        $db    = $_SESSION['db'];
        
        return   Yii::$app->$db->createCommand("[dbo].[ObtenerDatosSalariosAnio] @anio = {$anio}")->queryAll();
        
      
    }

    /*private function getPersonalActivoMesAnio(){
        
        $db    = $_SESSION['db'];

        return   Yii::$app->$db->createCommand("exec [dbo].[ObtenerEmpleadosActivosMesAnio]")->queryAll();
    }*/

   
    private function getDatosCredencial(){
        
        $db    = $_SESSION['db'];

        return Yii::$app->$db->createCommand("SELECT * FROM CSF_Vista_Empleados_Credenciales")->queryAll();
           
    }

    private function getPersonalActivo(){
        
        
        return (new \yii\db\Query())->select(
            [
                "departamento",
                "area.area",
                "emp.id_sys_rrhh_cedula", 
                "emp.nombres",
                "emp.email",
                "cargo.cargo",
                "genero",
                "id_sys_adm_ccosto",
                "(CASE emp.formacion_academica  WHEN 'P' then 'Primaria' WHEN 'S' then 'Secundaria' WHEN 'T' then 'Tercer Nivel' WHEN 'C' then 'Cuarto Nivel'  ELSE 's/d' END ) as formacion_academica",
                "isnull(emp.titulo_academico, 'NINGUNO') as titulo_academico",
                "fecha_nacimiento",
                "(cast(datediff(dd,fecha_nacimiento,GETDATE()) / 365.25 as int)) as edad",
                 "(CASE emp.estado_civil  WHEN 'C' then 'Casado/a' WHEN 'S' then 'Soltero/a' WHEN 'D' then 'Divorsiado/a' WHEN 'V' then 'Viudo/a'   WHEN 'U' then 'Unido/a'  ELSE 's/d' END ) as estado_civil",
                "telefono",
                "celular",
                "cta_banco",
                "direccion",
                "parroquia",
                "canton",
                "provincia",
                "(select top 1 fecha_ingreso from sys_rrhh_empleados_contratos contratos where contratos.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula order by contratos.fecha_ingreso desc) as fecha_ingreso",
                "isnull((select top 1 fecha_salida from sys_rrhh_empleados_contratos contratos where contratos.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula order by contratos.fecha_ingreso desc), '') as fecha_salida",
                "sueldo",
                "contrato"
            ])
            ->from("sys_rrhh_empleados emp")
            ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
            ->innerJoin("sys_adm_departamentos as departamento","cargo.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->leftJoin("sys_parroquias parroquia","emp.id_sys_parroquia = parroquia.id_sys_parroquia")
            ->leftJoin("sys_cantones canton","parroquia.id_sys_canton = canton.id_sys_canton")
            ->leftJoin("sys_provincias provincia","canton.id_sys_provincia = provincia.id_sys_provincia")
            ->innerJoin("sys_rrhh_empleados_sueldos sueldos","emp.id_sys_rrhh_cedula = sueldos.id_sys_rrhh_cedula")
            ->innerJoin("sys_rrhh_contratos contrato","emp.id_sys_rrhh_contrato = contrato.id_sys_rrhh_contrato")
            ->where("emp.id_sys_empresa = '001'")
            ->andWhere("emp.estado = 'A'")
            ->andWhere("sueldos.estado = 'A'")
            ->orderBy("area, departamento, nombres")
            ->all(SysRrhhEmpleadosNovedades::getDb());
           
    }
    
    private function getSueldosporpagar($empresa, $anio, $mes, $periodo, $tipo){
        
       $datos = [];
       
       $db    = $_SESSION['db'];
      
       if($tipo == 'C'):
       
          
          if ($periodo >= 72): 
          
             if($periodo == 72):

                $datos  = Yii::$app->$db->createCommand("EXEC dbo.[PagoUtilidadesEmpleadosActivos] @anio = '{$anio}', @tipo= '{$tipo}'")->queryAll();
             
             endif;
             
          else:
          
            $datos  = Yii::$app->$db->createCommand("EXEC dbo.sys_rrhh_pago_cheques @empresa = '{$empresa}', @anio= '{$anio}', @mes = '{$mes}', @periodo = '{$periodo}'")->queryAll();
          
          endif;
       
       elseif($tipo == 'A'):
       
           if($periodo >= 72):
           
             if($periodo == 72):
                     $datos  = Yii::$app->$db->createCommand("EXEC dbo.[PagoUtilidadesEmpleadosActivos] @anio = '{$anio}', @tipo= '{$tipo}'")->queryAll();
             endif;
             
           else:
          
           $datos  = Yii::$app->$db->createCommand("EXEC dbo.sys_rrhh_pago_cuenta_ahorros @empresa = '{$empresa}', @anio= '{$anio}', @mes = '{$mes}', @periodo = '{$periodo}'")->queryAll();

           endif;
       
           elseif($tipo == 'F'):
       
            if($periodo >= 72):
            
              if($periodo == 72):
                      $datos  = Yii::$app->$db->createCommand("EXEC dbo.[PagoUtilidadesEmpleadosActivos] @anio = '{$anio}', @tipo= '{$tipo}'")->queryAll();
              endif;
              
            else:
           
            $datos  = Yii::$app->$db->createCommand("EXEC dbo.sys_rrhh_pago_finiquito @empresa = '{$empresa}', @anio= '{$anio}', @mes = '{$mes}', @periodo = '{$periodo}'")->queryAll();
 
            endif;
        
        
        endif;
       
       return $datos;
        
        
        
        
    }
    
    public function actionRolcorreo(){
        
        
        if (Yii::$app->request->isAjax && Yii::$app->request->post()) {
            
                   $obj         =  json_decode(Yii::$app->request->post('datos'));
                   $empleados   =  $obj->{'empleados'};
                   $error       =  [];  
        
                   $meses      =  Yii::$app->params['meses'];
                   
                   $db         = $_SESSION['db'];
                           
                   $empresa    = SysEmpresa::find()->where(['db_name'=> $db])->one();
                   
                   foreach ($empleados as $data):
                   
                               $html = '';
                   
                               $datos =  (new \yii\db\Query())->select(
                                [
                                  "empleados.id_sys_rrhh_cedula",
                                  "empleados.nombres",
                                  "(select top(1) fecha_ingreso from sys_rrhh_empleados_contratos con where con.id_sys_rrhh_cedula = empleados.id_sys_rrhh_cedula order by con.id_sys_rrhh_empleados_contrato_cod desc) as fecha_ingreso",
                                  "cargo.cargo", 
                                  "empleados.id_sys_adm_cargo",
                                  "(select cantidad  from  sys_rrhh_empleados_rol_mov where anio =  rol_mov.anio and mes = rol_mov.mes and id_sys_rrhh_cedula = rol_mov.id_sys_rrhh_cedula  and id_sys_rrhh_concepto = 'SUELDO') as cantidad",
                                  "(case empleados.id_sys_rrhh_forma_pago when 'T' then 'Tarjeta Virtual' when 'R' then 'Cta.Corriente'  when 'A' then 'Cta.Ahorros'   when 'C' then 'Cheque'  else 'Efectivo' end ) as forma_pago",
                                  "cta_banco",
                                  "banco",
                                  "empleados.email",
                                  "rol_mov.id_sys_empresa"
                                ])
                                ->from("sys_rrhh_empleados_rol_mov as  rol_mov")
                                ->join("INNER JOIN", "sys_rrhh_empleados as empleados","empleados.id_sys_rrhh_cedula=rol_mov.id_sys_rrhh_cedula")->andwhere("empleados.id_sys_empresa=rol_mov.id_sys_empresa")
                              //  ->join("INNER JOIN", "sys_rrhh_empleados_contratos as contratos","contratos.id_sys_rrhh_cedula=empleados.id_sys_rrhh_cedula")->andwhere("contratos.id_sys_empresa=empleados.id_sys_empresa")
                                ->join("INNER JOIN", "sys_adm_cargos as cargo","empleados.id_sys_adm_cargo = cargo.id_sys_adm_cargo")->andwhere("cargo.id_sys_empresa = empleados.id_sys_empresa")
                                ->join("INNER JOIN", "sys_rrhh_bancos as banco","banco.id_sys_rrhh_banco=empleados.id_sys_rrhh_banco")->andwhere("banco.id_sys_empresa=empleados.id_sys_empresa")
                          
                                ->Where("rol_mov.anio = '{$obj->{'anio'}}'")
                                ->andwhere("rol_mov.mes= '{$obj->{'mes'}}'")
                                ->andwhere("rol_mov.periodo = '{$obj->{'periodo'}}'")
                                ->andwhere("rol_mov.id_sys_empresa= '001'")
                                ->andwhere("empleados.id_sys_rrhh_cedula = '{$data->id_sys_rrhh_cedula}'")
                              //  ->andwhere("fecha_salida is null")
                                ->one(SysRrhhEmpleados::getDb());
                            
                                  $nombres =  $datos['nombres'];
                                  $cedula  =  $datos['id_sys_rrhh_cedula'];
                                  
                      
                                  if($datos['email'] != ''):
                                  
                                  
                                              $variosemails = strpos(trim($datos['email']), ';');
                                              
                                              if($variosemails !== false):
                                              
                                                 $email   = explode(';', trim($datos['email']));
                                    
                                              
                                              else:
                                              
                                                 $email = [trim($datos['email'])];
                                              
                                              endif;
                                              
                                      
                                      
                                              $html = $this->renderAjax('_rolpago2',  ['datos'=> $datos, 'anio'=> $obj->{'anio'}, 'mes'=> $obj->{'mes'}, 'periodo'=>$obj->{'periodo'}]);
                                        
                                              $mpdf = new Mpdf([
                                                  'format'  => [210, 160]
                                              ]);
                                              $mpdf->WriteHTML($html);
                                              $nombrepdf = trim("ROLPAGOCC".$cedula.".pdf");
                                              $mpdf->Output('pdf/'.$nombrepdf, 'F');
                                              
                                            
                                              if($datos):
                                              
                                                      try {
                                                      
                                              
                                                         Yii::$app->mailer->setTransport([
                                                              
                                                              'class' => 'Swift_SmtpTransport',
                                                              'host' => trim($empresa->mail_host),
                                                              'username' => trim($empresa->mail_username),
                                                              'password' => trim($empresa->mail_password),
                                                              'port' => trim($empresa->mail_port),
                                                              'encryption' => 'tls',
                                                              'streamOptions' => [
                                                                  'ssl' => [
                                                                      'allow_self_signed' => true,
                                                                      'verify_peer' => false,
                                                                      'verify_peer_name' => false,
                                                                  ],
                                                              ],
                                                          ]);
                                                          
                                                          
                                                           Yii::$app->mailer->compose()
                                                          ->setTo($email)
                                                          ->setFrom([$empresa->mail_username => $empresa->razon_social])
                                                          ->setSubject('Rol de Pago - Gestión')
                                                          ->setHtmlBody("Estimado(a) <b>".utf8_encode($nombres) ."</b>,<br><p>Adjuntamos el rol correspondiente al mes de ".$meses[$obj->{'mes'}]." del ".$obj->{'anio'}."</p>")
                                                          ->attach('pdf/'.$nombrepdf)
                                                          ->send();
                                                         
                                                          /*fin de correo*/
                                                          unlink('pdf/'.$nombrepdf);
            
                                                        } catch (\Exception $e) {
                                                            
                                                            unlink('pdf/'.$nombrepdf);
                                                            
                                                            $error [] = array('Identificacion' => $data->id_sys_rrhh_cedula, 'email'=> $email, 'mensaje' => $e->getMessage());
                                                            continue;
                                                      }
                                                     
                                                    
                                                    
                                              endif;
                                 else:
                                       $error [] = array('Identificacion' => $data->id_sys_rrhh_cedula, 'email'=> '' );
                                 endif;
                              
                            
                        
                   endforeach;
                   
                   if(count($error) > 0) :
                   
                         return json_encode(['data'=> ['estado'=>  false , 'mjs'=> 'No se puedo enviar correo a las siguientes personas'.json_encode($error)]]);
                   
                   endif;
            
                   return  json_encode(['data'=> ['estado'=>  true , 'mjs'=> 'Exito']]);
                   
            }   
      }
      
    private function getRoles($anio, $mes, $periodo, $area, $departamento, $concepto){
          
          
   
          
          
          if($mes == 10 && $anio == 2019):
          
                   //revisa el departmamento por el cargo 
                  return  (new \yii\db\Query())->select(
                      [
                          "area.id_sys_adm_area",
                          "area.area",
                          "departamento.id_sys_adm_departamento",
                          "departamento.departamento",
                          "emp.nombres",
                          "emp.id_sys_rrhh_cedula",
                          "rol_mov.cantidad",
                          "emp.email",
                          "(case emp.id_sys_rrhh_forma_pago when 'T' then 'Tar.Vir' when 'R' then 'Cta.Cor'  when 'A' then 'Cta.Aho'   when 'C' then 'Che'  else 'Efectivo' end ) as forma_pago",
                          "emp.id_sys_adm_ccosto",
                          "cargo.cargo",
                          "(select top 1 fecha_ingreso from sys_rrhh_empleados_contratos contratos where contratos.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula order by contratos.fecha_ingreso desc) as fecha_ingreso",
                          "(select top 1 fecha_salida from sys_rrhh_empleados_contratos contratos where contratos.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula order by contratos.fecha_ingreso desc) as fecha_salida"
                      ])
                      ->from("sys_rrhh_empleados_rol_mov as rol_mov")
                      ->innerJoin('sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
                      ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
                      ->innerJoin("sys_adm_departamentos as departamento","cargo.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
                      ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
                      ->innerJoin("sys_rrhh_contratos contrato","emp.id_sys_rrhh_contrato = contrato.id_sys_rrhh_contrato")
                      ->where("rol_mov.anio = '{$anio}'")
                      ->andwhere("rol_mov.mes=  '{$mes}'")
                      ->andwhere("rol_mov.periodo=  {$periodo}")
                      ->andwhere("rol_mov.id_sys_empresa= '001'")
                      ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
                      ->andfilterWhere(['like', "area.id_sys_adm_area",$area])
                      ->andwhere("id_sys_rrhh_concepto = '$concepto'")
                      ->orderBy("nombres")
                      ->all(SysRrhhEmpleadosNovedades::getDb());
                  
                 
          
          else:
          
                  //revisa el departamento por el cual fue liquidado 

                    if($area == '' && $departamento == ''){
                  
                        return  (new \yii\db\Query())->select(
                        [
                            "area.id_sys_adm_area",
                            "area.area",
                            "departamento.id_sys_adm_departamento",
                            "departamento.departamento",
                            "emp.nombres",
                            "emp.id_sys_rrhh_cedula",
                            "rol_mov.cantidad",
                            "emp.email",
                            "(case emp.id_sys_rrhh_forma_pago when 'T' then 'Tar.Vir' when 'R' then 'Cta.Cor'  when 'A' then 'Cta.Aho'   when 'C' then 'Che'  else 'Efectivo' end ) as forma_pago",
                            "emp.id_sys_adm_ccosto",
                            "cargo.cargo",
                            "(select top 1 fecha_ingreso from sys_rrhh_empleados_contratos contratos where contratos.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula order by contratos.fecha_ingreso desc) as fecha_ingreso",
                            "(select top 1 fecha_salida from sys_rrhh_empleados_contratos contratos where contratos.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula order by contratos.fecha_ingreso desc) as fecha_salida"
                        ])
                        ->from("sys_rrhh_empleados_rol_mov as rol_mov")
                        ->innerJoin('sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
                        ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
                        ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
                        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
                        ->innerJoin("sys_rrhh_contratos contrato","emp.id_sys_rrhh_contrato = contrato.id_sys_rrhh_contrato")
                        ->where("rol_mov.anio = '{$anio}'")
                        ->andwhere("rol_mov.mes=  '{$mes}'")
                        ->andwhere("rol_mov.periodo=  {$periodo}")
                        ->andwhere("rol_mov.id_sys_empresa= '001'")
                        ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
                        ->andfilterWhere(['like', "area.id_sys_adm_area",$area])
                        ->andwhere("id_sys_rrhh_concepto = '$concepto'")
                        ->orderBy("nombres")
                        ->all(SysRrhhEmpleadosNovedades::getDb());
                    }elseif($area != '' && $departamento != ''){
                        return  (new \yii\db\Query())->select(
                            [
                                "area.id_sys_adm_area",
                                "area.area",
                                "departamento.id_sys_adm_departamento",
                                "departamento.departamento",
                                "emp.nombres",
                                "emp.id_sys_rrhh_cedula",
                                "rol_mov.cantidad",
                                "emp.email",
                                "(case emp.id_sys_rrhh_forma_pago when 'T' then 'Tar.Vir' when 'R' then 'Cta.Cor'  when 'A' then 'Cta.Aho'   when 'C' then 'Che'  else 'Efectivo' end ) as forma_pago",
                                "emp.id_sys_adm_ccosto",
                                "cargo.cargo",
                                "(select top 1 fecha_ingreso from sys_rrhh_empleados_contratos contratos where contratos.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula order by contratos.fecha_ingreso desc) as fecha_ingreso",
                                "(select top 1 fecha_salida from sys_rrhh_empleados_contratos contratos where contratos.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula order by contratos.fecha_ingreso desc) as fecha_salida"
                            ])
                            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
                            ->innerJoin('sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
                            ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
                            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
                            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
                            ->innerJoin("sys_rrhh_contratos contrato","emp.id_sys_rrhh_contrato = contrato.id_sys_rrhh_contrato")
                            ->where("rol_mov.anio = '{$anio}'")
                            ->andwhere("rol_mov.mes=  '{$mes}'")
                            ->andwhere("rol_mov.periodo=  {$periodo}")
                            ->andwhere("rol_mov.id_sys_empresa= '001'")
                            ->andWhere("departamento.id_sys_adm_departamento = $departamento")
                            ->andWhere("area.id_sys_adm_area = $area")
                            ->andwhere("id_sys_rrhh_concepto = '$concepto'")
                            ->orderBy("nombres")
                            ->all(SysRrhhEmpleadosNovedades::getDb());
                    }elseif($area != ''){
                        return  (new \yii\db\Query())->select(
                            [
                                "area.id_sys_adm_area",
                                "area.area",
                                "departamento.id_sys_adm_departamento",
                                "departamento.departamento",
                                "emp.nombres",
                                "emp.id_sys_rrhh_cedula",
                                "rol_mov.cantidad",
                                "emp.email",
                                "(case emp.id_sys_rrhh_forma_pago when 'T' then 'Tar.Vir' when 'R' then 'Cta.Cor'  when 'A' then 'Cta.Aho'   when 'C' then 'Che'  else 'Efectivo' end ) as forma_pago",
                                "emp.id_sys_adm_ccosto",
                                "cargo.cargo",
                                "(select top 1 fecha_ingreso from sys_rrhh_empleados_contratos contratos where contratos.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula order by contratos.fecha_ingreso desc) as fecha_ingreso",
                                "(select top 1 fecha_salida from sys_rrhh_empleados_contratos contratos where contratos.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula order by contratos.fecha_ingreso desc) as fecha_salida"
                            ])
                            ->from("sys_rrhh_empleados_rol_mov as rol_mov")
                            ->innerJoin('sys_rrhh_empleados as emp','rol_mov.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula')
                            ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
                            ->innerJoin("sys_adm_departamentos as departamento","rol_mov.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
                            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
                            ->innerJoin("sys_rrhh_contratos contrato","emp.id_sys_rrhh_contrato = contrato.id_sys_rrhh_contrato")
                            ->where("rol_mov.anio = '{$anio}'")
                            ->andwhere("rol_mov.mes=  '{$mes}'")
                            ->andwhere("rol_mov.periodo=  {$periodo}")
                            ->andwhere("rol_mov.id_sys_empresa= '001'")
                            ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
                            ->andwhere("area.id_sys_adm_area = $area")
                            ->andwhere("id_sys_rrhh_concepto = '$concepto'")
                            ->orderBy("nombres")
                            ->all(SysRrhhEmpleadosNovedades::getDb());
                    }
          endif;
            
      }
      
    private  function getRolIndividual($anio, $mes, $periodo, $area, $departamento, $concepto, $cedula){
          
          
          
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
      
    private function getConcepto ($periodo){
          
          $concepto = '';
          
 
          switch ($periodo) {
              
              case 1:
                  
                  $concepto = "ANTICIPO";
                  break;
                  
              case 2:
                  
                  $concepto = "SUELDO";
                  break;
                  
              case 90:
                  
                  $concepto = "VACACIONES";
                  break;
                  
              case 70:
                  
                  $concepto = "PAGO_DECIMO_TER";
                  break;
                  
              case 71:
                  
                  $concepto = "PAGO_DECIMO_CUA";
                  break;
                  
          }
          
        
          return $concepto;
          
      }
     
    private function getEstadoCivilesEmpleados(){
          
        $db = $_SESSION['db'];

        $anio = date('Y');

        return Yii::$app->$db->createCommand("EXEC [ObtenerEstadoCivilTotalXGenero] {$anio}")->queryAll();
        
    }

    private function getCargasEmpleados(){
          
        $db = $_SESSION['db'];

        $anio = date('Y');

        return Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerHijosEmpleados] {$anio}")->queryAll();
        
    }

    private function getCumpleaños($mes){
          
          
          return (new \yii\db\Query())->select(
              [
                  "departamento",
                  "area.area",
                  "emp.id_sys_rrhh_cedula",
                  "emp.nombres",
                  "fecha_nacimiento"
                  
              ])
              ->from("sys_rrhh_empleados emp")
              ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")->andWhere("emp.id_sys_empresa=cargo.id_sys_empresa")
              ->innerJoin("sys_adm_departamentos as departamento","cargo.id_sys_adm_departamento = departamento.id_sys_adm_departamento")->andWhere("emp.id_sys_empresa=departamento.id_sys_empresa")
              ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
              ->where("emp.id_sys_empresa = '001'")
              ->andWhere("emp.estado = 'A'")
              ->andWhere("month(fecha_nacimiento) = '{$mes}'")
              ->orderBy('day(fecha_nacimiento)')
              ->all(SysRrhhEmpleadosNovedades::getDb());
    }

    private function getCumpleañosLaborando($mes){
          
        $db = $_SESSION['db'];

        return Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerCumpleañosLaborando] @mes = '$mes'")->queryAll();
        
    }
      
}
