<?php

namespace app\controllers;

use Yii;
use app\models\SysEmpresa;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhCertificadosLaborales;
use app\models\User;
use app\models\Search\SysRrhhCertificadosLaboralesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use kartik\mpdf\Pdf;
use Mpdf\Mpdf;
use app\models\SysAdmUsuariosDep;
use app\models\SysAdmCargos;
use app\models\sysAdmMandos;
use app\models\SysAdmDepartamentos;
use yii\web\UrlManager;


/**
 * PermisoAlimentoController implements the CRUD actions for SysRrhhCertificadosLaborales model.
 */
class CertificadosLaboralesController extends Controller
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
     * Lists all SysRrhhCertificadosLaborales models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = '@app/views/layouts/main_emplados';
        $searchModel = new SysRrhhCertificadosLaboralesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhCertificadosLaborales model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $this->redirect(Yii::$app->homeUrl.'pdf/certificados-laborales/'.$id.'.pdf');
    }

    /**
     * Creates a new SysRrhhCertificadosLaborales model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysRrhhCertificadosLaborales();

        if ($model->load(Yii::$app->request->post())) {
            
            $model->usuario_creacion  = Yii::$app->user->username;
            $model->fecha_creacion = date('Ymd H:i:s');
            $model->estado = 'P';
            $model->anulado = 0;
            $model->save();
            
            $db = $_SESSION['db'];
            $empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();
            
            //Validar si el documento necesita autorizacion
            $documento = $this->getDocumento('SOLICITUD_CERTIFICADOS');
            
            //empleados
            $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
            
            $nivelempleado  =  $this->getNivelcargo($empleado->id_sys_adm_cargo, $empleado->id_sys_empresa);
                
            $departamento = $this->getDepartamento($empleado->id_sys_adm_cargo);

            $to = [];
            
            $cc = [];

            $ipserver  = Yii::$app->params['ipServer']; //ipserver
            
            $mensaje = "<p>Se ha generado una solicitud de certificación laboral del  Sr/Sr(a): <b>".$empleado->nombres."</b> con fecha de registro ".date('Y-m-d H:i:s')." con motivo ".$model->motivo."</p><p>Puede consultar el documento en el siguiente link:</p><a href='http://".$ipserver."/certificados-laborales/index' target='_blank'>Ver Solicitud</a>";
            
            $titulo = "Solicitud de Certificación Laboral";
            
            $mailUserCreate =  Yii::$app->user->identity->email;
            
            if($mailUserCreate != ""):
                 array_push($cc, $mailUserCreate);
            endif;
            
            $emailUserTalentoHumano = $this->ObtenerUsuariosGruposAutorizacionAll($documento['id']);
            
            foreach ($emailUserTalentoHumano as $user):
                
                if($mailUserCreate !=  $user['email']):
                    array_push($to, $user['email']);
                endif;
            
            endforeach;

            $html = $this->renderPartial('_certificado',['model'=> $model]);
                                        
            $mpdf = new Mpdf([
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
                'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
                'cssInline' => '.kv-heading-1{font-size:18px} .sin_margen{margin: 0px;} .line{ border-bottom: 1px solid black; margin-top: 20px;} .title{font-size: 20px;font-weight: bold;} .subtitle{font-size: 12px;} .negrita{font-weight: bold;} table {width: 100%} td { margin: 1px;} .margen-left{ margin: 20px;}',
            ]);
            $mpdf->WriteHTML($html);
            $mpdf->SetFooter('<img src="C:/xampp/htdocs/proyectonomina/web/logo/1391744064001/end.PNG">');
            $nombrepdf = trim($model->id.".pdf");
            $mpdf->Output("C:/xampp/htdocs/proyectonomina/web/pdf/certificados-laborales/".$nombrepdf,'F');
            
            $this->EnviarCorreoPre($to, $cc, $mensaje, $titulo, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, "", "P");
        
            
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'success','duration' => 3000,
                'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud ha sido registrada con éxito!',
                'positonY' => 'top','positonX' => 'right']);
            
            return $this->redirect(['index']);
            
        }

        return $this->render('create', [
            'model' => $model,
            'update'=> 0
        ]);
    }

    /**
     * Updates an existing SysRrhhCertificadosLaborales model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $model->motivo = utf8_encode($model->motivo);

        if ($model->load(Yii::$app->request->post())) {
            
           
        }
        
        return $this->render('update', [
            'model' => $model,
            'update' => 1
        ]);
    }

    /**
     * Deletes an existing SysRrhhCertificadosLaborales model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionAnularsolicitud($id)
    {
       $model =  $this->findModel($id);
       
       if($model->estado == 'P'):
       
        $model->anulado = 1;
        $model->estado = 'N';
        $model->save(false);
        

       Yii::$app->getSession()->setFlash('info', [
           'type' => 'success','duration' => 3000,
           'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud ha sido anulado con éxito!',
           'positonY' => 'top','positonX' => 'right']);
       
       else:
       
       Yii::$app->getSession()->setFlash('info', [
           'type' => 'warning','duration' => 3000,
           'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud no se puede anular porque se encuentra aprobado!',
           'positonY' => 'top','positonX' => 'right']);
       
       endif;

        return $this->redirect(['index']);
    }

    
    public function actionAprobarsolicitud($id){
        
        $model =  $this->findModel($id);

        //Obtenemos datos del empleados
        $empleado =  SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->one();

        $nivelempleado =   $this->getNivelcargo($empleado->id_sys_adm_cargo, $empleado->id_sys_empresa);

        $estado = "P";
        
        //Validar si el documento necesita autorizacion
        $documento = $this->getDocumento('SOLICITUD_CERTIFICADOS');
        
        $departamento = $this->getDepartamento($empleado->id_sys_adm_cargo);

        $tipousuario  = $this->getTipoUsuario(Yii::$app->user->identity->id);
        
        if($documento):
        
            $autorizacion = $this->getAutorizacion($documento['id'], Yii::$app->user->identity->id);
            
            if($autorizacion):
            
                if(trim(Yii::$app->user->identity->cedula) != trim($empleado->id_sys_rrhh_cedula)):
                    
                    if($tipousuario == 'D'):
                     
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
                    
                    $titulo = "Solicitud de Certificación Laboral";
                    
                    $mensaje = "<p>La solicitud de certificación laboral #".str_pad($model->id, 10, "0", STR_PAD_LEFT)."  del  Sr/Sr(a): <b>".$empleado->nombres."</b>, ha sido aprobada con éxito</p><p>Se adjunta archivo pdf de la solicitud</p>";
                    
                    $empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();
                    
                    $to = [];
                    $cc = [];

                    $emailUserTalentoHumano = $this->ObtenerUsuariosGruposAutorizacionAll($documento['id']);
                        
                    foreach ($emailUserTalentoHumano as $user):
                       array_push($to, $user['email']);
                    endforeach;
                                        
                    $mailUserCreate =  $this->getEmailCreacion($model->usuario_creacion);
                                        
                    if($mailUserCreate != ""):
                        array_push($cc, $mailUserCreate);
                    endif;
                                        
                    $mailempleado = $this->getEmailEmpleado($empleado->email);
                                        
                    if($mailempleado != ""):
                        array_push($to, $mailempleado);        
                    endif;
                                        
                    $this->EnviarCorreoPost($to, $cc, $mensaje, $titulo, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, $documento['mail_notificaciones'], $estado,$model->id);
                
                
                  Yii::$app->getSession()->setFlash('info', [
                     'type' => 'success','duration' => 3000,
                     'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud ha sido aprobado con éxito!',
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
            'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud se encuentra aprobado!',
            'positonY' => 'top','positonX' => 'right']);
        
        endif;
        
        return $this->redirect(['index']);
        
    }
    
    private function ObtenerUsuariosGruposAutorizacionAll($id_sys_documento){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerUsuariosGruposAutorizacionAll] @id_sys_documento = '{$id_sys_documento}'")->queryAll();
    }

    private function ObtenerUsuariosGruposAutorizacionXDepartamento($nivel_empleado, $id_sys_area, $id_sys_departamento){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerUsuariosGruposAutorizacionXDeparamento]  @nivel_empleado = {$nivel_empleado}, @id_sys_area = {$id_sys_area}, @id_sys_departamento = {$id_sys_departamento}")->queryAll();
    }

    private function getEmailEmpleado($emailEmpleado){        
        $email  = "";
        $emails = [];
        
         if(trim($emailEmpleado) != ""):
             
                 $variosemails = strpos($emailEmpleado, ';');
         
                 if($variosemails !== false):
                 
                     $emails    = explode(';', $emailEmpleado);
                     $email     = $emails[0];
                 
                 else:
                 
                     $email    = $emailEmpleado;
                 
                 endif;
         
         endif;
        
         return $email;
        
    }

    private function getNivelcargo($codcargo, $codempresa){
        
        $cargo =  SysAdmCargos::find()->where(['id_sys_adm_cargo'=> $codcargo])->andWhere(['id_sys_empresa'=> $codempresa])->one();
        
        if($cargo){
            
            $mando = sysAdmMandos::find()->where(['id_sys_adm_mando'=>$cargo->id_sys_adm_mando])->andWhere(['id_sys_empresa'=> $cargo->id_sys_empresa])->one();
            
            return $mando->nivel;
            
        }
        return 0; 
    }

    private function getDepartamento($id_sys_adm_cargo){
        
        //cargo usuario
        $cargo =  SysAdmCargos::find()->where(['id_sys_adm_cargo'=> $id_sys_adm_cargo])->one();
        //departamento usuario
        return  SysAdmDepartamentos::find()->where(['id_sys_adm_departamento'=> $cargo->id_sys_adm_departamento])->one();
        
        
    }
    
    private function EnviarCorreoPre($to, $cc, $mensaje, $titulo, $mail_host, $mail_username, $mail_password, $mail_port, $razon_social, $mail_cc, $estado){
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
    private function EnviarCorreoPost($to, $cc, $mensaje, $titulo, $mail_host, $mail_username, $mail_password, $mail_port, $razon_social, $mail_cc, $estado,$id){
        
        $model =   $this->findModel($id);
        
        $cC = $cc;
       
        $html = '';

        if($estado == 'A'):
        
            if($mail_cc != "" && strlen($mail_cc) > 0):
            
                $data = explode(";", $mail_cc);
                
                foreach ($data as $row):
                    array_push($cC, $row);
                endforeach;
            
            endif;

            $html = $this->renderPartial('_certificado',['model'=> $model]);
                                        
            $mpdf = new Mpdf([
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
                'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
                'cssInline' => '.kv-heading-1{font-size:18px} .sin_margen{margin: 0px;} .line{ border-bottom: 1px solid black; margin-top: 20px;} .title{font-size: 20px;font-weight: bold;} .subtitle{font-size: 12px;} .negrita{font-weight: bold;} table {width: 100%} td { margin: 1px;} .margen-left{ margin: 20px;}',
            ]);
            $mpdf->WriteHTML($html);
            $mpdf->SetFooter('<img src="C:/xampp/htdocs/proyectonomina/web/logo/1391744064001/end.PNG">');
            $nombrepdf = trim("Certificado-Laboral-".$model->id_sys_rrhh_cedula.".pdf");
            $mpdf->Output('pdf/'.$nombrepdf, 'F');
        
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
        ->attach('pdf/'.$nombrepdf)
        ->send();
        
        /*fin de correo*/
        unlink('pdf/'.$nombrepdf);
        
        else:
        
        Yii::$app->mailer->compose()
        ->setTo($to)
        ->setFrom([$mail_username => $razon_social])
        ->setSubject(''.$titulo.' - Gestión Nómina')
        ->setHtmlBody($mensaje)
        ->attach('pdf/'.$nombrepdf)
        ->send();
        
        /*fin de correo*/
        unlink('pdf/'.$nombrepdf);

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

    private function getTipoUsuario($id_usuario){
        
        $usertipo = SysAdmUsuariosDep::find()->where(['id_usuario'=> $id_usuario])->andwhere(['estado'=> 'A'])->one();
        
        if($usertipo):
        
        return $usertipo->usuario_tipo;
        
        endif;
        
        return 'N';
        
    }
    /**
     * Finds the SysRrhhCertificadosLaborales model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SysRrhhCertificadosLaborales the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhCertificadosLaborales::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
