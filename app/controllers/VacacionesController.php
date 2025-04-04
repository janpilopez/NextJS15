<?php

namespace app\controllers;
use Mpdf\Mpdf;
use Yii;
use app\models\SysRrhhEmpleadosPeriodoVacaciones;
use app\models\search\SysRrhhEmpleadosPeriodoVacacionesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\SysAdmCargos;
use app\models\SysAdmDepartamentos;
use app\models\SysAdmUsuariosDep;
use app\models\SysEmpresa;
use app\models\SysRrhhVacacionesSolicitud;
use app\models\SysRrhhEmpleados;
use app\models\SysAdmPeriodoVacaciones;
use app\models\User;
use app\models\sysAdmMandos;

/**
 * VacacionesController implements the CRUD actions for SysRrhhEmpleadosPeriodoVacaciones model.
 */
class VacacionesController extends Controller
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
     * Lists all SysRrhhEmpleadosPeriodoVacaciones models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = '@app/views/layouts/main_emplados';
        $searchModel = new SysRrhhEmpleadosPeriodoVacacionesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhEmpleadosPeriodoVacaciones model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new SysRrhhEmpleadosPeriodoVacaciones model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysRrhhEmpleadosPeriodoVacaciones();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_sys_rrhh_empleados_periodo_vacaciones]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
        
    }

    /**
     * Updates an existing SysRrhhEmpleadosPeriodoVacaciones model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_sys_rrhh_empleados_periodo_vacaciones]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SysRrhhEmpleadosPeriodoVacaciones model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        //$this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    //accion ver calendario
    public function actionVercalendario(){
        
        $this->layout = '@app/views/layouts/main_emplados';
        
        return $this->render('calendario');
        
   
    }
    public function actionCalendario($start=NULL,$end=NULL,$_=NULL){
        
       \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
           
        $userdeparta = SysAdmUsuariosDep::find()->where(['id_usuario'=> Yii::$app->user->id])->one();
        $areas = [];
        $departamentos =[];
        
        if(trim($userdeparta->area) != ''):
            $areas =  SysAdmUsuariosDep::find()->select('area')->where(['id_usuario'=> Yii::$app->user->id])->asArray()->column();
        else:
             $areas =  SysAdmUsuariosDep::find()->select('area')->asArray()->column();
        endif;
        
        if(trim($userdeparta->departamento) != ''):
            $departamentos =  SysAdmUsuariosDep::find()->select('departamento')->where(['id_usuario'=> Yii::$app->user->id])->asArray()->column();
        else:
            $departamentos =  SysAdmDepartamentos::find()->select('id_sys_adm_departamento')->asArray()->column();
        endif;
        
  
        
       $usuario        =  SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> Yii::$app->user->identity->cedula])->andWhere(['id_sys_empresa'=> '001'])->one();
        
       $nivel          =  $this->getNivelcargo($usuario->id_sys_adm_cargo, $usuario->id_sys_empresa);
       
       if($nivel == 1 ):
               $solicitud =  SysRrhhVacacionesSolicitud::find()
               ->joinWith(['empleado'])
               ->joinWith(['periodo'])
               ->join('join', 'sys_adm_cargos', 'sys_rrhh_empleados.id_sys_adm_cargo =  sys_adm_cargos.id_sys_adm_cargo')
               ->join('join', 'sys_adm_mandos', 'sys_adm_cargos.id_sys_adm_mando     = sys_adm_mandos.id_sys_adm_mando')
               ->join('join', 'sys_adm_departamentos', 'sys_adm_cargos.id_sys_adm_departamento =  sys_adm_departamentos.id_sys_adm_departamento')
               ->join('join', 'sys_adm_areas', 'sys_adm_departamentos.id_sys_adm_area =  sys_adm_areas.id_sys_adm_area')
               ->where(['between', 'fecha_inicio', $start, $end])
               ->andWhere(['sys_adm_departamentos.id_sys_adm_area'=> $areas])
               ->andWhere(['sys_adm_departamentos.id_sys_adm_departamento'=> $departamentos])
               ->andwhere(['sys_rrhh_vacaciones_solicitud.estado'=> 'A'])
               ->andwhere(['sys_rrhh_vacaciones_solicitud.id_sys_empresa'=> '001'])
               ->andWhere(['sys_adm_mandos.nivel' => '2'])
               ->all();
       else:
               $solicitud =  SysRrhhVacacionesSolicitud::find()
               ->joinWith(['empleado'])
               ->joinWith(['periodo'])
               ->join('join', 'sys_adm_cargos', 'sys_rrhh_empleados.id_sys_adm_cargo =  sys_adm_cargos.id_sys_adm_cargo')
               ->join('join', 'sys_adm_departamentos', 'sys_adm_cargos.id_sys_adm_departamento =  sys_adm_departamentos.id_sys_adm_departamento')
               ->join('join', 'sys_adm_areas', 'sys_adm_departamentos.id_sys_adm_area =  sys_adm_areas.id_sys_adm_area')
               ->where(['between', 'fecha_inicio', $start, $end])
               ->andWhere(['sys_adm_departamentos.id_sys_adm_area'=> $areas])
               ->andWhere(['sys_adm_departamentos.id_sys_adm_departamento'=> $departamentos])
               ->andwhere(['sys_rrhh_vacaciones_solicitud.estado'=> 'A'])
               ->andwhere(['sys_rrhh_vacaciones_solicitud.id_sys_empresa'=> '001'])
               ->all();
       endif;
       
       
       $events = array();
        
       foreach ($solicitud as $data){
          
            $empledo = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $data->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> $data->id_sys_empresa])->one();
            //Testing
            
            if($data->estado_solicitud == 'A'):
               
               $color = '#5cb85c';
            
            elseif($data->estado_solicitud == 'P'):
            
               $color = '#f0ad4e';
            
            else:
            
               $color = '#d9534f';
            
            endif;
            
            
            $Event                  = new  \yii2fullcalendar\models\Event();
            $Event->id              = $data->id_sys_rrhh_vacaciones_solicitud;
            $Event->title           = $empledo->nombres;
            $Event->start           = $data->fecha_inicio.'T12:00:00';
            $Event->end             = $data->fecha_fin.'T12:00:00';
            $Event->backgroundColor = $color;
         
          
           // $Event->fontSize = '10px';
            $events[]     = $Event;
        }
        return $events;
  
    }
    
    public  function actionVersolicitud($codsolicitud = null){
        
        
        
        $solicitud = SysRrhhVacacionesSolicitud::find()->where(['id_sys_rrhh_vacaciones_solicitud'=> $codsolicitud])->andwhere(['estado'=> 'A'])->one();
       
        $empleado  = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $solicitud->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> $solicitud->id_sys_empresa])->one();
          
        $periodo   =  SysAdmPeriodoVacaciones::find()->where(['id_sys_adm_periodo_vacaciones'=>$solicitud->id_sys_rrhh_vacaciones_periodo])->andWhere(['id_sys_empresa'=> $solicitud->id_sys_empresa])->one();
        
        $table = '';
      
        $table.= '<tr><td><b>Solicitud No</b></td><td>'.str_pad($solicitud->id_sys_rrhh_vacaciones_solicitud, 10, "0", STR_PAD_LEFT).'</td><td><b>Fecha Registro:</b></td><td>'.$solicitud->fecha_registro.'</td></tr>';
        $table.= '<tr><td><b>Nombres</b></td><td>'.$empleado->nombre.'</td><td><b>Apellidos</b></td><td>'.$empleado->apellidos.'</td></tr>';
        $table.= '<tr><td><b>Fecha Inicio</b></td><td>'.$solicitud->fecha_inicio.'</td><td><b>Fecha Fin</b></td><td>'.$solicitud->fecha_fin.'</td></tr>';
        $table.= '<tr><td><b>Periodo</b></td><td>'.$periodo->periodo.'</td><td><b>Comentario</b></td><td>'.$solicitud->comentario.'</td></tr>';
        $table.= '<tr><td colspan="4" style="text-align: center;"><button style="margin-right:5px;" class ="btn btn-xs btn-success" onclick ="aprobarSolicitud('.$solicitud->id_sys_rrhh_vacaciones_solicitud.')">Aprobar Solicitud</button><button class ="btn btn-xs btn-danger" onclick ="anularSolicitud('.$solicitud->id_sys_rrhh_vacaciones_solicitud.')">Anular Solicitud</button></td></tr>';
        
        echo $table;
        
    }
    
    private function getNivelcargo($codcargo, $codempresa){
        
        $cargo =  SysAdmCargos::find()->where(['id_sys_adm_cargo'=> $codcargo])->andWhere(['id_sys_empresa'=> $codempresa])->one();
        
        if($cargo){
            
            $mando = sysAdmMandos::find()->where(['id_sys_adm_mando'=>$cargo->id_sys_adm_mando])->andWhere(['id_sys_empresa'=> $cargo->id_sys_empresa])->one();
            
            return $mando->nivel;
            
        }
        return 0;
    }
    
   
    public function actionAprobarsolicitud(){
        
        
        if (Yii::$app->request->isAjax && Yii::$app->request->post()) {
        
               
                $db =  $_SESSION['db'];
                
                $empresa       = SysEmpresa::find()->where(['db_name'=> $db])->one();
                
            
                $codsolicitud = Yii::$app->request->post('codsolicitud');
            
                $model =  SysRrhhVacacionesSolicitud::find()->where(['id_sys_rrhh_vacaciones_solicitud'=> $codsolicitud])->andWhere(['id_sys_empresa'=> '001'])->one();
                
                $periodo = SysRrhhEmpleadosPeriodoVacaciones::find()->where(['id_sys_adm_periodo_vacaciones'=> $model->id_sys_rrhh_vacaciones_periodo])->andWhere(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> $model->id_sys_empresa])->one();
                
                
                //Obtenemos datos del usuario
                $user       =  SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> trim( Yii::$app->user->identity->cedula)])->andWhere(['id_sys_empresa'=> '001'])->one();
                
                //Obtenemos datos del empleados
                $empleado      =  SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> trim($model->id_sys_rrhh_cedula)])->andWhere(['id_sys_empresa'=> '001'])->one();
                

                
                $nivelempleado =   $this->getNivelcargo(trim($empleado->id_sys_adm_cargo), $empleado->id_sys_empresa);
                
   
                
                $tipousuario   = $this->getTipoUsuario(Yii::$app->user->identity->id);
                
                $estado        =  'P';
                
                
                //permisos medicos aprobados
                if($tipousuario == 'G'):
                
                     $estado = 'A';
                      
                //Permisos aprobados por jefatura y jefe de DDoo
                elseif($nivelempleado > 2):
                        
                        if($tipousuario == 'J' || $tipousuario == 'D'):
                        
                              $estado = 'A';
                        //Permisos aprobados  por usuarios backups
                        
                        elseif($this->getUserBackup(Yii::$app->user->identity->cedula)):
                        
                            if(Yii::$app->user->identity->cedula !=  $empleado->id_sys_rrhh_cedula):
                            
                            $estado = 'A';
                            
                            endif;
                        
                        endif;
                
                //Ddoo Aprueba pemrisos de jefaturas
                elseif ($nivelempleado == 2 && $tipousuario == 'D'):
                
                
                       if($user):
                    
                            $tipoempleado = $this->getTipoUsuario($user['id']);
                            
                            if($tipoempleado != $tipousuario):
                            
                                  $estado = 'A';
                            
                            endif;
                        
                        endif;
                
                endif;
            
      
                
                $transaction  = \Yii::$app->$db->beginTransaction();
                
            
                $date1 = new \DateTime($model->fecha_inicio);
                $date2 = new \DateTime($model->fecha_fin);
                $diff  = $date1->diff($date2);
                // will output 2 days
                $dias  = $diff->days + 1;
                
                $periodo->dias_otorgados    =  $periodo->dias_otorgados + intval($dias);
                
                if($periodo->dias_otorgados == $periodo->dias_disponibles){
                    
                    $periodo->estado = 'T';
                    
                }
                       
                if($model->estado_solicitud == 'P'):
                
                        if($estado == 'A'):
                        
                            $model->estado_solicitud   = $estado;
                            $model->usuario_aprobacion =  Yii::$app->user->username;
                            $model->fecha_aprobacion   =  date('Ymd H:i:s');
                            
                            
                            
                            if($model->save(false)):
                            
                                  if($periodo->save(false)):
                                  
                                            if($model->estado == 'G'):
                                                
                                             
                                                    //Mail Gerente
                                                    $mailgerente   =  $this->getEmaiUser($this->getGerente());
                                                    //Mail Empleado
                                                    $mailempleado  =  $this->getEmailEmpleado($empleado->email);
                                                    
                                                    //Mail Seguridad
                                                    $mailSeguridad = "garita.peatonal@pespesca.com";
                                                                
                                                    $to = $mailempleado;
                                                    
                                                    $cc = [];
                                                    
                                                    $mensaje = "<p  style = 'margin:1px;'>La solicitud de vacaciones #".str_pad($model->id_sys_rrhh_vacaciones_solicitud, 10, "0", STR_PAD_LEFT)." del Sr/Sr(a): <b>".$empleado->nombres."</b> ha sido aprobada.</p><p  style = 'margin:1px;'><b>Fecha inicio: </b>".$model->fecha_inicio."</p><p  style = 'margin:1px;'><b>Fecha Culminación: </b>".$model->fecha_fin."</p><p  style = 'margin:1px;'><b>Fecha reintegro de labores: </b>".$this->fechafinalizacion($model->fecha_fin)."</p>";
                                            
                                                    
                                                    if($nivelempleado > 2):
    
                                                        //Notifica a Jefatura;
                                                        $emailjefe = $this->getEmaiUser($this->getJefeInmediato($empleado->id_sys_adm_cargo, $empleado->id_sys_empresa));
                                                        
                                                        if($emailjefe != ""):
                                                            array_push($cc, $emailjefe);
                                                        endif;
                                                        
                                                    elseif($nivelempleado == 2):
                                                        //Notifica a Gerencia 
                                                        array_push($cc, $mailgerente);
                                                    
                                                    endif;
                                                    
                                                    //Notifica a Seguridad
                                                    array_push($cc, $mailSeguridad);
                                                    
                                                    //Notifica a DDOO
                                                    array_push($cc, $empresa->mail_username);
                                                    
                                                    //Enviar Correo
                                                    $this->EnviarCorreo($to, $cc, $mensaje);
                                                    
                                                
                                           endif;
                                                                                    
                                          $transaction->commit();
                                          echo  json_encode(['data' => [ 'estado' => true,'mensaje' => 'Los datos se ha registrado con exito!']]);
                                      
                                  else:
                                  
                                        $transaction->rollBack();
                                        echo  json_encode(['data' => [ 'estado' => false,'mensaje' => 'Ha ocurrido un error al intentar actualizar el periodo!']]);
                                        
                                        
                                  endif;
                             else:
                             
                                 $transaction->rollBack();
                                 echo  json_encode(['data' => [ 'estado' => false,'mensaje' => 'Ha ocurrido un error!']]);
                                 
                            endif;
                            
                       else:
                       
                             $transaction->rollBack();
                             echo  json_encode(['data' => [ 'estado' => false,'mensaje' => 'Usted no tiene permisos para aprobar la solicitud!']]);
                            
                       endif;
                else:
                
                        $transaction->rollBack();
                        echo  json_encode(['data' => [ 'estado' => false,'mensaje' => 'La solicitud no se puede aprobar, porque no está pendiente!']]);
                        
                endif;
        }else{
            
            echo  json_encode(['data' => [ 'estado' => false,'mensaje' => 'No se recibio la peticion POST!']]);
            
        }
        
    }
    public function actionAnularsolicitud(){
        
        
        if (Yii::$app->request->isAjax && Yii::$app->request->post()) {
            
            $db = $_SESSION['db'];
            
            $empresa       = SysEmpresa::find()->where(['db_name'=> $db])->one();
            
            $codsolicitud = Yii::$app->request->post('codsolicitud');
            
            $comentario   = Yii::$app->request->post('comentario');
           
            $transaction  = \Yii::$app->$db->beginTransaction();
            
            $model =  SysRrhhVacacionesSolicitud::find()->where(['id_sys_rrhh_vacaciones_solicitud'=> $codsolicitud])->andWhere(['id_sys_empresa'=> '001'])->one();
            
            $periodo = SysRrhhEmpleadosPeriodoVacaciones::find()->where(['id_sys_adm_periodo_vacaciones'=> $model->id_sys_rrhh_vacaciones_periodo])->andWhere(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> $model->id_sys_empresa])->one();
            
            
            $periodo = SysRrhhEmpleadosPeriodoVacaciones::find()->where(['id_sys_adm_periodo_vacaciones'=> $model->id_sys_rrhh_vacaciones_periodo])->andWhere(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> $model->id_sys_empresa])->one();
            
            //Obtenemos datos del empleados
            $empleado      =  SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->one();
            

            //nivel del empleado
            $nivelempleado =   $this->getNivelcargo($empleado->id_sys_adm_cargo, $empleado->id_sys_empresa);
            
            $tipousuario   = $this->getTipoUsuario(Yii::$app->user->identity->id);
              
            $estado = '';
            
 
           //anula jefe de ddoo y analista de nomina 
             if(trim($tipousuario) == 'D'):
                    
               $estado = 'N';
                    
            endif;
                

            
            if($estado == 'N'):
            
                              if($model->estado_solicitud == 'A'):
                        
                        
                                        if($periodo->dias_otorgados > 0):
                                
                                                $date1 = new \DateTime($model->fecha_inicio);
                                                $date2 = new \DateTime($model->fecha_fin);
                                                $diff  = $date1->diff($date2);
                                                // will output 2 days
                                                $dias  = $diff->days + 1;
                                                
                                                $periodo->dias_otorgados =  $periodo->dias_otorgados - intval($dias);
                                                
                                                $periodo->estado = 'P';
                                        
                                        endif;
                                        
                                      $model->estado = 'I';
                                      
                                endif;
                                
                                
                                $model->estado_solicitud  = 'N';
                                $model->comentario        =  $comentario;
                                $model->usuario_anulacion =  Yii::$app->user->username;
                                $model->fecha_anulacion   =  date('Ymd H:i:s'); 
                                
                                
                              
                                if($model->save(false)):
                                
                                        if($periodo->save(false)):
                                            
                                                if($model->tipo == 'G'):
                                                    
                                                        //Mail Jefe Ddoo
                                                        $mailddoo     =  $this->getEmaiUser($this->getJefeDdoo());
                                                        //Mail Gerente
                                                        $mailgerente  =  $this->getEmaiUser($this->getGerente());
                                                        //Mail Seguridad
                                                        $mailSeguridad = "garita.peatonal@pespesca.com";
                                                        //Mail Empleado
                                                        $mailEmpleado = $this->getEmaiUser($empleado->id_sys_rrhh_cedula);
                                                        
                                                        $to = $mailddoo;
                                                        
                                                        $cc = [];
                                                        //Notifica Empleado
                                                        array_push($cc, $mailEmpleado);
                                                        
                                                        $mensaje       =  "<p  style = 'margin:1px;'>La solicitud de vacaciones #".str_pad($model->id_sys_rrhh_vacaciones_solicitud, 10, "0", STR_PAD_LEFT)." del Sr/Sr(a): <b>".$empleado->nombres."</b> ha sido anulada.</p><p  style = 'margin:1px;'>Comentario: ".$model->comentario."</p>";
                                                        
                                                        if($nivelempleado > 2):
                                                            
                                                                //Notificar Jefe Inmediato
                                                                $mailJefe = $this->getEmaiUser($this->getJefeInmediato($empleado->id_sys_adm_cargo, $empleado->id_sys_empresa));
                                                        
                                                                if($mailJefe != ""):
                                                                  array_push($cc, $mailJefe);
                                                                endif;
                                                               
                                                        elseif($nivelempleado == 2):
                                                               //Notificar Gerencia
                                                               array_push($cc, $mailgerente);
                                                                
                                                        endif;
                                                        //Notificar DDOO
                                                        array_push($cc, $empresa->mail_username);
                                                        
                                                      
                                                        //Notificar Seguiridad
                                                        array_push($cc, $mailSeguridad);
                                                        
                                                        $this->EnviarCorreo($to, $cc, $mensaje);
                                                        
                                                endif;
                 
                                                
                                            $transaction->commit();
                                            echo  json_encode(['data' => [ 'estado' => true,'mensaje' => 'La solicitud ha sido anulada con éxito!']]);
                                            
                                        else:
                                        
                                            $transaction->rollBack();
                                            echo  json_encode(['data' => [ 'estado' => false,'mensaje' => 'Ha ocurrido un error al intentar actualizar el periodo!']]);
                                            
                                        endif;
                                else:
                                
                                        $transaction->rollBack();
                                        echo  json_encode(['data' => [ 'estado' => false,'mensaje' => 'Ha ocurrido un error!']]);
                                        
                                endif;
                               
                         
                else:         
                         
                    echo  json_encode(['data' => [ 'estado' => false,'mensaje' => 'Usted no tiene privilegios  para anular la solicitud!!']]);
                
                endif;      

        }else{
            
           echo  json_encode(['data' => [ 'estado' => false,'mensaje' => 'No se recibio la peticion POST!']]);
           
       }
 
       
    }
   
    
    public  function actionInfoperiodovacaciones(){
        
        $area          = '';
        $departamento  = '';
        $periodo       = '';
        $datos         = [];
        $estado        = '';
        
        if(Yii::$app->request->post()):
        
           $periodo      =  $_POST['periodo'] == null ?  '': $_POST['periodo'];
           $departamento =  $_POST['departamento']== null ?  '': $_POST['departamento'];
           $area         =  $_POST['area']== null ?  '': $_POST['area'];
           $estado       =  $_POST['estado']== null ?  '': $_POST['estado'];

           $datos = $this->getPeriodoempleados($area, $departamento, $periodo, $estado);
           

        endif;
        
       return $this->render('infoperiodovacaciones',['area'=> $area, 'departamento'=> $departamento, 'periodo'=> $periodo,  'datos'=> $datos, 'estado'=> $estado]);
        
    }
  
   
    
    public function actionInfoperiodovacacionespdf($area, $departamento, $periodo, $estado){
       
        $datos = $this->getPeriodoempleados($area, $departamento, $periodo, $estado);    
        
        
        header("Pragma: public");
        header("Expires: 0");
        $filename = "INFORME_VACACIONES_PERIODO_".$periodo.".xls";
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=$filename");
        
        
       return  $this->renderAjax('infoperiodovacacionespdf', ['area'=> $area, 'departamento'=> $departamento, 'periodo'=> $periodo,  'datos'=> $datos, 'estado'=> $estado]);
        
      /*  $mpdf = new Mpdf([
            'format' => 'A4',
           // 'orientation' => 'L'
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('PeriodoVacaciones.pdf', 'I');
        
        exit();*/
    }
    
    
    public function actionInformegeneral(){
        
        $datos        =   [];
        $fechaini     =   date('Y-m-d');
        $fechafin     =   date('Y-m-d');
        $solicitudes  =   [];
        
        if(Yii::$app->request->post()):
        
        $fechaini = $_POST['fechaini']== null ? $fechaini : $_POST['fechaini'];
        $fechafin = $_POST['fechafin']== null ? $fechafin : $_POST['fechafin'];
        
        $solicitudes  = $this->getSolicitudvacaciones($fechaini, $fechafin);
        
        foreach ($solicitudes as $solicitud):
        
        $datos[] = array(
            'id_sys_rrhh_cedula' => $solicitud['id_sys_rrhh_cedula'],
            'nombres' => $solicitud['nombres'],
            'fecha_inicio' => $solicitud['fecha_inicio'],
            'fecha_fin'=> $solicitud['fecha_fin'],
            'dias'=> $solicitud['dias'],
            'periodo'=> $solicitud['periodo'],
            'area'=> $solicitud['area'],
            'departamento'=> $solicitud['departamento'],
            'diasdisponibles'=> $this->diasDisponiblesPeriodo($solicitud['id_sys_rrhh_cedula'], $solicitud['id_sys_adm_periodo_vacaciones']),
            'valor'=>  $this->getValProvacaciones($solicitud['id_sys_rrhh_cedula'], $solicitud['anio_vac'], $solicitud['anio_vac_hab'], $solicitud['id_sys_adm_periodo_vacaciones'])
        );
        
        
        endforeach;
        
        
        endif;
        
        return $this->render('informegeneral',['fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'datos'=> $datos]);
        
    }
    public function actionInformegeneralpdf($fechaini, $fechafin){
       
        
        header("Pragma: public");
        header("Expires: 0");
        $filename = "INFORME_VACACIONES_PERIODO_".$fechaini."_".$fechafin.".xls";
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=$filename");
        
        
        $solicitudes  = $this->getSolicitudvacaciones($fechaini, $fechafin);
        

        $datos = [];
        
        foreach ($solicitudes as $solicitud):
        
        $datos[] = array(
            'id_sys_rrhh_cedula' => $solicitud['id_sys_rrhh_cedula'],
            'nombres' => $solicitud['nombres'],
            'fecha_inicio' => $solicitud['fecha_inicio'],
            'fecha_fin'=> $solicitud['fecha_fin'],
            'dias'=> $solicitud['dias'],
            'periodo'=> $solicitud['periodo'],
            'area'=> $solicitud['area'],
            'departamento'=> $solicitud['departamento'],
            'diasdisponibles'=> $this->diasDisponiblesPeriodo($solicitud['id_sys_rrhh_cedula'], $solicitud['id_sys_adm_periodo_vacaciones']),
            'valor'=>  $this->getValProvacaciones($solicitud['id_sys_rrhh_cedula'], $solicitud['anio_vac'], $solicitud['anio_vac_hab'], $solicitud['id_sys_adm_periodo_vacaciones'])
        );
        
        
        endforeach;
        
 
        return $this->renderAjax('_tableliquidacionvac',['fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'datos'=> $datos]);
        
    }
        
    
    private function  getEmaiUser($id){
        
        $user  = User::find()->where(['id'=> $id])->one();
        
        if($user):
        
            return  trim($user->email);
        
        endif;
        
        return "";
        
        
    }

    //obtener usuario jefe DDoo
    private function getJefeDdoo(){
        
        
        $usertipo = SysAdmUsuariosDep::find()->where(['usuario_tipo'=> 'D'])->andWhere(['estado'=> 'A'])->one();
        
        if($usertipo):
        
             return $usertipo->id_usuario;
        
        endif;
        
        return '0';
        
    }
    //obtener usuario gerente
    private function getGerente(){
        
        
        $usertipo = SysAdmUsuariosDep::find()->where(['usuario_tipo'=> 'G'])->andWhere(['estado'=> 'A'])->one();
        
        if($usertipo):
        
             return $usertipo->id_usuario;
        
        endif;
        
        return '0';
    }
    
    private function getJefeInmediato($codcargo, $codempresa){
        
        //cargo usuario
        $cargo =  SysAdmCargos::find()->where(['id_sys_adm_cargo'=> $codcargo])->one();
        
        if($cargo):
        
            //departamento usuario
            $departamento = SysAdmDepartamentos::find()->where(['id_sys_adm_departamento'=> $cargo->id_sys_adm_departamento])->one();
            
            if($departamento):
            
                $usertipo     = SysAdmUsuariosDep::find()->where(['usuario_tipo'=> 'J'])->andWhere(['estado'=> 'A'])->andWhere(['departamento'=> $departamento->id_sys_adm_departamento])->one();
                
                if($usertipo):
                     return   $usertipo->id_usuario;
                endif;
                
             endif;
            
        endif;
        
        return '0';
        
    }
    
    //obtiene el tipo de usuario
    private function getTipoUsuario($id_usuario){
        
        $usertipo = SysAdmUsuariosDep::find()->where(['id_usuario'=> $id_usuario])->one();
        
        if($usertipo):
        
             return $usertipo->usuario_tipo;
        
        endif;
        
        return 'N';
    }
    
    private function getSolicitudvacaciones($fechaini, $fechafin){
        
        return  (new \yii\db\Query())->select(["sol.id_sys_rrhh_cedula", "emp.nombres",  "fecha_inicio", "fecha_fin", "(DATEDIFF( day, fecha_inicio, fecha_fin) + 1) as dias", "periodo", "area", "departamento", "anio_vac", "anio_vac_hab", "id_sys_adm_periodo_vacaciones"])
         ->from("sys_rrhh_vacaciones_solicitud sol")
        ->innerjoin('sys_rrhh_empleados emp','sol.id_sys_rrhh_cedula =  emp.id_sys_rrhh_cedula')->andWhere('sol.id_sys_empresa = emp.id_sys_empresa')
        ->innerjoin('sys_adm_cargos cargo','emp.id_sys_adm_cargo =  cargo.id_sys_adm_cargo')->andWhere('emp.id_sys_empresa = cargo.id_sys_empresa')
        ->innerjoin('sys_adm_departamentos departamento','cargo.id_sys_adm_departamento =  departamento.id_sys_adm_departamento')->andWhere('cargo.id_sys_empresa = departamento.id_sys_empresa')
        ->innerjoin('sys_adm_areas areas','departamento.id_sys_adm_area =  areas.id_sys_adm_area')->andWhere('departamento.id_sys_empresa = areas.id_sys_empresa')
        ->innerjoin('sys_adm_periodo_vacaciones periodo','sol.id_sys_rrhh_vacaciones_periodo =  periodo.id_sys_adm_periodo_vacaciones')->andWhere('sol.id_sys_empresa =  periodo.id_sys_empresa')
        ->where("sol.id_sys_empresa  = '001'")
        ->andWhere("sol.estado = 'A'")
        ->andwhere("fecha_registro between '{$fechaini}' and '{$fechafin}'")
        ->orderBy('fecha_registro')
        ->all(SysRrhhEmpleadosPeriodoVacaciones::getDb());
        
    }
    
    private function getValProvacaciones($id_sys_rrhh_cedula, $aniovac, $aniohab, $codperiodo){
           
        
         
            $db = $_SESSION['db'];
        
            $mesingreso =  (new \yii\db\Query())->select("month(fecha_ingreso)")
            ->from("sys_rrhh_empleados_contratos")
            ->where("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
            ->andwhere("id_sys_empresa = '001'")
            ->scalar(SysRrhhEmpleadosPeriodoVacaciones::getDb());
          
               $anio_act         = 0;
               $anio_ant         = 0;
               $anio_ant_gestion = 0;
            
        
      if($aniohab < 2019):
      
        //revisar en el PBs
          $anio_ant  = Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes >= '{$mesingreso}'  and nrp_per_anio = '{$aniovac}';")->queryScalar();
          $anio_act  = Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes < '{$mesingreso}'  and nrp_per_anio = '{$aniohab}';")->queryScalar();
          
          return ( $anio_act + $anio_ant);
        
      else:
      
            if($aniohab > 2020):
            
              
                  //revisar en vacaciones en gestion
                  $anio_ant  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes >= '{$mesingreso}' and anio = '{$aniovac}'")->queryScalar();
                  $anio_act  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes < '{$mesingreso}' and anio = '{$aniohab}'")->queryScalar();
            
            else:
               //revisar periodo 2018 and 2019 
                  if($aniohab == 2019):
                
                        if($mesingreso > 9):
                         //revisar en el pbs anio 2018 y 2019
                            $anio_ant          = Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes >= '{$mesingreso}'  and nrp_per_anio = '{$aniovac}';")->queryScalar();
                            $anio_act          = Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes < '{$mesingreso}'  and nrp_per_anio = '{$aniohab}';")->queryScalar();
      
                          //revisar gestion 
                            $anio_ant_gestion  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes > 9")->queryScalar();
                          
                           return ($anio_act + $anio_ant + $anio_ant_gestion);
                           
                           
                           
                         else :
                         
                         //revisar en el pbs anio 2018 y 2019 hasta septiembre 
                          $anio_ant  = Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes >= '{$mesingreso}'  and nrp_per_anio = '{$aniovac}';")->queryScalar();
                          $anio_act  = Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes < '{$mesingreso}'  and nrp_per_anio = '{$aniohab}';")->queryScalar();
                          
                          return ($anio_act + $anio_ant);
                          
                          
                          
                        endif;
              
                  //revisar vacaciones del periodo 2019 hasta 2020
                  elseif($aniohab == 2020):
                    
                        if($mesingreso > 9):
                        
                            //revisar en el pbs anio 2019 y 2020
                              $anio_ant          = Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes >= '{$mesingreso}'  and nrp_per_anio = '{$aniovac}';")->queryScalar();
                              $anio_act          = Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes < '{$mesingreso}'  and nrp_per_anio = '{$aniohab}';")->queryScalar();
                              
                              //revisar gestion
                              $anio_ant_gestion  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'  and mes > 9")->queryScalar();
                              
                              return ($anio_act + $anio_ant + $anio_ant_gestion);
                        
                        else:
            
                            //revisar en el pbs anio 2019 y 2020 hasta septiembre
                            $anio_act          = Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes >= '{$mesingreso}'  and nrp_per_anio = '{$aniovac}';")->queryScalar();
                            $anio_ant          = Yii::$app->db_pbsgestion->createCommand("select sum(nrp_liq_valor) FROM db_pbsgestion.nrp_liquidar_con where nrp_emp_codigo = '{$id_sys_rrhh_cedula}' and nrp_con_codigo = 'VACACIONES' and nrp_per_mes < '{$mesingreso}'   and nrp_per_anio = '{$aniohab}';")->queryScalar();
                            
                            $anio_act_gestion  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}' and mes <  '{$mesingreso}' and anio = '{$aniohab}'")->queryScalar();
                            $anio_ant_gestion  = Yii::$app->$db->createCommand("select isnull(sum(valor), 0) FROM sys_rrhh_empleados_rol_mov where id_sys_rrhh_concepto ='VACACIONES' and id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}' and mes > 9 and anio = '{$aniovac}'")->queryScalar();
                            
                            return ($anio_act + $anio_ant+ $anio_ant_gestion + $anio_act_gestion);
                                  
                        endif;
                   endif;
             endif;
        endif;
       
      return 0;
        
    }
       
    private function getPeriodoempleados($area, $departamento, $periodo, $estado){
        
        
        return  (new \yii\db\Query())->select(["vac.id_sys_rrhh_cedula", "emp.nombres", "periodo", "area", "departamento", "dias_disponibles", "dias_otorgados", "vac.estado", "(select fecha_ingreso
  from [dbo].[sys_rrhh_empleados_contratos]
  where fecha_salida is null and id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula) as fechaing"])
        ->from("sys_rrhh_empleados_periodo_vacaciones vac")
        ->innerjoin('sys_rrhh_empleados emp','vac.id_sys_rrhh_cedula =  emp.id_sys_rrhh_cedula')->andWhere('vac.id_sys_empresa = emp.id_sys_empresa')
        ->innerjoin('sys_adm_cargos cargo','emp.id_sys_adm_cargo =  cargo.id_sys_adm_cargo')->andWhere('emp.id_sys_empresa = cargo.id_sys_empresa')
        ->innerjoin('sys_adm_departamentos departamento','cargo.id_sys_adm_departamento =  departamento.id_sys_adm_departamento')->andWhere('cargo.id_sys_empresa = departamento.id_sys_empresa')
        ->innerjoin('sys_adm_areas areas','departamento.id_sys_adm_area =  areas.id_sys_adm_area')->andWhere('departamento.id_sys_empresa = areas.id_sys_empresa')
        ->innerjoin('sys_adm_periodo_vacaciones periodo','vac.id_sys_adm_periodo_vacaciones =  periodo.id_sys_adm_periodo_vacaciones')->andWhere('vac.id_sys_empresa =  periodo.id_sys_empresa')
        ->where("vac.id_sys_empresa = '001'")
        ->andWhere("emp.estado = 'A'")
        ->andFilterWhere(["like","vac.id_sys_adm_periodo_vacaciones", $periodo])
        ->andFilterWhere(["like","areas.id_sys_adm_area", $area])
        ->andFilterWhere(["like", "departamento.id_sys_adm_departamento", $departamento])
        ->andFilterWhere(["like", "vac.estado", $estado])
        ->orderBy('emp.nombres')
        ->all(SysRrhhEmpleadosPeriodoVacaciones::getDb());
        
    }
    
    private function EnviarCorreo($to, $cc, $mensaje){
        
        
        $db = $_SESSION['db'];
        
        $empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();
        
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
                  
                $newCC = array_filter($cc, "strlen");
                
                if(count($newCC) > 0):
                    
                    Yii::$app->mailer->compose()
                    ->setTo($to)
                    ->setCc($newCC)
                    ->setFrom([$empresa->mail_username => $empresa->razon_social])
                    ->setSubject('Notificación de vacaciones - Gestión Nómina')
                    ->setHtmlBody($mensaje)
                    ->send();
                
                else:
                    
                    Yii::$app->mailer->compose()
                    ->setTo($to)
                    ->setFrom([$empresa->mail_username => $empresa->razon_social])
                    ->setSubject('Notificación de vacaciones - Gestión Nómina')
                    ->setHtmlBody($mensaje)
                    ->send();
                    
                endif;
                
        } catch (\Exception $e) {
            
        
        }
        
    }
       
    private function diasDisponiblesPeriodo($id_sys_rrhh_cedula,$codperiodo){
        
         return   (new \yii\db\Query())->select("dias_disponibles")
        ->from("sys_rrhh_empleados_periodo_vacaciones")
        ->where("id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
        ->andwhere("id_sys_adm_periodo_vacaciones = '{$codperiodo}'")
        ->andwhere("id_sys_empresa = '001'")
        ->scalar(SysRrhhEmpleadosPeriodoVacaciones::getDb());
       
    }
    
    private function  getUserBackup($cedula){
        
        return  User::find()->where(['cedula'=> $cedula])->andWhere(['backups'=> '1']) ->one();
        
    }
    
    //obtener email personal empleado
    
    private function getEmailEmpleado($emailEmpleado){
        
        $email  = "";
        $emails = [];
        
        if(trim($emailEmpleado) != ""):
        
                $variosemails = strpos($emailEmpleado, ';');
                
                if($variosemails !== false):
                
                    $emails    = explode(';', $emailEmpleado);
                    $email     = $emails[0];
                
                else:
                
                    $email     = $emailEmpleado;
                
                endif;
        
        endif;
        
        return $email;
        
        
    }
    
    
 
    /**
     * Finds the SysRrhhEmpleadosPeriodoVacaciones model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SysRrhhEmpleadosPeriodoVacaciones the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhEmpleadosPeriodoVacaciones::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
