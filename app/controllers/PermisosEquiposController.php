<?php

namespace app\controllers;

use Exception;
use Yii;
use app\models\Model;
use app\models\SysAdmCargos;
use app\models\SysAdmUsuariosDep;
use app\models\SysEmpresa;
use app\models\SysRrhhEmpleadosPermisosEquipos;
use app\models\User;
use app\models\sysAdmMandos;
use app\models\Search\SysRrhhEmpleadosPermisosEquiposSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosPermisos;
use app\models\SysRrhhEmpleadosPermisosEquiposDet;

/**
 * PermisosEquiposController implements the CRUD actions for SysRrhhEmpleadosPermisosEquipos model.
 */
class PermisosEquiposController extends Controller
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
     * Lists all SysRrhhEmpleadosPermisosEquipos models.
     * @return mixed
     */
    public function actionIndex()
    {
       
        $searchModel = new SysRrhhEmpleadosPermisosEquiposSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhEmpleadosPermisosEquipos model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        
        $db = $_SESSION['db'];
        
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new SysRrhhEmpleadosPermisosEquipos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model        = new SysRrhhEmpleadosPermisosEquipos();
        $modeldet     = [new SysRrhhEmpleadosPermisosEquiposDet()];

        $db = $_SESSION['db'];
        
        if ($model->load(Yii::$app->request->post())) {
            
            $empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();
            $detalles = Model::createDetallePrestamoEquipos(SysRrhhEmpleadosPermisosEquiposDet::classname());
            Model::loadMultiple($detalles, Yii::$app->request->post());
         
            $transaction = \Yii::$app->$db->beginTransaction();
            
            try {
                
                $model->usuario_creacion  = Yii::$app->user->username;
                $model->id_sys_empresa    = $empresa->id_sys_empresa;
                
                if ($flag = $model->save(false)) {
                    
                    //Agregar Detalle
                    foreach ($detalles  as $index => $detalle) {
                        
                        $newdetalle  =  new SysRrhhEmpleadosPermisosEquiposDet();
                        $newdetalle->id_sys_rrhh_empleados_permisos_equipo = $model->id;
                        $newdetalle->tipo =  $detalle['tipo'];
                        $newdetalle->marca = $detalle['marca'];
                        $newdetalle->modelo= $detalle['modelo'];
                        $newdetalle->serie = $detalle['serie'];
                 
                        if(!$newdetalle->save(false)){
                            $flag = false;
                            $transaction->rollBack();
                            break;
                        }
                        
                    }
                                        
                    if ($flag) {
                        
                        
                        //Obtenemos datos del empleados
                        $empleado      =  SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->one();
                       
                        //Validar si el documento necesita autorizacion
                        $documento = $this->getDocumento('PERMISO_EQUIPO_INFOR');
                        
                        $to = [];
                        
                        $cc = [];
                        
                        $mensaje = "<p>Se ha generado un permiso de salida de equipos informáticos #".str_pad($model->id, 5, "0", STR_PAD_LEFT)." del Sr/Sr(a): <b>".$empleado->nombres."</b> con fecha de registro ".date('Y-m-d H:i:s')."</p><p>Puede consultar el documento en el siguiente link:</p><a href='https://".Yii::$app->params['ipServer']."/permisos-equipos/view?id=".$model->id."' target='_blank'>Ver Solicitud</a>";
                        
                        $titulo = "Salida de Equipos Informáticos";
                        
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
                       
                        $transaction->commit();
                        
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'success','duration' => 3000,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso ha sido registrado con éxito!',
                            'positonY' => 'top','positonX' => 'right']);
                      
                    }
                    
                }
                
            }catch (Exception $e) {
                
                $transaction->rollBack();
                return $e->getMessage();
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador!'.$e->getMessage(),
                    'positonY' => 'top','positonX' => 'right']);
                   
            }
                    
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'modeldet'=> $modeldet
        ]);
    }

    /**
     * Updates an existing SysRrhhEmpleadosPermisosEquipos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->estado != 'A'){

            if ($model->load(Yii::$app->request->post()) && $model->save()) {
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
    
    public function actionAprobar($id){
        
        $model = $this->findModel($id);
        
        if($model->estado == 'P'):
        
            $estado = "P";
            
            $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
             
            //Validar si el documento necesita autorizacion
            $documento = $this->getDocumento('PERMISO_EQUIPO_INFOR');
            
           
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
        
            if($estado == "A"):
                
                if($empleado->id_sys_rrhh_cedula != Yii::$app->user->identity->cedula ):
                   
                    $model->estado = $estado;
                    $model->usuario_aprobacion = Yii::$app->user->username;
                    $model->fecha_aprobacion = date('Ymd H:i:s');

                    if($model->save(false)):
                    
                        $table = "";
                        $table.= "<table><tr><th>No</th><th>Tipo</th><th>Marca</th><th>Modelo</th><th>Serie</th></tr>";
                        
                        $detalle = SysRrhhEmpleadosPermisosEquiposDet::find()->where(['id_sys_rrhh_empleados_permisos_equipo'=> $model->id])->all();
                        $con = 0;
                        
                        foreach ($detalle as $index => $item):
                            
                            $con++;
                            $table.= "<tr><td>".$con."</td><td>".$this->getTipo($item['tipo'])."</td><td>".$item['marca']."</td><td>".$item['modelo']."</td><td>".$item['serie']."</td></tr>";
                            
                        endforeach;
                        
                        $table.= "</table>";
                        
                        $db      = $_SESSION['db'];
                        
                        $titulo  = "Salida de Equipos Informáticos";
                        
                        $mensaje = "<p>El permiso  #".str_pad($model->id, 5, "0", STR_PAD_LEFT)." para la salida de equipos informáticos  del  Sr/Sr(a): <b>".$empleado->nombres."</b>, mismo que inicia el ".$model->fecha_inicio." y culmina el ".$model->fecha_fin."</p><p>Descripción</p>".$table."<p>Ha sido aprobado con éxito.</p>";
                        
                        $empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();
                        
                        $to = [];
                        
                        $cc = [];
                        
                        //Copiar a gerencia el permiso
                         $emailUser = $this->ObtenerUsuariosGruposAutorizacionAll($documento['id']);
                        
                        foreach ($emailUser as $user):
                           array_push($to, $user['email']);
                        endforeach;
                        
                        $mailUserCreate =  $this->getEmailCreacion($model->usuario_creacion);
                        
                        array_push($cc, $mailUserCreate);
                        
                        /*
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
                        */
                                               
                        $this->EnviarCorreo($to, $cc, $mensaje, $titulo, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, $documento['mail_notificaciones'], $estado);
                            
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'success','duration' => 3000,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso sido aprobado con éxito!',
                            'positonY' => 'top','positonX' => 'right']);
                        
                        
                    else:
                    
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'warning','duration' => 3000,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso no  sido aprobado. Consulte con su admininistrador!',
                            'positonY' => 'top','positonX' => 'right']);
                    
                    endif;
                    
                 else:   
                 
                     Yii::$app->getSession()->setFlash('info', [
                     'type' => 'warning','duration' => 3000,
                     'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso no se puede autoaprobar. Consulte con su admininistrador!',
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
                'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso se encuentra  aprobado. Consulte con su admininistrador!',
                'positonY' => 'top','positonX' => 'right']);
            
        endif;
        
        return $this->redirect(['index']);
    }
   
    /**
     * Deletes an existing SysRrhhEmpleadosPermisosEquipos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        $model->anuladado = 1;
        
        $model->save(false);
  
        return $this->redirect(['index']);
    }
   
    private function ObtenerUsuariosGruposAutorizacionAll($id_sys_documento){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerUsuariosGruposAutorizacionAll]  @id_sys_documento = {$id_sys_documento}")->queryAll();
    }
    //obtener usuario gerente
    private function getGerente(){
        
        
        $usertipo = SysAdmUsuariosDep::find()->where(['usuario_tipo'=> 'G'])->andWhere(['estado'=> 'A'])->one();
        
        if($usertipo):
        
        return $usertipo->id_usuario;
        
        endif;
        
        return '0';
    }
    
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
    
    private function getTipo($tipo){
        
                switch ($tipo) {
                case 'P':
                    return  "PC";
                    break;
                case 'L':
                    return "Lapto";
                    break;
                case 'I':
                    return   "Impresora";
                    break;
                case 'O':
                    return   "Otros";
                    break;
                default:
                    echo "s/d";
  
          }
    }
    
    private function getNivelcargo($codcargo, $codempresa){
        
        $cargo =  SysAdmCargos::find()->where(['id_sys_adm_cargo'=> $codcargo])->andWhere(['id_sys_empresa'=> $codempresa])->one();
        
        if($cargo){
            
            $mando = sysAdmMandos::find()->where(['id_sys_adm_mando'=>$cargo->id_sys_adm_mando])->andWhere(['id_sys_empresa'=> $cargo->id_sys_empresa])->one();
            return $mando->nivel;
            
        }
        return 0;
    }
    
    private function getEmailCreacion($username){
        
        $user  = User::find()->where(['username'=> $username])->one();
        return  $user != null ? trim($user->email) : "";
        
    }
   
    private function ObtenerUsuariosGruposAutorizacion($id_grupo_autorizacion){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDocumentoAutorizacion]  @id_grupo_autorizacion = '{$id_grupo_autorizacion}'")->queryAll();
    }
    
    private function getDocumento($codigo){
        
        $db =  $_SESSION['db'];
       
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDocumentoAutorizacion]  @codigo = '{$codigo}'")->queryOne();
        
        
    }
    
    private function getAutorizacion($id_sys_documento, $id_usuario){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerAutorizacionDocumentoUsuario]  @id_sys_documento = {$id_sys_documento}, @id_usuario = {$id_usuario}")->queryOne();
        
    }
    /**
     * Finds the SysRrhhEmpleadosPermisosEquipos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SysRrhhEmpleadosPermisosEquipos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhEmpleadosPermisosEquipos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
