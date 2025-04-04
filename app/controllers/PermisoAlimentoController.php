<?php

namespace app\controllers;

use Yii;
use app\models\SysEmpresa;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhPermisoAlimentos;
use app\models\User;
use app\models\Search\SysRrhhPermisoAlimentosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PermisoAlimentoController implements the CRUD actions for SysRrhhPermisoAlimentos model.
 */
class PermisoAlimentoController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all SysRrhhPermisoAlimentos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysRrhhPermisoAlimentosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhPermisoAlimentos model.
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
     * Creates a new SysRrhhPermisoAlimentos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysRrhhPermisoAlimentos();

        if ($model->load(Yii::$app->request->post())) {
            
            $model->usuario_creacion  = Yii::$app->user->username;
            $model->fecha_creacion = date('Ymd H:i:s');
            $model->estado = 'P';
            $model->anulado = 0;
            $model->save();
            
            $db = $_SESSION['db'];
            $empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();
            
            //Validar si el documento necesita autorizacion
            $documento = $this->getDocumento('PERMISO_ENT_ALIMENTOS');
            
            //empleados
            $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
            
            $to = [];
            
            $cc = [];
            
            $mensaje = "<p>Se ha generado un permiso de entrada de alimento del  Sr/Sr(a): <b>".$empleado->nombres."</b>, mismo que inicia el ".$model->inicio." y culmina el ".$model->fin."</p>";
            
            
            $titulo = "Permiso de entrada de alimentos";
            
            $mailUserCreate =  Yii::$app->user->identity->email;
            
            if($mailUserCreate != ""):
                 array_push($cc, $mailUserCreate);
            endif;
            
            
            $emailUser = $this->ObtenerUsuariosGruposAutorizacionAll($documento['id']);
            
            foreach ($emailUser as $user):
                
                if($mailUserCreate !=  $user['email']):
                    array_push($to, $user['email']);
                endif;
            
            endforeach;
            
            $this->EnviarCorreo($to, $cc, $mensaje, $titulo, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, "", "P");
        
            
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'success','duration' => 3000,
                'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso ha sido registrado con éxito!',
                'positonY' => 'top','positonX' => 'right']);
            
            return $this->redirect(['index']);
            
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SysRrhhPermisoAlimentos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->estado != 'A'){

            if ($model->load(Yii::$app->request->post())) {
                
                return $this->redirect(['view', 'id' => $model->id]);
                
            }
            
        }else{

            Yii::$app->getSession()->setFlash('info', [
                'type' => 'danger','duration' => 1500,
                'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso se encuentra aprobado! ',
                'positonY' => 'top','positonX' => 'right']);
                
                return $this->redirect(['index']);

        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SysRrhhPermisoAlimentos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
       $model =  $this->findModel($id);
       
       if($model->estado == 'P'):
       
        $model->anulado = 1;
        $model->save(false);
       
       Yii::$app->getSession()->setFlash('info', [
           'type' => 'success','duration' => 3000,
           'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso ha sido anulado con éxito!',
           'positonY' => 'top','positonX' => 'right']);
       
       else:
       
       Yii::$app->getSession()->setFlash('info', [
           'type' => 'warning','duration' => 3000,
           'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso no se puede anular porque se encuentra aprobado!',
           'positonY' => 'top','positonX' => 'right']);
       
       endif;

        return $this->redirect(['index']);
    }

    
    public function actionAprobar($id){
        
        $model =  $this->findModel($id);
        
        $estado = "P";
        
        $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
        
        //Validar si el documento necesita autorizacion
        $documento = $this->getDocumento('PERMISO_ENT_ALIMENTOS');
        
        if($documento):
        
            $autorizacion = $this->getAutorizacion($documento['id'], Yii::$app->user->identity->id);
            
            if($autorizacion):
            
                if(trim(Yii::$app->user->identity->cedula) != trim($empleado->id_sys_rrhh_cedula)):
                    
                    //Todos los departamentos
                    if ($autorizacion['nivel_autorizacion'] < 3):
                         $estado = 'A';
                    endif;
                    
                endif;
                
            endif;
        
        endif;
      
        if($model->estado != 'A'):
        
        
            if($estado == "A"):
            
               
                $model->estado ='A';
                $model->usuario_aprobacion  = Yii::$app->user->username;
                $model->fecha_actualizacion = date('Ymd H:i:s');
                
               if ($model->save(FALSE)):
                
                        
                    $db      = $_SESSION['db'];
                    
                    $titulo  = "Permiso de entrada de alimentos";
                    
                    $mensaje = "<p>El permiso de entrada de alimentos  #".str_pad($model->id, 10, "0", STR_PAD_LEFT)."  del  Sr/Sr(a): <b>".$empleado->nombres."</b>, mismo que inicia el ".$model->inicio." y culmina el ".$model->fin." ha sido aprobado</p><p>".$model->motivo."</p>";
                    
                    $empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();
                    
                    $to = [];
                    
                    $cc = [];
                    
                    //Copiar a gerencia el permiso
                    $emailUser = $this->ObtenerUsuariosGruposAutorizacionAll($documento['id']);
                    
                    foreach ($emailUser as $user):
                        array_push($to, $user['email']);
                    endforeach;
                    
                    $mailUserCreate =  $this->getEmailCreacion($model->usuario_creacion);
                    
                    
                    if($mailUserCreate !=  ""):
                          array_push($cc, $mailUserCreate);
                    endif;
                    
                   $this->EnviarCorreo($to, $cc, $mensaje, $titulo, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, $documento['mail_notificaciones'], $estado);
                
                
                  Yii::$app->getSession()->setFlash('info', [
                     'type' => 'success','duration' => 3000,
                     'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso ha sido aprobado con éxito!',
                      'positonY' => 'top','positonX' => 'right']);
                    
                    
               else:
                
                  Yii::$app->getSession()->setFlash('info', [
                  'type' => 'danger','duration' => 3000,
                  'icon' => 'glyphicons glyphicons-robot','message' => 'Error al guardar los datos. Consulte con sus Administrador',
                  'positonY' => 'top','positonX' => 'right']);
                
                
               endif;
                
                
            else:
                
                Yii::$app->getSession()->setFlash('info', [
                'type' => 'warning','duration' => 3000,
                'icon' => 'glyphicons glyphicons-robot','message' => 'No tiene autorizacion de aprobación. Consulte con sus Administrador',
                'positonY' => 'top','positonX' => 'right']);
            
            endif;
            
        else:
        
            Yii::$app->getSession()->setFlash('info', [
            'type' => 'warning','duration' => 3000,
            'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso se encuentra aprobado!',
            'positonY' => 'top','positonX' => 'right']);
        
        endif;
        
        return $this->redirect(['index']);
        
    }
    
    private function ObtenerUsuariosGruposAutorizacionAll($id_sys_documento){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerUsuariosGruposAutorizacionAll] @id_sys_documento = '{$id_sys_documento}'")->queryAll();
    }
    
    private function EnviarCorreo($to, $cc, $mensaje, $titulo, $mail_host, $mail_username, $mail_password, $mail_port, $razon_social, $mail_cc, $estado){
        $cC = $cc;
        
        if($estado == 'A'):
        
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
    
    private function getAutorizacion($id_sys_documento, $id_usuario){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerAutorizacionDocumentoUsuario]  @id_sys_documento = {$id_sys_documento}, @id_usuario = {$id_usuario}")->queryOne();
        
    }
    
    private function getEmailCreacion($username){
        
        $user  = User::find()->where(['username'=> $username])->one();
        
        return  $user != null ? trim($user->email) : "";
        
    }
    
    /**
     * Finds the SysRrhhPermisoAlimentos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SysRrhhPermisoAlimentos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhPermisoAlimentos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
