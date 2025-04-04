<?php

namespace app\controllers;

use app\models\SysAdmUsuariosPer;
use DateTime;
use Yii;
use app\models\SysAdmCargos;
use app\models\SysEmpresa;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosPermisos;
use app\models\sysAdmMandos;
use app\models\Search\SysRrhhEmpleadosPermisosSearch;
use webvimark\modules\UserManagement\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\SysAdmDepartamentos;
use app\models\SysRrhhPermisos;
use app\models\SysAdmUsuariosDep;

/**
 * PermisosController implements the CRUD actions for SysRrhhEmpleadosPermisos model.
 */
class PermisosController extends Controller
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
     * Lists all SysRrhhEmpleadosPermisos models.
     * @return mixed
     */
    public function actionIndex()
    {
    
        $this->layout = '@app/views/layouts/main_emplados';
        $searchModel = new SysRrhhEmpleadosPermisosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhEmpleadosPermisos model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    public function actionPermisoslote(){
        
        return $this->render('_permisolote');
    }
    public function actionGuardarlote(){
        
        if (Yii::$app->request->isAjax && Yii::$app->request->post()) {
            
            $obj         =  json_decode(Yii::$app->request->post('cadena'));
            
            $empleados   =  $obj->{'empleados'};
         
            $db = $_SESSION['db'];
            
            $transaction =  Yii::$app->$db->beginTransaction();
            
            $flag = true;
            
            foreach ($empleados as $data ){
                
                $empleado  = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> trim($data->cedula)])->andWhere(['id_sys_empresa'=> '001'])->one();
                
                if($empleado){
                    
                    $codpermiso =  SysRrhhEmpleadosPermisos::find()->select(['max(CAST(id_sys_rrhh_empleados_permiso AS INT))'])->Where(['id_sys_empresa'=> '001'])->scalar();
                    
                    $model = new SysRrhhEmpleadosPermisos();
                    $model->id_sys_rrhh_empleados_permiso = $codpermiso + 1;
                    $model->id_sys_rrhh_cedula  =  $data->cedula;
                    $model->transaccion_usuario =  Yii::$app->user->username;
                    $model->fecha_ini           =  $obj->{'fechaini'};
                    $model->hora_ini            =  $obj->{'horaini'};
                    $model->fecha_fin           =  $obj->{'fechafin'};
                    $model->hora_fin            =  $obj->{'horafin'};
                    $model->id_sys_rrhh_permiso =  $obj->{'permiso'};
                    $model->tipo                =  $obj->{'tipo'};
                    $model->comentario          =  $obj->{'comentario'};
                    $model->estado_permiso      =  'A' ;
                    $model->usuario_aprobacion  =  Yii::$app->user->username;
                    $model->fecha_aprobacion    =  date('Ymd H:i:s');
                    $model->id_sys_empresa = '001';
                   
                    if(!$flag = $model->save(false)){
                            
                        break;
                       
                    }
                    
                }
                
                
            }
            
            if($flag){
                $transaction->commit();
                echo  json_encode(['data' => [ 'estado' => false,'mensaje' => 'Los datos se ha registrado con exito!']]);
                
            }else{
                $transaction->rollBack();
                echo  json_encode(['data' => ['estado' => false, 'mensaje' => 'Ha ocurrido un error al guardar el permiso!']]);
            }
             
          }else{
            
            echo  json_encode(['data' => ['estado' => false, 'mensaje' => 'Ha ocurrido un error!']]);
        }
        
    }
    public function actionEmpleadosdepartamento($departamento){
        
        $datos = [];
        
        $datos = (new \yii\db\Query())->select(['id_sys_rrhh_cedula', 'nombres'])
        ->from('sys_rrhh_empleados')
        ->innerjoin('sys_adm_cargos', 'sys_rrhh_empleados.id_sys_adm_cargo = sys_adm_cargos.id_sys_adm_cargo and sys_rrhh_empleados.id_sys_empresa = sys_adm_cargos.id_sys_empresa')
        ->where("sys_rrhh_empleados.id_sys_empresa = '001'")
        ->andWhere("sys_rrhh_empleados.estado = 'A'")
        ->andwhere("sys_adm_cargos.id_sys_adm_departamento = {$departamento}")
        ->orderby('nombres')
        ->all(SysRrhhEmpleadosPermisos::getDb());
        
        return $this->renderAjax('_listempleados', [
            'datos'=>$datos
        ]);
        
    }
   
    /**
     * Creates a new SysRrhhEmpleadosPermisos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model        = new SysRrhhEmpleadosPermisos();
        $tipousuario  = $this->getTipoUsuario(Yii::$app->user->identity->id);
        $listpermisos = [];
        
        $permisos = SysAdmUsuariosPer::find()->where(['usuario_tipo'=> $tipousuario])->all();

        foreach($permisos as $index){

            if($index['permiso'] != ''){

                if($index['estado'] == 'A'):

                    array_push($listpermisos, $index['permiso']);

                endif;

            }else{

                if($index['estado'] == 'A'):

                    $listpermisos =  SysRrhhPermisos::find()->select('id_sys_rrhh_permiso')->where(['estado'=>'A'])->column();
            
                endif;

            }

        }
 
        if ($model->load(Yii::$app->request->post())) {
            
            $titulo        = 'Solicitud de Permiso';
            
            $ipserver      = Yii::$app->params['ipServer']; //ipserver
            
            $db            = $_SESSION['db'];
            
            $empresa       = SysEmpresa::find()->where(['db_name'=> $db])->one();
            
            //Obtenemos datos del empleados
            $empleado      =  SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->one();
        
            //Obtenemos el nivel del cargo del empleador
            $nivelempleado =   $this->getNivelcargo($empleado->id_sys_adm_cargo, $empleado->id_sys_empresa);
            
            $estado = 'P';
            
            //Validar si el documento necesita autorizacion 
            $documento = $this->getDocumento($model->id_sys_rrhh_permiso);
            
            $departamento = $this->getDepartamento($empleado->id_sys_adm_cargo);
             
            if (!$documento):
            
                $estado = 'A';
            
            else:
            
                if($model->id_sys_rrhh_permiso == 'F'):
                
                   $estado = "A";
                
                endif;

                if($model->id_sys_rrhh_permiso == 'T'):

                    if($tipousuario == 'M'):

                        $estado = "A";

                    endif;
                
                endif;
                
            endif;
              
            $codpermiso =  SysRrhhEmpleadosPermisos::find()->select(['max(CAST(id_sys_rrhh_empleados_permiso AS INT))'])->Where(['id_sys_empresa'=> '001'])->scalar();
            $model->id_sys_rrhh_empleados_permiso = $codpermiso + 1;
            $model->transaccion_usuario =  Yii::$app->user->username;
            $model->hora_ini  = date('H:i:s', strtotime($model->fecha_ini));
            $model->fecha_ini = date('Y-m-d', strtotime($model->fecha_ini));
            $model->hora_fin  = date('H:i:s', strtotime($model->fecha_fin));
            $model->fecha_fin = $model->tipo == 'P' ?  date('Y-m-d', strtotime($model->fecha_ini)): date('Y-m-d', strtotime($model->fecha_fin));
            $model->id_sys_empresa = '001';
            $model->estado_permiso      = $estado;
            $model->usuario_aprobacion  = $estado != 'P' ? Yii::$app->user->username : null;
            $model->fecha_aprobacion    = $estado != 'P' ? date('Ymd H:i:s') : null;
            $model->comentario          = $model->comentario;
            $model->fecha_creacion      = date('Ymd H:i:s');
            
            /*if($model->id_sys_rrhh_permiso == 'W'):

                if($model->tipo == 'P'):

                    $date1 = new \DateTime($model->hora_ini);
                    $date2 = new \DateTime($model->hora_fin);
                    $diff  = $date1->diff($date2);
                    
                    $horas = $diff->format('%H:%I:%S');

                    $totalHoras = 0;
                    $decimalH = $this->HorasToDecimal($horas);
                    $horas_pendientes = $this->obtenerHorasPendientes($model->id_sys_rrhh_cedula);
                    $horas_pendientesT = $this->obtenerHorasPendientesTotal($model->id_sys_rrhh_cedula);

                    if($decimalH < $horas_pendientesT):

                        foreach($horas_pendientes as $hora):

                            $totalHoras = floatval($hora['horas']) - $decimalH;

                            if($totalHoras < 0):

                                Yii::$app->$db->createCommand("update sys_rrhh_empleados_horas_compesacion set horas = 0  where id_hora_compesacion = '{$hora['id_hora_compesacion']}'")->execute();

                                $decimalH = -$totalHoras;

                            else:

                                Yii::$app->$db->createCommand("update sys_rrhh_empleados_horas_compesacion set horas = '{$totalHoras}'  where id_hora_compesacion = '{$hora['id_hora_compesacion']}'")->execute();

                            endif;

                        endforeach;

                    else:

                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'danger','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'El número de horas a considerar es mayor a las horas pendientes por compensar! ',
                            'positonY' => 'top','positonX' => 'right']);
                        return $this->redirect('index');

                    endif;

                else:

                    $decimalH = $this->HorasToDecimal('08:30:00');
                    $horas_pendientes = $this->obtenerHorasPendientes($model->id_sys_rrhh_cedula);
                    $horas_pendientesT = $this->obtenerHorasPendientesTotal($model->id_sys_rrhh_cedula);

                    if($decimalH < $horas_pendientesT):

                        foreach($horas_pendientes as $hora):

                            $totalHoras = floatval($hora['horas']) - $decimalH;

                            if($totalHoras < 0):

                                Yii::$app->$db->createCommand("update sys_rrhh_empleados_horas_compesacion set horas = 0  where id_hora_compesacion = '{$hora['id_hora_compesacion']}'")->execute();

                                $decimalH = -$totalHoras;

                            else:

                                Yii::$app->$db->createCommand("update sys_rrhh_empleados_horas_compesacion set horas = '{$totalHoras}'  where id_hora_compesacion = '{$hora['id_hora_compesacion']}'")->execute();

                            endif;

                        endforeach;

                    else:

                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'danger','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'El número de horas a considerar es mayor a las horas pendientes por compensar! ',
                            'positonY' => 'top','positonX' => 'right']);
                        return $this->redirect('index');

                    endif;

                endif;
                
            endif;*/
          
            
            if($model->save(false)):
                    
                    //Enviar correo de flujos
                    if($documento):
                    
                        $cc = [];
                        
                        $to = [];

                        $mensaje = "";
                        //mail medico
                        $mailMedico = "";
                        //mail Salud ocupacional
                        $mailSaludOcupacional ="";
                        //Concadenar fecha
                        $desde = $model->fecha_ini.' '.$model->hora_ini;
                        $hasta = $model->fecha_fin.' '.$model->hora_fin;
                        
                        if($estado == "A"):

                                if($model->id_sys_rrhh_permiso == 'F'):
                            
                                    if($tipousuario == 'M'):

                                        $titulo = 'Permiso Médico';
                                        
                                        $mensaje = "<p>Se ha generado un permiso médico #".str_pad($model->id_sys_rrhh_empleados_permiso, 10, "0", STR_PAD_LEFT)." para el  Sr/Sr(a): <b>". $empleado->nombres."</b>, mismo que inicia el ".$desde." y culmina el ".$hasta."</b><p>Nota :  ".$model->comentario."</p>";
                                        //Mail Medico
                                        $mailMedico = $this->getEmaiUser(Yii::$app->user->identity->id);
                                        //Mail Jefe Ocupacional
                                        $mailSaludOcupacional = $this->getJefeOcupacional($empresa->id_sys_empresa);
                                        
                                        if($mailMedico != ""):
                                        array_push($cc, $mailMedico);
                                        endif;
                                        
                                        if($mailSaludOcupacional != ""):
                                        array_push($cc, $mailSaludOcupacional);
                                        endif;
                                        
                                    endif;
                                endif;
                        
                            //Enviar correo de aprobación
                            
                                if($model->id_sys_rrhh_permiso == 'T'):
                                
                                    if($tipousuario == 'M'):

                                        $titulo = 'Permiso Médico';
                                        
                                        $mensaje = "<p>Se ha generado un permiso médico #".str_pad($model->id_sys_rrhh_empleados_permiso, 10, "0", STR_PAD_LEFT)." para el  Sr/Sr(a): <b>". $empleado->nombres."</b>, mismo que inicia el ".$desde." y culmina el ".$hasta."</b><p>Nota :  ".$model->comentario."</p>";
                                        //Mail Medico
                                        $mailMedico = $this->getEmaiUser(Yii::$app->user->identity->id);
                                        //Mail Jefe Ocupacional
                                        $mailSaludOcupacional = $this->getJefeOcupacional($empresa->id_sys_empresa);
                                        
                                        if($mailMedico != ""):
                                        array_push($cc, $mailMedico);
                                        endif;
                                        
                                        if($mailSaludOcupacional != ""):
                                        array_push($cc, $mailSaludOcupacional);
                                        endif; 
                            
                                    endif;
                                endif;
                        
                        else:
                            
                            //Enviar correo de Notificación
                            $mensaje =  "<p>Se ha generado un  solicitud de permiso #".str_pad($model->id_sys_rrhh_empleados_permiso, 10, "0", STR_PAD_LEFT)." para el  Sr/Sr(a): <b>". $empleado->nombres."</b>, mismo que inicia el ".$desde." y culmina el ".$hasta."</p><p>Nota: ".$model->comentario."</p><p>Puede consultar el documento en el siguiente link:</p><a href='http://".$ipserver."/permisos/index' target='_blank'>Ver Permisos</a>";
                            
                        endif;
                        
                        $emailUser = $this->ObtenerUsuariosGruposAutorizacionXDepartamento($nivelempleado, $departamento->id_sys_adm_area, $departamento->id_sys_adm_departamento);
                        
                        foreach ($emailUser as $user):
                             array_push($to, $user['email']);
                        endforeach;
                        
                        if($mensaje != ""):
                            $this->EnviarCorreo($to, $cc, $mensaje, $titulo, $model->fecha_fin, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, $documento['mail_notificaciones'], $estado);
                        endif;
                            
                    endif;
                    
                    Yii::$app->getSession()->setFlash('info', [
                    'type' => 'success','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso ha sido registrado con éxito!',
                    'positonY' => 'top','positonX' => 'right']);
             else:
                 
                    Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                    'positonY' => 'top','positonX' => 'right']);
             
             endif;    
        
             return $this->redirect('index');
           
  
        }

        return $this->render('create', [
            'model' => $model,
            'listpermisos'=> $listpermisos,
            'tipousuario' => $tipousuario
        ]);
       
      
    }

    /**
     * Updates an existing SysRrhhEmpleadosPermisos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        
        $model        =  $this->findModel($id);
        
        $model->fecha_ini = $model->fecha_ini.' '.date('H:i', strtotime($model->hora_ini));
        $model->fecha_fin = $model->fecha_fin.' '.date('H:i', strtotime($model->hora_fin));
        
        $tipousuario  = $this->getTipoUsuario(Yii::$app->user->identity->id);
        
        $listpermisos = [];
        
        $permisos = SysAdmUsuariosPer::find()->where(['usuario_tipo'=> $tipousuario])->all();

        foreach($permisos as $index){

            if($index['permiso'] != ''){

                if($index['estado'] == 'A'):

                    array_push($listpermisos, $index['permiso']);

                endif;

            }else{

                if($index['estado'] == 'A'):

                    $listpermisos =  SysRrhhPermisos::find()->select('id_sys_rrhh_permiso')->where(['estado'=>'A'])->column();
            
                endif;
            }

        }    
        
        if($model->estado_permiso == 'A'){

            if($tipousuario == 'M'){

                if ($model->load(Yii::$app->request->post())) {
                    
                    
                    if($this->getCompruebaEstadoRol(date('Y-m-d', strtotime($model->fecha_ini))) != true):
                    
                    
                        $model->transaccion_usuario =  Yii::$app->user->username;
                        $model->hora_ini            = date('H:i', strtotime($model->fecha_ini));
                        $model->fecha_ini           = date('Y-m-d', strtotime($model->fecha_ini));
                        $model->hora_fin            = date('H:i:s', strtotime($model->fecha_fin));
                        $model->fecha_fin           = date('Y-m-d', strtotime($model->fecha_fin));
                    
                    
                            if($model->save(false)){
                                
                
                                Yii::$app->getSession()->setFlash('info', [
                                    'type' => 'success','duration' => 1500,
                                    'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso ha sido actualizado con éxito!',
                                    'positonY' => 'top','positonX' => 'right']);
            
                            }
                            else{
                                
                                Yii::$app->getSession()->setFlash('info', [
                                    'type' => 'danger','duration' => 1500,
                                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                                    'positonY' => 'top','positonX' => 'right']);
                                
                                
                            }
                        
                    else:
                        
                        Yii::$app->getSession()->setFlash('info', [
                        'type' => 'danger','duration' => 1500,
                        'icon' => 'glyphicons glyphicons-robot','message' => 'El periodo no se puede actualizar, porque está dentro de un periodo procesado! ',
                        'positonY' => 'top','positonX' => 'right']);
                    
                    
                    endif;  
                    
                    return $this->redirect('index');
                    
                }
            }else{

                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso se encuentra aprobado! ',
                    'positonY' => 'top','positonX' => 'right']);
                    
                    return $this->redirect(['index']);
    
            }

        }elseif($model->estado_permiso == 'P'){
            
            if ($model->load(Yii::$app->request->post())) {
                    
                    
                if($this->getCompruebaEstadoRol(date('Y-m-d', strtotime($model->fecha_ini))) != true):
                
                
                    $model->transaccion_usuario =  Yii::$app->user->username;
                    $model->hora_ini            = date('H:i', strtotime($model->fecha_ini));
                    $model->fecha_ini           = date('Y-m-d', strtotime($model->fecha_ini));
                    $model->hora_fin            = date('H:i:s', strtotime($model->fecha_fin));
                    $model->fecha_fin           = date('Y-m-d', strtotime($model->fecha_fin));
                
                
                        if($model->save(false)){
                            
            
                            Yii::$app->getSession()->setFlash('info', [
                                'type' => 'success','duration' => 1500,
                                'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso ha sido actualizado con éxito!',
                                'positonY' => 'top','positonX' => 'right']);
        
                        }
                        else{
                            
                            Yii::$app->getSession()->setFlash('info', [
                                'type' => 'danger','duration' => 1500,
                                'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                                'positonY' => 'top','positonX' => 'right']);
                            
                            
                        }
                    
                else:
                    
                    Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'El periodo no se puede actualizar, porque está dentro de un periodo procesado! ',
                    'positonY' => 'top','positonX' => 'right']);
                
                
                endif;  
                
                return $this->redirect('index');
                
            }
        }

        return $this->render('update', [
            'model' => $model,
            'listpermisos'=> $listpermisos,
            'tipousuario' => $tipousuario
        ]);
       
    }
    public function actionAprobar($id_sys_rrhh_empleados_permiso, $id_sys_empresa){
        
        $model =  SysRrhhEmpleadosPermisos::find()->where(['id_sys_rrhh_empleados_permiso'=> $id_sys_rrhh_empleados_permiso])->andWhere(['id_sys_empresa'=> $id_sys_empresa])->one();
       
        //Obtenemos datos del empleados
        $empleado      =  SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->one();
       
        //Obtenemos el nivel del empleador 
        $nivelempleado =   $this->getNivelcargo($empleado->id_sys_adm_cargo, $empleado->id_sys_empresa);
        
        $estado        =  'P';
        
        $db            = $_SESSION['db'];
        
        $empresa       = SysEmpresa::find()->where(['db_name'=> $db])->one();
        
        $titulo        = 'Solicitud de Permiso';
        
        $tipousuario  = $this->getTipoUsuario(Yii::$app->user->identity->id);
        
        //Validar si el documento necesita autorizacion
        $documento = $this->getDocumento($model->id_sys_rrhh_permiso);
        
        $departamento = $this->getDepartamento($empleado->id_sys_adm_cargo);
        
        
        if($documento):
        
            $autorizacion = $this->getAutorizacion($documento['id'], Yii::$app->user->identity->id);
            
            if(count($autorizacion) > 0):
            
                if(trim(Yii::$app->user->identity->cedula) != trim($empleado->id_sys_rrhh_cedula)):
                
                    
                if($tipousuario == 'D'):
                
                       $estado = 'A';
                else:
                      
                    if (count($autorizacion) > 1):
                    
                       foreach ($autorizacion as $aut):
                        
                          if(($aut['id_sys_area'] == 0) && ($aut['id_sys_departamento']) == 0):
                           
                              $estado = 'A';
                              break;
                              
                          else:    
                                
                               if($aut['id_sys_departamento'] != 0):
                               
                                 if($aut['id_sys_departamento'] == $departamento->id_sys_adm_departamento):
                                   
                                    $estado = 'A';
                                    break;
                                   
                                 endif;
                                    
                               else:
                                 
                                 if(($aut['id_sys_area'] == $departamento->id_sys_adm_area) && ($aut['id_sys_departamento'] == 0)):
                                    
                                   $estado = 'A';
                                   break;
                                    
                                 endif;
                               
                               endif;
                                              
                          endif;
                          
                       endforeach;
                    
                  else:
                    
                        //Todos los departamentos
                        if(($autorizacion[0]['id_sys_area']) == 0 && ($autorizacion[0]['id_sys_departamento'] == 0)):
                        
                              $estado = $this->AprobarPermiso($autorizacion[0]['nivel_autorizacion'], $nivelempleado);
                        
                        
                        elseif(($autorizacion[0]['id_sys_area']) != 0 && ($autorizacion[0]['id_sys_departamento'] == 0)):
                        
                            //Area del empleado
                            if(($autorizacion[0]['id_sys_area'] == $departamento->id_sys_adm_area)):
                            
                                $estado = $this->AprobarPermiso($autorizacion[0]['nivel_autorizacion'], $nivelempleado);
                            
                            endif;
                        
                        elseif(($autorizacion[0]['id_sys_area']) != 0 && ($autorizacion[0]['id_sys_departamento'] != 0)):
                            
                            //Departamento del empleado
                            if($autorizacion[0]['id_sys_departamento'] == $departamento->id_sys_adm_departamento):
                            
                                $estado = $this->AprobarPermiso($autorizacion[0]['nivel_autorizacion'], $nivelempleado);
                            
                            endif;
                        
                        endif;
                    
                    
                    endif;
                
                endif;
                
              endif;
            
            endif;
        else:
             $estado = 'A';
        endif;
        
        if($model->estado_permiso != 'A'):
                
            if($estado == 'A'):
            
               $model->estado_permiso      = $estado;
               $model->usuario_aprobacion  = $estado != 'P' ? Yii::$app->user->username : null;
               $model->fecha_aprobacion    = date('Ymd H:i:s');
               
               //Concadenar fecha
               $desde = $model->fecha_ini.' '.$model->hora_ini;
               $hasta = $model->fecha_fin.' '.$model->hora_fin;
               
               //Enviar correo de Notificación
               $mensaje = "<p>Se ha generado el permiso  #".str_pad($model->id_sys_rrhh_empleados_permiso, 10, "0", STR_PAD_LEFT)." para el  Sr/Sr(a): <b>". $empleado->nombres."</b>, mismo que inicia el ".$desde." y culmina el ".$hasta."</b><p>Nota :  ".$model->comentario."</p>";
               
            
               if($model->save(false)):
               
                   //Enviar correo de flujos
                   if($documento):
                   
                       $cc = [];
                       $to = [];
                       
                       //Concadenar fecha
                       $desde = $model->fecha_ini.' '.$model->hora_ini;
                       $hasta = $model->fecha_fin.' '.$model->hora_fin;
                   
       
                       //Enviar correo de aprobación
                       if($model->id_sys_rrhh_permiso == 'Z'):
                           
                          $titulo = 'Permiso Médico';   
                          $mensaje = "<p>Se ha generado un permiso médico #".str_pad($model->id_sys_rrhh_empleados_permiso, 10, "0", STR_PAD_LEFT)." para el  Sr/Sr(a): <b>". $empleado->nombres."</b>, mismo que inicia el ".$desde." y culmina el ".$hasta."</b><p>Nota :  ".$model->comentario."</p>";    
                          //Mail Medico
                          $mailMedico = $this->getEmaiUser(Yii::$app->user->identity->id);
                          //Mail Jefe Ocupacional
                          $mailSaludOcupacional = $this->getJefeOcupacional($empresa->id_sys_empresa);
                    
                          
                          if($mailMedico != ""):
                             array_push($cc, $mailMedico);
                          endif;
                          
                          if($mailSaludOcupacional != ""):
                             array_push($cc, $mailSaludOcupacional);
                          endif;
                          
                       endif;
                   
                      
                       $emailUser = $this->ObtenerUsuariosGruposAutorizacionXDepartamento($nivelempleado, $departamento->id_sys_adm_area, $departamento->id_sys_adm_departamento);
                       
                       foreach ($emailUser as $user):
                             array_push($to, $user['email']);
                       endforeach;
                       
                       
                       if($model->id_sys_rrhh_permiso != 'Z'):
                       
                            $addCC = false;
                            $mailUserCreate =  $this->getEmailCreacion($model->transaccion_usuario);
                            
                            foreach ($to as $item):   
                                if($item ==  $mailUserCreate):
                                     $addCC = true;
                                     break;
                                endif;
                            endforeach;
                          
                            if(!$addCC):
                                 array_push($cc, $mailUserCreate);
                            endif;
                        
                        endif;
                       
                        
                        $this->EnviarCorreo($to, $cc, $mensaje, $titulo, $model->fecha_fin, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, $documento['mail_notificaciones'], $estado);
                       
                     
                       
                   endif;
                   
                   
                       Yii::$app->getSession()->setFlash('info', [
                       'type' => 'success','duration' => 1500,
                       'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso ha sido aprobado  con éxito!',
                       'positonY' => 'top','positonX' => 'right']);
               
               else:
                   
                        Yii::$app->getSession()->setFlash('info', [
                       'type' => 'danger','duration' => 1500,
                       'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                       'positonY' => 'top','positonX' => 'right']);
               
               endif;   
            
               
            else:
            
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'warning','duration' => 3000,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Usted no tiene permisos para aprobar permisos!',
                    'positonY' => 'top','positonX' => 'right']);
                
            endif;


        else:
        
       
        Yii::$app->getSession()->setFlash('info', [
            'type' => 'warning','duration' => 3000,
            'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso ya ha sido aprobado!',
            'positonY' => 'top','positonX' => 'right']);
        
        endif;
        
        return $this->redirect(['index']);
        
          
    }

    /**
     * Deletes an existing SysRrhhEmpleadosPermisos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)    {
        $model = $this->findModel($id);
         
        if($this->getCompruebaEstadoRol($model->fecha_ini) != true):
        
            $model =  $this->findModel($id);
            $model->estado_permiso = 'N';
            $model->comentario     = 'PERMISO ANULADO '.Yii::$app->user->username;
            $model->save(false);
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'success','duration' => 1500,
                'icon' => 'glyphicons glyphicons-robot','message' => 'El Permiso ha sido eliminado con exito! ',
                'positonY' => 'top','positonX' => 'right']);
        else:
        
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'warning','duration' => 1500,
                'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso no puede ser eliminado, porque está dentro de un periodo procesado!',
                'positonY' => 'top','positonX' => 'right']);
        
        endif;
        return $this->redirect(['index']);
    }

    public function actionAnular($id_sys_rrhh_empleados_permiso,$id_sys_empresa){
        $model =  SysRrhhEmpleadosPermisos::find()->where(['id_sys_rrhh_empleados_permiso'=> $id_sys_rrhh_empleados_permiso])->andWhere(['id_sys_empresa'=> $id_sys_empresa])->one();
       
        //Obtenemos datos del empleados
        $empleado      =  SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->one();
       
        //Obtenemos el nivel del empleador 
        $nivelempleado =   $this->getNivelcargo($empleado->id_sys_adm_cargo, $empleado->id_sys_empresa);
        
        $estado        =  'P';
        
        $db            = $_SESSION['db'];
        
        $empresa       = SysEmpresa::find()->where(['db_name'=> $db])->one();
        
        $titulo        = 'Solicitud de Permiso';
        
        $tipousuario  = $this->getTipoUsuario(Yii::$app->user->identity->id);
        
        //Validar si el documento necesita autorizacion
        $documento = $this->getDocumento($model->id_sys_rrhh_permiso);
        
        $departamento = $this->getDepartamento($empleado->id_sys_adm_cargo);
        
        
        if($documento):
        
            $autorizacion = $this->getAutorizacion($documento['id'], Yii::$app->user->identity->id);
            
            if(count($autorizacion) > 0):
            
                if(trim(Yii::$app->user->identity->cedula) != trim($empleado->id_sys_rrhh_cedula)):
                
                    
                if($tipousuario == 'D'):
                
                       $estado = 'N';
                else:
                      
                    if (count($autorizacion) > 1):
                    
                       foreach ($autorizacion as $aut):
                        
                          if(($aut['id_sys_area'] == 0) && ($aut['id_sys_departamento']) == 0):
                           
                              $estado = 'N';
                              break;
                              
                          else:    
                                
                               if($aut['id_sys_departamento'] != 0):
                               
                                 if($aut['id_sys_departamento'] == $departamento->id_sys_adm_departamento):
                                   
                                    $estado = 'N';
                                    break;
                                   
                                 endif;
                                    
                               else:
                                 
                                 if(($aut['id_sys_area'] == $departamento->id_sys_adm_area) && ($aut['id_sys_departamento'] == 0)):
                                    
                                   $estado = 'N';
                                   break;
                                    
                                 endif;
                               
                               endif;
                                              
                          endif;
                          
                       endforeach;
                    
                  else:
                    
                        //Todos los departamentos
                        if(($autorizacion[0]['id_sys_area']) == 0 && ($autorizacion[0]['id_sys_departamento'] == 0)):
                        
                              $estado = $this->AnularPermiso($autorizacion[0]['nivel_autorizacion'], $nivelempleado);
                        
                        
                        elseif(($autorizacion[0]['id_sys_area']) != 0 && ($autorizacion[0]['id_sys_departamento'] == 0)):
                        
                            //Area del empleado
                            if(($autorizacion[0]['id_sys_area'] == $departamento->id_sys_adm_area)):
                            
                                $estado = $this->AnularPermiso($autorizacion[0]['nivel_autorizacion'], $nivelempleado);
                            
                            endif;
                        
                        elseif(($autorizacion[0]['id_sys_area']) != 0 && ($autorizacion[0]['id_sys_departamento'] != 0)):
                            
                            //Departamento del empleado
                            if($autorizacion[0]['id_sys_departamento'] == $departamento->id_sys_adm_departamento):
                            
                                $estado = $this->AnularPermiso($autorizacion[0]['nivel_autorizacion'], $nivelempleado);
                            
                            endif;
                        
                        endif;
                    
                    
                    endif;
                
                endif;
                
              endif;
            
            endif;
        else:
             $estado = 'N';
        endif;
        
        if($model->estado_permiso != 'A'):
                
            if($estado == 'N'):
            
               $model->estado_permiso      = $estado;
               $model->usuario_aprobacion  = $estado != 'P' ? Yii::$app->user->username : null;
               $model->fecha_aprobacion    = date('Ymd H:i:s');
               
               //Concadenar fecha
               $desde = $model->fecha_ini.' '.$model->hora_ini;
               $hasta = $model->fecha_fin.' '.$model->hora_fin;
               
               //Enviar correo de Notificación
               $mensaje = "<p>Se ha anulado el permiso  #".str_pad($model->id_sys_rrhh_empleados_permiso, 10, "0", STR_PAD_LEFT)." para el  Sr/Sr(a): <b>". $empleado->nombres;
               
               if($model->save(false)):
               
                   //Enviar correo de flujos
                   if($documento):
                   
                       $cc = [];
                       $to = [];
                      
                       $emailUser = $this->ObtenerUsuariosGruposAutorizacionXDepartamento($nivelempleado, $departamento->id_sys_adm_area, $departamento->id_sys_adm_departamento);
                       
                       foreach ($emailUser as $user):
                             array_push($to, $user['email']);
                       endforeach;
                       
                       
                       if($model->id_sys_rrhh_permiso != 'Z'):
                       
                            $addCC = false;
                            $mailUserCreate =  $this->getEmailCreacion($model->transaccion_usuario);
                            
                            foreach ($to as $item):   
                                if($item ==  $mailUserCreate):
                                     $addCC = true;
                                     break;
                                endif;
                            endforeach;
                          
                            if(!$addCC):
                                 array_push($cc, $mailUserCreate);
                            endif;
                        
                        endif;
                       
                       $this->EnviarCorreo($to, $cc, $mensaje, $titulo, $model->fecha_fin, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, $documento['mail_notificaciones'], $estado);
                       
                   endif;
                   
                   
                       Yii::$app->getSession()->setFlash('info', [
                       'type' => 'success','duration' => 1500,
                       'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso ha sido anulado con éxito!',
                       'positonY' => 'top','positonX' => 'right']);
               
               else:
                   
                        Yii::$app->getSession()->setFlash('info', [
                       'type' => 'danger','duration' => 1500,
                       'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                       'positonY' => 'top','positonX' => 'right']);
               
               endif;   
            
               
            else:
            
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'warning','duration' => 3000,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Usted no tiene permisos para aprobar permisos!',
                    'positonY' => 'top','positonX' => 'right']);
                
            endif;


        else:
        
       
        Yii::$app->getSession()->setFlash('info', [
            'type' => 'warning','duration' => 3000,
            'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso ya ha sido anulado!',
            'positonY' => 'top','positonX' => 'right']);
        
        endif;
        
        return $this->redirect(['index']);

    }
    /**
     * Finds the SysRrhhEmpleadosPermisos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return SysRrhhEmpleadosPermisos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhEmpleadosPermisos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    private function getDepartamento($id_sys_adm_cargo){
        
        //cargo usuario
        $cargo =  SysAdmCargos::find()->where(['id_sys_adm_cargo'=> $id_sys_adm_cargo])->one();
        //departamento usuario
        return  SysAdmDepartamentos::find()->where(['id_sys_adm_departamento'=> $cargo->id_sys_adm_departamento])->one();
        
      
    }
    
    private  function AprobarPermiso($nivelUsuario, $nivelEmpleado){
        
        $estado = "P";
       
        if($nivelUsuario == 1):
          
           $estado = "A";
        
        elseif($nivelUsuario == 2 || $nivelUsuario == 3):
           
             if($nivelEmpleado >= 2): 
                $estado = "A";
             endif;
           
        endif;
        
       return $estado;
    }

    private  function AnularPermiso($nivelUsuario, $nivelEmpleado){
        
        $estado = "P";
       
        if($nivelUsuario == 1):
          
           $estado = "N";
        
        elseif($nivelUsuario == 2 || $nivelUsuario == 3):
           
             if($nivelEmpleado >= 2): 
                $estado = "N";
             endif;
           
        endif;
        
       return $estado;
    }
    
    private function ObtenerUsuariosGruposAutorizacion($id_grupo_autorizacion){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDocumentoAutorizacion]  @id_grupo_autorizacion = '{$id_grupo_autorizacion}'")->queryAll();
    }
    
    private function ObtenerUsuariosGruposAutorizacionXDepartamento($nivel_empleado, $id_sys_area, $id_sys_departamento){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerUsuariosGruposAutorizacionXDeparamento]  @nivel_empleado = {$nivel_empleado}, @id_sys_area = {$id_sys_area}, @id_sys_departamento = {$id_sys_departamento}")->queryAll();
    }
    
    private function getDocumento($id_sys_rrhh_permiso){
        
        $db =  $_SESSION['db'];
        
        $codigo = Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerCodificacionPermisoFlujo]  @id_sys_rrhh_permiso = '{$id_sys_rrhh_permiso}'")->queryScalar();
       
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDocumentoAutorizacion]  @codigo = '{$codigo}'")->queryOne();
       
        
    }
   
    private function getAutorizacion($id_sys_documento, $id_usuario){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerAutorizacionDocumentoUsuario]  @id_sys_documento = {$id_sys_documento}, @id_usuario = {$id_usuario}")->queryAll();
        
    }
    
    private function getNivelcargo($codcargo, $codempresa){
        
        $cargo =  SysAdmCargos::find()->where(['id_sys_adm_cargo'=> $codcargo])->andWhere(['id_sys_empresa'=> $codempresa])->one();
        
        if($cargo){
            
            $mando = sysAdmMandos::find()->where(['id_sys_adm_mando'=>$cargo->id_sys_adm_mando])->andWhere(['id_sys_empresa'=> $cargo->id_sys_empresa])->one();
            return $mando->nivel;
            
        }
        return 0;
    }
    
    private function getEmaiUser($id){        
        $user  = User::find()->where(['id'=> $id])->one();
       
        if($user):
            return  trim($user->email);
        endif;
        
        return "";
       
    }
   
    private function EnviarCorreo($to, $cc, $mensaje, $titulo, $fechafin, $mail_host, $mail_username, $mail_password, $mail_port, $razon_social, $mail_cc, $estado){        
                $cC = $cc;
        
               if($estado == 'A'):
               
                   if($mail_cc != "" && strlen($mail_cc) > 0):
                   
                       $data = explode(";", $mail_cc);
                       
                       foreach ($data as $row):
                         array_push($cC, $row);
                       endforeach;
                       
                   endif;
               
               endif;
               
       
               
              if($fechafin >= date('Y-m-d')):
                
                            Yii::$app->mailer->setTransport([
                                
                                'class' => 'Swift_SmtpTransport',
                                'host' => trim($mail_host),
                                'username' => trim($mail_username),
                                'password' => trim($mail_password),
                                'port' => trim($mail_port),
                                'encryption' => 'tls',
                                'streamOptions' => [
                                    'ssl' => [
                                        'allow_self_signed' => true,
                                        'verify_peer' => false,
                                        'verify_peer_name' => false,
                                    ],
                                ],
                            ]);
                            
                           
                            
                               if(count($cC) > 0):
                           
                            
                                Yii::$app->mailer->compose()
                                ->setTo($to)
                                ->setCc($cC)
                               // ->setBcc(Yii::$app->params['email']['JefeSistemas'])
                                ->setFrom([$mail_username => $razon_social])
                                ->setSubject(''.$titulo.' - Gestión Nómina')
                                ->setHtmlBody($mensaje)
                                ->send();
                            
                            
                            else:
                            
                                Yii::$app->mailer->compose()
                                ->setTo($to)
                                ->setBcc(Yii::$app->params['email']['JefeSistemas'])
                                ->setFrom([$mail_username => $razon_social])
                                ->setSubject(''.$titulo.' - Gestión Nómina')
                                ->setHtmlBody($mensaje)
                                ->send();
                            
                            endif;
                 endif;  
                
 
    }

    private function getCompruebaEstadoRol($fecha){
        
        $db  = $_SESSION['db'];
        
        $rolprocesado = Yii::$app->$db->createCommand("SELECT * FROM sys_rrhh_empleados_rol_cab where  '{$fecha}' >= fecha_ini_liq and '{$fecha}' <= fecha_fin_liq and periodo = '2' and estado = 'P'")->queryOne();
        
        if($rolprocesado):
         return true ;
        endif;
        
        return false;
        
    }
    
    private function getEmailCreacion($username){ 
        
        $user  = User::find()->where(['username'=> $username])->one();
        
        return  $user != null ? trim($user->email) : "";
        
    }
    //obtiene el tipo de usuario
    private function getTipoUsuario($id_usuario){        
        
        $usertipo = SysAdmUsuariosDep::find()->where(['id_usuario'=> $id_usuario])->andwhere(['estado'=> 'A'])->one();
       
       if($usertipo):
        
            return $usertipo->usuario_tipo;
        
        endif;
        
       return 'N';    
     
    }

    private function getJefeOcupacional($id_sys_empresa){
       
        $user = User::find()->select(['email'])
       ->join('join', 'auth_assignment', 'id =  auth_assignment.user_id')
       ->andWhere(["item_name"=> "JEFEOCUPACIONAL"])
       ->andWhere(["status" => "1"])
       ->andWhere(["empresa" => $id_sys_empresa])
       ->one();
       
       if($user):
          return $user['email'];
       endif;
       
       return "";
   }

    public function actionPanelpermisos(){        
        $this->layout = '@app/views/layouts/main_emplados';
        $datos = $this->getDatos();
        return $this->render('panelpermisos', [
            'datos' => $datos,
        ]);
    }


    public function actionDatospanel(){        
        $datos = $this->getDatos();
        echo json_encode([$datos]);
    }

    private function getDatos(){
            
        $db = $_SESSION['db']; 
        $datos = Yii::$app->$db->createCommand("exec [dbo].[ObtenerPermisosEmpleadosDiarios]")->queryAll();
        $datosEnviar = [];

        foreach ($datos as $data){
            $nuevahora = date('H:i:s',strtotime($data['hora_fin']."+ 1 hour")); 
            if($nuevahora > date('H:i:s')){
                array_push($datosEnviar, $data);
            }
        }

        return $datosEnviar;
    }

    public function actionDatospanelpermisosequipos(){        
        $datos = $this->getDatosEquipos();
        echo json_encode([$datos]);
    }

    private function getDatosEquipos(){
            
        $db = $_SESSION['db']; 
        $datos = Yii::$app->$db->createCommand("exec [dbo].[ObtenerPermisosEmpleadosEquiposDiarios]")->queryAll();
        $datosEnviar = [];

        foreach ($datos as $data){
       
            array_push($datosEnviar, $data);
            
        }

        return $datosEnviar;
    }

    public function actionDatospanelpermisosalimentos(){        
        $datos = $this->getDatosAlimentos();
        echo json_encode([$datos]);
    }

    private function getDatosAlimentos(){
            
        $db = $_SESSION['db']; 
        $datos = Yii::$app->$db->createCommand("exec [dbo].[ObtenerPermisosEmpleadosAlimentosDiarios]")->queryAll();
        $datosEnviar = [];

        foreach ($datos as $data){
            
            array_push($datosEnviar, $data);
         
        }

        return $datosEnviar;
    }

 public function actionInformepermisos(){
        
    $fechaini        =  date('Y-m-d');
    $fechafin        =  date('Y-m-d');
    $area = '';
    $departamento = '';
    $filtro = '';
    $tipo = '';
    $datos = [];
    $datos_medicos = [];
    
    $this->layout = '@app/views/layouts/main_emplados';
    
    if(Yii::$app->request->post()):
    
        $fechaini        = $_POST['fechaini']== null ?  $fechaini : $_POST['fechaini'];
        $fechafin        = $_POST['fechafin']== null ?  $fechafin : $_POST['fechafin'];
        $departamento    = $_POST['departamento']== null ?  '': $_POST['departamento'];
        $area            = $_POST['area']== null ? '': $_POST['area'];
        $filtro          = $_POST['nombres']== null ? '': trim($_POST['nombres']);
        $tipo            = $_POST['tipo']== null ? '': $_POST['tipo'];

    $datos = $this->getPermisosMensuales($fechaini, $fechafin, $area, $departamento,$filtro,$tipo);
    $datos_medicos = $this->getCertificadosMedicos($fechaini, $fechafin);
    
    
    endif;
    
    return $this->render('_informepermisos',['datos'=> $datos,'datos_medicos' => $datos_medicos,'fechaini'=> $fechaini, 'fechafin'=> $fechafin,'filtro' => $filtro,'area'=> $area,'departamento'=>$departamento,'tipo'=>$tipo]) ;
        
}

public function actionInformepermisosxls($fechaini,$fechafin,$area,$departamento,$filtro,$tipo){
    
    $datos= [];
    $datos_medicos= [];
    
    $datos =   $this->getPermisosMensuales($fechaini, $fechafin, $area, $departamento,$filtro,$tipo);
    $datos_medicos = $this->getCertificadosMedicos($fechaini, $fechafin);
         
    return   $this->render('_informepermisosxls', ['datos'=> $datos,'datos_medicos' => $datos_medicos,'fechaini'=>$fechaini, 'fechafin'=>$fechafin]);
}

public function actionHorascompensargeneradas($cedula){
    
    $db    = $_SESSION['db'];
    echo json_encode(Yii::$app->$db->createCommand("exec [dbo].[ObtenerHorasPorCompensarEmpleado] @cedula = '$cedula'")->queryAll());
}

private  function getCertificadosMedicos($fechaini, $fechafin){
       
    $db    = $_SESSION['db'];
    return Yii::$app->$db->createCommand("exec [dbo].[MedObtenerCertificadosMedicosEmpleados]  @fechaini = '{$fechaini}', @fechafin= '{$fechafin}'")->queryAll();
    
}


private function obtenerHorasPendientes($cedula)    {
    $db    = $_SESSION['db'];
    return Yii::$app->$db->createCommand("exec [dbo].[ObtenerHorasPorCompensarEmpleado] @cedula = '$cedula'")->queryAll();
}

function obtenerHorasPendientesTotal($cedula)    {
    $db    = $_SESSION['db'];
    return Yii::$app->$db->createCommand("exec [dbo].[ObtenerHorasPorCompensarEmpleadoTotal] @cedula = '$cedula'")->queryScalar();
}

function HorasToDecimal($hora){
    
    $array = explode(':', trim($hora));
    $h     = floatval($array[0]);
    $m     = floatval($array[1]);
    
    if ($h != 0 || $m != 0 ):
    
        if($m > 0 ):
            
            $m = $m/60;
        endif;
        
        return $h+$m;
        
    else: 
      return 0;
    endif;
}


private  function getPermisosMensuales($fechaini, $fechafin, $area, $departamento,$filtro,$tipo){
       
    $db    = $_SESSION['db'];

    if($area != "" &&  $departamento == "" && $filtro == "" && $tipo == ""):
          
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerPermisosMensualesXArea] @fechaini = '{$fechaini}', @fechafin= '{$fechafin}', @area = '{$area}'")->queryAll(); 
        
    elseif($area != "" && $departamento != "" && $filtro == "" && $tipo == ""):
     
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerPermisosMensualesXAreaXDepartamento] @fechaini = '{$fechaini}', @fechafin= '{$fechafin}', @area= '{$area}', @departamento = '$departamento'")->queryAll(); 
     
    elseif ($area != "" && $departamento != "" && $filtro != "" && $tipo == ""):
     
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerPermisosMensualesXAreaXDepartamentoEmpleado] @fechaini = '{$fechaini}', @fechafin= '{$fechafin}', @area= '{$area}', @departamento = '$departamento', @nombre_empleado = '{$filtro}'")->queryAll(); 
          
    elseif ($area != "" && $departamento == "" && $filtro != "" && $tipo == ""):
     
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerPermisosMensualesXAreaEmpleado] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @area = '{$area}', @nombre_empleado = '{$filtro}'")->queryAll(); 
    
    elseif($area != "" && $departamento == "" && $filtro == "" && $tipo != "" ):
     
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerPermisosMensualesXAreaXTipo] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @area = '{$area}', @tipo = '{$tipo}'")->queryAll(); 
    
    elseif($area != "" && $departamento != "" && $filtro == "" && $tipo != "" ):
     
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerPermisosMensualesXAreaXDepartamentoXTipo] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @area = '{$area}', @departamento = '{$departamento}', @tipo = '{$tipo}'")->queryAll(); 
   
    elseif($area == "" && $departamento == "" && $filtro != "" && $tipo != "" ):
     
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerPermisosMensualesEmpleadoXTipo] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @nombre_empleado = '{$filtro}', @tipo = '{$tipo}'")->queryAll(); 
        
    elseif($area == "" && $departamento == "" && $filtro != "" && $tipo == "" ):
     
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerPermisosMensualesEmpleado] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @nombre_empleado = '{$filtro}'")->queryAll(); 

    elseif($area == "" && $departamento == "" && $filtro == "" && $tipo != "" ):
     
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerPermisosMensualesXTipo] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @tipo = '{$tipo}'")->queryAll(); 

    elseif($area != "" && $departamento == "" && $filtro != "" && $tipo != "" ):
     
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerPermisosMensualesXAreaXEmpleadoXTipo] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @area = '{$area}', @tipo = '{$tipo}', @nombre_empleado = '{$filtro}'")->queryAll(); 
     
    else:
     
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerPermisosMensuales] @fechaini = '{$fechaini}', @fechafin= '{$fechafin}'")->queryAll();
     
    endif;
    
    
}
 
}
