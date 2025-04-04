<?php
namespace app\controllers;
use Yii;
use app\models\Model;
use app\models\SysAdmUsuariosDep;
use app\models\SysEmpresa;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhPrestamosCab;
use app\models\User;
use app\models\Search\SysRrhhPrestamosCabSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\SysRrhhPrestamosDet;
use Exception;
/**
 * PrestamosController implements the CRUD actions for SysRrhhPrestamosCab model.
 */
class PrestamosController extends Controller
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
     * Lists all SysRrhhPrestamosCab models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysRrhhPrestamosCabSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhPrestamosCab model.
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

    /**
     * Creates a new SysRrhhPrestamosCab model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model     = new SysRrhhPrestamosCab();
        $modeldet  = [new SysRrhhPrestamosDet()];
        $meses     = Yii::$app->params['meses'];
        $anio      = date('Y');
        $model->anio_ini = $anio;
        $secuencia =  ['1'=> 'Cada Mes', 'Cada Dos Mes'];
        $peridododes = ['2' => 'Mensual', 'Quincena'];
        
        $db =  $_SESSION['db'];
        
        if ($model->load(Yii::$app->request->post())) {
            
            
            $detalleprestamo = Yii::$app->request->post('SysRrhhPrestamosDet');
            
            $transaction = \Yii::$app->$db->beginTransaction();
            
            try {
                
                $codprestamo                          =  SysRrhhPrestamosCab::find()->select(['max(CAST(id_sys_rrhh_prestamos_cab AS INT))'])->Where(['id_sys_empresa'=> '001'])->scalar() + 1 ;
                $model->id_sys_rrhh_prestamos_cab     =  $codprestamo;
                $model->id_sys_empresa                =  '001';
                $model->fecha                         =  date('Y-m-d');
                $model->estado                        =  'P';
                $model->usuario_creacion              =   Yii::$app->user->username;
                $model->save(false);
                if ($flag = $model->save(false)) {
                    
           
                    foreach ($detalleprestamo as  $detalle) {
                        
                        
                        $codigo     =  SysRrhhPrestamosDet::find()->select(['max(CAST(id_sys_rrhh_prestamos_det AS INT))'])->scalar() + 1;
                        
                        $newdetalle =  new SysRrhhPrestamosDet();
                        
                        $newdetalle->id_sys_rrhh_prestamos_det = $codigo;
                        $newdetalle->id_sys_rrhh_prestamos_cab = $codprestamo;
                        $newdetalle->anio  = $detalle['anio'];
                        $newdetalle->mes   = $detalle['mes'];
                        $newdetalle->valor = $detalle['valor'];
                        $newdetalle->saldo = $detalle['saldo'];
                        $newdetalle->id_sys_empresa = '001';
                        
               
                        if(!$newdetalle->save(false)){
                            $flag=false;
                            $transaction->rollBack();
                            break;
                        }
                    }
                    
                }
                
                if ($flag) {
               
                    $transaction->commit();
                        
                    //Enviar Correo

                    $empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();
                    
                    $documento = $this->getDocumento('SOLICITUD_PRESTAMO');

                    $estado = "P";
                        
                    $to = [];
                    
                    $cc = [];

                    $empleado    = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();

                    $mensaje     = "<p>Se generado una solicitud de préstamo  #".str_pad($model->id_sys_rrhh_prestamos_cab, 5, "0", STR_PAD_LEFT)." del Sr/Sr(a): <b>".$empleado->nombres."</b> con fecha de registro ".date('Y-m-d H:i:s')."</p><p>Puede consultar el documento en el siguiente link:</p><a href='https://".Yii::$app->params['ipServer']."/prestamos/view?id=".$model->id_sys_rrhh_prestamos_cab."' target='_blank'>Ver Solicitud</a>";
                    
                    $titulo      = "Solicitud de Préstamo";

                    $emailUser = $this->ObtenerUsuariosGruposAutorizacionAll($documento['id']);
                        
                    foreach ($emailUser as $user):
                        array_push($to, $user['email']);
                    endforeach;
                   
                    $this->EnviarCorreo($to, $cc, $mensaje, $titulo, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, '', $estado);
                                         
                    Yii::$app->getSession()->setFlash('info', [
                        'type' => 'success','duration' => 1500,
                        'icon' => 'glyphicons glyphicons-robot','message' => 'El préstamo fue registrado con éxito!',
                        'positonY' => 'top','positonX' => 'right']);
                    
                }
                
            
               }catch (Exception $e) {
                   
                $transaction->rollBack();
                throw new Exception($e);
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                    'positonY' => 'top','positonX' => 'right']);
          
                
            }
            
            
            return $this->redirect(['index']);
            

        }

        return $this->render('create', [
            'model' => $model,
            'modeldet'=> $modeldet,
            'meses'=> $meses,
            'secuencia'=> $secuencia,
            'periododes'=> $peridododes,
            'update'=> 0
        ]);
    }

    private function getDocumento($codigo){
        
        $db =  $_SESSION['db'];
       
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDocumentoAutorizacion]  @codigo = '{$codigo}'")->queryOne();
        
    }
    private function ObtenerUsuariosGruposAutorizacionAll($id_sys_documento){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerUsuariosGruposAutorizacionAll]  @id_sys_documento = {$id_sys_documento}")->queryAll();
    }
  
    /**
     * Updates an existing SysRrhhPrestamosCab model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modeldet  = [];
        $meses     = Yii::$app->params['meses'];
        $anio      = date('Y');
        $model->anio_ini = $anio;
        $secuencia =  ['1'=> 'Cada Mes', 'Cada Dos Mes'];
        $peridododes = ['2' => 'Mensual', 'Quincena'];
        
        $detalle = SysRrhhPrestamosDet::find()->where(['id_sys_rrhh_prestamos_cab'=> $model->id_sys_rrhh_prestamos_cab])->andWhere(['id_sys_empresa'=> '001'])->all();
        
        
        $db =  $_SESSION['db'];
               
        if($detalle):
        
            foreach ($detalle as $data):
        
            $obj = new SysRrhhPrestamosDet();
            $obj->id_sys_rrhh_prestamos_det = $data->id_sys_rrhh_prestamos_det;
            $obj->anio                      = $data->anio;
            $obj->mes                       = $data->mes;
            $obj->valor                     = $data->valor;
            $obj->saldo                     = floatval($data->saldo);
            
            array_push($modeldet, $obj);
            
        
            endforeach;
            
        else:
        
        
            array_push($modeldet, new SysRrhhPrestamosDet());
            
        
        endif;
        
        

        if ($model->load(Yii::$app->request->post())) {
            
      
            $arraydet   = Yii::$app->request->post('SysRrhhPrestamosDet');
            
          
            
            $transaction = \Yii::$app->$db->beginTransaction();
            
            try {
                
                
                if ($flag = $model->save(false)) {
                    
                    foreach ($arraydet as  $data) {
                        
                            
                         $md         = SysRrhhPrestamosDet::find()->where(['id_sys_rrhh_prestamos_det'=> $data['id_sys_rrhh_prestamos_det']])->one();
                         
                         $md->anio   = $data['anio'];
                         $md->mes    = $data['mes'];
                         $md->valor  = $data['valor'];
                         $md->saldo  = $data['saldo'];
                         
                         $flag = $md->save(false);
                        
                        if(!$flag){
                            $transaction->rollBack();
                            break;
                        }
                        
                    }
                    
                   
                    if ($flag) {
                        $transaction->commit();
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'success','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'El préstamo fue actualizado  con éxito!',
                            'positonY' => 'top','positonX' => 'right']);
                    }else{
                        $transaction->rollBack();
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'success','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'El préstamo no pudo ser actualizado!',
                            'positonY' => 'top','positonX' => 'right']);
                    }
                    
                    
                }
          
                
            }catch (Exception $e) {
                
                $transaction->rollBack();
                throw new Exception($e);
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                    'positonY' => 'top','positonX' => 'right']);
                
                
            }
           
            return $this->redirect('index');
        }

        return $this->render('update', [
            'model' => $model,
            'modeldet'=> $modeldet,
            'meses'=> $meses,
            'secuencia'=> $secuencia,
            'periododes'=> $peridododes,
            'update'=> 1
        ]);
    }
    
    public  function actionAprobarprestamos($id){
        
        $model = $this->findModel($id);

        if($model->estado == "P"):
            $estado = "P";

            $documento = $this->getDocumento('SOLICITUD_PRESTAMO');

            if($documento):
                
                $autorizacion = $this->getAutorizacion($documento['id'], Yii::$app->user->identity->id);
                
                if($autorizacion):
                
                    if(trim(Yii::$app->user->identity->cedula)):
                    
                        //Todos los departamentos
                      if ($autorizacion['nivel_autorizacion'] < 3):
                          $estado = 'A';
                      endif;
                
                    endif;
                
                endif;
            
            endif;

            if($estado == "A"):
                $model->usuario_aprobacion = Yii::$app->user->username;
                $model->fecha_aprobacion   = date('Ymd H:i:s');
                $model->autorizacion       = 'A';
                $model->estado             = $estado;

                if($model->save(false)):
                    $db      = $_SESSION['db'];

                    $empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();
                            
                    $to = [];
                    
                    $cc = []; 

                    $titulo = "Solicitud de Préstamo";

                    $emailUser = $this->ObtenerUsuariosGruposAutorizacionAll($documento['id']);
                            
                    foreach ($emailUser as $user):
                        array_push($to, $user['email']);
                    endforeach;

                    $empleado       = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();        
                    $mensaje        = "<p>La solicitud de préstamo   #".str_pad($model->id_sys_rrhh_prestamos_cab, 5, "0", STR_PAD_LEFT)." del  Sr/Sr(a): <b>".$empleado->nombres.".</b> ha sido aprobada con éxito.</p>";  
        
                    $this->EnviarCorreo($to, $cc, $mensaje, $titulo, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, $documento['mail_notificaciones'], $estado);
            
                    Yii::$app->getSession()->setFlash('info', [
                    'type' => 'success','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'El préstamo ha sido aprobado con éxito!',
                    'positonY' => 'top','positonX' => 'right']);
                else:
            
                    Yii::$app->getSession()->setFlash('info', [
                    'type' => 'warning','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'El préstamo no pudo ser aprobado!',
                    'positonY' => 'top','positonX' => 'right']);
                
                endif;

            else:
                
                Yii::$app->getSession()->setFlash('info', [
                'type' => 'warning','duration' => 3000,
                'icon' => 'glyphicons glyphicons-robot','message' => 'No tiene autorización de aprobación. Consulte con su admininistrador!',
                'positonY' => 'top','positonX' => 'right']);
            
            endif;

        else:
            
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'warning','duration' => 3000,
                'icon' => 'glyphicons glyphicons-robot','message' => 'El préstamo se encuentra  aprobado. Consulte con su admininistrador!',
                'positonY' => 'top','positonX' => 'right']);
            
        endif;
      
        return $this->redirect(['index']);
        
    }

    private function getAutorizacion($id_sys_documento, $id_usuario){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerAutorizacionDocumentoUsuario]  @id_sys_documento = {$id_sys_documento}, @id_usuario = {$id_usuario}")->queryOne();
        
    }
    
    public function actionNoaprobarprestamos($id){
        
        
        $model                     = $this->findModel($id);
        
        if($model->estado == "P"):
            $estado = "P";

            $documento = $this->getDocumento('SOLICITUD_PRESTAMO');

            if($documento):
                
                $autorizacion = $this->getAutorizacion($documento['id'], Yii::$app->user->identity->id);
                
                if($autorizacion):
                
                    if(trim(Yii::$app->user->identity->cedula)):
                    
                        //Todos los departamentos
                      if ($autorizacion['nivel_autorizacion'] < 3):
                          $estado = 'N';
                      endif;
                
                    endif;
                
                endif;
            
            endif;

            if($estado == "N"):
            
                $model->autorizacion       = 'N';
                $model->estado             = $estado;

                if($model->save(false)):
                    $db      = $_SESSION['db'];

                    $empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();
                            
                    $to = [];
                    
                    $cc = []; 

                    $titulo = "Solicitud de Préstamo";

                    $emailUser = $this->ObtenerUsuariosGruposAutorizacionAll($documento['id']);
                            
                    foreach ($emailUser as $user):
                        array_push($to, $user['email']);
                    endforeach;
                    
                    $mailUserDO = $this->ObtenerUsuarioJefeNomina();

                    array_push($cc, $mailUserDO['email']);
            
                    $mailTesoreria = 'tesoreria@pespesca.com';
                    
                    array_push($cc, $mailTesoreria);

                     //Enviar Correo aprobacion
                    $empleado       = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
                    $mensaje        = "<p>La solicitud de préstamo   #".str_pad($model->id_sys_rrhh_prestamos_cab, 5, "0", STR_PAD_LEFT)." del  Sr/Sr(a): <b>".$empleado->nombres.".</b> no aplica.</p>";
                    
                    $this->EnviarCorreo($to, $cc, $mensaje, $titulo, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, $documento['mail_notificaciones'], $estado);
                    
                    Yii::$app->getSession()->setFlash('info', [
                        'type' => 'success','duration' => 1500,
                        'icon' => 'glyphicons glyphicons-robot','message' => 'El préstamo ha sido actualizado con éxito!',
                        'positonY' => 'top','positonX' => 'right']);
                else:
            
                    Yii::$app->getSession()->setFlash('info', [
                    'type' => 'warning','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error!',
                    'positonY' => 'top','positonX' => 'right']);
                
                endif;

            else:
                
                Yii::$app->getSession()->setFlash('info', [
                'type' => 'warning','duration' => 3000,
                'icon' => 'glyphicons glyphicons-robot','message' => 'No tiene autorización de aprobación. Consulte con su admininistrador!',
                'positonY' => 'top','positonX' => 'right']);
            
            endif;

        else:
            
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'warning','duration' => 3000,
                'icon' => 'glyphicons glyphicons-robot','message' => 'El préstamo se encuentra aprobado. Consulte con su admininistrador!',
                'positonY' => 'top','positonX' => 'right']);
            
        endif;
        
        
        return $this->redirect(['index']);
        
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
    //obtener usuario gerente
    
    private function  getEmaiUser($id){
        
        $user  = User::find()->where(['id'=> $id])->one();
        
        if($user):
            return  trim($user->email);
        endif;
        
        return "";
        
    }
    
    private function getUser($username, $empresa){
           
        return User::find()->where(['username'=> $username])->andWhere(['empresa'=> $empresa])->one();
        
    }

    /**
     * Deletes an existing SysRrhhPrestamosCab model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
       

        $model = $this->findModel($id);
        
       if($model->autorizacion != 'A'):
       
            $model->anulado =  1;
            $model->save(false);
            
            Yii::$app->getSession()->setFlash('info', [
            'type' => 'success','duration' => 1500,
            'icon' => 'glyphicons glyphicons-robot','message' => 'El préstamo ha sido anulado con éxito!',
            'positonY' => 'top','positonX' => 'right']);
         
       else:
       
           Yii::$app->getSession()->setFlash('info', [
           'type' => 'warning','duration' => 1500,
           'icon' => 'glyphicons glyphicons-robot','message' => 'El préstamo anular el préstamo, porque se encuentra aprobado!',
           'positonY' => 'top','positonX' => 'right']);
       
       endif;
        
      
        
        return $this->redirect(['index']);
    }

    public function actionPrestamos(){
        $fechaini      = date('Y');
        $fechafin      = date('Y');
        $tipo          = 'A';
        $datos         = [];
    
        if(Yii::$app->request->post()){
            
            $fechaini         =  $_POST['fechaini']== null ?  '' : $_POST['fechaini'];
            $fechafin         =  $_POST['fechafin']== null ?  '' : $_POST['fechafin'];
            $tipo             =  $_POST['tipo'];


            if($tipo == 'A'):

                $datos = $this->getPrestamos();

            elseif($tipo == 'U'):

                $datos = $this->getPrestamosAnios($fechaini,$fechafin);

            endif;
        }
        
        return $this->render('infoprestamos', ['datos'=> $datos,'fechaini' => $fechaini,'fechafin' => $fechafin, 'tipo' => $tipo]);
    }

    public function actionPrestamos2(){
        $datos = $this->getPrestamos();
        
        return $this->render('infoprestamos2', ['datos'=> $datos]);
    }
    
 
    private function getPrestamos(){
        $db    = $_SESSION['db'];
        
        return   Yii::$app->$db->createCommand("exec [dbo].[ObtenerPrestamosEmpleados] ")->queryAll();
    }

    private function getPrestamosAnios($ini,$fin){
        $db    = $_SESSION['db'];
        
        return   Yii::$app->$db->createCommand("exec [dbo].[ObtenerPrestamosXAniosEmpleados] @ini = '{$ini}', @fin = '{$fin}' ")->queryAll();
    }

    public function actionPrestamosxls($fechaini,$fechafin,$tipo){

        if($tipo == 'A'):

            $datos = $this->getPrestamos();

            return $this->render('_infoprestamosxls', ['datos'=> $datos,'fechaini' => $fechaini,'fechafin' => $fechafin, 'tipo' => $tipo]);

        elseif($tipo == 'U'):

            $datos = $this->getPrestamosAnios($fechaini,$fechafin);

            return $this->render('_infoprestamos2xls', ['datos'=> $datos,'fechaini' => $fechaini,'fechafin' => $fechafin, 'tipo' => $tipo]);

        endif;
    
    }

    public function actionPrestamos2xls(){
    
        $datos = $this->getPrestamos();
        
        return $this->render('_infoprestamos2xls', ['datos'=> $datos]);
    }
    /**
     * Finds the SysRrhhPrestamosCab model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return SysRrhhPrestamosCab the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhPrestamosCab::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
