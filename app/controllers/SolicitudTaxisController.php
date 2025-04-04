<?php

namespace app\controllers;

use app\models\SysRrhhEmpleadosSueldos;
use app\models\SysRrhhMarcacionesEmpleados;
use app\models\SysRrhhEmpleadosNovedades;
use Exception;
use Yii;
use app\models\SysRrhhSolicitudTaxis;
use app\models\search\SysRrhhSolicitudTaxisSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\models\Model;
use app\models\SysAdmAreas;
use app\models\SysAdmDepartamentos;
use app\models\SysRrhhSolicitudTaxisEmpleados;
use app\models\SysRrhhEmpleados;
use app\models\SysEmpresa;
use app\models\User;
use app\models\SysAdmUsuariosDep;
use kartik\mpdf\Pdf;

/**
 * SolicitudTaxisController implements the CRUD actions for SysRrhhSolicitudTaxis model.
 */
class SolicitudTaxisController extends Controller
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
     * Lists all SysRrhhSolicitudTaxis models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysRrhhSolicitudTaxisSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhSolicitudTaxis model.
     * @param string $id_sys_rrhh_SolicitudTaxis
     * @param string $id_sys_empresa
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
     * Creates a new SysRrhhSolicitudTaxis model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model    = new SysRrhhSolicitudTaxis();
        $modeldet = [new SysRrhhSolicitudTaxisEmpleados()];
        
        $db  =  $_SESSION['db'];
                    
        $titulo  = "Solicitud de Taxi";

        $empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();

        if ($model->load(Yii::$app->request->post())) {
            
            $modeldet = Model::createEmpleadosSolicitudTaxis(SysRrhhSolicitudTaxisEmpleados::classname());
            Model::loadMultiple($modeldet, Yii::$app->request->post());
            
            $transaction = \Yii::$app->$db->beginTransaction();
            
            try {

                $fecha = $model->fecha_solicitada;
                
                $model->transaccion_usuario           = Yii::$app->user->username;
                $model->id_sys_empresa                = '001';
                $model->fecha_solicitada              = date('Ymd',strtotime($fecha));
                $model->hora_solicitada               = date('H:i:s',strtotime($fecha));
                $model->estado                        = 'P';
                $model->fecha_creacion                = date('Ymd H:i:s');

                if ($flag = $model->save(false)) {
                    
                   //Agregar Empleados       
                    foreach ($modeldet as $index => $modeldetalle) {
                            
                        $md =  new SysRrhhSolicitudTaxisEmpleados();
                        
                        if($modeldetalle->id_sys_rrhh_cedula == Null || empty($modeldetalle->id_sys_rrhh_cedula)){
                               
                        }else{
                        
                            $md->id_sys_rrhh_cedula                = $modeldetalle->id_sys_rrhh_cedula;
                            $md->id_sys_empresa                    = '001';
                            $md->id_sys_rrhh_sotaxis               = $model->id_sys_rrhh_sotaxis;
                           
    
                            if (! ($flag = $md->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                                    
                        }
                    }
                    
                    if ($flag) {

                        $area = SysAdmAreas::find()->where(['id_sys_adm_area' => $model->id_sys_adm_area])->one();

                        $departamento = SysAdmDepartamentos::find()->where(['id_sys_adm_departamento' => $model->id_sys_adm_departamento])->one();
                    
                        $mensaje = "<p  style = 'margin:1px;'>Se ha generado una solicitud de taxis #".str_pad($model->id_sys_rrhh_sotaxis, 5, "0", STR_PAD_LEFT)." del Departamento: <b>".$departamento->departamento."</b><p>Comentario: ".$model->comentario."</p><p>Puede consultar el documento en el siguiente link:</p><a href='https://".Yii::$app->params['ipServer']."/solicitud-taxis/view?id=".$model->id_sys_rrhh_sotaxis."' target='_blank'>Ver Solicitud</a>";
                       
                        $documento = $this->getDocumento('SOLICITUD_TAXIS');

                        $to = [];
                        $cc = [];

                        $emailUser = $this->ObtenerUsuariosGruposAutorizacionAll($documento['id'],$model->id_sys_adm_area,$model->id_sys_adm_departamento);
                        
                        foreach ($emailUser as $user): 
                            array_push($to, $user['email']);
                        endforeach;

                        $mailUserCreate =  $this->getEmailCreacion($model->transaccion_usuario);
                        
                        if($mailUserCreate != ""):
                            array_push($cc, $mailUserCreate);
                        endif;
                        
                        $this->EnviarCorreo($to, $cc, $mensaje, $titulo, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, "", "P");
                        
                        $transaction->commit();
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'success','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud se ha creado con éxito!',
                            'positonY' => 'top','positonX' => 'right']);
                        
                        return $this->redirect(['index']);
                    }
                   
                }
                
            }catch (Exception $e) {
                $transaction->rollBack();
                throw new Exception($e);
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                    'positonY' => 'top','positonX' => 'right']);
                return $this->redirect(['index']);
                
            }      
        }

        return $this->render('create', [
            'model' => $model,
            'modeldet'=> $modeldet,
            'update'=> 0,
            'esupdate'=> 0,
        ]);
    }
    
    public function actionEmpleadosdepartamento($departamento){
        
        $db =  $_SESSION['db'];
        
        $datos = [];
        
        $datos =   Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerEmpleadosXDepartamento]  @departamento = {$departamento}")->queryAll(); 
        
        return $this->renderAjax('_listempleados', [
            'datos'=>$datos
        ]);
        
    }

    public function actionEmpleadosarea($area,$fecha){
        
        $db =  $_SESSION['db'];
        
        $datos = [];
        
        $datos =   Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboralXArea]  @fechaini = '{$fecha}', @fechafin = '{$fecha}', @id_sys_adm_area = '$area'")->queryAll(); 
        
        return $this->renderAjax('_listempleados', [
            'datos'=>$datos
        ]);
    }

    public function actionEmpleadosXArea($area,$fecha){
        
        $db =  $_SESSION['db'];
        
        return Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboralXArea]  @fechaini = '{$fecha}',  @fechafin = '{$fecha}', @id_sys_adm_area = '$area'")->queryAll(); 
            
    }

    public function actionEmpleadosXDepartamento($departamento,$fecha){
        
        $db =  $_SESSION['db'];
        
        return Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboralXDepartamento]  @fecha = '{$fecha}', @id_sys_adm_departamento = {$departamento}")->queryAll(); 
            
    }
    
    /**
     * Updates an existing SysRrhhSolicitudTaxis model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id_sys_rrhh_SolicitudTaxis
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $db    = $_SESSION['db'];
       
        $modeldet = [];        
        
        $datos= Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDetalladoSolicitudTaxis] @id = {$id}")->queryAll();
        
        if ($datos){
            foreach ($datos as $data){
                $obj                                   = new SysRrhhSolicitudTaxisEmpleados();
               // $emp                                   = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $data->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> $data->id_sys_empresa])->one();
                $obj->id_sys_rrhh_sotaxis              = $data['id_sys_rrhh_sotaxis'];
                $obj->id_sys_rrhh_cedula               = $data['id_sys_rrhh_cedula'];
                $obj->id_sys_empresa                   = $data['nombres'];
                array_push($modeldet, $obj);
            }
        }else{
            array_push($modeldet, new SysRrhhSolicitudTaxisEmpleados());
        }
    

        if($model->estado != 'A'){

            if ($model->load(Yii::$app->request->post())) {

                $oldIDs    = ArrayHelper::map($modeldet, 'id_sys_rrhh_sotaxis', 'id_sys_rrhh_sotaxis');
            
                $array  = Yii::$app->request->post('SysRrhhSolicitudTaxisEmpleados');
                
                if ($array){
                    
                    $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($array, 'id_sys_rrhh_sotaxis', 'id_sys_rrhh_sotaxis')));
                }
                
                if(!empty($deletedIDs)){
                    
                    SysRrhhSolicitudTaxisEmpleados::deleteAll(['id_sys_rrhh_sotaxis' => $deletedIDs]);
                }
                
                $transaction = \Yii::$app->$db->beginTransaction();
                
                try {
                    
                    if ($flag = $model->save(false)) {
                        
            
                        if ($array){
                            
                            foreach ($array as $index => $modeldetalle) {
                            
                                if($modeldetalle['id_sys_rrhh_sotaxis_emp'] != ''){
                                    
                                    $md =  SysRrhhSolicitudTaxisEmpleados::find()->from('sys_rrhh_solicitud_taxis_emp WITH (nolock)')->where(['id_sys_empresa'=> '001', 'id_sys_rrhh_sotaxis_emp'=> $modeldetalle['id_sys_rrhh_sotaxis_emp']])->one();
                                }
                                else{

                                    $md =  new SysRrhhSolicitudTaxisEmpleados();
                                
                                }

                                $md->id_sys_rrhh_cedula                = $modeldetalle['id_sys_rrhh_cedula'];
                                $md->id_sys_rrhh_sotaxis               = $model->id_sys_rrhh_sotaxis;
                                $md->id_sys_empresa                    = '001';
                                        
                                if (! ($flag = $md->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }

                            }
                           
                        }else{
                            
                            $flag= true;
                        }
                        
                        if ($flag) {
                            $transaction->commit();
                            Yii::$app->getSession()->setFlash('info', [
                                'type' => 'success','duration' => 1500,
                                'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud ha sido actualizada con éxito!',
                                'positonY' => 'top','positonX' => 'right']);
                            
                            return $this->redirect(['index']);
                        }
                    }
                    
                }catch (Exception $e) {
                    $transaction->rollBack();
                    throw new Exception($e);
                    Yii::$app->getSession()->setFlash('info', [
                        'type' => 'danger','duration' => 1500,
                        'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                        'positonY' => 'top','positonX' => 'right']);
                    return $this->redirect(['index']);
                }
           
            }
    
        }else{
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'danger','duration' => 1500,
                'icon' => 'glyphicons glyphicons-robot','message' => 'No puede realizar cambios, la solicitud ya ha sido aprobada/desaprobada/revisada!',
                'positonY' => 'top','positonX' => 'right']);
            
            return $this->redirect(['index']);
        }
        
        
        return $this->render('update', [
            'model'   => $model,
            'modeldet' => $modeldet,
            'update'=> 1,
            'esupdate'=> 1,
        ]);
    }

    /**
     * Deletes an existing SysRrhhSolicitudTaxis model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id_sys_rrhh_sotaxis
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model =$this->findModel($id);
        
        $estado        = 'P';
            
        //Validar si el documento necesita autorizacion
        $documento = $this->getDocumento('SOLICITUD_TAXIS');
           
        if($documento):
           
            $autorizacion = $this->getAutorizacion($documento['id'], Yii::$app->user->identity->id);
               
            if($autorizacion):

                if($model->id_sys_adm_area == 1):

                    if($autorizacion['id_sys_area'] == $model->id_sys_adm_area && $autorizacion['id_sys_departamento'] == $model->id_sys_adm_departamento):
                    
                        $estado = 'N';
            
                    endif;
                
                else:

                    if($autorizacion['id_sys_area'] == $model->id_sys_adm_area):
                    
                        $estado = 'N';
            
                    endif;

                endif;

            endif;
        endif;
           
        if($model->estado == 'P'):
                   
            if($estado == 'N'):
               
                $model->estado               = $estado;
                $model->usuario_anulacion    = $estado != 'P' ? Yii::$app->user->username : null;
                $model->fecha_anulacion      = date('Ymd H:i:s');
               
                if($model->save(false)):

                    Yii::$app->getSession()->setFlash('info', [
                       'type' => 'success','duration' => 1500,
                       'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud ha sido anulada con éxito!',
                       'positonY' => 'top','positonX' => 'right']);

                    return $this->redirect(['index']);
               
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
                   'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud ya ha sido aprobada!',
                   'positonY' => 'top','positonX' => 'right']);
           
           endif;

        return $this->redirect(['index']);
    }

    public function actionAprobar($id){
        
        $model         = SysRrhhSolicitudTaxis::find()->where(['id_sys_rrhh_sotaxis'=> $id])->one();
        
        $estado        = 'P';
        
        $db            = $_SESSION['db'];
        
        $empresa       = SysEmpresa::find()->where(['db_name'=> $db])->one();
        
        $titulo        = 'Solicitud de Taxis';
        
        $tipousuario   = $this->getTipoUsuario(Yii::$app->user->identity->id);
        
        //Validar si el documento necesita autorizacion
        $documento = $this->getDocumento('SOLICITUD_TAXIS');

        $transaction = \Yii::$app->$db->beginTransaction();
        
        if($documento):
        
            $autorizacion = $this->getAutorizacion($documento['id'], Yii::$app->user->identity->id);
            
            if($autorizacion):

                if($tipousuario == 'G'){

                    $estado = 'A';

                }else if($model->id_sys_adm_area == 1):

                    if($autorizacion['id_sys_area'] == $model->id_sys_adm_area && $autorizacion['id_sys_departamento'] == $model->id_sys_adm_departamento):
                    
                        $estado = 'A';
            
                    endif;
                
                else:

                    if($autorizacion['id_sys_area'] == $model->id_sys_adm_area):
                    
                        $estado = 'A';
            
                    endif;

                endif;

            endif;

        endif;
        
        if($model->estado == 'P'):
                
            if($estado == 'A'):
            
                $model->estado     = $estado;
                $model->usuario_aprobacion  = $estado != 'P' ? Yii::$app->user->username : null;
                $model->fecha_aprobacion    = date('Ymd H:i:s');
               
                if($model->save(false)):

                    $tablecab = "";
                    $tablecab.= "<table border='1'><tr><th>TELEFONO</th><th>OBSERVACIONES</th></tr>";

                    $table = "";
                    $table.= "<table border='1'><tr><th>Cédula</th><th>Nombres</th></tr>";
                    
                    $detalle = SysRrhhSolicitudTaxisEmpleados::find()->where(['id_sys_rrhh_sotaxis'=> $model->id_sys_rrhh_sotaxis])->all();
                    $con = 0;
                    
                    foreach ($detalle as $index => $item):
                        
                        $con++;

                        $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula' => $item['id_sys_rrhh_cedula']])->one();

                        $table.= "<tr><td>".$item['id_sys_rrhh_cedula']."</td><td>".$empleado->nombres."</td></tr>";
                        
                    endforeach;
                    
                    $table.= "</table>";
                    
                    $area = SysAdmAreas::find()->where(['id_sys_adm_area' => $model->id_sys_adm_area])->one();

                    $departamento = SysAdmDepartamentos::find()->where(['id_sys_adm_departamento' => $model->id_sys_adm_departamento])->one();

                    $tablecab.= "<tr><td>".$model->telefono."</td><td>".$model->comentario."</td></tr>";
                    
                    $tablecab.= "</table>";

                    $mensaje = "<p  style = 'margin:1px;'>La solicitud de taxis #".str_pad($model->id_sys_rrhh_sotaxis, 5, "0", STR_PAD_LEFT)." del Departamento: <b>".$departamento->departamento."</b><p>Fecha: <b>".$model->fecha_solicitada."</b></p><p>Requerimiento de taxi:</p>".$tablecab."<p>Contacto:</p>".$table."<p>Ha sido aprobada con éxtio</p>";
                
                    $to = [];
                    $cc = [];

                    $emailUser = $this->ObtenerUsuariosGruposAutorizacionAll($documento['id'],$model->id_sys_adm_area,$model->id_sys_adm_departamento);
                        
                    foreach ($emailUser as $user): 
                        array_push($to, $user['email']);
                    endforeach;
                    
                    $mailUserCreate =  $this->getEmailCreacion($model->transaccion_usuario);
                    
                    if($mailUserCreate != ""):
                        array_push($cc, $mailUserCreate);
                    endif;

                    $addCC = false ;
                        
                    foreach ($to as $item):
                    
                        if($item ==  Yii::$app->user->identity->email):
                            $addCC = true;
                            break;
                        endif;
                        
                    endforeach;
                    
                    if(!$addCC):
                        array_push($cc, Yii::$app->user->identity->email);
                    endif;
                    
                    $this->EnviarCorreo($to, $cc, $mensaje, $titulo, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, $documento['mail_notificaciones'], $estado);
                    
                    $transaction->commit();
                    Yii::$app->getSession()->setFlash('info', [
                    'type' => 'success','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud ha sido aprobada con éxito!',
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
                'icon' => 'glyphicons glyphicons-robot','message' => 'Usted no tiene permisos para aprobar solicitud de taxis!',
                'positonY' => 'top','positonX' => 'right']);
                
            endif;


        else:
        
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'warning','duration' => 3000,
                'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud ya ha sido aprobada!',
                'positonY' => 'top','positonX' => 'right']);
        
        endif;
        
        return $this->redirect(['index']);
             
    }

    private function EnviarCorreo($to, $cc, $mensaje, $titulo, $mail_host, $mail_username, $mail_password, $mail_port, $razon_social, $mail_cc, $estado){
        $cC = $cc;
        
        if($estado == 'P' || $estado == 'A'):
        
            if($mail_cc != "" && strlen($mail_cc) > 0):
            
                $data = explode(";", $mail_cc);
                
                foreach ($data as $row):
                     array_push($cC, $row);
                endforeach;
                
            endif;
        
        endif;
        
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
          ->setFrom([$mail_username => $razon_social])
          ->setSubject(''.$titulo.' - Gestión Nómina')
          ->setHtmlBody($mensaje)
          ->send();
                
            
       else:
            
         Yii::$app->mailer->compose()
         ->setTo($to)
         ->setFrom([$mail_username => $razon_social])
         ->setSubject(''.$titulo.' - Gestión Nómina')
         ->setHtmlBody($mensaje)
         ->send();
            
       endif;
           
    }

    private function getDocumento($codigo){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDocumentoAutorizacion]  @codigo = '{$codigo}'")->queryOne();  
    
    }

    private function getTipoUsuario($id_usuario){        
        
        $usertipo = SysAdmUsuariosDep::find()->where(['id_usuario'=> $id_usuario])->andwhere(['estado'=> 'A'])->one();
       
        if($usertipo):   
            return $usertipo->usuario_tipo;
        endif;
        
        return 'N';    
    
    }

    private function getAutorizacion($id_sys_documento, $id_usuario){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerAutorizacionDocumentoUsuario]  @id_sys_documento = {$id_sys_documento}, @id_usuario = {$id_usuario}")->queryOne();
        
    }

    private function ObtenerUsuariosGruposAutorizacionAll($id_sys_documento,$area,$departamento){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerUsuariosGruposAutorizacionTaxisAll]  @id_sys_documento = {$id_sys_documento}, @area = {$area}, @departamento = {$departamento}")->queryAll();
    }

    private function getEmailCreacion($username){
        
        $user  = User::find()->where(['username'=> $username])->one();
        return  $user != null ? trim($user->email) : "";
        
    }


    /**
     * Finds the SysRrhhSolicitudTaxis model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_sys_rrhh_SolicitudTaxis
     * @param string $id_sys_empresa
     * @return SysRrhhSolicitudTaxis the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhSolicitudTaxis::find()->from('sys_rrhh_solicitud_taxis WITH (nolock)')->where(['id_sys_rrhh_sotaxis' => $id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
