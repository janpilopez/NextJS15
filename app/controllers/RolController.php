<?php

namespace app\controllers;

use app\models\SysAdmCanastaBasica;
use app\models\SysRrhhEmpleadosNucleoFamiliar;
use Yii;
use app\models\SysRrhhEmpleadosRolCab;
use app\models\SysRrhhEmpleadosRolCabSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\SysEmpresa;
use app\models\SysRrhhCuadrillasJornadasMov;
use app\models\SysRrhhEmpleadosMarcacionesReloj;
use app\models\SysRrhhEmpleadosRolMov;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosSueldos;
use app\models\SysRrhhEmpleadosContratos;
use app\models\SysAdmCargos;
use app\models\SysRrhhFeriados;
use app\models\SysConfiguracion;
use app\models\SysRrhhEmpleadosNovedades;
use app\models\SysRrhhEmpleadosHaberes;
use app\models\SysRrhhPrestamosDet;
use app\models\SysRrhhPrestamosCab;
use Mpdf\Mpdf;
use app\models\SysRrhhConceptos;
use app\models\SysRrhhEmpleadosRolLiq;
use app\models\SysRrhhMareasCab;
use app\models\SysRrhhMareasDet;
use app\models\sysAdmMandos;
use app\models\SysRrhhContratos;
use app\models\SysRrhhUtilidadesDet;
use Codeception\Command\Console;

/**
 * RolController implements the CRUD actions for SysRrhhEmpleadosRolCab model.
 */
class RolController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'ghost-access'=> [
                'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
            ],
        ];
    }

    
    
    /**
     * Lists all SysRrhhEmpleadosRolCab models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysRrhhEmpleadosRolCabSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhEmpleadosRolCab model.
     * @param string $anio
     * @param string $mes
     * @param string $periodo
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($anio, $mes, $periodo, $id_sys_empresa)
    {
        return $this->render('view', [
            'model' => $this->findModel($anio, $mes, $periodo, $id_sys_empresa),
        ]);
    }

    public function actionAsistencia2(){
        
        
        $dataliquidacion = $this->CalcularAsistencia( '2022-10-23', '2022-11-22', '1314893411', '001', 433.16);
        
        echo json_encode($dataliquidacion);
        
    }
    
    
    /**
     * Creates a new SysRrhhEmpleadosRolCab model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model             = new SysRrhhEmpleadosRolCab();
        $mes               = date('n');
        $anio              = date('Y');
        $periodo           = '';
       
         $periodos  = ['1'=> 'Quincena', '2'=> 'Mensual', '90'=> 'Proviciones', '70'=> 'Decimo Tercero',  '71'=> 'Decimo Cuarto'];
        
        $db =  $_SESSION['db'];
         
        $transaction  = \Yii::$app->$db->beginTransaction();
   
        if ($model->load(Yii::$app->request->post())) {
            
            $model->id_sys_empresa      = '001';
            $model->transaccion_usuario = Yii::$app->user->username;
            $model->estado              = 'Q';
            $model->fecha_creacion      = date('Ymd H:i:s');
            
            $rol =  SysRrhhEmpleadosRolCab::find()->where(['anio' => $model->anio])->andWhere(['mes'=> $model->mes])->andWhere(['periodo'=> $model->periodo])->one();
            
            if(!$rol):
                
                    if($model->save(false)):
                       
                                if($model->periodo == '2'):
                            
                                     $proviciones =  new SysRrhhEmpleadosRolCab();
                                
                                     $proviciones->anio = $model->anio;
                                     $proviciones->mes  = $model->mes;
                                     $proviciones->periodo =  '90';
                                     $proviciones->fecha_registro = $model->fecha_registro;
                                     $proviciones->estado  =  'Q';
                                     $proviciones->id_sys_empresa = $model->id_sys_empresa;
                                     $proviciones->transaccion_usuario = Yii::$app->user->username;
                                     $proviciones->fecha_ini_liq  = $model->fecha_ini_liq;
                                     $proviciones->fecha_fin_liq = $model->fecha_fin_liq;
                                     $proviciones->fecha_ini   = $model->fecha_ini;
                                     $proviciones->fecha_fin = $model->fecha_fin;
                                     $proviciones->fecha_creacion = date('Ymd H:i:s');
                                     $proviciones->save(false);
                                 
                                 endif;
                            
                            
                                     $transaction->commit();
                                    Yii::$app->getSession()->setFlash('info', [
                                    'type' => 'success','duration' => 3000,
                                    'icon' => 'glyphicons glyphicons-robot','message' => 'El periodo se ha generado con exito!',
                                    'positonY' => 'top','positonX' => 'right']);
                                    return $this->redirect('index');
            
                        
                    else:
                    
                             $transaction->rollBack();
                             Yii::$app->getSession()->setFlash('info', [
                            'type' => 'warning','duration' => 3000,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error al intentar guardar el periodo!',
                            'positonY' => 'top','positonX' => 'right']);
                             return $this->redirect('index');
                    endif;
             else:
             
                     Yii::$app->getSession()->setFlash('info', [
                     'type' => 'warning','duration' => 3000,
                     'icon' => 'glyphicons glyphicons-robot','message' => 'El periodo ya ha sido registrado!',
                     'positonY' => 'top','positonX' => 'right']);
                     
                     
                     return $this->render('create', [
                         'model' => $model,
                         'mes'=> $mes,
                         'anio'=> $anio,
                         'periodos'=> $periodos,
                         'periodo'=> $periodo
                         
                     ]);
                 
             endif;
          
        }

        return $this->render('create', [
            'model' => $model,
            'mes'=> $mes,
            'anio'=> $anio,
            'periodos'=> $periodos,
            'periodo'=> $periodo
            
        ]);
    }

    /**
     * Updates an existing SysRrhhEmpleadosRolCab model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $anio
     * @param string $mes
     * @param string $periodo
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($anio, $mes, $periodo, $id_sys_empresa)
    {
        
        $model    = $this->findModel($anio, $mes, $periodo, $id_sys_empresa);
  
        $periodos =  Yii::$app->params['periodos'];
        
        if ($model->load(Yii::$app->request->post())) {
            
            if($model->estado != 'P'):
            
                if($model->save(false)):
                
                    Yii::$app->getSession()->setFlash('info', [
                        'type' => 'succees','duration' => 3000,
                        'icon' => 'glyphicons glyphicons-robot','message' => 'El periodo ha sido actualizado  con éxito',
                        'positonY' => 'top','positonX' => 'right']);
                
                else:
            
                     Yii::$app->getSession()->setFlash('info', [
                        'type' => 'warning','duration' => 3000,
                        'icon' => 'glyphicons glyphicons-robot','message' => 'El periodo ha sido actualizado  con éxito',
                        'positonY' => 'top','positonX' => 'right']);
                    
                
                
                endif;
 
            else:
            
                    Yii::$app->getSession()->setFlash('info', [
                    'type' => 'warning','duration' => 3000,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'El periodo no se puede actualizar, porque el periodo se encuentra procesado!!',
                    'positonY' => 'top','positonX' => 'right']);
            
            
            
            endif;
                  
            
            return $this->redirect('index');
        }

        return $this->render('update', [
            'model' => $model,
            'mes'=> $model->mes,
            'anio'=> $model->anio,
            'periodo'=> $model->periodo,
            'periodos'=> $periodos,
        ]);
    }

    /**
     * Deletes an existing SysRrhhEmpleadosRolCab model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $anio
     * @param string $mes
     * @param string $periodo
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($anio, $mes, $periodo, $id_sys_empresa)
    {
       
          $model =   $this->findModel($anio, $mes, $periodo, $id_sys_empresa);
         
          if($model->estado == 'Q'):
          
                  $roldetalle  = SysRrhhEmpleadosRolMov::find()->where(['anio'=> $model->anio])->andWhere(['mes'=> $model->mes])->andWhere(['periodo'=> $model->periodo])->andWhere(['id_sys_empresa'=> $model->id_sys_empresa])->andWhere(['estado'=> 'P'])->one();
          
                  
                  if(!$roldetalle):
                  
                         $model->delete();
              
                          Yii::$app->getSession()->setFlash('info', [
                              'type' => 'succees','duration' => 3000,
                              'icon' => 'glyphicons glyphicons-robot','message' => 'El periodo ha sido eliminado con éxito',
                              'positonY' => 'top','positonX' => 'right']);
                   else:
                   
                       Yii::$app->getSession()->setFlash('info', [
                           'type' => 'warning','duration' => 3000,
                           'icon' => 'glyphicons glyphicons-robot','message' => 'No se puede eliminar el periodo, porque existen areas procesadas!',
                           'positonY' => 'top','positonX' => 'right']);
                   
                   endif;
              
           else:
         
                 Yii::$app->getSession()->setFlash('info', [
                   'type' => 'warning','duration' => 3000,
                   'icon' => 'glyphicons glyphicons-robot','message' => 'No se puede eliminar el periodo, porque se encuentra procesado!',
                   'positonY' => 'top','positonX' => 'right']);
           
           
          endif;
          
  

        return $this->redirect(['index']);
    }
    
    public function actionProcesar($anio, $mes, $periodo, $id_sys_empresa){
        
   
        $roldetalle  = SysRrhhEmpleadosRolMov::find()->where(['anio'=> $anio])->andWhere(['mes'=> $mes])->andWhere(['periodo'=> $periodo])->andWhere(['id_sys_empresa'=> $id_sys_empresa])->andWhere(['estado'=> 'Q'])->one();
        
        
        if(!$roldetalle):
       
            $model                       =  $this->findModel($anio, $mes, $periodo, $id_sys_empresa);
        
            if($model->estado != 'P'):
            
                    $model->estado               = 'P';
                    $model->fecha_liquidacion    =  date('Ymd H:i:s');
                    $model->usuario_liquidacion  =  Yii::$app->user->username;
                    if($model->save(false)):
                               
                    
                           if($model->periodo == '2'):
                           
                               $novedades                       =  $this->findModel($anio, $mes, '90', $id_sys_empresa);
                               $novedades->estado               = 'P';
                               $novedades->fecha_liquidacion    =  date('Ymd H:i:s');
                               $novedades->usuario_liquidacion  =  Yii::$app->user->username;
                               $novedades->save(false);
                               
                            endif;
                    
                            Yii::$app->getSession()->setFlash('info', [
                                'type' => 'success','duration' => 3000,
                                'icon' => 'glyphicons glyphicons-robot','message' => 'El periodo ha sido procesado con éxito!!',
                                'positonY' => 'top','positonX' => 'right']);
                            
                               return $this->redirect('index');
                            
                    else:
                   
                            Yii::$app->getSession()->setFlash('info', [
                                'type' => 'warning','duration' => 3000,
                                'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error al procesar el periodo!!',
                                'positonY' => 'top','positonX' => 'right']);
                               
                                 return $this->redirect('index');
                        
                    endif;
            else:
             
                    Yii::$app->getSession()->setFlash('info', [
                        'type' => 'warning','duration' => 3000,
                        'icon' => 'glyphicons glyphicons-robot','message' => 'El periodo actualmente se encuentra liquidado!!',
                        'positonY' => 'top','positonX' => 'right']);
                    
                    return $this->redirect('index');
                    
            endif;
            

        else:
        
           Yii::$app->getSession()->setFlash('info', [
               'type' => 'warning','duration' => 3000,
               'icon' => 'glyphicons glyphicons-robot','message' => 'El periodo no ha sido liquidado en su totalidad.!!',
               'positonY' => 'top','positonX' => 'right']);
            
              return $this->redirect('index');
       
       
       endif;
      
      
     
        
    }
    
    public function actionEnviaremails($anio = null,$mes = null, $periodo = null ){
        
       /* $meses =  Yii::$app->params['meses'];
        
        $roles  = [];
        
        $concepto = $this->getConcepto($periodo);
        
        
        if($anio != null && $mes != null && $concepto != ''):
        
        
            $roles =  (new \yii\db\Query())->select(
                [
                    "empleados.id_sys_rrhh_cedula",
                    "empleados.nombres",
                    "fecha_ingreso",
                    "cargo.cargo",
                    "empleados.id_sys_adm_cargo",
                    "(select cantidad  from  sys_rrhh_empleados_rol_mov where anio =  rol_mov.anio and mes = rol_mov.mes and id_sys_rrhh_cedula = rol_mov.id_sys_rrhh_cedula  and id_sys_rrhh_concepto = 'SUELDO') as cantidad",
                    "( case empleados.id_sys_rrhh_forma_pago when 'T' then 'Tarjeta Virtual' when 'R' then 'Cta.Corriente'  when 'A' then 'Cta.Ahorros'   when 'C' then 'Cheque'  else 'Efectivo' end ) as forma_pago",
                    "cta_banco",
                    "banco",
                    "empleados.email",
                    "rol_mov.id_sys_empresa"
                ])
                ->from("sys_rrhh_empleados_rol_mov as  rol_mov")
                ->join("INNER JOIN", "sys_rrhh_empleados as empleados","empleados.id_sys_rrhh_cedula=rol_mov.id_sys_rrhh_cedula")->andwhere("empleados.id_sys_empresa=rol_mov.id_sys_empresa")
                ->join("INNER JOIN", "sys_rrhh_empleados_contratos as contratos","contratos.id_sys_rrhh_cedula=empleados.id_sys_rrhh_cedula")->andwhere("contratos.id_sys_empresa=empleados.id_sys_empresa")
                ->join("INNER JOIN", "sys_adm_cargos as cargo","empleados.id_sys_adm_cargo = cargo.id_sys_adm_cargo")->andwhere("cargo.id_sys_empresa = empleados.id_sys_empresa")
                ->join("INNER JOIN", "sys_rrhh_bancos as banco","banco.id_sys_rrhh_banco=empleados.id_sys_rrhh_banco")->andwhere("banco.id_sys_empresa=empleados.id_sys_empresa")
                
                ->Where("rol_mov.anio = '{$anio}'")
                ->andwhere("rol_mov.mes= '{$mes}'")
                ->andwhere("rol_mov.periodo = '{$periodo}'")
                ->andwhere("rol_mov.id_sys_empresa= '001'")
                ->andwhere("rol_mov.id_sys_rrhh_concepto = '{$concepto}'")
                ->andwhere("rol_mov.id_sys_adm_departamento = '8'")
                ->andwhere("fecha_salida is null")
                ->all(SysRrhhEmpleados::getDb());
        
                foreach ($roles as $index => $datos):
                
                    $html = '';
                    $nombres =  $datos['nombres'];
                    $cedula  =  $datos['id_sys_rrhh_cedula'];
                    
                    
                    if($datos['email'] != ''):
                            
                            
                            $variosemails = strpos(trim($datos['email']), ';');
                            
                            if($variosemails !== false):
                            
                                $email   = explode(';', trim($datos['email']));
                            
                            
                            else:
                            
                                $email   = [trim($datos['email'])];
                            
                            endif;
                            
                         
                            
                            $html = $this->renderAjax('_rolpago',  ['datos'=> $roles[$index], 'anio'=> $anio, 'mes'=> $mes, 'periodo'=>$periodo]);
                            
                            $mpdf = new Mpdf([
                                'format'  => [210, 160]
                            ]);
                            $mpdf->WriteHTML($html);
                            $nombrepdf = "ROLPAGOCC".$cedula.".pdf";
                            $path      =  $mpdf->Output('', 'S');
                            
                  
                            try {
                                
                                
                                Yii::$app->mailer->compose()
                                ->setTo($email)
                                ->setFrom([Yii::$app->params["adminEmail"] => 'PESPESCA'])
                                ->setSubject('Rol de Pago - Gestión')
                                ->setHtmlBody("Estimado(a) <b>".utf8_encode($nombres) ."</b>,<br><p>Adjuntamos el rol correspondiente al mes de ".$meses[$mes]." del ".$anio."</p>")
                                ->attachContent($path, ['fileName' => $nombrepdf , 'contentType' => 'application/pdf'])
                                ->send();
                    
                                
                            } catch (\Exception $e) {
                              
                                continue;
                            }
                            
                        
                 endif;
                
             endforeach;
             
             Yii::$app->getSession()->setFlash('info', [
                 'type' => 'success','duration' => 3000,
                 'icon' => 'glyphicons glyphicons-robot','message' => 'El Proceso de envio de correos se completo con exito!!',
                 'positonY' => 'top','positonX' => 'right']);
             
             return $this->redirect('index');
             
        endif;
        */
        
    }
    
    public function actionLiquidar($anio, $mes, $periodo, $id_sys_empresa){
        
        $this->layout = '@app/views/layouts/main_emplados';
        
        $empleados  =  SysRrhhEmpleados::find()->where(['estado'=> 'A'])->orderBy('nombres')->all();  
        
        $model =   $this->findModel($anio, $mes, $periodo, $id_sys_empresa);
        
        return $this->render('_liquidar', ['model'=> $model, 'empleados'=> $empleados]);
    }
    
    public function actionEmpleadosarea($area){
        
        $emp   = [];
        
        $datos = [];
       
        $datos = SysRrhhEmpleados::find()->select(['id_sys_rrhh_cedula', 'nombres'])
        ->joinWith(['sysAdmCargo'])
        ->join('join', 'sys_adm_departamentos', 'sys_adm_cargos.id_sys_adm_departamento =  sys_adm_departamentos.id_sys_adm_departamento')
        ->andWhere(["sys_rrhh_empleados.id_sys_empresa"=> "001"])
        ->andWhere(['sys_rrhh_empleados.estado'=> 'A'])
        ->andFilterWhere(["sys_adm_departamentos.id_sys_adm_area"=> $area])
        ->orderBy(['nombres'=>SORT_ASC])
        ->all();
        
        
        foreach ($datos as $data):
        
          $emp [] = array('id_sys_rrhh_cedula'=> $data['id_sys_rrhh_cedula'], 'nombres'=> $data['nombres']);
        
        
        endforeach;
     
        return json_encode($emp);
    }
    
    public function actionEmpleadosdepartamento($area, $departamento){
        
        $emp    = [];
        
        $datos  = [];
        
        $datos = SysRrhhEmpleados::find()->select(['id_sys_rrhh_cedula', 'nombres'])
        ->joinWith(['sysAdmCargo'])
        ->join('join', 'sys_adm_departamentos', 'sys_adm_cargos.id_sys_adm_departamento =  sys_adm_departamentos.id_sys_adm_departamento')
        ->andWhere(["sys_rrhh_empleados.id_sys_empresa"=> "001"])
        ->andWhere(['sys_rrhh_empleados.estado'=> 'A'])
        ->andWhere(["sys_adm_departamentos.id_sys_adm_area"=> $area])
        ->andFilterWhere(["sys_adm_departamentos.id_sys_adm_departamento"=>$departamento])
        ->orderBy(['nombres'=>SORT_ASC])
        ->all();
        
     
        foreach ($datos as $data):
        
           $emp [] = array('id_sys_rrhh_cedula'=> $data['id_sys_rrhh_cedula'], 'nombres'=>$data['nombres']);
        
        
        endforeach;
        
        return json_encode($emp);
        
    }
    
    public function actionDetallerol(){
        
        
        $obj     =  json_decode(Yii::$app->request->post('datos'));
        
        $table   = '';
        
        foreach ($obj->empleados as $emp):
        
             $table .= $this->renderPartial('_detalleliquidacion',['id_sys_rrhh_cedula'=> trim($emp->id_sys_rrhh_cedula), 'id_sys_empresa'=> '001', 'anio'=> trim($obj->anio), 'mes'=> trim($obj->mes), 'periodo'=>trim($obj->periodo)]);
       
        endforeach;
    
        
        return $table;
    }
    
    public function actionLiquidacion(){
        
        //datos recibidos por ajax 
       
                $datos       = Yii::$app->request->post('datos');
                $obj         =  json_decode($datos);

                $rolcab      =  SysRrhhEmpleadosRolCab::find()->where(['anio'=> $obj->anio ])->andWhere(['mes'=> $obj->mes ])->andWhere(['periodo'=> $obj->periodo])->andWhere(['id_sys_empresa'=>'001'])->one();
                
                //liquidar periodo si esta generado 
                if($rolcab->estado == 'Q'):
              
                          switch ( $obj->periodo) {
                                case '1':
                                    
                                            $mjs   =   $this->LiquidaQuincena($rolcab, $obj->empleados);
                                            
                                            if($mjs):
                                                  echo  json_encode(['estado'=> false, 'mensaje'=> $mjs]);
                                            else:
                                                  echo  json_encode(['estado'=> true, 'mensaje'=> 'El rol fue liquidado con éxito']);
                                            endif;
                                    
                                    break;
                                case '2':
                                    
                                            $mjs =   $this->LiquidaMes($rolcab, $obj->empleados);
                                    
                                            if($mjs):
                                                echo  json_encode(['estado'=> false, 'mensaje'=> $mjs]);
                                            else:
                                                echo  json_encode(['estado'=> true, 'mensaje'=> 'El rol fue liquidado con éxito']);
                                            endif;
                                    
                                    break;
                                case '70':
                                            $mjs =   $this->LiquidaDecimoTercero($rolcab, $obj->empleados);
                                            
                                            if($mjs):
                                                    echo  json_encode(['estado'=> false, 'mensaje'=> $mjs]);
                                            else:
                                                    echo  json_encode(['estado'=> true, 'mensaje'=> 'El rol fue liquidado con éxito']);
                                            endif;
                                    break;
                                case '71':
                                 
                                            $mjs =   $this->LiquidarDecimoCuarto($rolcab, $obj->empleados);
                                            
                                            if($mjs):
                                                 echo  json_encode(['estado'=> false, 'mensaje'=> $mjs]);
                                            else:
                                                 echo  json_encode(['estado'=> true, 'mensaje'=> 'El rol fue liquidado con éxito']);
                                            endif;
                                    
                                   break;
                            }
                  else:
                     echo  json_encode(['estado'=> false, 'mensaje'=> 'El rol no puede se liquidado']);
                  endif;
                
       
    }
    
    public function actionProcesarrolempleado(){
        
    
            $db          = $_SESSION['db'];
        
            $datos       =  Yii::$app->request->post('datos');
            
            $obj         =  json_decode($datos);
            
            $rolcab      =  SysRrhhEmpleadosRolCab::find()->where(['anio'=> $obj->anio ])->andWhere(['mes'=> $obj->mes ])->andWhere(['periodo'=> $obj->periodo])->andWhere(['id_sys_empresa'=>'001'])->one();
            
            if($rolcab->estado == 'Q'):
            
    
                   foreach ($obj->empleados as $emp):
             
                       Yii::$app->$db->createCommand("update  sys_rrhh_empleados_rol_mov  set estado = 'P'  where anio = '{$rolcab->anio}' and mes = '{$rolcab->mes}' and periodo = '{$obj->periodo}' and id_sys_rrhh_cedula = '{$emp->id_sys_rrhh_cedula}'")->execute();
             
                       if($rolcab->periodo == '2'):
                       
                           Yii::$app->$db->createCommand("update  sys_rrhh_empleados_rol_mov  set estado = 'P'  where anio = '{$rolcab->anio}' and mes = '{$rolcab->mes}' and periodo = '90' and id_sys_rrhh_cedula = '{$emp->id_sys_rrhh_cedula}'")->execute();
                         
                           //Actualizar en caso de que tenga prestamo 
                           
                           $prestamos = SysRrhhEmpleadosRolMov::find()
                           ->where(['anio'=> $rolcab->anio])
                           ->andWhere(['mes'=> $rolcab->mes])
                           ->andwhere(['periodo'=> '2'])
                           ->andWhere(['id_sys_rrhh_concepto'=> 'PREST_OFICINA'])
                           ->andWhere(['id_sys_rrhh_cedula'=> $emp->id_sys_rrhh_cedula])
                           ->andWhere(['id_sys_empresa'=> $rolcab->id_sys_empresa])
                           ->one();
                           
                           if($prestamos):
                           
                               $objprestamo = SysRrhhPrestamosCab::find()
                               ->where(['id_sys_rrhh_cedula'=>$emp->id_sys_rrhh_cedula])
                               ->andWhere(['id_sys_empresa'=> $rolcab->id_sys_empresa])
                               ->andWhere(['periodo_rol'=> '2'])
                               ->andWhere(['estado'=> 'P'])
                               ->andWhere(["autorizacion" => 'A'])
                               ->andWhere(["anulado" => 0])
                               ->one();
                                 
                               if($objprestamo):
                                  
                                    $prestamodetalle = SysRrhhPrestamosDet::find()
                                    ->where(['id_sys_rrhh_prestamos_cab'=>$objprestamo->id_sys_rrhh_prestamos_cab])
                                    ->andwhere(['anio'=> $rolcab->anio])
                                    ->andWhere(['mes'=> $rolcab->mes])
                                    ->andWhere(['id_sys_empresa'=> $rolcab->id_sys_empresa])
                                    ->one();
                                    
                                    $prestamodetalle->saldo = 0;
                                    $prestamodetalle->save(false);
                                    
                                    
                                    $prestamodetalle = SysRrhhPrestamosDet::find()
                                    ->where(['id_sys_rrhh_prestamos_cab'=>$objprestamo->id_sys_rrhh_prestamos_cab])
                                    ->andWhere(['id_sys_empresa'=> $rolcab->id_sys_empresa])
                                    ->andWhere(['>', 'saldo', 0])
                                    ->one();
                                    
                                    if(!$prestamodetalle):
                                    
                                        $objprestamo->estado = 'C';
                                        $objprestamo->save(false);
                                        
                                    endif;
                                    
                               
                                endif;
                           
                            
                           endif;
                           
                           
                        endif;
                       
                   endforeach;
                   
                   
                   $mjs = ['estado'=> true,  'mensaje'=> 'El rol fue procesado con exito'];
                   
                   
            
            else:
            
                   $mjs = ['estado'=> false,  'mensaje'=> 'El periodo a liquidar se encuentra procesado!'];
            
            endif;
            
            echo json_encode($mjs);
            
        
       
    }
    
    private  function LiquidaQuincena($rolcab, $empleados){        
        
        $errores = [];
        $error   = [];
        //concepto quincena
        $conceptoquincena = SysRrhhConceptos::find()->where(['id_sys_rrhh_concepto'=> 'ANTICIPO'])->andwhere(['pago'=> '1'])->one();
        
        $db = $_SESSION['db'];
        
        foreach ($empleados as $emp):
        
         $anticipo = 0;
         $dias     = 15;
         
         $rolmov  =  SysRrhhEmpleadosRolMov::find()->where(['anio'=> $rolcab->anio])->andWhere(['mes'=> $rolcab->mes])->andWhere(['periodo'=> $rolcab->periodo])->andWhere(['id_sys_empresa'=>$rolcab->id_sys_empresa])->andWhere(['estado'=> 'P'])->andWhere(['id_sys_rrhh_cedula'=> $emp->id_sys_rrhh_cedula])->one();
         
         //Si no está procesado liquidamos el rol del empleador
         
         if(!$rolmov):
         
            //eliminamos el rol de detalle del empleador
            Yii::$app->$db->createCommand("delete  FROM sys_rrhh_empleados_rol_mov where anio = '{$rolcab->anio}' and mes = '{$rolcab->mes}' and periodo = '{$rolcab->periodo}' and id_sys_rrhh_cedula = '{$emp->id_sys_rrhh_cedula}'")->execute();
        
            //Obtenemos datos del empleador
            $empleado     = SysRrhhEmpleados::find()
            ->where(['id_sys_rrhh_cedula'=> $emp->id_sys_rrhh_cedula])
            ->andWhere(['id_sys_empresa'=> $rolcab->id_sys_empresa])
            ->andWhere(['estado'=> 'A'])
            ->one();
            
            //obtenemos el anticipo del empleador 
             $sueldoant    =  SysRrhhEmpleadosSueldos::find()->select('sueldo_anticipo')
            ->where(['id_sys_rrhh_cedula'=> $empleado->id_sys_rrhh_cedula])
            ->andWhere(['id_sys_empresa'=> $rolcab->id_sys_empresa])
            ->andWhere(['estado'=> 'A'])
            ->scalar();
            
            
            //obtenemos el cargo del empleador
            $cargoemp     =  SysAdmCargos::find()
            ->where(['id_sys_adm_cargo'=> $empleado->id_sys_adm_cargo])
            ->andWhere(['id_sys_empresa'=> $rolcab->id_sys_empresa])
            ->one();
       
           
             $anticipo = floatval($sueldoant);
             $dias     = 15;   
                  
           if($conceptoquincena):
           
                   if($anticipo > 0):
                   
                          
                            //insertar anticipo de quincena 
                            $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $empleado->id_sys_rrhh_cedula, $conceptoquincena->id_sys_rrhh_concepto, $rolcab->id_sys_empresa, $dias, $anticipo, $cargoemp->id_sys_adm_departamento);
                             
                            if($newconcepto['estado'] == false):
                            
                                 $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                            
                            endif;
                            
                            //Inserta Habere y Descuentos
                            $error = $this->getCalculaHaberes($rolcab, $emp, $rolcab->periodo, $cargoemp);
                            if($error):
                               $errores [] = $error;
                            endif;
                            
                      endif; //anticipo mayor que 0
                endif; //si exite el concepto anticipo 
          endif; //if rol
         
     endforeach;
     
     return $errores;
     
    }
   
    private function LiquidaMes($rolcab, $empleados){
         
        $errores = [];
        
        $error   = [];
        
        $db = $_SESSION['db'];
        
        //concepto quincena
        $conceptoSueldo         = SysRrhhConceptos::find()->where(['id_sys_rrhh_concepto'=> 'SUELDO'])->andwhere(['pago'=> '2'])->one();
        
        //Descuento Anticipo Quincena 
        $conceptoDesQuincena    = SysRrhhConceptos::find()->where(['id_sys_rrhh_concepto'=> 'ANTICIPOS'])->andwhere(['pago'=> '2'])->one();

        //Descuento Dias No Laborados
        $conceptoDiasNL         = SysRrhhConceptos::find()->where(['id_sys_rrhh_concepto'=> 'DESC_DIAS_NL'])->andwhere(['pago'=> '2'])->one();
        
        //concepto horas extras 25%
        $concepto25             = SysRrhhConceptos::find()->where(['id_sys_rrhh_concepto'=> 'PAGO_HORAS_25'])->andwhere(['pago'=> '2'])->andWhere(['>', 'valor', '0'])->andWhere(['tipo_valor'=> 'P'])->andWhere(['proceso'=> 'C'])->one();
        
        //concepto horas extras 50%
        $concepto50             = SysRrhhConceptos::find()->where(['id_sys_rrhh_concepto'=> 'PAGO_HORAS_50'])->andwhere(['pago'=> '2'])->andWhere(['>', 'valor', '0'])->andWhere(['tipo_valor'=> 'P'])->andWhere(['proceso'=> 'C'])->one(); 
        
        //concepto horas extras 100%
        $concepto100            = SysRrhhConceptos::find()->where(['id_sys_rrhh_concepto'=> 'PAGO_HORAS_100'])->andwhere(['pago'=> '2'])->andWhere(['>', 'valor', '0'])->andWhere(['tipo_valor'=> 'P'])->andWhere(['proceso'=> 'C'])->one();
        
        //concepto pago decimo tercer sueldo
        $conceptoPagodecimoTer = SysRrhhConceptos::find()->where(['id_sys_rrhh_concepto'=> 'DEC_TERCERO_PAG'])->andwhere(['pago'=> '2'])->andWhere([ 'valor' => '0'])->one();
        
        //concepto pago decimo cuarto sueldo
        $conceptoPagodecimoCua = SysRrhhConceptos::find()->where(['id_sys_rrhh_concepto'=> 'DEC_CUARTO_PAG'])->andwhere(['pago'=> '2'])->andWhere([ 'valor' => '0'])->one();
        
        //concepto pago fondos de reserva 
        $conceptoPagoFondoReserva  = SysRrhhConceptos::find()->where(['id_sys_rrhh_concepto'=> 'FONDO_RESERVA'])->andwhere(['pago'=> '2'])->andWhere(['>', 'valor', '0'])->andWhere(['tipo_valor'=> 'P'])->andWhere(['proceso'=> 'C'])->one();
        
        //sueldo basico valor
        $SueldoBasico              = SysRrhhConceptos::find()->select('valor')->where(['concepto_sueldo'=> 'SU'])->andwhere(['pago'=> '2'])->andWhere(['>', 'valor', '0'])->andWhere(['tipo_valor'=> 'V'])->scalar();
       
        //concepto prestamo empresa 
        $conceptoPrestOficina      = SysRrhhConceptos::find()->where(['id_sys_rrhh_concepto'=> 'PREST_OFICINA'])->andwhere(['pago'=> '2'])->andWhere([ 'valor' => '0'])->one();
       
       //Concepto IESS
        $conceptoIess              = SysRrhhConceptos::find()->where(['id_sys_rrhh_concepto'=> 'IESS'])->andwhere(['pago'=> '2'])->andWhere(['>', 'valor', '0'])->andWhere(['tipo_valor'=> 'P'])->andWhere(['proceso'=> 'C'])->one();
        
      
        $conceptoIessPasantes      = SysRrhhConceptos::find()->where(['id_sys_rrhh_concepto'=> 'IESS_PASANTE'])->andwhere(['pago'=> '2'])->andWhere(['>', 'valor', '0'])->andWhere(['tipo_valor'=> 'P'])->one();
        
          
        //CONCEPTO IMPUESTO RENTA 
        $conceptoImpRenta         =  SysRrhhConceptos::find()->where(['id_sys_rrhh_concepto'=> 'IMPUESTO_RENTA'])->andwhere(['pago'=> '2'])->andWhere([ 'valor' => '0'])->one();
        
        
        //PROVICIONES 
        //Provicion decimo tercero
        $conceptoProviDecimoTer    = SysRrhhConceptos::find()->where(['id_sys_rrhh_concepto'=> 'DECIMO_TERCERO'])->andwhere(['pago'=> '90'])->andWhere([ 'valor' => '0'])->one();
        
        //Provicion decimo cuarto 
        $conceptoProviDecimoCua    = SysRrhhConceptos::find()->where(['id_sys_rrhh_concepto'=> 'DECIMO_CUARTO'])->andwhere(['pago'=> '90'])->andWhere([ 'valor' => '0'])->one();
        
     
        //Proviones Calculo por Porcentaje 
        $proviciones               = SysRrhhConceptos::find()->where(['>', 'valor', '0'])->andwhere(['pago'=> '90'])->andWhere(['tipo_valor'=> 'P'])->all();
        
        //Proviones Vacaciones 
        $conceptoProviVacaciones   = SysRrhhConceptos::find()->where(['id_sys_rrhh_concepto'=> 'VACACIONES'])->andwhere(['>', 'valor', '0'])->andwhere(['pago'=> '90'])->andWhere(['tipo_valor'=> 'D'])->one();
        
        
        
        foreach ($empleados as $emp):
        
  
              $sueldo          = 0;
              $dias            = 30;
              $horaspermiso    = 0;
              $diaspermiso     = 0;
              $valorhora       = 0;
              $valordia        = 0;
              $dataliquidacion = [];
              $longitud        = 0;
              $subcidio        = 0;
              $faltas          = 0;
              $descuento       = 0;
              //cargamos todos los conceptos 
              
             
              $rolmov  =  SysRrhhEmpleadosRolMov::find()->where(['anio'=> $rolcab->anio])->andWhere(['mes'=> $rolcab->mes])->andWhere(['periodo'=> $rolcab->periodo])->andWhere(['id_sys_empresa'=>$rolcab->id_sys_empresa])->andWhere(['estado'=> 'P'])->andWhere(['id_sys_rrhh_cedula'=> $emp->id_sys_rrhh_cedula])->one();
              
              //Si no está procesado liquidamos el rol del empleador 
           
              if(!$rolmov):
                 
             
                  //eliminamos el rol de detalle del empleador 
                   Yii::$app->$db->createCommand("delete  FROM sys_rrhh_empleados_rol_mov where anio = '{$rolcab->anio}' and mes = '{$rolcab->mes}' and periodo = 2 and id_sys_rrhh_cedula = '{$emp->id_sys_rrhh_cedula}'")->execute();
                   
                   
                   
                   //eliminamos el rol de detalle del empleador
                   Yii::$app->$db->createCommand("delete  FROM sys_rrhh_empleados_rol_mov where anio = '{$rolcab->anio}' and mes = '{$rolcab->mes}' and periodo = 90 and id_sys_rrhh_cedula = '{$emp->id_sys_rrhh_cedula}'")->execute();
                   
                   //eliminamos el rol de detalle del empleador
                   Yii::$app->$db->createCommand("delete  FROM sys_rrhh_empleados_rol_liq where anio = {$rolcab->anio} and mes = {$rolcab->mes} and id_sys_rrhh_cedula = '{$emp->id_sys_rrhh_cedula}'")->execute();
                   
                   
             
                   
                   
                  //Obtenemos datos del empleador 
                   $empleado     = SysRrhhEmpleados::find()
                                 ->where(['id_sys_rrhh_cedula'=> $emp->id_sys_rrhh_cedula])
                                 ->andWhere(['estado'=> 'A'])
                                  ->one();
                  
                   //obtenemos el sueldo del empleador 
                   $sueldoemp    =  SysRrhhEmpleadosSueldos::find()->select('sueldo')
                                    ->where(['id_sys_rrhh_cedula'=> $empleado->id_sys_rrhh_cedula])
                                    ->andWhere(['estado'=> 'A'])
                                    ->scalar();
                   
                                    
                                    
                                    
                   //obtenemos el contrato del empleador    
                   $contratoemp  =  SysRrhhEmpleadosContratos::find()
                                   ->where(['id_sys_rrhh_cedula'=> $empleado->id_sys_rrhh_cedula])
                                   ->orderBy(['id_sys_rrhh_empleados_contrato_cod' => SORT_DESC])
                                   ->one();
          
                                   
                   //Obtener Contrato
                   $contrato = SysRrhhContratos::find()->where(['id_sys_rrhh_contrato'=> $empleado->id_sys_rrhh_contrato])->one();
                   
                   
                                   
                   //obtenemos el cargo del empleador 
                   $cargoemp     =  SysAdmCargos::find()
                                   ->where(['id_sys_adm_cargo'=> $empleado->id_sys_adm_cargo])
                                   ->one();
                       
                                   
                   //verificamos si empleador esta activo 
                   if($contratoemp->fecha_salida == null):
                   
                   
                   
                         if($contratoemp->fecha_ingreso <= $rolcab->fecha_ini):
                        
            
                           
                                    if($contratoemp->fecha_ingreso >= $rolcab->fecha_ini_liq && $contratoemp->fecha_ingreso <= $rolcab->fecha_fin_liq):
                                    
                            
                                          //si no resgistra entrada revisar permisos
                                              
                                               if($cargoemp->reg_ent_salida == 'S'):
                                            
                                                  $dataliquidacion = $this->CalcularAsistencia($contratoemp->fecha_ingreso, $rolcab->fecha_fin_liq, $empleado->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $sueldoemp);
                                               
                                               
                                               else:
                                                    //busca pemrisos
                                                    $dataliquidacion = $this->BuscaPermisos($contratoemp->fecha_ingreso, $rolcab->fecha_fin_liq, $empleado->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $sueldoemp);
                                                  
                                               endif;
                                    
                                    else:
                                    
                                     
                                            if($cargoemp->reg_ent_salida == 'S'):
                                            
                                            
                                                $dataliquidacion = $this->CalcularAsistencia($rolcab->fecha_ini_liq, $rolcab->fecha_fin_liq, $empleado->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $sueldoemp);
                                            
                                            else:
                                            
                                                 //busca permisos
                                            $dataliquidacion = $this->BuscaPermisos($rolcab->fecha_ini_liq, $rolcab->fecha_fin_liq,  $empleado->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $sueldoemp);
                                            
                                            endif;
                                           
                                  endif;
                                  
                                  
                         elseif($contratoemp->fecha_ingreso >= $rolcab->fecha_ini && $contratoemp->fecha_ingreso  <= $rolcab->fecha_fin):
                         
                            if($cargoemp->reg_ent_salida == 'S'):
                                                          
                               $dataliquidacion = $this->CalcularAsistencia($contratoemp->fecha_ingreso, $rolcab->fecha_fin, $empleado->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $sueldoemp);
                            
                            else:
                                
                                //busca permisos
                                $dataliquidacion = $this->BuscaPermisos($contratoemp->fecha_ingreso, $rolcab->fecha_fin, $empleado->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $sueldoemp);
                            
                            endif;

                         endif;
                         
                         
                  
                   
                  else:
                      //si el emmpleador tiene fecha de salida 
                      
                      if($cargoemp->reg_ent_salida == 'S'):
                     
                            if($contratoemp->fecha_ingreso >= $rolcab->fecha_ini && $contratoemp->fecha_ingreso  <= $rolcab->fecha_fin):
                          
                                $dataliquidacion = $this->CalcularAsistencia($contratoemp->fecha_ingreso, $contratoemp->fecha_salida, $empleado->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $sueldoemp);
                           
                            else:
                            
                                //Valida asistencia dentro del corte 
                               if($contratoemp->fecha_ingreso >= $rolcab->fecha_ini_liq && $contratoemp->fecha_ingreso  <= $rolcab->fecha_fin_liq):
                                
                                    $dataliquidacion = $this->CalcularAsistencia($contratoemp->fecha_ingreso, $contratoemp->fecha_salida, $empleado->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $sueldoemp);
                                
                                else:
                                
                                    $dataliquidacion = $this->CalcularAsistencia($rolcab->fecha_ini, $contratoemp->fecha_salida, $empleado->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $sueldoemp);
                                
                                endif;
                                                     
                            endif;
     
                      else:
                          //busca permisos
                          $dataliquidacion = $this->BuscaPermisos($rolcab->fecha_ini, $contratoemp->fecha_salida, $empleado->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $sueldoemp);
                      
                      endif;
                  
                      
                 endif;
                 
                 
                 $longitud = count($dataliquidacion);
                 
                    if($longitud > 0 ):
                 
                        //datos liquidacion
                    
                        $sueldo       =  floatval($sueldoemp);
                        $cont         =  $dataliquidacion[$longitud- 1]['cont'];
                        $faltas       =  array_sum(array_column($dataliquidacion, 'faltas'));
                        $horaspermiso =  array_sum(array_column($dataliquidacion, 'horaspermiso')); 
                        $diaspermiso  =  array_sum(array_column($dataliquidacion, 'diaspermisos')); 
                        $valordia     =  floatval($sueldoemp/30);
                        $valorhora    =  floatval($sueldoemp/240);
                        //   $subcidio     =  $dataliquidacion[$longitud- 1]['subcidio'];
                        //actualizamos el sueldo del empleador si existen faltas 
                        $subcidio     = floatval( array_sum(array_column($dataliquidacion, 'subcidio')));
                        $descuentoFaltas = 0;
                        $descuentoPermisos = 0;
                                 
                        if($contratoemp->fecha_salida == null):
         
                            //dias en caso de ser nuevo persona
                            if($contratoemp->fecha_ingreso >= $rolcab->fecha_ini && $contratoemp->fecha_ingreso  < $rolcab->fecha_fin):
                                 
                                $date1     = new \DateTime($contratoemp->fecha_ingreso);
                                $date2     = new \DateTime($rolcab->fecha_fin);
                                $diff      = $date1->diff($date2);
                                // will output 2 days
                                $dias      = intval($diff->days) + 1;
                                       
                                //Mes Biciesto
                                $mesbisiesto =  $this->max_dia($rolcab->mes, $rolcab->anio);
                                         
                                if($mesbisiesto < 30):

                                    if($mesbisiesto == 29):
                                        $dias++;
                                    else:
                                        $dias = $dias + 2;
                                    endif;
                                            
                                         
                                endif;
                                         
                                //$faltas    = 0;
                                $sueldo    = ($sueldoemp/30) * $dias; 

                                //Calcular Sueldo flotas
                                if($cargoemp->reg_ent_salida == 'N'  &&  $empleado->tipo_empleado  == 'T'):
                                
                                    //Marea
                                    
                                    if($contratoemp->fecha_salida != null):
                                    
                                        $sueldo  =  ($SueldoBasico/30) * $dias;
                                    
                                    elseif($contratoemp->fecha_ingreso > $rolcab->fecha_ini && $contratoemp->fecha_ingreso  < $rolcab->fecha_fin):
                                    
                                        $sueldo  =  ($SueldoBasico/30) * $dias;
                                    
                                    else: 
                                        $sueldo  = $SueldoBasico;    
                                    
                                    endif;

                                endif;
                        
                                //Sueldo de jefaturas 
                    
                                //Faltas 
                                if($faltas > 0):
                                
                        
                                    $faltas       =   $faltas * 2;
                                    $dias         =   $dias   - $faltas;
                                    $sueldo       =   $sueldo -  floatval(($valordia * $faltas));
                                    
                                endif;
                    
                                //Permisos 
                                if($diaspermiso > 0):
                                
                            
                                    if($cargoemp->reg_ent_salida == 'N'):
                                    
                                        $sueldo           = $sueldoemp;
                                    
                                    endif;
                                
                                
                                    $dias         =    $dias - $diaspermiso;
                                    $sueldo       =    $sueldo -   floatval(($valordia * $diaspermiso));
                                    
                                
                                endif;
                                
                            
                                    if($sueldo > 0):
                                            
                                        //Insertar Liquidacion dias 
                                        $this->AgregaLiquidacion(intval($rolcab->anio), intval($rolcab->mes), $emp->id_sys_rrhh_cedula, $dias, $faltas);
                                        

                                        //1.- insertamos sueldo
                                        $sueldopag = 0;
                                    
                                        //Subcidios 
                                        if($subcidio > 0):
                                            
                                            $sueldopag = number_format($sueldo, 2, '.', '') - number_format($subcidio, 2, '.', '');
                                        
                                        else:
                                        
                                            $sueldopag = $sueldo;   
                                        
                                        endif;
                                        

                                        $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, $conceptoSueldo->id_sys_rrhh_concepto, $rolcab->id_sys_empresa, $dias, $sueldopag, $cargoemp->id_sys_adm_departamento);
                                            
                                        if($newconcepto):
                                        
                                                //1- HORAS EXTRAS
                                                if($cargoemp->reg_horas_extras == 'S'):
                                                
                                                    $error  = $this->getCalculaHorasExtras($emp, $rolcab, $concepto25, $concepto50, $concepto100, $cargoemp, $valorhora);
                                        
                                                    if($error):
                                                    
                                                    $errores [] = $error;  
                                                    
                                                    endif;
                                                        
                                    
                                                endif;
                                                //2- NOVEDADES 
                                                $error =  $this->getCalculaNovedades($emp, $rolcab, $cargoemp);
                                                
                                                if($error):
                                                
                                                    $errores [] = $error;  
                                                
                                                endif;
                                                
                                                //HABERES Y DESCUENTOS 
                                                
                                                $error = $this->getCalculaHaberes($rolcab, $emp, $rolcab->periodo, $cargoemp);
                                                
                                                if($error):
                                                
                                                    $errores [] = $error;  
                                                
                                                
                                                endif;
                                                
                                                
                                                //INCENTIVO POR MAREA 
                                                if($cargoemp->reg_ent_salida == 'N'  &&  $empleado->tipo_empleado== 'T'):
                                                    
                                                    $error  = $this->CalculaIncentivoMarea($rolcab, $empleado->id_sys_rrhh_cedula);
                                                    
                                                    if($error):
                                                    
                                                            $errores [] = $error;
                                                
                                                    endif;
                                                    
                                                endif;
                                                                            
                                                //9 PAGO DECIMOS 
                                                if($empleado->decimo == 'S'):
                                                
                                                        $error = $this->getCalculaDecimoTer($emp, $sueldo, $rolcab, $conceptoPagodecimoTer, $cargoemp, $rolcab->periodo, $dias);
                                        
                                                        if($error):
                                                        
                                                            $errores [] = $error;  
                                                        
                                                        endif;
                                                
                                                        $error = $this->getCalculaDecimoCua($emp, $rolcab, $conceptoPagodecimoCua, $cargoemp, $contratoemp, $dias, $SueldoBasico, $rolcab->periodo);
                                                        
                                                        if($error):
                                                        
                                                            $errores [] = $error;
                                                        
                                                        endif;
                                                
                                                
                                                endif;
                                                
                                                //FONDO DE RESERVA
                                                if($empleado->freserva == 'S' && $empleado->provision_freserva == 'S'):
                                                
                                                            $aniolaboral = floatval( $this->getAnioLaboral($rolcab->fecha_fin, $empleado->id_sys_rrhh_cedula));
                                                            
                                                            if($aniolaboral >= 1):
                                                            
                                                                $error = $this->getCalculoFondoReserva($emp, $sueldo, $rolcab, $conceptoPagoFondoReserva, $cargoemp, $rolcab->periodo, intval($aniolaboral),$contratoemp);
                                                            
                                                                if($error):
                                                                
                                                                    $errores [] = $error;
                                                                
                                                                endif;
                                                                
                                                            endif;     
                                                endif;
                                                
                                            
                                                //CALCULO IESS
                                                if($contrato->provisiones == true):   
                                                
                                                    $error = $this->getCalculoIess($emp, $sueldo, $rolcab, $conceptoIess, $cargoemp);
                                                
                                                else:
                                                
                                                    $error = $this->getCalculoIess($emp, $sueldo, $rolcab, $conceptoIessPasantes, $cargoemp);
                                                
                                                endif;
                                                
                                                if($error):
                                                
                                                        $errores [] = $error;
                                                
                                                endif;
                                                
                                                //DESCUENTOS HORAS NO LABORADAS
                                                $horasNoLaboradasComedor = $this->getHorasNoLaboradasComedor($emp->id_sys_rrhh_cedula, $rolcab->fecha_ini_liq, $rolcab->fecha_fin_liq);
                                                
                                                
                                                if ($horasNoLaboradasComedor > 0) :
                                                
                                                    $horaspermiso = $horaspermiso + $horasNoLaboradasComedor;
                                                
                                                endif;
                                                
                                                
                                                
                                                
                                                if($horaspermiso > 0):
                                                
                                                    $descuento     =    floatval($horaspermiso * $valorhora);
                                                
                                                    $newconcepto   =    $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, 'DES_HORAS_NL', $rolcab->id_sys_empresa,  $horaspermiso, $descuento, $cargoemp->id_sys_adm_departamento);
                                                    
                                                    if($newconcepto['estado'] == false):
                                                    
                                                        $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                                                    
                                                    endif;
                                                    
                                                endif;
                                                
                                                //ANTICIPO QUINCENA 
                                                
                                                $error = $this->getAnticipoFinMes($emp, $rolcab, $conceptoDesQuincena, $cargoemp);
                                                
                                                if($error):
                                                    
                                                    $errores [] = $error;
                                                
                                                endif;
                                                
                                                
                                                //PRESTAMOS EMPRESAS 
                                                
                                                $error = $this->getCuotaPrestamoEmpresa($emp, $rolcab, $conceptoPrestOficina, $cargoemp);
                                                
                                                if($error):
                                                
                                                        $errores [] = $error;
                                                
                                                endif;
                                                
                                                //IMPUESTO A LA RENTA 
                                                $error = $this->getImpuestoRenta($emp, $sueldoemp, $rolcab, $conceptoImpRenta, $cargoemp, $conceptoIess,$contratoemp);
                                                
                                                if($error):
                                                
                                                    $errores [] = $error;
                                                
                                                endif;
                                                
                                                
                                                //CALCULO DE PROVICIONES 
                                                //GENERA PROVIVIONES DEPENDIENDO DEL TIPO DE CONTRATO
                                                
                                                
                                                if($contrato->provisiones == true):   
                                                
                                                        //PROVICION DECIMOS 
                                                        
                                                        if($empleado->decimo == 'N'):
                                                                
                                                                $error = $this->getCalculaDecimoTer($emp, $sueldo, $rolcab, $conceptoProviDecimoTer, $cargoemp, '90',$dias);
                                                                
                                                                if($error):
                                                                
                                                                    $errores [] = $error;
                                                                
                                                                endif;
                                                        
                                                                $error = $this->getCalculaDecimoCua($emp, $rolcab, $conceptoProviDecimoCua, $cargoemp, $contratoemp, $dias, $SueldoBasico, '90');
                                                                
                                                                if($error):
                                                                
                                                                    $errores [] = $error;
                                                                
                                                                endif;
                                                                        
                                                        
                                                        endif;
                                                        
                                                        //PROVICION FONDOS DE RESERVA 
                                                        if($empleado->freserva == 'N' && $empleado->provision_freserva == 'S'):
                                                                
                                                                //validamos si el empleador cumple el año de trabajo
                                                                $aniolaboral =  floatval( $this->getAnioLaboral($rolcab->fecha_fin, $emp->id_sys_rrhh_cedula));
                                                                
                                                                if($aniolaboral >= 1):
                                                                
                                                                    $error = $this->getCalculoFondoReserva($emp, $sueldo, $rolcab, $conceptoPagoFondoReserva, $cargoemp, '90', intval($aniolaboral), $contratoemp);
                                                                    
                                                                    if($error):
                                                                    
                                                                        $errores [] = $error;
                                                                    
                                                                    endif;
                                                                
                                                                endif;
                                                        endif;
                                                        
                                                        //CALCULO PROVICIONES 
                                                        
                                                        //IECE, SECAP, APORTE PATRONAL 
                                                        
                                                        $error = $this->getProviciones($emp, $sueldo, $rolcab, $proviciones, $cargoemp);
                                                        
                                                        if($error):
                                                        
                                                        $errores [] = $error;
                                                        
                                                        endif;
                                                        
                                                        
                                                        //PROVICION DE VACACIONES 
                                                        $error = $this->getProvicionesVacaciones($emp, $sueldo, $rolcab, $conceptoProviVacaciones, $cargoemp);
                                                        
                                                        if($error):
                                                        
                                                        $errores [] = $error;
                                                        
                                                        endif;
                            
                                                endif;
                                                
                                                
                                                
                                        endif;
                                    else:
                                        $errores [] = array('mensaje'=> 'No se pudo liquidar la siguiente persona'.$emp->id_sys_rrhh_cedula.' Revisar parametros sueldo'.$faltas);
                                    endif;//sueldo
                                         
                            //Validacion de personas con fecha ingreso y cortes        
                            elseif($contratoemp->fecha_ingreso >= $rolcab->fecha_ini_liq && $contratoemp->fecha_ingreso <= $rolcab->fecha_fin_liq):
  
                                $dias   = 30;
                                $sueldo = ($sueldoemp/30) * $dias; 

                                if($cargoemp->reg_ent_salida == 'N'  &&  $empleado->tipo_empleado  == 'T'):
                                
                                    //Marea
                                    
                                    if($contratoemp->fecha_salida != null):
                                    
                                        $sueldo  =  ($SueldoBasico/30) * $dias;
                                    
                                    elseif($contratoemp->fecha_ingreso > $rolcab->fecha_ini && $contratoemp->fecha_ingreso  < $rolcab->fecha_fin):
                                    
                                        $sueldo  =  ($SueldoBasico/30) * $dias;
                                    
                                    else: 
                                        $sueldo  = $SueldoBasico;    
                                    
                                    endif;

                                endif;
                        
                                //Sueldo de jefaturas 
                    
                                //Faltas 
                                if($faltas > 0):
                                
                        
                                    $faltas       =   $faltas * 2;
                                    $dias         =   $dias   - $faltas;
                                    $sueldo       =   $sueldo -  floatval(($valordia * $faltas));
                                    
                                endif;
                    
                                //Permisos 
                                if($diaspermiso > 0):
                                
                            
                                    if($cargoemp->reg_ent_salida == 'N'):
                                    
                                        $sueldo           = $sueldoemp;
                                    
                                    endif;
                                
                                
                                    $dias         =    $dias - $diaspermiso;
                                    $sueldo       =    $sueldo -   floatval(($valordia * $diaspermiso));
                                    
                                
                                endif;
                                
                            
                                    if($sueldo > 0):
                                            
                                        //Insertar Liquidacion dias 
                                        $this->AgregaLiquidacion(intval($rolcab->anio), intval($rolcab->mes), $emp->id_sys_rrhh_cedula, $dias, $faltas);
                                        

                                        //1.- insertamos sueldo
                                        $sueldopag = 0;
                                    
                                        //Subcidios 
                                        if($subcidio > 0):
                                            
                                            $sueldopag = number_format($sueldo, 2, '.', '') - number_format($subcidio, 2, '.', '');
                                        
                                        else:
                                        
                                            $sueldopag = $sueldo;   
                                        
                                        endif;
                                        

                                        $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, $conceptoSueldo->id_sys_rrhh_concepto, $rolcab->id_sys_empresa, $dias, $sueldopag, $cargoemp->id_sys_adm_departamento);
                                            
                                        if($newconcepto):
                                        
                                                //1- HORAS EXTRAS
                                                if($cargoemp->reg_horas_extras == 'S'):
                                                
                                                    $error  = $this->getCalculaHorasExtras($emp, $rolcab, $concepto25, $concepto50, $concepto100, $cargoemp, $valorhora);
                                        
                                                    if($error):
                                                    
                                                    $errores [] = $error;  
                                                    
                                                    endif;
                                                        
                                    
                                                endif;
                                                //2- NOVEDADES 
                                                $error =  $this->getCalculaNovedades($emp, $rolcab, $cargoemp);
                                                
                                                if($error):
                                                
                                                    $errores [] = $error;  
                                                
                                                endif;
                                                
                                                //HABERES Y DESCUENTOS 
                                                
                                                $error = $this->getCalculaHaberes($rolcab, $emp, $rolcab->periodo, $cargoemp);
                                                
                                                if($error):
                                                
                                                    $errores [] = $error;  
                                                
                                                
                                                endif;
                                                
                                                
                                                //INCENTIVO POR MAREA 
                                                if($cargoemp->reg_ent_salida == 'N'  &&  $empleado->tipo_empleado== 'T'):
                                                    
                                                    $error  = $this->CalculaIncentivoMarea($rolcab, $empleado->id_sys_rrhh_cedula);
                                                    
                                                    if($error):
                                                    
                                                            $errores [] = $error;
                                                
                                                    endif;
                                                    
                                                endif;
                                                                            
                                                //9 PAGO DECIMOS 
                                                if($empleado->decimo == 'S'):
                                                
                                                        $error = $this->getCalculaDecimoTer($emp, $sueldo, $rolcab, $conceptoPagodecimoTer, $cargoemp, $rolcab->periodo, $dias);
                                        
                                                        if($error):
                                                        
                                                            $errores [] = $error;  
                                                        
                                                        endif;
                                                
                                                        $error = $this->getCalculaDecimoCua($emp, $rolcab, $conceptoPagodecimoCua, $cargoemp, $contratoemp, $dias, $SueldoBasico, $rolcab->periodo);
                                                        
                                                        if($error):
                                                        
                                                            $errores [] = $error;
                                                        
                                                        endif;
                                                
                                                
                                                endif;
                                                
                                                //FONDO DE RESERVA
                                                if($empleado->freserva == 'S' && $empleado->provision_freserva == 'S'):
                                                
                                                            $aniolaboral = floatval( $this->getAnioLaboral($rolcab->fecha_fin, $empleado->id_sys_rrhh_cedula));
                                                            
                                                            if($aniolaboral >= 1):
                                                            
                                                                $error = $this->getCalculoFondoReserva($emp, $sueldo, $rolcab, $conceptoPagoFondoReserva, $cargoemp, $rolcab->periodo, intval($aniolaboral),$contratoemp);
                                                            
                                                                if($error):
                                                                
                                                                    $errores [] = $error;
                                                                
                                                                endif;
                                                                
                                                            endif;     
                                                endif;
                                                
                                            
                                                //CALCULO IESS
                                                if($contrato->provisiones == true):   
                                                
                                                    $error = $this->getCalculoIess($emp, $sueldo, $rolcab, $conceptoIess, $cargoemp);
                                                
                                                else:
                                                
                                                    $error = $this->getCalculoIess($emp, $sueldo, $rolcab, $conceptoIessPasantes, $cargoemp);
                                                
                                                endif;
                                                
                                                if($error):
                                                
                                                        $errores [] = $error;
                                                
                                                endif;
                                                
                                                //DESCUENTOS HORAS NO LABORADAS
                                                $horasNoLaboradasComedor = $this->getHorasNoLaboradasComedor($emp->id_sys_rrhh_cedula, $rolcab->fecha_ini_liq, $rolcab->fecha_fin_liq);
                                                
                                                
                                                if ($horasNoLaboradasComedor > 0) :
                                                
                                                    $horaspermiso = $horaspermiso + $horasNoLaboradasComedor;
                                                
                                                endif;
                                                
                                                
                                                
                                                
                                                if($horaspermiso > 0):
                                                
                                                    $descuento     =    floatval($horaspermiso * $valorhora);
                                                
                                                    $newconcepto   =    $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, 'DES_HORAS_NL', $rolcab->id_sys_empresa,  $horaspermiso, $descuento, $cargoemp->id_sys_adm_departamento);
                                                    
                                                    if($newconcepto['estado'] == false):
                                                    
                                                        $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                                                    
                                                    endif;
                                                    
                                                endif;
                                                
                                                //ANTICIPO QUINCENA 
                                                
                                                $error = $this->getAnticipoFinMes($emp, $rolcab, $conceptoDesQuincena, $cargoemp);
                                                
                                                if($error):
                                                    
                                                    $errores [] = $error;
                                                
                                                endif;
                                                
                                                
                                                //PRESTAMOS EMPRESAS 
                                                
                                                $error = $this->getCuotaPrestamoEmpresa($emp, $rolcab, $conceptoPrestOficina, $cargoemp);
                                                
                                                if($error):
                                                
                                                        $errores [] = $error;
                                                
                                                endif;
                                                
                                                //IMPUESTO A LA RENTA 
                                                $error = $this->getImpuestoRenta($emp, $sueldoemp, $rolcab, $conceptoImpRenta, $cargoemp, $conceptoIess,$contratoemp);
                                                
                                                if($error):
                                                
                                                    $errores [] = $error;
                                                
                                                endif;
                                                
                                                
                                                //CALCULO DE PROVICIONES 
                                                //GENERA PROVIVIONES DEPENDIENDO DEL TIPO DE CONTRATO
                                                
                                                
                                                if($contrato->provisiones == true):   
                                                
                                                        //PROVICION DECIMOS 
                                                        
                                                        if($empleado->decimo == 'N'):
                                                                
                                                                $error = $this->getCalculaDecimoTer($emp, $sueldo, $rolcab, $conceptoProviDecimoTer, $cargoemp, '90',$dias);
                                                                
                                                                if($error):
                                                                
                                                                    $errores [] = $error;
                                                                
                                                                endif;
                                                        
                                                                $error = $this->getCalculaDecimoCua($emp, $rolcab, $conceptoProviDecimoCua, $cargoemp, $contratoemp, $dias, $SueldoBasico, '90');
                                                                
                                                                if($error):
                                                                
                                                                    $errores [] = $error;
                                                                
                                                                endif;
                                                                        
                                                        
                                                        endif;
                                                        
                                                        //PROVICION FONDOS DE RESERVA 
                                                        if($empleado->freserva == 'N' && $empleado->provision_freserva == 'S'):
                                                                
                                                                //validamos si el empleador cumple el año de trabajo
                                                                $aniolaboral =  floatval( $this->getAnioLaboral($rolcab->fecha_fin, $emp->id_sys_rrhh_cedula));
                                                                
                                                                if($aniolaboral >= 1):
                                                                
                                                                    $error = $this->getCalculoFondoReserva($emp, $sueldo, $rolcab, $conceptoPagoFondoReserva, $cargoemp, '90', intval($aniolaboral), $contratoemp);
                                                                    
                                                                    if($error):
                                                                    
                                                                        $errores [] = $error;
                                                                    
                                                                    endif;
                                                                
                                                                endif;
                                                        endif;
                                                        
                                                        //CALCULO PROVICIONES 
                                                        
                                                        //IECE, SECAP, APORTE PATRONAL 
                                                        
                                                        $error = $this->getProviciones($emp, $sueldo, $rolcab, $proviciones, $cargoemp);
                                                        
                                                        if($error):
                                                        
                                                        $errores [] = $error;
                                                        
                                                        endif;
                                                        
                                                        
                                                        //PROVICION DE VACACIONES 
                                                        $error = $this->getProvicionesVacaciones($emp, $sueldo, $rolcab, $conceptoProviVacaciones, $cargoemp);
                                                        
                                                        if($error):
                                                        
                                                        $errores [] = $error;
                                                        
                                                        endif;
                            
                                                endif;
                                                
                                                
                                                
                                        endif;
                                    else:
                                        $errores [] = array('mensaje'=> 'No se pudo liquidar la siguiente persona'.$emp->id_sys_rrhh_cedula.' Revisar parametros sueldo'.$faltas);
                                    endif;//sueldo
 
                            else:
                                
                                //Calcular Sueldo flotas
                                if($cargoemp->reg_ent_salida == 'N'  &&  $empleado->tipo_empleado  == 'T'):
                            
                                    //Marea
                                
                                    if($contratoemp->fecha_salida != null):
                            
                                        $sueldo  =  ($SueldoBasico/30) * $dias;
                            
                                    elseif($contratoemp->fecha_ingreso > $rolcab->fecha_ini && $contratoemp->fecha_ingreso  < $rolcab->fecha_fin):
                            
                                        $sueldo  =  ($SueldoBasico/30) * $dias;
                            
                                    else: 

                                        $sueldo  = $SueldoBasico;    
                            
                                    endif;

                                endif;
                    
                                //Sueldo de jefaturas 
                    
                                //Faltas 
                                if($faltas > 0):
                                
                        
                                    $faltas       =   $faltas * 2;
                                    $diasfaltas   =   $faltas;
                                    $dias         =   $dias   - $faltas;

                                    $descuentoFaltas       =   floatval(($valordia * $faltas));
                                    
                                endif;
                
                                //Permisos 
                                if($diaspermiso > 0):
                                
                            
                                    if($cargoemp->reg_ent_salida == 'N'):
                                    
                                        $sueldo           = $sueldoemp;
                                    
                                    endif;
                                
                                
                                    $dias         =    $dias - $diaspermiso;
                                    $descuentoPermisos =  floatval(($valordia * $diaspermiso));
                                    
                                
                                endif;
                
            
                                if($sueldo > 0):
                                            
                                    //Insertar Liquidacion dias 
                                    $this->AgregaLiquidacion(intval($rolcab->anio), intval($rolcab->mes), $emp->id_sys_rrhh_cedula, $dias, $faltas);
                                        

                                    //1.- insertamos sueldo
                                    $sueldopag = 0;
                                    
                                    //Subcidios 
                                    if($subcidio > 0):
                                            
                                        $sueldopag = number_format($sueldoemp, 2, '.', '') - number_format($subcidio, 2, '.', '');
                                        
                                    else:
                                        
                                        $sueldopag = $sueldoemp;   
                                        
                                    endif;
                                        

                                    $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, $conceptoSueldo->id_sys_rrhh_concepto, $rolcab->id_sys_empresa, $dias, $sueldopag, $cargoemp->id_sys_adm_departamento);
                                            
                                    if($newconcepto):

                                        if($descuentoFaltas > 0 && $descuentoPermisos > 0):

                                            $descuentoFaltas = $descuentoFaltas + $descuentoPermisos;

                                            $diasfaltas = $diasfaltas + $diaspermiso;

                                            $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, $conceptoDiasNL->id_sys_rrhh_concepto, $rolcab->id_sys_empresa, $diasfaltas, $descuentoFaltas, $cargoemp->id_sys_adm_departamento);

                                        elseif($descuentoFaltas > 0):
                                            
                                            $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, $conceptoDiasNL->id_sys_rrhh_concepto, $rolcab->id_sys_empresa, $diasfaltas, $descuentoFaltas, $cargoemp->id_sys_adm_departamento);
                                        
                                        elseif($descuentoPermisos > 0):

                                            $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, $conceptoDiasNL->id_sys_rrhh_concepto, $rolcab->id_sys_empresa, $diaspermiso, $descuentoPermisos, $cargoemp->id_sys_adm_departamento);

                                        endif;
                                        
                                        //1- HORAS EXTRAS
                                        if($cargoemp->reg_horas_extras == 'S'):
                                                
                                            $error  = $this->getCalculaHorasExtras($emp, $rolcab, $concepto25, $concepto50, $concepto100, $cargoemp, $valorhora);
                                        
                                            if($error):
                                                    
                                                $errores [] = $error;  
                                                    
                                            endif;
                                                        
                                        endif;
                                                
                                        //2- NOVEDADES 
                                        $error =  $this->getCalculaNovedades($emp, $rolcab, $cargoemp);
                                                
                                        if($error):
                                                
                                            $errores [] = $error;  
                                                
                                        endif;
                                                
                                        //HABERES Y DESCUENTOS 
                                                
                                        $error = $this->getCalculaHaberes($rolcab, $emp, $rolcab->periodo, $cargoemp);
                                                
                                        if($error):
                                                
                                            $errores [] = $error;  
            
                                        endif;     
                                                
                                        //INCENTIVO POR MAREA 
                                        if($cargoemp->reg_ent_salida == 'N'  &&  $empleado->tipo_empleado== 'T'):
                                                    
                                            $error  = $this->CalculaIncentivoMarea($rolcab, $empleado->id_sys_rrhh_cedula);
                                                    
                                            if($error):
                                                        
                                                $errores [] = $error;
                                                    
                                            endif;
                                                    
                                        endif;
                                                                            
                                        //9 PAGO DECIMOS 
                                        if($empleado->decimo == 'S'):
                                                
                                            $error = $this->getCalculaDecimoTer($emp, $sueldoemp, $rolcab, $conceptoPagodecimoTer, $cargoemp, $rolcab->periodo, $dias);
                                        
                                            if($error):
                                                        
                                                $errores [] = $error;  
                                                        
                                            endif;
                                                
                                            $error = $this->getCalculaDecimoCua($emp, $rolcab, $conceptoPagodecimoCua, $cargoemp, $contratoemp, $dias, $SueldoBasico, $rolcab->periodo);
                                                        
                                            if($error):
                                                        
                                                $errores [] = $error;
                                                        
                                            endif;
        
                                        endif;
                                                
                                        //FONDO DE RESERVA
                                        if($empleado->freserva == 'S' && $empleado->provision_freserva == 'S'):
                                                
                                            $aniolaboral = floatval( $this->getAnioLaboral($rolcab->fecha_fin, $empleado->id_sys_rrhh_cedula));
                                                            
                                            if($aniolaboral >= 1):
                                                            
                                                $error = $this->getCalculoFondoReserva($emp, $sueldoemp, $rolcab, $conceptoPagoFondoReserva, $cargoemp, $rolcab->periodo, intval($aniolaboral),$contratoemp);
                                                            
                                                if($error):
                                                                
                                                    $errores [] = $error;
                                                                
                                                endif;
                                                                
                                            endif; 

                                        endif;
                                                
                                            
                                        //CALCULO IESS
                                        if($contrato->provisiones == true):   
                                                
                                            $error = $this->getCalculoIess($emp, $sueldoemp, $rolcab, $conceptoIess, $cargoemp);
                                                
                                        else:
                                                
                                            $error = $this->getCalculoIess($emp, $sueldoemp, $rolcab, $conceptoIessPasantes, $cargoemp);
                                                
                                        endif;
                                                
                                        if($error):
                                                
                                            $errores [] = $error;
                                                
                                        endif;
                                                
                                        //DESCUENTOS HORAS NO LABORADAS
                                        $horasNoLaboradasComedor = $this->getHorasNoLaboradasComedor($emp->id_sys_rrhh_cedula, $rolcab->fecha_ini_liq, $rolcab->fecha_fin_liq);
                                                
                                                
                                        if ($horasNoLaboradasComedor > 0) :
                                                
                                            $horaspermiso = $horaspermiso + $horasNoLaboradasComedor;
                                                
                                        endif;
                                                
                                        if($horaspermiso > 0):
                                                
                                            $descuento     =    floatval($horaspermiso * $valorhora);
                                                
                                            $newconcepto   =    $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, 'DES_HORAS_NL', $rolcab->id_sys_empresa,  $horaspermiso, $descuento, $cargoemp->id_sys_adm_departamento);
                                                    
                                            if($newconcepto['estado'] == false):
                                                    
                                                $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                                                    
                                            endif;
                                                    
                                        endif;
                                                
                                        //ANTICIPO QUINCENA 
                                                
                                        $error = $this->getAnticipoFinMes($emp, $rolcab, $conceptoDesQuincena, $cargoemp);
                                                
                                        if($error):
                                                    
                                            $errores [] = $error;
                                                
                                        endif;
                                                
                                                
                                        //PRESTAMOS EMPRESAS 
                                                
                                        $error = $this->getCuotaPrestamoEmpresa($emp, $rolcab, $conceptoPrestOficina, $cargoemp);
                                                
                                        if($error):
                                                
                                            $errores [] = $error;
                                                
                                        endif;
                                                
                                        //IMPUESTO A LA RENTA 
                                        $error = $this->getImpuestoRenta($emp, $sueldoemp, $rolcab, $conceptoImpRenta, $cargoemp, $conceptoIess,$contratoemp);
                                                
                                        if($error):
                                                
                                            $errores [] = $error;
                                                
                                        endif;
        
                                        //CALCULO DE PROVICIONES 
                                        //GENERA PROVIVIONES DEPENDIENDO DEL TIPO DE CONTRATO
            
                                        if($contrato->provisiones == true):   
                                                
                                            //PROVICION DECIMOS 
                                                        
                                            if($empleado->decimo == 'N'):
                                                                
                                                $error = $this->getCalculaDecimoTer($emp, $sueldoemp, $rolcab, $conceptoProviDecimoTer, $cargoemp, '90',$dias);
                                                                
                                                if($error):
                                                                
                                                    $errores [] = $error;
                                                                
                                                endif;
                                                        
                                                $error = $this->getCalculaDecimoCua($emp, $rolcab, $conceptoProviDecimoCua, $cargoemp, $contratoemp, $dias, $SueldoBasico, '90');
                                                                
                                                if($error):
                                                                
                                                    $errores [] = $error;
                                                                
                                                endif;
                
                                            endif;
                                                        
                                            //PROVICION FONDOS DE RESERVA 
                                            if($empleado->freserva == 'N' && $empleado->provision_freserva == 'S'):
                                                                    
                                                //validamos si el empleador cumple el año de trabajo
                                                $aniolaboral =  floatval( $this->getAnioLaboral($rolcab->fecha_fin, $emp->id_sys_rrhh_cedula));
                                                                    
                                                if($aniolaboral >= 1):
                                                                    
                                                    $error = $this->getCalculoFondoReserva($emp, $sueldoemp, $rolcab, $conceptoPagoFondoReserva, $cargoemp, '90', intval($aniolaboral), $contratoemp);
                                                                        
                                                    if($error):
                                                                        
                                                        $errores [] = $error;
                                                                        
                                                    endif;
                                                                    
                                                endif;

                                            endif;
                                                        
                                            //CALCULO PROVICIONES 
                                                            
                                            //IECE, SECAP, APORTE PATRONAL 
                                                            
                                            $error = $this->getProviciones($emp, $sueldoemp, $rolcab, $proviciones, $cargoemp);
                                                            
                                            if($error):
                                                            
                                                $errores [] = $error;
                                                            
                                            endif;
                                                        
                                                        
                                            //PROVICION DE VACACIONES 
                                            $error = $this->getProvicionesVacaciones($emp, $sueldoemp, $rolcab, $conceptoProviVacaciones, $cargoemp);
                                                            
                                            if($error):
                                                            
                                                $errores [] = $error;
                                                            
                                            endif;
                            
                                        endif;
                                                    
                                    endif;

                                else:
                                        
                                    $errores [] = array('mensaje'=> 'No se pudo liquidar la siguiente persona'.$emp->id_sys_rrhh_cedula.' Revisar parametros sueldo'.$faltas);
                                    
                                endif;//sueldo

                            endif;

                                  
                        else:
                         
                            //Calcular sueldo en base fecha salida
                              
                            if($contratoemp->fecha_ingreso > $rolcab->fecha_ini):
                                 
                                $date1     = new \DateTime($contratoemp->fecha_ingreso);
                                $date2     = new \DateTime($contratoemp->fecha_salida);
                                $diff      = $date1->diff($date2);
  
                            else:
                                     
                                $date1     = new \DateTime($rolcab->fecha_ini);
                                $date2     = new \DateTime($contratoemp->fecha_salida);
                                $diff      = $date1->diff($date2);
                                 
                            endif;
                                 
                            // will output 2 days
                            $dias      = intval($diff->days) + 1;
                            $sueldo    =  ($sueldoemp/30) * $dias;

                            //Calcular Sueldo flotas
                            if($cargoemp->reg_ent_salida == 'N'  &&  $empleado->tipo_empleado  == 'T'):
                           
                                //Marea
                            
                                if($contratoemp->fecha_salida != null):
                           
                                    $sueldo  =  ($SueldoBasico/30) * $dias;
                           
                                elseif($contratoemp->fecha_ingreso > $rolcab->fecha_ini && $contratoemp->fecha_ingreso  < $rolcab->fecha_fin):
                           
                                    $sueldo  =  ($SueldoBasico/30) * $dias;
                           
                                else: 
                              
                                    $sueldo  = $SueldoBasico;    
                           
                                endif;

                            endif;
                
                            //Sueldo de jefaturas 
                
                            //Faltas 
                            if($faltas > 0):
                            
                    
                                $faltas       =   $faltas * 2;
                                $dias         =   $dias   - $faltas;
                                $sueldo       =   $sueldo -  floatval(($valordia * $faltas));
                                
                            endif;
               
                            //Permisos 
                            if($diaspermiso > 0):

                                if($cargoemp->reg_ent_salida == 'N'):
                                
                                    $sueldo           = $sueldoemp;
                                
                                endif;
               
                                $dias         =    $dias - $diaspermiso;
                                $sueldo       =    $sueldo -   floatval(($valordia * $diaspermiso));
   
                            endif;
               
          
                            if($sueldo > 0):
                        
                                //Insertar Liquidacion dias 
                                $this->AgregaLiquidacion(intval($rolcab->anio), intval($rolcab->mes), $emp->id_sys_rrhh_cedula, $dias, $faltas);
                                
                                //1.- insertamos sueldo
                                $sueldopag = 0;
                
                                //Subcidios 
                                if($subcidio > 0):
                                    
                                    $sueldopag = number_format($sueldo, 2, '.', '') - number_format($subcidio, 2, '.', '');
                                
                                else:
                                
                                    $sueldopag = $sueldo;   
                                
                                endif;
                      

                                $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, $conceptoSueldo->id_sys_rrhh_concepto, $rolcab->id_sys_empresa, $dias, $sueldopag, $cargoemp->id_sys_adm_departamento);
                                    
                                if($newconcepto):
                                
                                    //1- HORAS EXTRAS
                                    if($cargoemp->reg_horas_extras == 'S'):
                                        
                                        $error  = $this->getCalculaHorasExtras($emp, $rolcab, $concepto25, $concepto50, $concepto100, $cargoemp, $valorhora);
                                
                                        if($error):
                                            
                                            $errores [] = $error;  
                                            
                                        endif;

                                    endif;
                                        
                                    //2- NOVEDADES 
                                    $error =  $this->getCalculaNovedades($emp, $rolcab, $cargoemp);
                                        
                                    if($error):
                                        
                                        $errores [] = $error;  
                                        
                                    endif;
                                        
                                    //HABERES Y DESCUENTOS 
                                        
                                    $error = $this->getCalculaHaberes($rolcab, $emp, $rolcab->periodo, $cargoemp);
                                        
                                    if($error):
                                        
                                        $errores [] = $error;  

                                    endif;
                                        
                                        
                                    //INCENTIVO POR MAREA 
                                    if($cargoemp->reg_ent_salida == 'N'  &&  $empleado->tipo_empleado== 'T'):
                                            
                                        $error  = $this->CalculaIncentivoMarea($rolcab, $empleado->id_sys_rrhh_cedula);
                                            
                                        if($error):
                                            
                                            $errores [] = $error;
                                        
                                        endif;
                                            
                                    endif;
                                                                    
                                    //9 PAGO DECIMOS 
                                    if($empleado->decimo == 'S'):
                                        
                                        $error = $this->getCalculaDecimoTer($emp, $sueldo, $rolcab, $conceptoPagodecimoTer, $cargoemp, $rolcab->periodo, $dias);
                                
                                        if($error):
                                                
                                            $errores [] = $error;  
                                                
                                        endif;
                                        
                                        $error = $this->getCalculaDecimoCua($emp, $rolcab, $conceptoPagodecimoCua, $cargoemp, $contratoemp, $dias, $SueldoBasico, $rolcab->periodo);
                                                
                                        if($error):
                                                
                                            $errores [] = $error;
                                                
                                        endif;
       
                                    endif;
                                        
                                    //FONDO DE RESERVA
                                    if($empleado->freserva == 'S' && $empleado->provision_freserva == 'S'):
                                        
                                        $aniolaboral = floatval( $this->getAnioLaboral($rolcab->fecha_fin, $empleado->id_sys_rrhh_cedula));
                                                    
                                        if($aniolaboral >= 1):
                                                    
                                            $error = $this->getCalculoFondoReserva($emp, $sueldo, $rolcab, $conceptoPagoFondoReserva, $cargoemp, $rolcab->periodo, intval($aniolaboral),$contratoemp);
                                                    
                                            if($error):
                                                        
                                                $errores [] = $error;
                                                        
                                            endif;
                                                        
                                        endif;

                                    endif;

                                    //CALCULO IESS
                                    if($contrato->provisiones == true):   
                                        
                                        $error = $this->getCalculoIess($emp, $sueldo, $rolcab, $conceptoIess, $cargoemp);
                                        
                                    else:
                                        
                                        $error = $this->getCalculoIess($emp, $sueldo, $rolcab, $conceptoIessPasantes, $cargoemp);
                                        
                                    endif;
                                        
                                    if($error):
                                        
                                        $errores [] = $error;
                                        
                                    endif;
                                        
                                    //DESCUENTOS HORAS NO LABORADAS
                                    $horasNoLaboradasComedor = $this->getHorasNoLaboradasComedor($emp->id_sys_rrhh_cedula, $rolcab->fecha_ini_liq, $rolcab->fecha_fin_liq);

                                    if ($horasNoLaboradasComedor > 0) :
                                        
                                        $horaspermiso = $horaspermiso + $horasNoLaboradasComedor;
                                        
                                    endif;

                                    if($horaspermiso > 0):
                                        
                                        $descuento     =    floatval($horaspermiso * $valorhora);
                                        
                                        $newconcepto   =    $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, 'DES_HORAS_NL', $rolcab->id_sys_empresa,  $horaspermiso, $descuento, $cargoemp->id_sys_adm_departamento);
                                            
                                        if($newconcepto['estado'] == false):
                                            
                                            $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                                            
                                        endif;
                                            
                                    endif;
                                        
                                    //ANTICIPO QUINCENA 
                                        
                                    $error = $this->getAnticipoFinMes($emp, $rolcab, $conceptoDesQuincena, $cargoemp);
                                        
                                    if($error):
                                            
                                        $errores [] = $error;
                                        
                                    endif;
                                        
                                    //PRESTAMOS EMPRESAS 
                                        
                                    $error = $this->getCuotaPrestamoEmpresa($emp, $rolcab, $conceptoPrestOficina, $cargoemp);
                                        
                                    if($error):
                                        
                                        $errores [] = $error;
                                        
                                    endif;
                                        
                                    //IMPUESTO A LA RENTA 
                                    $error = $this->getImpuestoRenta($emp, $sueldoemp, $rolcab, $conceptoImpRenta, $cargoemp, $conceptoIess,$contratoemp);
                                        
                                    if($error):
                                        
                                        $errores [] = $error;
                                        
                                    endif;
                                        
                                        
                                    //CALCULO DE PROVICIONES 
                                    //GENERA PROVIVIONES DEPENDIENDO DEL TIPO DE CONTRATO
                                        
                                        
                                    if($contrato->provisiones == true):   
                                        
                                        //PROVICION DECIMOS 
                                                
                                        if($empleado->decimo == 'N'):
                                                        
                                            $error = $this->getCalculaDecimoTer($emp, $sueldo, $rolcab, $conceptoProviDecimoTer, $cargoemp, '90',$dias);
                                                        
                                            if($error):
                                                        
                                                $errores [] = $error;
                                                        
                                            endif;
                                                
                                            $error = $this->getCalculaDecimoCua($emp, $rolcab, $conceptoProviDecimoCua, $cargoemp, $contratoemp, $dias, $SueldoBasico, '90');
                                                        
                                            if($error):
                                                        
                                                $errores [] = $error;
                                                        
                                            endif;
                                                                
                                                
                                        endif;
                                                
                                        //PROVICION FONDOS DE RESERVA 
                                        if($empleado->freserva == 'N' && $empleado->provision_freserva == 'S'):
                                                        
                                            //validamos si el empleador cumple el año de trabajo
                                            $aniolaboral =  floatval( $this->getAnioLaboral($rolcab->fecha_fin, $emp->id_sys_rrhh_cedula));
                                                        
                                            if($aniolaboral >= 1):
                                                        
                                                $error = $this->getCalculoFondoReserva($emp, $sueldo, $rolcab, $conceptoPagoFondoReserva, $cargoemp, '90', intval($aniolaboral), $contratoemp);
                                                            
                                                if($error):
                                                            
                                                    $errores [] = $error;
                                                            
                                                endif;
                                                        
                                            endif;
                                        
                                        endif;
                                                
                                        //CALCULO PROVICIONES 
                                                
                                        //IECE, SECAP, APORTE PATRONAL 
                                                
                                        $error = $this->getProviciones($emp, $sueldo, $rolcab, $proviciones, $cargoemp);
                                                
                                        if($error):
                                                
                                            $errores [] = $error;
                                                
                                        endif;
                                                
                                                
                                        //PROVICION DE VACACIONES 
                                        $error = $this->getProvicionesVacaciones($emp, $sueldo, $rolcab, $conceptoProviVacaciones, $cargoemp);
                                                
                                        if($error):
                                                
                                            $errores [] = $error;
                                                
                                        endif;
                    
                                    endif;
                                        
                                endif;

                            else:

                                $errores [] = array('mensaje'=> 'No se pudo liquidar la siguiente persona'.$emp->id_sys_rrhh_cedula.' Revisar parametros sueldo'.$faltas);
                            
                            endif;//sueldo
                                
                        endif;
                        
                    endif;
                
                endif;
              
         endforeach;
         
         
         
        
      return $errores;
  
        
    }
     
    private function LiquidaDecimoTercero($rolcab, $empleados){
        
       
        $errores = [];
        $decimo_ter     = 0;
        $dias           = 0;
        $valorProvision = 0;
        $diasLaborados = 0;
        
        $anioIni        = 0;
        $mesIni         = 0;
        $anioFin        = 0;
        $mesFin         = 0;
        $anioIng        = 0;
        $mesIng         = 0;
       
        $db = trim($_SESSION['db']);
        
        /*$dbMysql = "";
        
        if($db == "DB_GestionTalleres"):
        
             $dbMysql = "db_tallermarsa";
        
        elseif($db == "DB_GestionMarPesca"):
        
             $dbMysql = "db_marpesca";
        
        elseif($db == "DB_GestionFletatun"):
        
             $dbMysql = "db_buques";
        
        endif;
        
        if($dbMysql != ""):
        
            $conexion = $this->getConexionPBS($dbMysql);
        
        endif;*/
        
        foreach ($empleados as $emp):
       
                $decimo_ter     = 0;
                $valorProvision = 0;
                $diasLaborados = 0;
                $dias           = 0;
        
                $rolmov  =  SysRrhhEmpleadosRolMov::find()->where(['anio'=> $rolcab->anio])->andWhere(['mes'=> $rolcab->mes])->andWhere(['periodo'=> $rolcab->periodo])->andWhere(['id_sys_empresa'=>$rolcab->id_sys_empresa])->andWhere(['estado'=> 'P'])->andWhere(['id_sys_rrhh_cedula'=> $emp->id_sys_rrhh_cedula])->one();
                
                //Si no está procesado liquidamos el rol del empleador
                
                if(!$rolmov):
               
                        //eliminamos el rol de detalle del empleador
                        Yii::$app->$db->createCommand("delete  FROM sys_rrhh_empleados_rol_mov where anio = '{$rolcab->anio}' and mes = '{$rolcab->mes}' and periodo = '{$rolcab->periodo}' and id_sys_rrhh_cedula = '{$emp->id_sys_rrhh_cedula}'")->execute();
                        
                       
                        //Obtenemos datos del empleador
                        $empleado     = SysRrhhEmpleados::find()
                        ->where(['id_sys_rrhh_cedula'=> $emp->id_sys_rrhh_cedula])
                        ->andWhere(['id_sys_empresa'=> $rolcab->id_sys_empresa])
                        ->andWhere(['estado'=> 'A'])
                        ->one(SysRrhhEmpleados::getDb());
    
                        
                        //obtenemos el contrato del empleador
                        $contratoemp  =  SysRrhhEmpleadosContratos::find()
                        ->where(['id_sys_rrhh_cedula'=> $empleado->id_sys_rrhh_cedula])
                        ->andWhere(['id_sys_empresa'=> $rolcab->id_sys_empresa])
                        ->orderBy(['id_sys_rrhh_empleados_contrato_cod' => SORT_DESC])
                        ->one(SysRrhhEmpleados::getDb());
                        
                        
                        //obtenemos el cargo del empleador
                        $cargoemp     =  SysAdmCargos::find()
                        ->where(['id_sys_adm_cargo'=> $empleado->id_sys_adm_cargo])
                        ->andWhere(['id_sys_empresa'=> $rolcab->id_sys_empresa])
                        ->one(SysRrhhEmpleados::getDb());
                        
                        
                        //verificamos si empleador esta activo
                        if($contratoemp->fecha_salida == null):
                        
                       
                       
                                              
                                    $anioIni        = date('Y', strtotime($rolcab->fecha_ini_liq));
                                    $mesIni         = date('n', strtotime($rolcab->fecha_ini_liq));
                                    $anioFin        = date('Y', strtotime($rolcab->fecha_fin_liq));
                                    $mesFin         = date('n', strtotime($rolcab->fecha_fin_liq));
                                    $anioIng        =  date('Y', strtotime($contratoemp->fecha_ingreso)); 
                                    $mesIng         = date('n', strtotime($contratoemp->fecha_ingreso)); 
                        
                                    
                                    if($anioIng <= $anioIni):
                                    
                                    
                                     
                                             //Provisiones anio anterior 
                                             $valorProvision =   $valorProvision +  (new \yii\db\Query())
                                             ->select(["sum(valor)"])
                                             ->from("sys_rrhh_empleados_rol_mov")
                                             ->where("id_sys_rrhh_concepto = 'DECIMO_TERCERO'")
                                             ->andwhere("id_sys_rrhh_cedula = '{$empleado->id_sys_rrhh_cedula}'")
                                             ->andwhere("anio = {$anioIni}")
                                             ->andwhere("mes >= {$mesIni}")
                                             ->scalar(SysRrhhEmpleados::getDb());
                                             
                                             $diasLaborados =   $diasLaborados +  (new \yii\db\Query())
                                             ->select(["sum(cantidad)"])
                                             ->from("sys_rrhh_empleados_rol_mov")
                                             ->where("id_sys_rrhh_concepto = 'SUELDO'")
                                             ->andwhere("id_sys_rrhh_cedula = '{$empleado->id_sys_rrhh_cedula}'")
                                             ->andwhere("anio = {$anioIni}")
                                             ->andwhere("mes >= {$mesIni}")
                                             ->scalar(SysRrhhEmpleados::getDb());
                                             
                    
                                            
                                             //Provisiones anio actual 
                                             $valorProvision =   $valorProvision +  (new \yii\db\Query())
                                             ->select(["sum(valor)"])
                                             ->from("sys_rrhh_empleados_rol_mov")
                                             ->where("id_sys_rrhh_concepto = 'DECIMO_TERCERO'")
                                             ->andwhere("id_sys_rrhh_cedula = '{$empleado->id_sys_rrhh_cedula}'")
                                             ->andwhere("anio = {$anioFin}")
                                             ->andwhere("mes <= {$mesFin}")
                                             ->scalar(SysRrhhEmpleados::getDb());
                                             
                                             $diasLaborados =   $diasLaborados +  (new \yii\db\Query())
                                             ->select(["sum(cantidad)"])
                                             ->from("sys_rrhh_empleados_rol_mov")
                                             ->where("id_sys_rrhh_concepto = 'SUELDO'")
                                             ->andwhere("id_sys_rrhh_cedula = '{$empleado->id_sys_rrhh_cedula}'")
                                             ->andwhere("anio = {$anioFin}")
                                             ->andwhere("mes <= {$mesFin}")
                                             ->scalar(SysRrhhEmpleados::getDb());
                          
                                                 
                                             
                                             
                                             
                                     else:
                                       
                                          if($anioIng == $anioFin):
                                          
                                            //Provisiones anio actual
                                             $valorProvision =   $valorProvision +  (new \yii\db\Query())
                                             ->select(["sum(valor)"])
                                             ->from("sys_rrhh_empleados_rol_mov")
                                             ->where("id_sys_rrhh_concepto = 'DECIMO_TERCERO'")
                                             ->andwhere("id_sys_rrhh_cedula = '{$empleado->id_sys_rrhh_cedula}'")
                                             ->andwhere("anio = {$anioFin}")
                                             ->andwhere("mes >= {$mesIng} and mes <= {$mesFin}")
                                             ->scalar(SysRrhhEmpleados::getDb());
                                             
                                             $diasLaborados =   $diasLaborados +  (new \yii\db\Query())
                                             ->select(["sum(cantidad)"])
                                             ->from("sys_rrhh_empleados_rol_mov")
                                             ->where("id_sys_rrhh_concepto = 'SUELDO'")
                                             ->andwhere("id_sys_rrhh_cedula = '{$empleado->id_sys_rrhh_cedula}'")
                                             ->andwhere("anio = {$anioFin}")
                                             ->andwhere("mes >= {$mesIng} and mes <= {$mesFin}")
                                             ->scalar(SysRrhhEmpleados::getDb());
                                          
                                          
                                          endif;
                                          
                                     
                                     endif;
                                     
                        
                         
                         
                         if($diasLaborados > 360):
                         
                             $dias = 360;
                         
                         else:
                            
                                 $provicionesAux = (new \yii\db\Query())
                                 ->select(["count(*)"])
                                 ->from("sys_rrhh_empleados_rol_liq_aux")
                                 ->where("id_sys_rrhh_concepto = 'SUELDO'")
                                 ->andwhere("id_sys_rrhh_cedula = '{$empleado->id_sys_rrhh_cedula}'")
                                 ->scalar(SysRrhhEmpleados::getDb());
                         
                         
                                  if ($provicionesAux > 0):
                                  
                                  
                                     $diasLaborados   =  $diasLaborados + Yii::$app->$db->createCommand("select isnull(sum(cantidad),0) from [dbo].[sys_rrhh_empleados_rol_liq_aux] where anio = '{$anioIni}' and  mes >= {$mesIni} and id_sys_rrhh_concepto = 'SUELDO' and periodo = 2 and id_sys_rrhh_cedula = '{$empleado->id_sys_rrhh_cedula}'")->queryScalar();
                                  
                                     $diasLaborados   =  $diasLaborados + Yii::$app->$db->createCommand("select isnull(sum(cantidad),0) from [dbo].[sys_rrhh_empleados_rol_liq_aux] where anio = '{$anioFin}' and  mes <= {$mesFin} and id_sys_rrhh_concepto = 'SUELDO' and periodo = 2 and id_sys_rrhh_cedula = '{$empleado->id_sys_rrhh_cedula}'")->queryScalar();
                                     
                                     $valorProvision  =  $valorProvision + Yii::$app->$db->createCommand("select isnull(sum(valor),0) from [dbo].[sys_rrhh_empleados_rol_liq_aux] where anio = '{$anioIni}' and  mes >= {$mesIni} and id_sys_rrhh_concepto = 'DECIMO_TERCERO' and periodo = 90 and id_sys_rrhh_cedula = '{$empleado->id_sys_rrhh_cedula}'")->queryScalar();
                                     
                                     $valorProvision  =  $valorProvision + Yii::$app->$db->createCommand("select isnull(sum(valor),0) from [dbo].[sys_rrhh_empleados_rol_liq_aux] where anio = '{$anioFin}' and  mes <= {$mesFin} and id_sys_rrhh_concepto = 'DECIMO_TERCERO' and periodo = 90 and id_sys_rrhh_cedula = '{$empleado->id_sys_rrhh_cedula}'")->queryScalar();
                                     
                                    
                                  endif;
                                  
                                  
                                  if ($diasLaborados > 360):
    
                                    $dias = 360;
                                  
                                  endif;
                                  
                                  $dias = $diasLaborados;
                         
                         endif;
                         
                             $decimo_ter = floatval($valorProvision);
                        
                             //verificamos el valor del decimo       
                              if($decimo_ter > 0):
                              
                                      //insertamos el valor del decimo Tercero
                                      $newconcepto =    $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, 'PAGO_DECIMO_TER', $rolcab->id_sys_empresa,  $dias, $decimo_ter, $cargoemp->id_sys_adm_departamento);
                                      
                                      if($newconcepto['estado'] == false):
                                      
                                              $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                                      
                                      endif;
                              
                                      //haberes y descuentos con iess ;
                              
                              
                              endif;//decimo
                              
                              //6. Inserta Haberes y Descuentos
                              $haberes  = $this->getHaberes($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, '2');
                              
                              if($haberes):
                              
                                      foreach ($haberes as $haber):
                                      
                                             if($haber['decimo'] == 'S'):
                                             
                                                 $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, $haber['id_sys_rrhh_concepto'], $rolcab->id_sys_empresa,  '1', $haber['cantidad'], $cargoemp->id_sys_adm_departamento);
                                                 
                                                 if($newconcepto['estado'] == false):
                                                 
                                                        $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                                                 
                                                 endif;
                                             
                                             endif;
                                      
                                      endforeach; //recorrido haberes
                              
                              endif;
                              
                        endif;
                        
                  endif;
                  
                  
        endforeach;
        
        
        /*if($dbMysql != ""):
        
              $conexion->close();
        
        endif;*/
        
 
        return $errores;
        
        
    }

    private function LiquidarDecimoCuarto($rolcab, $empleados){
        
       
        $errores      = [];
        $dias         = 0;
        $decimo       = 0;
        $rol          =  intval($rolcab->anio."".str_pad($rolcab->mes, 2, "0", STR_PAD_LEFT));
        $SueldoBasico = SysRrhhConceptos::find()->select('valor')->where(['concepto_sueldo'=> 'SU'])->andwhere(['pago'=> '2'])->andWhere(['>', 'valor', '0'])->andWhere(['tipo_valor'=> 'V'])->scalar();
        
         $db           = $_SESSION['db'];
        
        foreach ($empleados as $emp):
        
               
                $rolmov  =  SysRrhhEmpleadosRolMov::find()->where(['anio'=> $rolcab->anio])->andWhere(['mes'=> $rolcab->mes])->andWhere(['periodo'=> $rolcab->periodo])->andWhere(['id_sys_empresa'=>$rolcab->id_sys_empresa])->andWhere(['estado'=> 'P'])->andWhere(['id_sys_rrhh_cedula'=> $emp->id_sys_rrhh_cedula])->one();
                
                //Si no está procesado liquidamos el rol del empleado
                if(!$rolmov):
        
                    //eliminamos el rol de detalle del empleador
                    Yii::$app->$db->createCommand("delete  FROM sys_rrhh_empleados_rol_mov where anio = '{$rolcab->anio}' and mes = '{$rolcab->mes}' and periodo = '{$rolcab->periodo}' and id_sys_rrhh_cedula = '{$emp->id_sys_rrhh_cedula}'")->execute();
                    
                endif;
       
                                //Obtenemos datos del empleador
                                $empleado     = SysRrhhEmpleados::find()
                                ->where(['id_sys_rrhh_cedula'=> $emp->id_sys_rrhh_cedula])
                                ->andWhere(['estado'=> 'A'])
                                ->one();
                                

                                //obtenemos el cargo del empleador
                                $cargoemp     =  SysAdmCargos::find()
                                ->where(['id_sys_adm_cargo'=> $empleado->id_sys_adm_cargo])
                                ->andWhere(['id_sys_empresa'=> $rolcab->id_sys_empresa])
                                ->one();

                                //obtenemos contrato del empleador
                                $contratoemp  =  SysRrhhEmpleadosContratos::find()
                                ->where(['id_sys_rrhh_cedula'=> $empleado->id_sys_rrhh_cedula])
                                ->andWhere(['id_sys_empresa'=> $rolcab->id_sys_empresa])
                                ->orderBy(['id_sys_rrhh_empleados_contrato_cod' => SORT_DESC])
                                ->one(SysRrhhEmpleados::getDb());
                                
                                $mesIniLiq =  date('m', strtotime($rolcab->fecha_ini_liq));
                                $anioIniLiq = date('Y', strtotime($rolcab->fecha_ini_liq));
                                
                                $mesFinLiq =  date('m', strtotime($rolcab->fecha_fin_liq));
                                $anioFinLiq = date('Y', strtotime($rolcab->fecha_fin_liq));
                                
                                $decimo  = 0;
                                $dias    = 0;

                                if($contratoemp->fecha_ingreso <= $rolcab->fecha_fin_liq):
                                
                                    $decimo =  Yii::$app->$db->createCommand("select isnull(sum(valor),0) from [dbo].[sys_rrhh_empleados_rol_mov] where anio = '{$anioIniLiq}' and  mes >= {$mesIniLiq} and id_sys_rrhh_concepto = 'DECIMO_CUARTO' and periodo = 90 and id_sys_rrhh_cedula = '{$emp->id_sys_rrhh_cedula}'")->queryScalar();
                                    
                                    $dias   =  Yii::$app->$db->createCommand("select isnull(sum(cantidad),0) from [dbo].[sys_rrhh_empleados_rol_mov] where anio = '{$anioIniLiq}' and  mes >= {$mesIniLiq} and id_sys_rrhh_concepto = 'DECIMO_CUARTO' and periodo = 90 and id_sys_rrhh_cedula = '{$emp->id_sys_rrhh_cedula}'")->queryScalar();
                                    
                                    $decimo =  $decimo +  Yii::$app->$db->createCommand("select isnull(sum(valor),0) from [dbo].[sys_rrhh_empleados_rol_mov] where anio = '{$anioFinLiq}' and  mes <= {$mesFinLiq} and id_sys_rrhh_concepto = 'DECIMO_CUARTO' and periodo = 90 and id_sys_rrhh_cedula = '{$emp->id_sys_rrhh_cedula}'")->queryScalar();
                                    
                                    $dias =    $dias +  Yii::$app->$db->createCommand("select isnull(sum(cantidad),0) from [dbo].[sys_rrhh_empleados_rol_mov] where anio = '{$anioFinLiq}' and  mes <= {$mesFinLiq} and id_sys_rrhh_concepto = 'DECIMO_CUARTO' and periodo = 90 and id_sys_rrhh_cedula = '{$emp->id_sys_rrhh_cedula}'")->queryScalar();
                                
                                endif;
                                
                                if($dias == 360):
                                
                                    $decimo = $SueldoBasico;  
                               
                                else :
                                  
                                        $provicionesAux = (new \yii\db\Query())
                                        ->select(["count(*)"])
                                        ->from("sys_rrhh_empleados_rol_liq_aux")
                                        ->where("id_sys_rrhh_concepto = 'SUELDO'")
                                        ->andwhere("id_sys_rrhh_cedula = '{$empleado->id_sys_rrhh_cedula}'")
                                        ->scalar(SysRrhhEmpleados::getDb());
                                        
                                        if ($provicionesAux > 0) :
                                        
                                            $decimo =  $decimo +  Yii::$app->$db->createCommand("select isnull(sum(valor),0) from [dbo].[sys_rrhh_empleados_rol_liq_aux] where anio = '{$anioIniLiq}' and  mes >= {$mesIniLiq} and id_sys_rrhh_concepto = 'DECIMO_CUARTO' and periodo = 90 and id_sys_rrhh_cedula = '{$emp->id_sys_rrhh_cedula}'")->queryScalar();
                                            
                                            $dias   =  $dias + Yii::$app->$db->createCommand("select isnull(sum(cantidad),0) from [dbo].[sys_rrhh_empleados_rol_liq_aux] where anio = '{$anioIniLiq}' and  mes >= {$mesIniLiq} and id_sys_rrhh_concepto = 'DECIMO_CUARTO' and periodo = 90 and id_sys_rrhh_cedula = '{$emp->id_sys_rrhh_cedula}'")->queryScalar();
                                            
                                            $decimo =  $decimo +  Yii::$app->$db->createCommand("select isnull(sum(valor),0) from [dbo].[sys_rrhh_empleados_rol_liq_aux] where anio = '{$anioFinLiq}' and  mes <= {$mesFinLiq} and id_sys_rrhh_concepto = 'DECIMO_CUARTO' and periodo = 90 and id_sys_rrhh_cedula = '{$emp->id_sys_rrhh_cedula}'")->queryScalar();
                                            
                                            $dias =    $dias +  Yii::$app->$db->createCommand("select isnull(sum(cantidad),0) from [dbo].[sys_rrhh_empleados_rol_liq_aux] where anio = '{$anioFinLiq}' and  mes <= {$mesFinLiq} and id_sys_rrhh_concepto = 'DECIMO_CUARTO' and periodo = 90 and id_sys_rrhh_cedula = '{$emp->id_sys_rrhh_cedula}'")->queryScalar();
                                            
                                        endif;
                                        
                                        if ($dias == 360):
                                        
                                            $decimo = $SueldoBasico;  
                                        
                                        else:
                                        
                                            //Validar Proviciones
                                            
                                        
                                        
                                        
                                        
                                        
                                        
                                        
                                            $decimo = ($dias * $SueldoBasico) / 360;
                                        
                                        endif;
           
                                endif;
                               
                                
                                    //insertar concepto
                                    if($decimo > 0):
                                    
                                            
                                            //insertamos el valor del decimo Tercero
                                            $newconcepto =    $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, 'PAGO_DECIMO_CUA', $rolcab->id_sys_empresa,  $dias, $decimo, $cargoemp->id_sys_adm_departamento);
                                            
                                            if($newconcepto['estado'] == false):
                                            
                                                 $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                                            
                                            endif;
                                            
                                            
                                            //6. Inserta Haberes y Descuentos
                                            $haberes  = $this->getHaberes($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, '2');
                                            
                                            if($haberes):
                                            
                                                  foreach ($haberes as $haber):
                                                                
                                                        if($haber['decimo'] == 'S'):
                                                        
                                                                $inihab = intval($haber['anio_ini']."".str_pad($haber['mes_ini'], 2, "0", STR_PAD_LEFT));
                                            
                                                                if($rol >= $inihab):
                                                                
                                                                  $finhab = intval($haber['anio_fin']."".str_pad($haber['mes_fin'], 2, "0", STR_PAD_LEFT));
                                                                
                                                                 
                                                                  if($rol <=  $finhab ||  intval($finhab) == 0):
                                                                   
                                                                          
                                                                          $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, $haber['id_sys_rrhh_concepto'], $rolcab->id_sys_empresa,  '1', $haber['cantidad'], $cargoemp->id_sys_adm_departamento);
                                                                           
                                                                           if($newconcepto['estado'] == false):
                                                                               
                                                                               $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                                                                               break;
                                                                           
                                                                           endif;
                                                                   
                                                                   endif;
                                                                  
                                                                   
                                                                endif;
                                                        
                                                        endif;
                                                
                                                  endforeach; //recorrido haberes
                                            
                                            endif;
                                     
                               endif;
                  
       endforeach; 
        
       return $errores;
        
    }
    
    //Proviciones  IECE , SECAP, APORTE PATRONAL
    private function getProviciones($emp, $sueldo, $rolcab, $proviciones, $cargoemp){
        
        $errores = [];
        
        $valor    = 0;
        
        if($proviciones):
        
            $novedadesAportaIess = $this->getNovedadesAportaIess($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $rolcab->anio, $rolcab->mes, $rolcab->periodo);
            
            $sueldo = $sueldo + $novedadesAportaIess;
        
        
             foreach ($proviciones as $provicion):
         
                 $valor = floatval($sueldo) * floatval($provicion->valor)/100;
             
                 if($valor > 0):
                 
                     $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, '90', $emp->id_sys_rrhh_cedula, $provicion->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  '1', $valor, $cargoemp->id_sys_adm_departamento);
                     
                     if($newconcepto['estado'] == false):
                     
                        $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                 
                     endif;
                 
                 endif;
             
             
             endforeach;
        
        endif;
        
      return $errores;
    }
 
    private function getProvicionesVacaciones($emp, $sueldo, $rolcab, $vacaciones, $cargoemp){
       
        $errores = [];
        
        $valor    = 0;
        
        if($vacaciones):
        
                $novedadesAportaIess = $this->getNovedadesAportaIess($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $rolcab->anio, $rolcab->mes, $rolcab->periodo);
                
                $sueldo = $sueldo + $novedadesAportaIess;
                
                $valor = floatval($sueldo / intval($vacaciones->valor));
                
                if($valor):
                
                     $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, '90', $emp->id_sys_rrhh_cedula, $vacaciones->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  '1', $valor, $cargoemp->id_sys_adm_departamento);
                    
                    if($newconcepto['estado'] == false):
                    
                      $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                    
                    endif;
                    
                endif;
        
        
        endif;
        
        return $errores;
        
    }
    
    //proviciones 
    
    private function getCalculaHaberes($rolcab, $emp, $periodo, $cargoemp){
        
        $errores = [];
        
        $inihab  =  0;
        $rol     =  0;
        $finhab  =  0;
        
        $rol = intval($rolcab->anio."".str_pad($rolcab->mes, 2, "0", STR_PAD_LEFT));
        
        //6. Inserta Haberes y Descuentos
        $haberes  = $this->getHaberes($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $periodo);
        
        if($haberes):
        
                foreach ($haberes as $haber):
                   
                
                          $inihab = intval($haber['anio_ini']."".str_pad($haber['mes_ini'], 2, "0", STR_PAD_LEFT));
                
                          if($rol >= $inihab):
                          
                             $finhab = intval($haber['anio_fin']."".str_pad($haber['mes_fin'], 2, "0", STR_PAD_LEFT));
                          
                             
                             if($finhab > 0):
                           
                                     if($rol <=  $finhab):
                                     
                                             $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, $haber['id_sys_rrhh_concepto'], $rolcab->id_sys_empresa,  '1', $haber['cantidad'], $cargoemp->id_sys_adm_departamento);
                                             
                                             if($newconcepto['estado'] == false):
                                                 
                                                 $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                                                 break;
                                             
                                             endif;
                                     
                                     endif;
                                     
                             elseif($finhab == 0) :
                             
                                         $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, $haber['id_sys_rrhh_concepto'], $rolcab->id_sys_empresa,  '1', $haber['cantidad'], $cargoemp->id_sys_adm_departamento);
                                         
                                         if($newconcepto['estado'] == false):
                                         
                                             $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                                             break;
                                         
                                         endif;
                             
                             endif;
      
                          endif;
                          
                endforeach; //recorrido haberes
        
      endif;
      
      return $errores;
          
    }
    
    private function getCalculaNovedades($emp, $rolcab, $cargoemp){
        
        
        $errores = [];
        
        
        //5. Inserta Noevedades
        $novedades  = $this->getNovedades($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $rolcab->fecha_ini_liq, $rolcab->fecha_fin_liq, $rolcab->periodo);
        
        if($novedades):
       
            foreach ($novedades as $novedad):
            
                
                $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, $novedad['novedad'], $rolcab->id_sys_empresa,  $novedad["numero"], $novedad["valor"], $cargoemp->id_sys_adm_departamento);
                
                
                if($newconcepto['estado'] == false):
                
                     $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                
                endif;
            
            
            endforeach;
   
        endif;
        
        return $errores;
        
        
    }
    
    private function getCalculaHorasExtras($emp, $rolcab, $concepto25, $concepto50, $concepto100, $cargoemp, $valorhora){
        
        
        $errores = [];
        
        
        if($concepto25):
        
            $total25    =  $this->getValor25($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $rolcab->fecha_ini_liq, $rolcab->fecha_fin_liq);
            //horas del 25 %
            
            if($total25 > 0):
                
                $val25 = floatval(($valorhora * $concepto25->valor) * $total25);
                
                $newconcepto =    $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, $concepto25->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  $total25, $val25, $cargoemp->id_sys_adm_departamento);
                
                if($newconcepto['estado'] == false):
                
                     $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                
                endif;
                
            endif;
        
        endif;
        
        if($concepto50):
        
            $total50    =  $this->getValor50($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $rolcab->fecha_ini_liq, $rolcab->fecha_fin_liq);
            //horas del 50 %
            
            if($total50 > 0):
            
                $newvalor      = floatval(($valorhora * $concepto50->valor)) + $valorhora;
                
                $val50         = floatval($newvalor * floatval($total50));
                
                $newconcepto   = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, $concepto50->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  $total50, $val50, $cargoemp->id_sys_adm_departamento);
                
                if($newconcepto['estado'] == false):
                
                  $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                
                endif;
            
            endif;
        
        endif;
        
        
        if($concepto100):
        
            
            $total100    =  $this->getValor100($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $rolcab->fecha_ini_liq, $rolcab->fecha_fin_liq);
            //horas del 100 %
            
            if($total100 > 0):
            
                $val100   = floatval(($valorhora * 2) * $total100);
                
                $newconcepto =    $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, $concepto100->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  $total100, $val100, $cargoemp->id_sys_adm_departamento);
                
                if($newconcepto['estado'] == false):
                    
                    $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                    
                endif;
            
            endif;
        
        endif;
        
        return $errores;
    }
    
    private function getCalculaDecimoTer($emp, $sueldo, $rolcab,$decimo, $cargoemp, $periodo, $dias){
        
        
        $errores = [];
        
        if($decimo):
        
                $novedadesAportaIess = $this->getNovedadesAportaIess($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $rolcab->anio, $rolcab->mes, $rolcab->periodo);
                
                $sueldo = $sueldo + $novedadesAportaIess;
                
                $decimo_tercero =  floatval($sueldo/ 12);
                
                //Decimo Tercer Sueldo
                $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $periodo, $emp->id_sys_rrhh_cedula, $decimo->id_sys_rrhh_concepto, $rolcab->id_sys_empresa, $dias, $decimo_tercero, $cargoemp->id_sys_adm_departamento);
                
                if($newconcepto['estado'] == false):
                
                    $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                
                endif;
        
        endif;
        
        return $errores;
        
    }

    private function getCalculaDecimoCua($emp, $rolcab, $decimo, $cargoemp, $contrato, $dias, $sueldobasico, $periodo){
        
          $errores = [];
          $d       =  30;
          $decimo_cuarto = 0;
          
          if($decimo):   
         
                if($sueldobasico > 0):
                
                            if($contrato->fecha_ingreso > $rolcab->fecha_ini && $contrato->fecha_ingreso  < $rolcab->fecha_fin && $contrato->fecha_salida == null):
                           
                            
                                    $date1     = new \DateTime($contrato->fecha_ingreso);
                                    $date2     = new \DateTime($rolcab->fecha_fin);
                                    $diff      = $date1->diff($date2);
                                    // will output 2 days
                                    $d        = intval($diff->days) + 1;
                                    
                                    //Mes Biciesto
                                    $mesbisiesto =  $this->max_dia($rolcab->mes, $rolcab->anio);
                                    
                                    if($mesbisiesto < 30):
                                    
                                        if($mesbisiesto == 29):
                                             $d++;
                                        else:
                                             $d= $d + 2;
                                        endif;
                                    
                                    endif;
                                    
                                    $decimo_cuarto   = (floatval($sueldobasico)/ 360) *  $d;
                               
                            
                            elseif($contrato->fecha_salida != null):
                            
                                         if($contrato->fecha_ingreso > $rolcab->fecha_ini):
                                            
                                                 $date1     = new \DateTime($contrato->fecha_ingreso);
                                                 $date2     = new \DateTime($contrato->fecha_salida);
                                                 $diff      = $date1->diff($date2);
                                                
                                          else:
                                            
                                                $date1     = new \DateTime($rolcab->fecha_ini);
                                                $date2     = new \DateTime($contrato->fecha_salida);
                                                $diff      = $date1->diff($date2);
                                            
                                         endif;
                                        // will output 2 days
                                        $d      = intval($diff->days) + 1;
                                        $decimo_cuarto   = (floatval($sueldobasico)/ 360) *  $d;
                                    
                            else:
                            
                             $decimo_cuarto   = (floatval($sueldobasico)/ 360) *  $d;
                            
                            endif;
                            
                   
                        
                            $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $periodo, $emp->id_sys_rrhh_cedula, $decimo->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  $d,  $decimo_cuarto, $cargoemp->id_sys_adm_departamento);
                            
                            if($newconcepto['estado'] == false):
                            
                                 $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                            
                            endif;
                        
                 endif;
            endif;
       
        
       return $errores;
        
    }
    
    private function getCalculoFondoReserva($emp, $sueldo, $rolcab, $fondoreserva, $cargoemp, $periodo, $anio, $contrato){
       
        $errores = [];
        
        //Provicion Fondo Reserva
        $conceptoProviFondoReserva = SysRrhhConceptos::find()->where(['id_sys_rrhh_concepto'=> 'FONDO_RES_PROV'])->andwhere(['pago'=> '90'])->andWhere([ 'valor' => '0'])->one();
        
        if($fondoreserva):

            $mesContrato = intval(date('m', strtotime($contrato->fecha_ingreso)));

            $mesLiq = intval(date('m', strtotime($rolcab->fecha_fin)));

            $anioContrato = intval(date('Y', strtotime(($contrato->fecha_ingreso."+ 1 years"))));

            $anioLiq = intval(date('Y', strtotime($rolcab->fecha_fin)));

            if($anio == 1):

                if($mesContrato == $mesLiq):

                    if($anioContrato == $anioLiq):

                        $newfechaaio = date("Y-m-d", strtotime($contrato->fecha_ingreso."+ 1 years"));

                        $date1     = new \DateTime($newfechaaio);
                        $date2     = new \DateTime($rolcab->fecha_fin);

                        $ultimoDiaMes = (int) $date2->format('d');

                        if ($date2->format('m') == '02' && $ultimoDiaMes < 30) {
                            $diasFaltantes = 30 - $ultimoDiaMes;
                            $date2->modify("+{$diasFaltantes} days");
                        }

                        $diff      = $date1->diff($date2);

                        $dias      = intval($diff->days);
                        $sueldo    =  ($sueldo/30) * $dias;

                        $novedadesAportaIess = $this->getNovedadesAportaIess($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $rolcab->anio, $rolcab->mes, $rolcab->periodo);
                    
                        $sueldo              = $sueldo + $novedadesAportaIess;
                    
                        $valor               = floatval($sueldo) * floatval($fondoreserva->valor) / 100;
                        
                        if($valor > 0):
                            
                            if($periodo == '2'):
                    
                                $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $periodo, $emp->id_sys_rrhh_cedula, $fondoreserva->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  $anio , $valor, $cargoemp->id_sys_adm_departamento);
                    
                            elseif($periodo == '90'):
                                
                                $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $periodo, $emp->id_sys_rrhh_cedula, $conceptoProviFondoReserva->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  $anio, $valor, $cargoemp->id_sys_adm_departamento);
                                
                            endif;
                                
                            if($newconcepto['estado'] == false):
                                
                                $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                                
                            endif;
                            
                        endif;

                    else:
                    
                        $novedadesAportaIess = $this->getNovedadesAportaIess($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $rolcab->anio, $rolcab->mes, $rolcab->periodo);
                    
                        $sueldo              = $sueldo + $novedadesAportaIess;
                    
                        $valor               = floatval($sueldo) * floatval($fondoreserva->valor) / 100;
                        
                        if($valor > 0):
                            
                            if($periodo == '2'):
                    
                                $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $periodo, $emp->id_sys_rrhh_cedula, $fondoreserva->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  $anio , $valor, $cargoemp->id_sys_adm_departamento);
                    
                            elseif($periodo == '90'):
                                
                                $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $periodo, $emp->id_sys_rrhh_cedula, $conceptoProviFondoReserva->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  $anio, $valor, $cargoemp->id_sys_adm_departamento);
                                
                            endif;
                                
                            if($newconcepto['estado'] == false):
                                
                                $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                                
                            endif;
                            
                        endif;

                    endif;

                else:

                    $contratos = SysRrhhEmpleadosContratos::find()->where(['id_sys_rrhh_cedula'=>$emp->id_sys_rrhh_cedula])->andWhere(['activo'=>0])->all();
                    $diasTotales = 0;
                    $contratoInicial =  new \DateTime($contrato->fecha_ingreso);
                    $fecha_inicio = '';
                    $fecha_fin = '';

                    if($contratos):

                        foreach ($contratos as $contrato) {
                            $fecha_inicio = new \DateTime($contrato['fecha_ingreso']);
                            $fecha_fin = new \DateTime($contrato['fecha_salida']);
                        
                            // Calcular días de diferencia e incluir el día de inicio (+1)
                            $dias_periodo = $fecha_inicio->diff($fecha_fin)->days + 1;
                            
                            // Sumar al total
                            $diasTotales += $dias_periodo;
                        };
    
                        /// Restar los días totales a la fecha de ingreso actual
                        $contratoInicial->modify("-$diasTotales days");
                        $contratoInicial->modify("+1 year");
                        $fechaInicialCalculada = $contratoInicial->format('Y/m/d');

                        $mesContrato = intval(date('m', strtotime($fechaInicialCalculada)));
                        $mesLiq = intval(date('m', strtotime($rolcab->fecha_fin)));

                        if($mesContrato == $mesLiq):

                            $date1     = new \DateTime($fechaInicialCalculada);
                            $date2     = new \DateTime($rolcab->fecha_fin);

                            $ultimoDiaMes = (int) $date2->format('d');

                            if ($date2->format('m') == '02' && $ultimoDiaMes < 30) {
                                $diasFaltantes = 30 - $ultimoDiaMes;
                                $date2->modify("+{$diasFaltantes} days");
                            }

                            $diff      = $date1->diff($date2);

                            $dias      = intval($diff->days);
                            $sueldo    =  ($sueldo/30) * $dias;

                            $novedadesAportaIess = $this->getNovedadesAportaIess($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $rolcab->anio, $rolcab->mes, $rolcab->periodo);
                        
                            $sueldo              = $sueldo + $novedadesAportaIess;
                        
                            $valor               = floatval($sueldo) * floatval($fondoreserva->valor) / 100;
                            
                            if($valor > 0):
                                
                                if($periodo == '2'):
                        
                                    $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $periodo, $emp->id_sys_rrhh_cedula, $fondoreserva->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  $anio , $valor, $cargoemp->id_sys_adm_departamento);
                        
                                elseif($periodo == '90'):
                                    
                                    $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $periodo, $emp->id_sys_rrhh_cedula, $conceptoProviFondoReserva->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  $anio, $valor, $cargoemp->id_sys_adm_departamento);
                                    
                                endif;
                                    
                                if($newconcepto['estado'] == false):
                                    
                                    $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                                    
                                endif;
                                
                            endif;

                        else:
                            
                            $novedadesAportaIess = $this->getNovedadesAportaIess($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $rolcab->anio, $rolcab->mes, $rolcab->periodo);
                        
                            $sueldo              = $sueldo + $novedadesAportaIess;
                            
                            $valor               = floatval($sueldo) * floatval($fondoreserva->valor) / 100;
                                
                            if($valor > 0):
                                    
                                if($periodo == '2'):
                            
                                    $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $periodo, $emp->id_sys_rrhh_cedula, $fondoreserva->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  $anio , $valor, $cargoemp->id_sys_adm_departamento);
                            
                                elseif($periodo == '90'):
                                        
                                    $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $periodo, $emp->id_sys_rrhh_cedula, $conceptoProviFondoReserva->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  $anio, $valor, $cargoemp->id_sys_adm_departamento);
                                        
                                endif;
                                    
                                if($newconcepto['estado'] == false):
                                        
                                    $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                                        
                                endif;
                                    
                            endif;

                        endif;
                        
                    else:

                        $novedadesAportaIess = $this->getNovedadesAportaIess($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $rolcab->anio, $rolcab->mes, $rolcab->periodo);
                        
                        $sueldo              = $sueldo + $novedadesAportaIess;
                        
                        $valor               = floatval($sueldo) * floatval($fondoreserva->valor) / 100;
                            
                        if($valor > 0):
                                
                            if($periodo == '2'):
                        
                                $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $periodo, $emp->id_sys_rrhh_cedula, $fondoreserva->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  $anio , $valor, $cargoemp->id_sys_adm_departamento);
                        
                            elseif($periodo == '90'):
                                    
                                $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $periodo, $emp->id_sys_rrhh_cedula, $conceptoProviFondoReserva->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  $anio, $valor, $cargoemp->id_sys_adm_departamento);
                                    
                            endif;
                                
                            if($newconcepto['estado'] == false):
                                    
                                $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                                    
                            endif;
                                
                        endif;

                    endif;
                    
                endif;

            else:
                
                $novedadesAportaIess = $this->getNovedadesAportaIess($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $rolcab->anio, $rolcab->mes, $rolcab->periodo);
                
                $sueldo              = $sueldo + $novedadesAportaIess;
                
                $valor               = floatval($sueldo) * floatval($fondoreserva->valor) / 100;
                    
                if($valor > 0):
                        
                    if($periodo == '2'):
                
                        $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $periodo, $emp->id_sys_rrhh_cedula, $fondoreserva->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  $anio , $valor, $cargoemp->id_sys_adm_departamento);
                
                    elseif($periodo == '90'):
                            
                        $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $periodo, $emp->id_sys_rrhh_cedula, $conceptoProviFondoReserva->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  $anio, $valor, $cargoemp->id_sys_adm_departamento);
                            
                    endif;
                        
                    if($newconcepto['estado'] == false):
                            
                        $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                            
                    endif;
                        
                endif;
       
            endif;
                
        endif;
        
      return $errores;  
    }
    
    private function getCalculoIess($emp, $sueldo, $rolcab, $iess, $cargoemp){
        
        $errores              = [];
        
        if($iess):
        
            $novedadesAportaIess = $this->getNovedadesAportaIess($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $rolcab->anio, $rolcab->mes, $rolcab->periodo);
           
            $sueldo              = $sueldo + $novedadesAportaIess;
            
            $valor               = (floatval($sueldo) * floatval($iess->valor))/100; 
            
            //Insertar pago de Iess
            $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, $iess->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  '1', $valor, $cargoemp->id_sys_adm_departamento);
            
            if($newconcepto['estado'] == false):
            
                  $errores [] = array('mensaje'=> $newconcepto['mensaje']);
            
            endif;
        
        endif;
        
        return $errores;
    }
    
    private function getAnticipoFinMes($emp, $rolcab, $antipoquincena, $cargoemp){
        
        $errores = [];
        
        if($antipoquincena):
        
            $valorquincena = SysRrhhEmpleadosRolMov::find()
            ->where(['anio'=> $rolcab->anio])
            ->andWhere(['mes'=> $rolcab->mes])
            ->andWhere(['periodo'=> '1'])
            ->andWhere(['id_sys_rrhh_cedula'=> $emp->id_sys_rrhh_cedula])
            ->andWhere(['id_sys_rrhh_concepto'=> 'ANTICIPO'])
            ->andwhere(['id_sys_empresa'=> $rolcab->id_sys_empresa])
            ->one();
            
            if($valorquincena):
            
                $newconcepto =    $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, $antipoquincena->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  $valorquincena->cantidad, $valorquincena->valor, $cargoemp->id_sys_adm_departamento);
                
                if($newconcepto['estado'] == false):
                
                    $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                    
                endif;
                
            endif;
        
        endif;
  
        return $errores;
    }
  
    private function getCuotaPrestamoEmpresa($emp, $rolcab, $prestamoempresa, $cargoemp){
       
        $errores = [];
        
       if($prestamoempresa):
        
                $prestamo = $this->getPrestamo($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $rolcab->anio, $rolcab->mes, '2');
                
                if($prestamo > 0):
                
                    $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, $prestamoempresa->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  '1', $prestamo, $cargoemp->id_sys_adm_departamento);
                    
                    if($newconcepto['estado'] == false):
                    
                       $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                    
                    endif;
                    
                endif;
        
        endif;
        
        return $errores;
        
    }
    
    private function getImpuestoRenta($emp, $sueldo, $rolcab,  $impuestorenta, $cargoemp, $iess, $contrato){
        
        $errores = [];
       
        $ban = false;
        
        $anio = date('Y');
        
        $anioingreso =  date('Y', strtotime($contrato->fecha_ingreso));
        
        if($contrato->fecha_salida != null):
           
            $anioingreso =  date('Y', strtotime($contrato->fecha_salida));
        
        else:
            
            $anioingreso =  date('Y', strtotime($contrato->fecha_ingreso));
        
        endif;
        
     
        if($impuestorenta):
        
        
            $iess = (floatval($sueldo) * floatval($iess->valor))/100 ;
        
            if($iess > 0): 
                
                if($anio !=  $anioingreso):
                                   
                    if($contrato->fecha_salida == null):
                                  
                        //Comprobar si no ha tenido variante de sueldo en el año actual
                        $sueldoemp    =  SysRrhhEmpleadosSueldos::find()
                        ->where(['id_sys_rrhh_cedula'=> $emp->id_sys_rrhh_cedula])
                        ->andWhere(['estado' => 'A'])
                        ->one();

                        $aniosueldo =  date('Y', strtotime($sueldoemp->fecha));
                                           
                        if($aniosueldo != $anio):
                                           
                            $meses = 12; 
                                    
                        else:
                                               
                            $meseslab   =  12  - intval(date('m', strtotime($sueldoemp->fecha)) - 1 );
                            $meses      =  $meseslab;
                            $ban = true;
                                           
                        endif;
                                       
  
                    else:
                                        
                        $meseslab   =  intval(date('m', strtotime($contrato->fecha_salida))) - 1 ;
                        $diaslab    =  intval(date('d', strtotime($contrato->fecha_salida)));
                        $diaslab    =  (intval($meseslab) * 30) + $diaslab;
                        $meses      =   $diaslab/30;
                                   
                        endif;

                else:
                                  
                    if($contrato->fecha_salida == null):
                                  
                        $meseslab   =   12  - intval(date('m', strtotime($contrato->fecha_ingreso)));
                        $diaslab =   30 - intval(date('d', strtotime($contrato->fecha_ingreso)));
                        $diaslab =   (intval($meseslab) * 30) + $diaslab;
                        $meses   =   $diaslab/30;
                    
                    else:
                                  
                        $mesing     =  (intval(date('m', strtotime($contrato->fecha_ingreso)))) + 1;
                        $diasing    =  30 - intval(date('d', strtotime($contrato->fecha_ingreso)));
                        $messal     =  intval(date('m', strtotime($contrato->fecha_salida)));
                        $diassal    =  intval(date('d', strtotime($contrato->fecha_salida)));
                        $meseslab   =  $messal - $mesing;
                        $diaslab    =  $diasing + $diassal;
                        $meses      =  $diaslab/30;
                                           
                    endif;
                               
                                  
                endif;
                
                
                // Impuesto a la renta
                $valoresrenta = $this->getNovedadesAportaRenta($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa, $rolcab->anio, $rolcab->mes, $rolcab->periodo);
                                
                //descontamos iees
                $renta         = (floatval($sueldo) - $iess) + (floatval($valoresrenta));
      
                $sueldo_anual   = $renta * $meses;
                                
                                
                if($ban == true):
                                
                    //Obtenemos sus sueldos anteriores
                    $sueldo_anual = $sueldo_anual + $this->getSalariosAnteriores($emp->id_sys_rrhh_cedula, $rolcab->anio, intval(date('m', strtotime($sueldoemp->fecha))));
                                
                endif;

                /*$anioactual = date('Y');

                $anioanterior =  date('Y',strtotime($anioactual."- 1 years"));

                $utilidades = SysRrhhUtilidadesDet::find()->where(['anio'=>$anioanterior])->andWhere(['id_sys_rrhh_cedula'=>$emp->id_sys_rrhh_cedula])->one();

                $total_ingreso = floatval($sueldo_anual);

                $valor_utilidades = 0 ;

                if($utilidades):
                    $valor_utilidades = floatval($utilidades->uti_empleados) + floatval($utilidades->uti_cargas);

                    $total_ingreso = floatval($sueldo_anual) + floatval($valor_utilidades);
                endif;*/
                
                $gastosdeduciblesemp  = $this->getGastosDeducibles($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa);
                                
                $maxgatosdeducibles   = $this->getMaxGastosDedudibles($rolcab->id_sys_empresa);
 
                $total_base = floatval($sueldo_anual);

                $fracion = $this->getFracionTabla($total_base, $rolcab->id_sys_empresa);

                if($fracion):
                                    
                    if($fracion['fraccion_basica'] > 0  ):
                                      
                        if($gastosdeduciblesemp <= $maxgatosdeducibles):    
                            
                            $ir_anual = ((floatval($total_base) - floatval($fracion['fraccion_basica'])) * (floatval($fracion['impuesto_fraccion_excedente'])/100)) + floatval($fracion['impuesto_fraccion_basica']);

                            $cargas = SysRrhhEmpleadosNucleoFamiliar::find()->where(['id_sys_rrhh_cedula'=>$emp->id_sys_rrhh_cedula])->andWhere(['rentas'=>1])->count();
                                                          
                            $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=>$emp->id_sys_rrhh_cedula])->one();

                            $canasta = SysAdmCanastaBasica::find()->where(['anio'=>date('Y')])->one();

                            if($empleado->enfermedad == 'N' && $cargas != 0):
                                                    
                                $canatas = $this->numcanastas($cargas);
                                $valor_reducir = floatval(min($gastosdeduciblesemp,($canasta->canasta_basica * $canatas)) * 0.18);    
                                                    
                            elseif($empleado->enfermedad == 'S' && $cargas != 0):
                                                    
                                $valor_reducir = floatval(min($gastosdeduciblesemp,($canasta->canasta_basica * 20)) * 0.18);    
                                                    
                            else:

                                $valor_reducir = floatval(min($gastosdeduciblesemp,($canasta->canasta_basica * 7)) * 0.18);    
                                                    
                            endif;
                            
                            $valor_renta = floatval($ir_anual) - floatval($valor_reducir);
                            
                            if($valor_renta > 0):

                                $impuesto_final = floatval($valor_renta/11);

                                $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, $impuestorenta->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  '1', $impuesto_final, $cargoemp->id_sys_adm_departamento);
                                                            
                                if($newconcepto['estado'] == false):
                                                                
                                    $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                                                                
                                endif;
                         
                            endif;
                        
                        endif;
                    
                    endif;
                
                endif;
                
                
                //sumarizamos todos los gastos deducibles
                /*$gastosdeduciblesemp  = $this->getGastosDeducibles($emp->id_sys_rrhh_cedula, $rolcab->id_sys_empresa);
                                
                $maxgatosdeducibles   = $this->getMaxGastosDedudibles($rolcab->id_sys_empresa);
 
                $ingreso_total =  floatval($sueldo_anual) - floatval($gastosdeduciblesemp);
                                
                $fracion = $this->getFracionTabla($ingreso_total, $rolcab->id_sys_empresa);
                                
                if($fracion):
                                    
                    if($fracion['fraccion_basica'] > 0  ):
                                      
                        if($gastosdeduciblesemp <= $maxgatosdeducibles):        
                                      
                            $base         = floatval($ingreso_total) - floatval($fracion['fraccion_basica']);
                                                
                            $base         =  $base  *  (floatval($fracion['impuesto_fraccion_excedente'])/100);
                                                    
                            $valor_renta  = floatval($base)  + floatval($fracion['impuesto_fraccion_basica']);
                                                       
                            $cargas = SysRrhhEmpleadosNucleoFamiliar::find()->where(['id_sys_rrhh_cedula'=>$emp->id_sys_rrhh_cedula])->count();
                                                          
                            $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=>$emp->id_sys_rrhh_cedula])->one();

                            $canasta = SysAdmCanastaBasica::find()->where(['anio'=>date('Y')])->one();

                            if($empleado->enfermedad == 'N' && $cargas >= 0):
                                                    
                                $canatas = $this->numcanastas($cargas);
                                $valor_reducir = floatval(min($gastosdeduciblesemp,($canasta->canasta_basica * $canatas)) * 0.18);
                                                    
                            elseif($empleado->enfermedad == 'S' && $cargas != 0):
                                                    
                                $valor_reducir = floatval(min($gastosdeduciblesemp,($canasta->canasta_basica * 20)) * 0.18);    
                                                    
                            else:

                                $valor_reducir = floatval(min($gastosdeduciblesemp,($canasta->canasta_basica * 7)) * 0.18);    
                                                    
                            endif;
                                                    
                            $impuesto_final = floatval($valor_renta) - floatval($valor_reducir);
                                                    
                            if($impuesto_final > 0):

                                $impuesto_final = floatval($impuesto_final /12);

                                $newconcepto = $this->insertarConsepto($rolcab->anio, $rolcab->mes, $rolcab->periodo, $emp->id_sys_rrhh_cedula, $impuestorenta->id_sys_rrhh_concepto, $rolcab->id_sys_empresa,  '1', $impuesto_final, $cargoemp->id_sys_adm_departamento);
                                                            
                                if($newconcepto['estado'] == false):
                                                                
                                    $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                                                                
                                endif;
                         
                            endif;
                        
                        endif;
                    
                    endif;
                
                //endif;*/               
                    
            endif;
        
          endif;
        
        return $errores;
        
        
    }
    
    public function numcanastas($cargas){
        switch ($cargas) {
            case 1:
              return $cargas = 9;
            case 2:
              return $cargas = 11;
            case 3:
              return $cargas = 14;
            case 4:
              return $cargas = 17;
            default:
              return $cargas = 20;
        }
    }

    public  function actionAsistencia($anio, $mes, $periodo, $id_sys_rrhh_cedula, $id_sys_empresa){
        
        
         $fechainicio = '';
         $fechafin    = '';
 
        //Obtenemos el rol del empleado 
        $rolcab      =  SysRrhhEmpleadosRolCab::find()->where(['anio'=> $anio])->andWhere(['mes'=> $mes])->andWhere(['periodo'=> $periodo])->andWhere(['id_sys_empresa'=> $id_sys_empresa])->one();
        
   
        //obtenemos el contrato del empleador
        $contratoemp  =  SysRrhhEmpleadosContratos::find()
        ->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])
        ->andWhere(['id_sys_empresa'=> $id_sys_empresa])
        ->orderBy(['fecha_salida' => SORT_ASC])
        ->one();
        
        
        //Obtenemos datos del empleador
        $empleado     = SysRrhhEmpleados::find()
        ->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])
        ->andWhere(['id_sys_empresa'=> $rolcab->id_sys_empresa])
        ->andWhere(['estado'=> 'A'])
        ->one();
       
        
        
        //obtenemos el sueldo del empleador
        $sueldoemp    =  SysRrhhEmpleadosSueldos::find()->select('sueldo')
        ->where(['id_sys_rrhh_cedula'=> $empleado->id_sys_rrhh_cedula])
        ->andWhere(['id_sys_empresa'=> $rolcab->id_sys_empresa])
        ->orderBy(['id_sys_rrhh_empleados_sueldo_cod' => SORT_DESC])
        ->scalar();
        

        if($periodo == '70' ||  $periodo == '71'):
        
        
             $concepto = $periodo == '70' ? 'PAGO_DECIMO_TER': 'PAGO_DECIMO_CUA';
              
             return $this->redirect(['rol-detalle/update?anio='.$anio.'&mes='.$mes.'&periodo='.$periodo.'&id_sys_rrhh_cedula='.$id_sys_rrhh_cedula.'&id_sys_rrhh_concepto='.$concepto.'&id_sys_empresa=001']);
              
        
         else:
        
             if($contratoemp->fecha_salida == null):
             
                if($contratoemp->fecha_ingreso < $rolcab->fecha_ini):
                      
                    $fechainicio =  $rolcab->fecha_ini_liq;
                    $fechafin    =  $rolcab->fecha_fin_liq;
                        
                 elseif($contratoemp->fecha_ingreso >= $rolcab->fecha_ini && $contratoemp->fecha_ingreso  <= $rolcab->fecha_fin):
                 
                   
                     $fechainicio =  $contratoemp->fecha_ingreso;
                     $fechafin    =  $rolcab->fecha_fin;
                     
                     
                 endif;
             else:
             
                 if($contratoemp->fecha_ingreso >= $rolcab->fecha_ini && $contratoemp->fecha_ingreso  <= $rolcab->fecha_fin):
                 
                    $fechainicio = $contratoemp->fecha_ingreso;
                 
                 else:
                 
                     //Valida asistencia dentro del corte
                     if($contratoemp->fecha_ingreso >= $rolcab->fecha_ini_liq && $contratoemp->fecha_ingreso  <= $rolcab->fecha_fin_liq):
                     
                        $fechainicio= $contratoemp->fecha_ingreso;
                     
                     else:
                     
                        $fechainicio = $rolcab->fecha_ini;
                     
                     endif;
                 
                 endif;
                 
                 $fechafin    =  $contratoemp->fecha_salida;
             
             
             endif;
         
               return $this->redirect(['asistencia/asistenciaempleadopdf2?fechaini='.$fechainicio.'&fechafin='.$fechafin.'&cedula='.$empleado->id_sys_rrhh_cedula]);
  
         endif;
               
  
    }
      
    private function BuscaPermisos($fecha_inicio, $fecha_fin, $id_sys_rrhh_cedula, $id_sys_empresa, $sueldoemp){
        
        
        $dias         =  0;
        
        $sueldo       =  0;
        
        $valordia     =  (floatval($sueldoemp)/30);
        
        $horaspermiso =  0;
        
        $diaspermisos  = 0;
        
        $falta        =  0;
       
        $cont         =  0;
        
        $contpermiso  = 0;
        
        $subcidio     = 0;
        
        
        $datos        = [];
              
        while(strtotime($fecha_inicio) <= strtotime($fecha_fin))
        {
           
            $cont++;
            $contpermiso = 0;
            $dias++;
            $sueldo+= $valordia;
            $horaspermiso = 0;
            $permiso = $this->getPermiso($fecha_inicio, $id_sys_rrhh_cedula, $id_sys_empresa);
            $falta = 0;
            $diaspermisos = 0;
            $subcidio = 0;
            if($permiso):
            
               if ($permiso['estado_permiso'] == 'A'):
                
                     $contpermiso = 1;
                
                    if($permiso['descuento'] == 'S'):
                    
                        if($permiso['tipo'] == 'P'):
                        
                           $horaspermiso = $this->getHoraPermiso($permiso['hora_ini'], $permiso['hora_fin']);
                
                        else:
                        
                            $diaspermisos = 1;
                            $sueldo-= $valordia;
                        
                        endif;
                    
                    else:
                       
                        if($permiso['subcidio'] > 0):
                        
                           if($cont < 31):
                           
                               $porcentaje = $permiso['subcidio']/100;
                               $subcidio = floatval($valordia * $porcentaje);
                               
                           endif;
                        
                        endif;

                    endif;
                    
               elseif($permiso['estado_permiso'] == 'P'):
                
                     $falta = 1;
                
               endif;
                
           endif;
        
             
          $datos[] = array('fecha'=> $fecha_inicio, 'sueldoliq' => $sueldo, 'faltas'=> $falta, 'permisos'=> $contpermiso, 'horaspermiso'=> $horaspermiso, 'diaspermisos' => $diaspermisos, 'cont'=> $cont, 'subcidio'=> $subcidio);
           
           
          $fecha_inicio = date("Y-m-d", strtotime($fecha_inicio . " + 1 day"));
            
          
        }
        
    
        return $datos;
    }
 
    private function CalcularAsistencia($fecha_inicio, $fecha_fin, $id_sys_rrhh_cedula, $id_sys_empresa, $sueldoemp){
      
       
        $sueldo       =  0;
       
        $valordia     =  (floatval($sueldoemp)/30);
         
        $horaspermiso =  0;
        
        $diaspermisos = 0;
      
        $falta        =  0;
        
        $cont         =  0;
        
        $contpermiso  =  0;
        
        $subcidio      = 0;
   
        
        $datos  = [];
        
        if(floatval($sueldoemp) > 0):
        
            while(strtotime($fecha_inicio) <= strtotime($fecha_fin))
            {
             
                $cont++;
                $contpermiso  = 0;
                $falta        = 0;
                $horaspermiso = 0;
                $diaspermisos = 0;
                $subcidio = 0;
              
                 //obtenemos el tipo de jornada
                $jornada = $this->getTipoJornada($id_sys_empresa, $id_sys_rrhh_cedula, $fecha_inicio);
                
                //obtenemos el numero de dia de la semana 
                $dia = date("N", strtotime($fecha_inicio));
              
                $sueldo+= $valordia;
                
                
                //jornada Normal 
                if($jornada == 'N'):
                
                                      //obtenemos la marcacion  
                                      $marcacion = $this->getMarcacionEmpleado($fecha_inicio, $id_sys_rrhh_cedula);
                                      
                                      if($marcacion):
                                        
                                                  if(count($marcacion) > 0):
                                               
                                                  
                                                       //si tiene una marcacion validamos el permiso
                                                       $permiso = $this->getPermiso($fecha_inicio, $id_sys_rrhh_cedula, $id_sys_empresa);
                                                  
                                                        if($permiso):
                                                        
                                                        
                                                            if($permiso['estado_permiso'] == 'A'):
                                                            
                                                                $contpermiso = 1;
                                                        
                                                                if($permiso['descuento'] == 'S'):
                                                                
                                                                
                                                                    if(trim($permiso['tipo']) == 'P'):
                                                                    
                                                                        $horaspermiso =  $this->getHoraPermiso($permiso['hora_ini'], $permiso['hora_fin']);
                                                                    
                                                                    else:
                                                                         $diaspermisos = 1;
                                                                    
                                                                    endif;
                                                                
                                                                else:
                                                                    
                                                                    if($permiso['subcidio'] > 0):
                                                                    
                                                                        if($cont < 31):
                                                                        
                                                                            $porcentaje = $permiso['subcidio']/100;
                                                                            $subcidio = floatval($valordia * $porcentaje);
                                                                            
                                                                        endif;
                                                                    
                                                                    endif;
                                                                    
                                                                
                                                                endif;
                                                            
                                                            
                                                            elseif($permiso['estado_permiso'] == 'P'):
                                                            
                                                                $falta = 1;
                                                           
                                                            endif;
                                                        
                                                        
                                                       elseif(count($marcacion) == 1) :
                                                       
                                                           $falta = 1;
                                                       
                                                       endif;
                                                        
                                                        
                                                  
                                                  endif;
                                      
                                      else:
                                            //Validamos si tiene un permiso
                                            $permiso    = $this->getPermiso($fecha_inicio, $id_sys_rrhh_cedula, $id_sys_empresa);
                                      
                                            if($permiso):
                                                
                                                if($permiso['estado_permiso'] == 'A'):
                                                
                                                    $contpermiso = 1;
                                                        
                                                    if($permiso['descuento'] == 'S'):
                                                         
                                                        if(trim($permiso['tipo']) == 'P'):
      
                                                             $horaspermiso =  $this->getHoraPermiso($permiso['hora_ini'], $permiso['hora_fin']);
                                                        
                                                        else:
                                                             $diaspermisos = 1;   
                                                        endif;
                                                    
                                                    else:
                                                    
                                                        if($permiso['subcidio'] > 0):
                                                    
                                                            if($cont < 31):
                                                            
                                                                $porcentaje = $permiso['subcidio']/100;
                                                                $subcidio = floatval($valordia * $porcentaje);
                                                                
                                                            endif;
                                                        
                                                        endif;
                                                        
                                                    endif;
    
                                                elseif($permiso['estado_permiso'] == 'P'):
                                                
                                                    $falta = 1;
                                                
                                                endif;

                                            else:
                                                   //validamos si tiene gozo de vacaciones 
                                                          
                                                    $vacaciones =  $this->getVacaciones($fecha_inicio, $id_sys_rrhh_cedula);
                                                
                                                    if(!$vacaciones):
                                                        
                                                   
                                                              //validamos feriado
                                                             $feriado = $this->getFeriado($fecha_inicio);
                                                             
                                                             if(!$feriado):
                                                             
                                                        
                                                                     //fines de Semanaa
                                                                      if($dia < 6):
                                                             
                                                                         $falta = 1;
                                                          
                                                                      endif;
                                                                     
                                                             endif;
                                                
                                                    endif;
                                            endif;    
                                  endif;
                else:
                
                   
                      //obtenemos la marcacion
                       $marcacion = $this->getMarcacionEmpleado($fecha_inicio, $id_sys_rrhh_cedula);
                        
                       if($marcacion):
                                        
                                        if(count($marcacion) > 0):
                                           
            
                                                     $permiso    = $this->getPermiso($fecha_inicio, $id_sys_rrhh_cedula, $id_sys_empresa);
                                           
                                                     if($permiso):
                                                     
                                                     
                                                         if($permiso['estado_permiso'] == 'A'):
                                                             
                                                             $contpermiso = 1;
                                                             
                                                             if($permiso['descuento'] == 'S'):
                                                             
                                                                 
                                                                 if(trim($permiso['tipo']) == 'P'):
                                                                 
                                                                    $horaspermiso =  $this->getHoraPermiso($permiso['hora_ini'], $permiso['hora_fin']);
                                                                 
                                                                 else:
                                                                 
                                                                    $diaspermisos = 1;
                                                                 
                                                                 endif;
                                                             
                                                             else:
                                                             
                                                                 if($permiso['subcidio'] > 0):
                                                                 
                                                                     if($cont < 31):
                                                                     
                                                                         $porcentaje = $permiso['subcidio']/100;
                                                                         $subcidio = floatval($valordia * $porcentaje);
                                                                         
                                                                     endif;
                                                                 
                                                                 endif;
                                                                 
                                                             endif;
                                           
                                                         elseif($permiso['estado_permiso'] == 'P'):
                                                         
                                                             $falta = 1;
                                                         
                                                         endif;
                                                         
                                                    elseif(count($marcacion) == 1):
                                                       
                                                      $falta++;   
                                                    
                                                    endif;
                                        endif;
                        
                        else:
    
                        //Validamos si tiene un permiso
                        $permiso    = $this->getPermiso($fecha_inicio, $id_sys_rrhh_cedula, $id_sys_empresa);
                        
                        if($permiso):
                         
                            if($permiso['estado_permiso'] == 'A'):
                            
                                $contpermiso = 1;
                                
                                if($permiso['descuento'] == 'S'):
                                    
                                    
                                    if(trim($permiso['tipo']) == 'P'):
                                    
                                         $horaspermiso =  $this->getHoraPermiso($permiso['hora_ini'], $permiso['hora_fin']);
                                    
                                    else:
                                    
                                         $diaspermisos = 1;
                                    
                                    endif;
                                
                                else:
                                
                                    if($permiso['subcidio'] > 0):
                                    
                                        if($cont < 31):
                                        
                                            $porcentaje = $permiso['subcidio']/100;
                                            $subcidio = floatval($valordia * $porcentaje);
                                            
                                        endif;
                                    
                                    endif;
                                      
                                endif;
                           
                            elseif($permiso['estado_permiso'] == 'P'):
                            
                                $falta = 1;
                            
                            endif;
                        else:
                        //validamos si tiene gozo de vacaciones
                                
                                $vacaciones =  $this->getVacaciones($fecha_inicio, $id_sys_rrhh_cedula);
                                
                                if(!$vacaciones):
                        
                                        //validamos feriado
                                        $feriado = $this->getFeriado($fecha_inicio);
                                        
                                        if(!$feriado):
                                            
                                                $libre =  $this->getLibre($id_sys_rrhh_cedula, $fecha_inicio);
                                            
                                                if($libre == false):
                                      
                                                   $falta= 1;
                                         
                                                endif;
                                            
                                         endif;
                                        
                                   endif;
                                
                              endif;
                            
                        endif;
                       
                endif;
              
                
                
             $datos[] = array('fecha'=> $fecha_inicio, 'sueldoliq' => $sueldo,  'faltas'=> $falta, 'permisos'=> $contpermiso, 'horaspermiso'=> $horaspermiso, 'diaspermisos'=> $diaspermisos, 'cont'=> $cont, 'subcidio'=> $subcidio);
                 
             $fecha_inicio = date("Y-m-d", strtotime($fecha_inicio . " + 1 day"));
       
            }
             
            
       endif;
        
     
        return $datos;

     }
    
    private function getTipoJornada($empresa, $cedula, $fecha){
         
  
             $agendamiento = SysRrhhCuadrillasJornadasMov::find()
             ->where(['id_sys_empresa'=> $empresa])
             ->andWhere(['id_sys_rrhh_cedula'=> $cedula])->andWhere(['fecha_laboral'=> $fecha])->one();
             
             if($agendamiento):
                  return 'R';
             endif;
               
           return 'N';
            
     }
     
     private  function getSalariosAnteriores($id_sys_rrhh_cedula, $anio, $mes){
         
       $sueldo =  (new \yii\db\Query())
         ->select(['sum([valor])'])
         ->from("sys_rrhh_empleados_rol_mov")
         ->where("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
         ->andwhere("anio = '{$anio}'")
         ->andwhere("mes < {$mes}")
         ->andwhere("id_sys_rrhh_concepto = 'SUELDO'")
         ->andwhere("estado = 'P'")
         ->scalar(SysRrhhEmpleados::getDb());
         
         $iess = (new \yii\db\Query())
         ->select(['sum([valor])'])
         ->from("sys_rrhh_empleados_rol_mov")
         ->where("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
         ->andwhere("anio = '{$anio}'")
         ->andwhere("mes < {$mes}")
         ->andwhere("id_sys_rrhh_concepto = 'IESS'")
         ->andwhere("estado = 'P'")
         ->scalar(SysRrhhEmpleados::getDb());
         
         return  floatval( $sueldo) -  floatval($iess);
         
         
     }
     
    private function getMarcacionEmpleado($fecha, $id_sys_rrhh_cedula){
         
         
          $marcacion = SysRrhhEmpleadosMarcacionesReloj::find()->where(['fecha_jornada'=> $fecha, 'id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])->andWhere(['estado'=> 'A'])->andWhere(['tipo' => ['E','S']])->all();
        
          return $marcacion;
     }
     
    private function getPermiso($fecha, $id_sys_rrhh_cedula, $id_sys_empresa){
         
         return  (new \yii\db\Query())
         ->select(["sys_rrhh_empleados_permisos.id_sys_rrhh_permiso", "tipo", "hora_ini", "hora_fin", "fecha_ini", "fecha_fin", "estado_permiso", "subcidio", "descuento"])
         ->from("sys_rrhh_empleados_permisos")
         ->Join('join','sys_rrhh_permisos','sys_rrhh_empleados_permisos.id_sys_rrhh_permiso = sys_rrhh_permisos.id_sys_rrhh_permiso')
         ->where("fecha_ini <= '{$fecha}' and fecha_fin >= '{$fecha}'")
         ->andwhere("sys_rrhh_empleados_permisos.id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
         ->andwhere("estado_permiso <> 'N'")
         ->orderby("nivel")
         ->one(SysRrhhEmpleados::getDb());
         
         
     }
     
    private function getFeriado($fecha){
         
         return  SysRrhhFeriados::find()->where(['id_sys_empresa'=> '001'])->andWhere(['fecha'=> $fecha ])->one();
         
     }
     
    private function getLibre($id_sys_rrhh_cedula , $fecha){
         
         
         
         $agenda = SysRrhhCuadrillasJornadasMov::find()
         ->select(['isnull(id_sys_rrhh_jornada,100)'])
         ->where(['id_sys_empresa'=> '001'])
         ->andWhere(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])
         ->andWhere(['fecha_laboral'=> $fecha])
         ->orderBy(['fecha_registro'=> SORT_DESC])
         ->scalar();
         
         if($agenda > 0 ):
         
              if(intval($agenda) == 100):
         
                 return true;
         
              endif;
         endif;
         
         return false;
     }
     
    private function getVacaciones($fecha, $cedula){
         
    
         return  (new \yii\db\Query())
         ->select('*')
         ->from("sys_rrhh_vacaciones_solicitud")
         ->where("'{$fecha}' >= fecha_inicio and '{$fecha}'<= fecha_fin")
         ->andwhere("id_sys_rrhh_cedula = '{$cedula}'")
         ->andwhere("id_sys_empresa = '001'")
         ->andwhere("estado_solicitud = 'A'")
         ->all(SysRrhhCuadrillasJornadasMov::getDb());
   
     }
    
    private function getHoraPermiso($entrada, $salida){
         
         $totalhoras = 0;
         $totalmin   = 0;
         
         $ent        = explode(':', $entrada);
         $sal        = explode(':', $salida) ;
       
         $horaentra  = $ent[0];
         $minentrada = $ent[1];
         $horasalida = $sal[0];
         $minsalida  = $sal[1];
         
         $minentrada = 60 - $minentrada;
         $horaentra++;
         
         $totalmin = $minentrada + $minsalida;
         
         if ($totalmin >= 60):
         
             $totalmin   = $totalmin - 60;
             $horasalida++;
         
         endif;
         
         $totalhoras =  $horasalida - $horaentra;
         
         if($totalmin > 0 ):
         
              $totalmin   =  ($totalhoras + ($totalmin/60));
         
         else:
         
              $totalmin   =  $totalhoras;
         
         endif;
         
         return $totalmin;
         
     }
     
    private function insertarConsepto($anio,$mes, $periodo, $id_sys_rrhh_cedula, $id_sys_rrhh_concepto, $id_sys_empresa, $cantidad, $valor, $departamento){
         
         $db = $_SESSION['db'];
        
         $transaction = \Yii::$app->$db->beginTransaction();

 
         try {
             //insertamos el sueldo del empleador
             
             //Valida insertar concepto
             
             $objconcepto = SysRrhhEmpleadosRolMov::find()->where(['anio'=> $anio])->andWhere(['mes'=> $mes])->andWhere(['periodo'=> $periodo])->andWhere(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])->andWhere(['id_sys_rrhh_concepto'=> $id_sys_rrhh_concepto])->one();
             
             if(!$objconcepto):
             
                 $concepto                          = new SysRrhhEmpleadosRolMov();
                 $concepto->anio                    = $anio;
                 $concepto->mes                     = $mes;
                 $concepto->periodo                 = $periodo;
                 $concepto->id_sys_rrhh_cedula      = $id_sys_rrhh_cedula;
                 $concepto->id_sys_rrhh_concepto    = $id_sys_rrhh_concepto;
                 $concepto->id_sys_empresa          = $id_sys_empresa;
                 $concepto->cantidad                = $cantidad;
                 $concepto->valor                   = $valor;
                 $concepto->estado                  = 'Q';
                 $concepto->transaccion_usuario     = Yii::$app->user->username;
                 $concepto->id_sys_adm_departamento = $departamento;
                 $concepto->save(false);            
                
            endif;
            $transaction->commit();
            $mjs = ['estado'=> true, 'mensaje'=> 'OK'];    
            return $mjs;
         
             
         } catch (\ErrorException $e) {
             
             $transaction->rollBack();
             $mjs = ['estado'=> false, 'mensaje'=> json_encode($e->getMessage()).' Cedula '.$id_sys_rrhh_cedula. ' Concepto '.$id_sys_rrhh_concepto];   
             return $mjs;
         }
         
       
         
     }
      
    private function getValor25 ($id_sys_rrhh_cedula, $id_sys_empresa, $fecha_ini, $fecha_fin){
         
         
         return  (new \yii\db\Query())
         ->select(['isnull(sum(horas25), 0)'])
         ->from("sys_rrhh_marcaciones_empleados")
         ->where("fecha_laboral between  '{$fecha_ini}' and  '{$fecha_fin}'")
         ->andwhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
         ->andwhere("pago25 = 1")
         ->scalar(SysRrhhEmpleados::getDb());
 
     }
    
    private function getValor50 ($id_sys_rrhh_cedula, $id_sys_empresa, $fecha_ini, $fecha_fin){
         
         
         return  (new \yii\db\Query())
         ->select(['isnull(sum(horas50), 0)'])
         ->from("sys_rrhh_marcaciones_empleados")
         ->where("fecha_laboral between  '{$fecha_ini}' and  '{$fecha_fin}'")
         ->andwhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
         ->andwhere("pago50 = 1")
         ->scalar(SysRrhhEmpleados::getDb());
         
     }
     
    private function getValor100 ($id_sys_rrhh_cedula, $id_sys_empresa, $fecha_ini, $fecha_fin){
         
         
         return  (new \yii\db\Query())
         ->select(['isnull(sum(horas100), 0)'])
         ->from("sys_rrhh_marcaciones_empleados")
         ->where("fecha_laboral between  '{$fecha_ini}' and  '{$fecha_fin}'")
         ->andwhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
         ->andwhere("pago100 = 1")
         ->scalar(SysRrhhEmpleados::getDb());
         
     }
     
    private function getNovedades($id_sys_rrhh_cedula, $id_sys_empresa, $fecha_ini, $fecha_fin, $pago){
         
         return  (new \yii\db\Query())
         ->select(["sum(cantidad) as valor", "sys_rrhh_empleados_novedades.id_sys_rrhh_concepto as novedad", "count(sys_rrhh_conceptos.id_sys_rrhh_concepto)  as numero"])
         ->from("sys_rrhh_empleados_novedades")
         ->innerJoin("sys_rrhh_conceptos", "sys_rrhh_empleados_novedades.id_sys_rrhh_concepto = sys_rrhh_conceptos.id_sys_rrhh_concepto")
         ->where("fecha between  '{$fecha_ini}' and  '{$fecha_fin}'")
         ->andwhere("sys_rrhh_empleados_novedades.id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
         ->groupBy(["sys_rrhh_empleados_novedades.id_sys_rrhh_concepto <> 'DES_HORAS_NL'"])
         ->andwhere("pago = '{$pago}'")
         ->groupBy(["sys_rrhh_empleados_novedades.id_sys_rrhh_concepto"])
         ->all(SysRrhhEmpleados::getDb());
         
     }
     
     private function getHorasNoLaboradasComedor($id_sys_rrhh_cedula, $fecha_ini, $fecha_fin){
         
         return  (new \yii\db\Query())
         ->select(["count(sys_rrhh_conceptos.id_sys_rrhh_concepto) * 0.25  as total"])
         ->from("sys_rrhh_empleados_novedades")
         ->innerJoin("sys_rrhh_conceptos", "sys_rrhh_empleados_novedades.id_sys_rrhh_concepto = sys_rrhh_conceptos.id_sys_rrhh_concepto")
         ->where("fecha between  '{$fecha_ini}' and  '{$fecha_fin}'")
         ->andwhere("sys_rrhh_empleados_novedades.id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
         ->andwhere("sys_rrhh_empleados_novedades.id_sys_rrhh_concepto = 'DES_HORAS_NL'")
         ->groupBy(["sys_rrhh_empleados_novedades.id_sys_rrhh_concepto"])
         ->scalar(SysRrhhEmpleados::getDb());
         
     }
     
    private function getHaberes ($id_sys_rrhh_cedula, $id_sys_empresa, $pago){
         
         
         return  (new \yii\db\Query())
         ->select(["id_sys_rrhh_empleados_haber", "id_sys_rrhh_concepto", "cantidad","mes_ini","anio_ini", "mes_fin", "anio_fin", "decimo"])
         ->from("sys_rrhh_empleados_haberes")
         ->where("cantidad > 0")
         ->andwhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
         ->andwhere("pago = '{$pago}'")
         ->andwhere("id_sys_rrhh_concepto <> 'INCE_MAREA'")
         ->all(SysRrhhEmpleados::getDb());
         
         
     }
     
    private function getPrestamo($id_sys_rrhh_cedula, $id_sys_empresa, $anio, $mes, $pago){
        
        $db = $_SESSION['db']; 
    
        $datos = Yii::$app->$db->createCommand("exec [dbo].[ObtenerCuotaPrestamosEmpresa] @cedula= '{$id_sys_rrhh_cedula}', @periodo = '{$pago}', @anio = '{$anio}', @mes= '{$mes}'")->queryScalar();

        return $datos;
    }
     
    private function getNovedadesAportaIess($id_sys_rrhh_cedula, $id_sys_empresa, $anio, $mes, $periodo){
         
         return  (new \yii\db\Query())
         ->select(["isnull(sum(valor), 0)"])
         ->from("sys_rrhh_empleados_rol_mov")
         ->Where("id_sys_rrhh_concepto in (select id_sys_rrhh_concepto  from sys_rrhh_conceptos where aporta_iess = 'S' and id_sys_rrhh_concepto not like '%SUELDO%')")
         ->andWhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
         ->andWhere("anio = '{$anio}'")
         ->andWhere("mes  = '{$mes}'")
         ->andWhere("periodo = '{$periodo}'")
         ->scalar(SysRrhhEmpleados::getDb());
         
         
     }
     
    private function getNovedadesAportaRenta($id_sys_rrhh_cedula, $id_sys_empresa, $anio, $mes, $periodo){
         
         return  (new \yii\db\Query())
         ->select(["isnull(sum(valor), 0)"])
         ->from("sys_rrhh_empleados_rol_mov")
         ->Where("id_sys_rrhh_concepto in (select id_sys_rrhh_concepto  from sys_rrhh_conceptos where aporta_rentas = 'S' and id_sys_rrhh_concepto not like '%SUELDO%')")
         ->andWhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
         ->andWhere("anio = '{$anio}'")
         ->andWhere("mes  = '{$mes}'")
         ->andWhere("periodo = '{$periodo}'")
         ->scalar(SysRrhhEmpleados::getDb());
         
         
     }
     
    private function getGastosDeducibles($id_sys_rrhh_cedula, $id_sys_empresa){
         
         return  (new \yii\db\Query())
         ->select(["isnull(sum(cantidad), 0)"])
         ->from("sys_rrhh_empleados_gastos")
         ->andWhere("id_sys_rrhh_cedula = '$id_sys_rrhh_cedula'")
         ->scalar(SysRrhhEmpleados::getDb());
         
         
     }
     
    private function getMaxGastosDedudibles($id_sys_empresa){
         
         return  (new \yii\db\Query())
         ->select(["isnull(sum(max_gasto), 0)"])
         ->from("sys_rrhh_rubros_gastos")
         ->scalar(SysRrhhEmpleados::getDb());
         
     }
  
    private function getFracionTabla($totalingreso, $id_sys_empresa){
         
         return  (new \yii\db\Query())
         ->select(["fraccion_basica", "fraccion_excedente", "impuesto_fraccion_basica", "impuesto_fraccion_excedente"])
         ->from("sys_rrhh_impuesto_renta")
         ->where("fraccion_basica <= '{$totalingreso}' and  '{$totalingreso}' <= fraccion_excedente")
         ->orderBy("id_sys_rrhh_impuesto_renta asc")
         ->one(SysRrhhEmpleados::getDb());
         
     }
          
    private function getAnioLaboral($fecharol, $id_sys_rrhh_cedula){
          
        $db = $_SESSION['db']; 
        
        $diasLaborados = intval(Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerAniosLaborados] @id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")->queryScalar());
      
        //Consultar dias en PBS
        /*$db = trim($_SESSION['db']);          
         $dbMysql = "";
                
         if ($db == "DB_GestionPespesca"):
                
            $dbMysql = "db_pbsgestion";
                
        elseif($db == "DB_GestionTalleres"):
                
            $dbMysql = "db_tallermarsa";
                
        elseif($db == "DB_GestionMarPesca"):
                
            $dbMysql = "db_marpesca";
                
       elseif($db == "DB_GestionFletatun"):
                
           $dbMysql = "db_buques";
                
       endif;
                
                
       if($dbMysql != ""):
                
          $pbs = $this->getConexionPBS($dbMysql);
                
           $diasLaborados = $diasLaborados +  intval( $pbs->createCommand("select sum(nrp_liq_dias_laborados) from nrp_liquidar_emp where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_per_numero =2;")->queryScalar());
                  
          $pbs->close();
                
       endif;*/
        
  
        return $diasLaborados/366;
            
     }
       
    private  function anioRentas($fechaing, $fechafin){
         
         
         $anios = 0;
         
         $diaingreso  = date('j', strtotime($fechaing));
         $mesingreso  = date('n', strtotime($fechaing));
         $anioingreso = date('Y', strtotime($fechaing));
         $anioact     = date('Y', strtotime($fechafin));
         $anios       = $anioact - $anioingreso;
         
         if($anios > 0):
         
             if(date('n', strtotime($fechafin)) <  $mesingreso):
             
                 $anios--;
                
             elseif(date('n', strtotime($fechafin)) == $mesingreso && date('j', strtotime($fechafin)) < $diaingreso):
             
                 $anios--;
             
             endif;
         
         endif;
         
        return $anios;
        
     }
       
    private function CalcularDias($fecha1,$fecha2) {
         
         $europeo=true;
         
         //try switch dates: min to max
         if( $fecha1 > $fecha2 ) {
             $temf = $fecha1;
             $fecha1 = $fecha2;
             $fecha2 = $temf;
         }
         
         list($yy1, $mm1, $dd1) = explode('-', $fecha1);
         list($yy2, $mm2, $dd2) = explode('-', $fecha2);
         
         if( $dd1==31) { $dd1 = 30; }
         
         if(!$europeo) {
             if( ($dd1==30) and ($dd2==31) ) {
                 $dd2=30;
             } else {
                 if( $dd2==31 ) {
                     $dd2=30;
                 }
             }
         }
         
         if( ($dd1<1) or ($dd2<1) or ($dd1>30) or ($dd2>31) or
             ($mm1<1) or ($mm2<1) or ($mm1>12) or ($mm2>12) or
             ($yy1>$yy2) ) {
                 return(-1);
             }
             if( ($yy1==$yy2) and ($mm1>$mm2) ) { return(-1); }
             if( ($yy1==$yy2) and ($mm1==$mm2) and ($dd1>$dd2) ) { return(-1); }
             
             //Calc
             $yy = $yy2-$yy1;
             $mm = $mm2-$mm1;
             $dd = $dd2-$dd1;
             
             return( ($yy*360)+($mm*30)+$dd );
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
       
    private function AgregaLiquidacion($anio, $mes, $id_sys_rrhh_cedula, $dias, $faltas){
             
         $model = SysRrhhEmpleadosRolLiq::find()->where(['anio'=> $anio])->andWhere(['mes'=> $mes])->andWhere(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])->one();
         
         
         if($model):
            
            $model->dias   =  $dias;
            $model->faltas =  $faltas;
            $model->save(false);
           
         else:
         
             $newmodel                     =  new SysRrhhEmpleadosRolLiq();
             $newmodel->anio               =  $anio;
             $newmodel->mes                =  $mes;
             $newmodel->id_sys_rrhh_cedula =  $id_sys_rrhh_cedula;
             $newmodel->dias               =  $dias;
             $newmodel->faltas             =  $faltas;
             $newmodel->save(false);
           
         endif;
        
        
         
     }
     
    private function max_dia ($mes, $anio){
         
         
         return   date("d",(mktime(0,0,0,$mes+1,1,$anio)-1));
         
         
     }
       
    private function getMarea($fechafin){
         
    
      return  (new \yii\db\Query())
      ->select(["*"])
      ->from("sys_rrhh_mareas_cab")
      ->where("'{$fechafin}' <=  fecha_fin")
      ->andwhere("estado = 'C'")
      ->orderBy("fecha_inicio desc")
      ->one(SysRrhhEmpleados::getDb());
      
      
        
    }
    
    private function CalculaIncentivoMarea($rol, $id_sys_rrhh_cedula){
        
        
         $errores        = [];
         $db             = $_SESSION['db'];
         $marea          = $this->getMarea($rol->fecha_fin);
         $mesini         = 0;
         $mesfin         = 0;
         $anioini        = 0;
         $aniofin        = 0;
         $valorincentivo = 0;
         $descuento      = 0;
         $incentivoMarea =    (new \yii\db\Query())
         ->select(["id_sys_rrhh_concepto", "cantidad"])
         ->from("sys_rrhh_empleados_haberes")
         ->where("cantidad > 0")
         ->andwhere("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
         ->andwhere("id_sys_rrhh_concepto = 'INCE_MAREA'")
         ->one(SysRrhhEmpleados::getDb());
         
       
         
                 if($marea):
                 
                   
                 
                          $tripuMarea =  $this->getEmpleadoMarea($marea['id_sys_rrhh_mareas_cab'], $id_sys_rrhh_cedula);
                     
                          if($tripuMarea):
                         
                             if($marea['fecha_fin'] != null):
                             
                                   $mesini  =  intval(date('m', strtotime($marea['fecha_inicio'])));
                                   $mesfin  =  intval(date('m', strtotime($marea['fecha_fin'])));
                                   $anioini =  date('Y', strtotime($marea['fecha_inicio']));
                                   $aniofin =  date('Y', strtotime($marea['fecha_fin']));
                                   
                                   if($anioini == $aniofin):
                                  
                                      $descuento = Yii::$app->$db->createCommand("select sum(valor) from sys_rrhh_empleados_rol_mov where id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}' and id_sys_rrhh_concepto = 'SUELDO' and anio = '{$anioini}' and mes >= '{$mesini}' and mes <= '{$mesfin}'")->queryScalar();
                                            
                                   elseif($anioini != $aniofin):
                                   
                                       $descuento = Yii::$app->$db->createCommand("select sum(valor) from sys_rrhh_empleados_rol_mov where id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}' and id_sys_rrhh_concepto = 'SUELDO' and anio = '{$anioini}' and mes >= '{$mesini}'")->queryScalar();
                                       $descuento = $descuento + Yii::$app->$db->createCommand("select sum(valor) from sys_rrhh_empleados_rol_mov where id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}' and id_sys_rrhh_concepto = 'SUELDO' and anio = '{$aniofin}' and mes <= '{$mesfin}'")->queryScalar();
                                       
                                   endif;
                                   
                                   if($incentivoMarea):
                                   
                                           $valorincentivo =  floatval($incentivoMarea['cantidad']) * floatval($marea['tonelada']);
                                           
                                           $valorincentivo = $valorincentivo - $descuento;
                                           
                                   
                                           if($valorincentivo > 0):
                                           
                                           $newconcepto = $this->insertarConsepto($rol->anio, $rol->mes, $rol->periodo, $id_sys_rrhh_cedula, $incentivoMarea['id_sys_rrhh_concepto'], $rol->id_sys_empresa,  1, $valorincentivo, $marea['id_sys_rrhh_barcos']);
                                                   
                                                   
                                                   if($newconcepto['estado'] == false):
                                                   
                                                         $errores [] = array('mensaje'=> $newconcepto['mensaje']);
                                                   
                                                   endif;
                  
                                           endif;
                                   
                                   endif;
                                   
                             endif;
                             
                        endif;
         
            endif;
         
        
        return $errores;
        
    }
    
    private function getEmpleadoMarea($id_marea, $id_sys_rrhh_cedula){
        
        return SysRrhhMareasDet::find()->where(['id_sys_rrhh_marea_cab'=> $id_marea])->andWhere(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])->one();
        
    }
    
    private function getConexionPBS($database){
        
        
        $connection = new \yii\db\Connection([
            'dsn' => 'mysql:host=192.168.1.5;dbname='.$database,
            'username' => 'root',
            'password' => 'pespesca',
            'charset' => 'utf8',
            
        ]);
        
        return $connection;
    }
    
    
  
    /**
     * Finds the SysRrhhEmpleadosRolCab model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $anio
     * @param string $mes
     * @param string $periodo
     * @param string $id_sys_empresa
     * @return SysRrhhEmpleadosRolCab the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($anio, $mes, $periodo, $id_sys_empresa)
    {
        if (($model = SysRrhhEmpleadosRolCab::findOne(['anio' => $anio, 'mes' => $mes, 'periodo' => $periodo, 'id_sys_empresa' => $id_sys_empresa])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
