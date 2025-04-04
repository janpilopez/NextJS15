<?php

namespace app\controllers;

use app\models\SysAccesoProveedores;
use Exception;
use Yii;
use kartik\mpdf\Pdf;
use app\models\Model;
use app\models\SysAdmCargos;
use app\models\SysAdmUsuariosDep;
use app\models\SysEmpresa;
use yii\helpers\ArrayHelper;
use app\models\SysRrhhEmpleadosPermisosIngresos;
use app\models\User;
use app\models\sysAdmMandos;
use app\models\Search\SysRrhhEmpleadosPermisosIngresosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosPermisos;
use app\models\SysRrhhEmpleadosPermisosIngresosDet;
use yii\web\UploadedFile;
use Mpdf\Mpdf;

/**
 * PermisosIngresosController implements the CRUD actions for SysRrhhEmpleadosPermisosIngresos model.
 */
class PermisosIngresosController extends Controller
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
     * Lists all SysRrhhEmpleadosPermisosIngresos models.
     * @return mixed
     */
    public function actionIndex()
    {
       
        $searchModel = new SysRrhhEmpleadosPermisosIngresosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhEmpleadosPermisosIngresos model.
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
     * Creates a new SysRrhhEmpleadosPermisosIngresos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $fechaini       = date('Y-m-d H:i');
        $fechasalida    = date('Y-m-d H:i');
        $model          = new SysRrhhEmpleadosPermisosIngresos();
        $modeldet       = [new SysRrhhEmpleadosPermisosIngresosDet()];
        $envioCorreo    = false;
        $id             = [];
        $usernameCC     = "";
        $tipo           = "";
        $observacion    = "";
        $estado         = "";
        $nombreempresa  = "";

        if (Yii::$app->request->post()){

            $db = $_SESSION['db'];
            $fechaini     = $_POST['fechainicio'];
            $fechasalida     = $_POST['fechasalida'];
            $empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();

            while(date("Y-m-d H:i:s", strtotime($fechaini)) <= date("Y-m-d H:i:s", strtotime($fechasalida))){
            
                $db = $_SESSION['db'];
                
                if ($model->load(Yii::$app->request->post())) {
                    
                    $empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();
                    $detalles = Model::createDetallePrestamoIngresos(SysRrhhEmpleadosPermisosIngresosDet::classname());
                    Model::loadMultiple($detalles, Yii::$app->request->post());
                
                    $transaction = \Yii::$app->$db->beginTransaction();
                    
                    try {
    
                        $model->empresa           = strtoupper($model->empresa);
                        $model->usuario_creacion  = Yii::$app->user->username;
                        $model->id_sys_empresa    = $empresa->id_sys_empresa;
                        $model->fecha_creacion    = date('Ymd H:i:s');
                        $model->hora_ingreso      = date('H:i:s', strtotime($fechaini));
                        $model->fecha_ingreso     = date('Y-m-d', strtotime($fechaini));

                        if($model->tipo_visita == 8):
                            $model->estado            = 'A';
                        else:
                            $model->estado            = 'P';
                        endif;

                            $model->file =  UploadedFile::getInstance($model, 'file');
                    
                            if($model->file):
                            
                                    $ruta =  "C:/fotos/documentos/".$model->fecha_ingreso.'_'.$model->empresa.'.'.$model->file->extension;
                                    $model->file->saveAs($ruta);
                                    $model->documento = $ruta;
                            
                            endif;
                            
                            if ($flag = $model->save(false)) {
                                
                                //Agregar Detalle
                                foreach ($detalles  as $index => $detalle) {

                                    
                                    if($detalle->id_sys_rrhh_cedula == Null || empty($detalle->id_sys_rrhh_cedula)){
                                        
                                    }else{
                                    
                                        $newdetalle  =  new SysRrhhEmpleadosPermisosIngresosDet();
                                        $newdetalle->id_sys_rrhh_empleados_permisos_ingresos = $model->id;

                                        if(strlen(trim($detalle['id_sys_rrhh_cedula'])) > 15){
                                            $transaction->rollBack();
                                            Yii::$app->getSession()->setFlash('info', [
                                                'type' => 'danger','duration' => 1500,
                                                'icon' => 'glyphicons glyphicons-robot','message' => 'Error! . El número de dígitos en la cédula es mayor a 15',
                                                'positonY' => 'top','positonX' => 'right']);
                                            return $this->redirect(['index']);  
                                        }else{

                                            $newdetalle->id_sys_rrhh_cedula =  trim($detalle['id_sys_rrhh_cedula']);
                                            $newdetalle->nombres = strtoupper($detalle['nombres']);
                                            $newdetalle->telefono = $detalle['telefono'];
                                            $newdetalle->laptop = $detalle['laptop'];
                                            $newdetalle->auto = $detalle['auto'];
                                            $newdetalle->marca_auto = $detalle['marca_auto'];
                                            $newdetalle->otros = $detalle['otros'];
                                            $newdetalle->estado = 0;

                                            $existe = $this->existenciaProveedor(trim($detalle['id_sys_rrhh_cedula'])); 

                                            if(!$existe){

                                                $proveedor = new SysAccesoProveedores();

                                                $proveedor->cedula = trim($detalle['id_sys_rrhh_cedula']);
                                                $proveedor->nombreProveedor = strtoupper($detalle['nombres']);
                                                $proveedor->nivel_riesgo = 1;

                                                $proveedor->save(false);

                                                if(!$newdetalle->save(false)){
                                                    $flag = false;
                                                    $transaction->rollBack();
                                                    break;
                                                }

                                            }else{
                                                if($existe['nivel_riesgo'] != 3){
                                                    if(!$newdetalle->save(false)){
                                                        $flag = false;
                                                        $transaction->rollBack();
                                                        break;
                                                    }
                                                }else{
                                                    $transaction->rollBack();
                                                    Yii::$app->getSession()->setFlash('info', [
                                                        'type' => 'danger','duration' => 1500,
                                                        'icon' => 'glyphicons glyphicons-robot','message' => 'La visita '. $detalle['id_sys_rrhh_cedula'].' posee un nivel de riesgo alto!',
                                                        'positonY' => 'top','positonX' => 'right']);
                                                    return $this->redirect(['index']);
                                                }
                                            }
                                        }
                                    }
                                }
                                                    
                                if ($flag) {
                                    
                                    //Validar si el documento necesita autorizacion
                                    
                                    array_push($id,$model->id);
                                    $usernameCC =  Yii::$app->user->identity->email;
                                    $tipo = $model->tipo_visita;
                                    $observacion = $model->observacion;
                                    $estado = $model->estado;
                                    $nombreempresa = $model->empresa;
                                    $envioCorreo = true;


                                    $transaction->commit();

                                }

                            }
                        
                    }catch (Exception $e) {
                        
                        $transaction->rollBack();
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'danger','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador!'.$e->getMessage(),
                            'positonY' => 'top','positonX' => 'right']);
                        return $e->getMessage();
                    }
                            
                    //return $this->redirect(['index']);
                }

            
                $fechaini = date("Y-m-d H:i:s", strtotime($fechaini." + 1 day"));
                $model        = new SysRrhhEmpleadosPermisosIngresos();
                    
            }


            if($envioCorreo == true){

                $documento = $this->getDocumento('PERMISO_INGRESO');
                                
                $to = [];
                                
                $cc = [];
                                
                $mensaje = "<p>Se ha generado los permisos de Visita #".str_pad(implode(",",$id), 5, "0", STR_PAD_LEFT)."</b> con fecha desde ".date('Y-m-d H:i:s',strtotime($_POST['fechainicio']))." hasta ".date('Y-m-d H:i:s',strtotime($_POST['fechasalida']))."<p>Observación: ".$observacion."</p></p><p>Puede consultar el documento en el siguiente link:</p><a href='https://".Yii::$app->params['ipServer']."/permisos-ingresos/index' target='_blank'>Ver Solicitud</a>";
                                
                $titulo = "Ingreso de Visita a la Empresa";

                if($usernameCC != ""):
                    array_push($cc, $usernameCC);
                endif;
                
                $emailUser = $this->ObtenerUsuariosGruposAutorizacionAll($documento['id'],$tipo);
                                
                foreach ($emailUser as $user):
                                        
                    if($usernameCC !=  $user['email']):
                        array_push($to, $user['email']);
                    endif;
                    
                endforeach;

                if($estado == 'A'):

                    $table = "";
                    $table.= "<table border='1'><tr><th>No</th><th>Cédula</th><th>Nombres</th><th>Teléfono</th><th>Laptop</th><th>Auto</th><th>Placa</th><th>Otros</th></tr>";
                                    
                    $detalle = SysRrhhEmpleadosPermisosIngresosDet::find()->where(['id_sys_rrhh_empleados_permisos_ingresos'=> $id[0]])->all();
                    $con = 0;
                                    
                    foreach ($detalle as $index => $item):
                                        
                        $con++;
            
                        if($item['telefono'] == 1):
                            $item['telefono'] = 'Autorizado';
                        else:
                            $item['telefono'] = 'No Autorizado';
                        endif;
            
                        if($item['laptop'] == 1):
                            $item['laptop'] = 'Autorizado';
                        else:
                            $item['laptop'] = 'No Autorizado';
                        endif;
            
                        if($item['auto'] == 1):
                            $item['auto'] = 'Autorizado';
                        else:
                            $item['auto'] = 'No Autorizado';
                            $item['marca_auto'] = '';
                        endif;
            
                        if($item['otros'] == 1):
                            $item['otros'] = 'Autorizado';
                        else:
                            $item['otros'] = 'No Autorizado';
                        endif;
            
                        $table.= "<tr><td>".$con."</td><td>".$item['id_sys_rrhh_cedula']."</td><td>".$item['nombres']."</td><td>".$item['telefono']."</td><td>".$item['laptop']."</td><td>".$item['auto']."</td><td>".$item['marca_auto']."</td><td>".$item['otros']."</td></tr>";
                                        
                    endforeach;
                                    
                    $table.= "</table>";
                                    
                    $db      = $_SESSION['db'];
                                    
                    $titulo  = "Ingreso de Visita a la Empresa";
                                        
                    $mensaje = "<p>Los ingresos  #".str_pad(implode(",",$id), 5, "0", STR_PAD_LEFT)." para la visita a planta, con fecha desde ".date('Y-m-d H:i:s',strtotime($_POST['fechainicio']))." hasta ".date('Y-m-d H:i:s',strtotime($_POST['fechasalida']))."</p><p>Empresa:".$nombreempresa."</p><p>Personal a Ingresar:</p>".$table."<p>Observación: ".$observacion."</p><p>Ha sido aprobado con éxito.</p>";

                    $this->EnviarCorreoAprobar($to, $cc, $mensaje, $titulo, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social,$documento['mail_notificaciones'], "A",$id[0]);

                else:

                    $this->EnviarCorreo($to, $cc, $mensaje, $titulo, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social,"", "P");

                endif;
            }

            Yii::$app->getSession()->setFlash('info', [
                'type' => 'success','duration' => 3000,
                'icon' => 'glyphicons glyphicons-robot','message' => 'El permiso ha sido registrado con éxito!',
                'positonY' => 'top','positonX' => 'right']);
               
            return $this->redirect(['index']);

        }

        return $this->render('create', [
            'model' => $model,
            'modeldet'=> $modeldet,
            'update' => 0,
            'esupdate' => 0,
            'fechaini' => $fechaini,
            'fechasalida' => $fechasalida
        ]);
    }

    /**
     * Updates an existing SysRrhhEmpleadosPermisosIngresos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $db    = $_SESSION['db'];
        $model = $this->findModel($id);
        $modeldet = [];

        $datos= SysRrhhEmpleadosPermisosIngresosDet::find()
        ->where(['id_sys_rrhh_empleados_permisos_ingresos'=>$id])
        ->orderBy('nombres')
        ->all();

        if ($datos){
            foreach ($datos as $data){
                $obj                                   = new SysRrhhEmpleadosPermisosIngresosDet();
                $obj->id_sys_rrhh_empleados_permisos_ingresos = $data->id_sys_rrhh_empleados_permisos_ingresos;
                $obj->id_sys_rrhh_cedula               = $data->id_sys_rrhh_cedula;
                $obj->nombres              = $data->nombres;
                array_push($modeldet, $obj);
            }
        }else{
            array_push($modeldet, new SysRrhhEmpleadosPermisosIngresosDet());
        }

        if($model->estado == 'A'){
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'warning','duration' => 3000,
                'icon' => 'glyphicons glyphicons-robot','message' => 'No se puede modificar el permiso se encuentra aprobado!',
                'positonY' => 'top','positonX' => 'right']);

            return $this->redirect(['index']);
        }else{
            if ($model->load(Yii::$app->request->post())) {

                $oldIDs    = ArrayHelper::map($modeldet, 'id_sys_rrhh_empleados_permisos_ingresos', 'id_sys_rrhh_empleados_permisos_ingresos');
            
                $array  = Yii::$app->request->post('SysRrhhEmpleadosPermisosIngresosDet');
                
                if ($array){
                    
                    $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($array, 'id_sys_rrhh_empleados_permisos_ingresos', 'id_sys_rrhh_empleados_permisos_ingresos')));
                }
                
                if(!empty($deletedIDs)){
                    
                    SysRrhhEmpleadosPermisosIngresosDet::deleteAll(['id_sys_rrhh_empleados_permisos_ingresos' => $deletedIDs]);
                }
                
                $transaction = \Yii::$app->$db->beginTransaction();
                
                try {
                    
                    if ($flag = $model->save(false)) {
                        
                        //nucleo familiar
                        if ($array){
                            
                            foreach ($array as $index => $modeldetalle) {
                            
                                $md = SysRrhhEmpleadosPermisosIngresosDet::find()->where(['id_sys_rrhh_empleados_permisos_ingresos'=> $modeldetalle['id_sys_rrhh_empleados_permisos_ingresos']])->one();
                                $md->id_sys_rrhh_cedula                = $modeldetalle['id_sys_rrhh_cedula'];
                                $md->nombres               = $modeldetalle['nombres'];
                                 
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
        }
       

        return $this->render('update', [
            'model' => $model,
            'modeldet' => $modeldet,
            'update' => 1,
            'esupdate'=> 1,
        ]);
    }
    
    public function actionAprobar($id){
        
        $model = $this->findModel($id);
        
        if($model->estado == 'P'):
        
            $estado = "P";
             
            //Validar si el documento necesita autorizacion
            $documento = $this->getDocumento('PERMISO_INGRESO');
            
            if($documento):
                
                $autorizacion = $this->getAutorizacion($documento['id'], Yii::$app->user->identity->id,$model->tipo_visita);
                
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
                   
                    $model->estado = $estado;
                    $model->usuario_aprobacion = Yii::$app->user->username;;
                    $model->fecha_aprobacion = date('Ymd H:i:s');

                    if($model->save(false)):
                    
                        $table = "";
                        $table.= "<table border='1'><tr><th>No</th><th>Cédula</th><th>Nombres</th><th>Teléfono</th><th>Laptop</th><th>Auto</th><th>Placa</th><th>Otros</th></tr>";
                        
                        $detalle = SysRrhhEmpleadosPermisosIngresosDet::find()->where(['id_sys_rrhh_empleados_permisos_ingresos'=> $model->id])->all();
                        $con = 0;
                        
                        foreach ($detalle as $index => $item):
                            
                            $con++;

                            if($item['telefono'] == 1):
                                $item['telefono'] = 'Autorizado';
                            else:
                                $item['telefono'] = 'No Autorizado';
                            endif;

                            if($item['laptop'] == 1):
                                $item['laptop'] = 'Autorizado';
                            else:
                                $item['laptop'] = 'No Autorizado';
                            endif;

                            if($item['auto'] == 1):
                                $item['auto'] = 'Autorizado';
                            else:
                                $item['auto'] = 'No Autorizado';
                                $item['marca_auto'] = '';
                            endif;

                            if($item['otros'] == 1):
                                $item['otros'] = 'Autorizado';
                            else:
                                $item['otros'] = 'No Autorizado';
                            endif;

                            $table.= "<tr><td>".$con."</td><td>".$item['id_sys_rrhh_cedula']."</td><td>".$item['nombres']."</td><td>".$item['telefono']."</td><td>".$item['laptop']."</td><td>".$item['auto']."</td><td>".$item['marca_auto']."</td><td>".$item['otros']."</td></tr>";
                            
                        endforeach;
                        
                        $table.= "</table>";
                        
                        $db      = $_SESSION['db'];
                        
                        $titulo  = "Ingreso de Visita a la Empresa";
                            
                        $mensaje = "<p>El ingreso  #".str_pad($model->id, 5, "0", STR_PAD_LEFT)." para la visita a planta, mismo que inicia el ".$model->fecha_ingreso." a la hora ".date('H:i:s', strtotime($model->hora_ingreso))."</p><p>Empresa:".$model->empresa."</p><p>Personal a Ingresar:</p>".$table."<p>Observación: ".$model->observacion."</p><p>Ha sido aprobado con éxito.</p>";
                        
                        $empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();
                        
                        $to = [];
                        
                        $cc = [];

                        $mailUserCreate =  $this->getEmailCreacion($model->usuario_creacion);
                        
                        array_push($cc, $mailUserCreate);
                        
                        $addCC = false ;

                        $emailUser = $this->ObtenerUsuariosGruposAutorizacionAll($documento['id'],$model->tipo_visita);
                        
                        foreach ($emailUser as $user):
                                
                            if($mailUserCreate !=  $user['email']):
                                array_push($to, $user['email']);
                            endif;
             
                        endforeach;

                        foreach ($to as $item):
                        
                            if($item ==  Yii::$app->user->identity->email):
                                $addCC = true;
                                break;
                            endif;
                                
                        endforeach;
                            
                        if(!$addCC):
                            array_push($cc, Yii::$app->user->identity->email);
                        endif;
                                       
                        $this->EnviarCorreoAprobar($to, $cc, $mensaje, $titulo, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, $documento['mail_notificaciones'], $estado, $model->id);
                            
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
     * Deletes an existing SysRrhhEmpleadosPermisosIngresos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        if($model->estado == 'P'):
        
            $estado = "P";
             
            //Validar si el documento necesita autorizacion
            $documento = $this->getDocumento('PERMISO_INGRESO');
            $flujo = $this->obtenerFlujo('PERMISO_INGRESO',Yii::$app->user->id);
            
           
            if($documento):
                
                $autorizacion = $this->getAutorizacion($documento['id'], Yii::$app->user->identity->id,$model->tipo_visita);
                
                if($autorizacion):
                
                    //Todos los departamentos
                    if ($autorizacion['nivel_autorizacion'] < 3):
                        $estado = 'N';
                    endif;
            
                endif;
            
            endif;
        
            if($estado == "N"):
                   
                    $model->estado = $estado;

                    $model->anuladado = 1;

                    $model->usuario_anulacion = Yii::$app->user->username;

                    if($model->save(false)):
                    
                        $table = "";
                        $table.= "<table><tr><th>No</th><th>Cédula</th><th>Nombres</th></tr>";
                        
                        $detalle = SysRrhhEmpleadosPermisosIngresosDet::find()->where(['id_sys_rrhh_empleados_permisos_ingresos'=> $model->id])->all();
                        $con = 0;
                        
                        foreach ($detalle as $index => $item):
                            
                            $con++;
                            $table.= "<tr><td>".$con."</td><td>".$item['id_sys_rrhh_cedula']."</td><td>".$item['nombres']."</td></tr>";
                            
                        endforeach;
                        
                        $table.= "</table>";
                        
                        $db      = $_SESSION['db'];
                        
                        $titulo  = "Ingreso de Visita a la Empresa";
                       
                        $mensaje = "<p>El permiso  #".str_pad($model->id, 5, "0", STR_PAD_LEFT)." para el ingreso a planta de personal externo.</p><p>No ha sido aprobado.</p>";
                        
                        $empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();
                        
                        $to = [];
                        
                        $cc = [];
                        
                        $mailUserCreate =  $this->getEmailCreacion($model->usuario_creacion);
                        
                        array_push($cc, $mailUserCreate);
                        
                        $addCC = false ;

                        $emailUser = $this->ObtenerUsuariosGruposAutorizacionAll($documento['id'],$model->tipo_visita);
                        
                        foreach ($emailUser as $user):
                                
                            if($mailUserCreate !=  $user['email']):
                                array_push($to, $user['email']);
                            endif;
             
                        endforeach;

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
   
    public function actionVeringresos(){
        
        $this->layout = '@app/views/layouts/main_emplados';
            
        $fechaini  =  date('Y-m-d');
        $fechafin  =  date('Y-m-d');
        $cedula    = ''; 
        $datos = [];
        if(Yii::$app->request->post()){
           
            $db =  $_SESSION['db'];
            
            $fechaini        = $_POST['fechaini']== null ?  $fechaini : $_POST['fechaini'];
            $fechafin        = $_POST['fechafin']== null ?  $fechafin : $_POST['fechafin'];
            $cedula          = $_POST['cedula'] == null ?    $cedula  : $_POST['cedula'];  
            
            $datos =  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDatosVisitantes]  @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @cedula = '{$cedula}'")->queryAll(); 
           
        }
        return $this->render('personalingresoindividual', ['fechaini'=> $fechaini, 'fechafin' => $fechafin, 'cedula'=> $cedula, 'datos'=> $datos]);
    }

    public function actionVeringresosgeneral(){
        
        $this->layout = '@app/views/layouts/main_emplados';
            
        $fechaini  =  date('Y-m-d');
        $fechafin  =  date('Y-m-d');
        $datos = [];
        if(Yii::$app->request->post()){
           
            $db =  $_SESSION['db'];
            
            $fechaini        = $_POST['fechaini']== null ?  $fechaini : $_POST['fechaini'];
            $fechafin        = $_POST['fechafin']== null ?  $fechafin : $_POST['fechafin'];
            
            $datos =  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDatosVisitantesGeneral] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}'")->queryAll(); 
           
        }
        return $this->render('personalingreso', ['fechaini'=> $fechaini, 'fechafin' => $fechafin, 'datos'=> $datos]);
    }
    
    public function actionPersonalingresoindividualpdf($fechaini, $fechafin, $cedula){
        
        ini_set("pcre.backtrack_limit", "5000000");
        
        $db =  $_SESSION['db'];
        
        $empleado = SysRrhhEmpleadosPermisosIngresosDet::find()->where(['id_sys_rrhh_cedula'=> $cedula])->one();
        $datos =  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDatosVisitantes]  @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @cedula = '{$cedula}'")->queryAll(); 
        
        $html =  $this->renderPartial('_personalingresoindividualpdf',[
           'datos' => $datos, 'empleado'=> $empleado, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin
        ]);
       
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
            'marginTop' => 19,
            'marginBottom' => 19,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px} .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 { border:0;padding:0;margin-left:-0.00001;} .fuente_table {font-size: 8px;}',
            
            // set mPDF properties on the fly
            //'options' => ['title' => 'Solicitud de Vacaciones'],
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' =>  'Informe de Visitas Individual',
                'SetHeader'=>['Sistema Gestión de Nómina - Informe Visitas Individual||'],
                'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}']
            ]
        ]);
        
        // return the pdf output as per the destination setting
        return $pdf->render();
        
    }

    public function actionPersonalingresopdf($fechaini, $fechafin){
        
        ini_set("pcre.backtrack_limit", "5000000");
        
        $db =  $_SESSION['db'];
        
        $datos =  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDatosVisitantesGeneral]  @fechaini = '{$fechaini}', @fechafin = '{$fechafin}'")->queryAll(); 
        
        $html =  $this->renderPartial('_personalingresopdf',[
           'datos' => $datos, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin
        ]);
       
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
            'marginTop' => 19,
            'marginBottom' => 19,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px} .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 { border:0;padding:0;margin-left:-0.00001;} .fuente_table {font-size: 8px;}',
            
            // set mPDF properties on the fly
            //'options' => ['title' => 'Solicitud de Vacaciones'],
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' =>  'Informe de Visistas General',
                'SetHeader'=>['Sistema Gestión de Nómina - Informe Visitas General||'],
                'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}']
            ]
        ]);
        
        // return the pdf output as per the destination setting
        return $pdf->render();
        
    }

    public function actionPersonalingresoindividualxls($fechaini, $fechafin, $cedula){
       
        $db =  $_SESSION['db'];
        
        $empleado = SysRrhhEmpleadosPermisosIngresosDet::find()->where(['id_sys_rrhh_cedula'=> $cedula])->one();
        $datos =  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDatosVisitantes]  @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @cedula = '{$cedula}'")->queryAll(); 
         
        return $this->render('_personalingresoindividualxls', ['datos' => $datos, 'empleado'=> $empleado, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin]);
    }

    public function actionPersonalingresoxls($fechaini, $fechafin){
       
        $db =  $_SESSION['db'];
        
        $datos =  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDatosVisitantesGeneral]  @fechaini = '{$fechaini}', @fechafin = '{$fechafin}'")->queryAll(); 
         
        return $this->render('_personalingresoxls', ['datos' => $datos, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin]);
    }

    private function ObtenerUsuariosGruposAutorizacionAll($id_sys_documento,$tipo){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerUsuariosGruposAutorizacionXTipoVisita]  @tipo = {$tipo}, @documento = {$id_sys_documento}")->queryAll();
    }

    private function ObtenerUsuariosGruposAutorizacionAllXDepartamento($id_sys_documento,$area,$departamento){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerUsuariosGruposAutorizacionXAreaXDepartamento]  @area = {$area}, @documento = {$id_sys_documento}, @departamento = {$departamento}")->queryAll();
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
          ->setBcc(Yii::$app->params['email']['JefeSistemas'])
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
           
    }

    private function EnviarCorreoAprobar($to, $cc, $mensaje, $titulo, $mail_host, $mail_username, $mail_password, $mail_port, $razon_social, $mail_cc, $estado, $id){
        $cC = $cc;
        $model =   $this->findModel($id);

        if($estado == 'A'):
        
            if($mail_cc != "" && strlen($mail_cc) > 0):
            
                $data = explode(";", $mail_cc);
                
                foreach ($data as $row):
                     array_push($cC, $row);
                endforeach;
                
            endif;

        endif;

        $path = $model->documento;

            if (file_exists($path)) {
            
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
                    ->setBcc(Yii::$app->params['email']['JefeSistemas'])
                    ->setFrom([$mail_username => $razon_social])
                    ->setSubject(''.$titulo.' - Gestión Nómina')
                    ->setHtmlBody($mensaje)
                    ->attach($path)
                    ->send();
                   
                else:
                        
                    Yii::$app->mailer->compose()
                    ->setTo($to)
                    ->setBcc(Yii::$app->params['email']['JefeSistemas'])
                    ->setFrom([$mail_username => $razon_social])
                    ->setSubject(''.$titulo.' - Gestión Nómina')
                    ->setHtmlBody($mensaje)
                    ->attach($path)
                    ->send();
                        
                endif;
            }else{

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
                    ->setBcc(Yii::$app->params['email']['JefeSistemas'])
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
            }
           
    }

    
    private function getEmailCreacion($username){
        
        $user  = User::find()->where(['username'=> $username])->one();
        return  $user != null ? trim($user->email) : "";
        
    }

    public function actionProveedores($proveedor){
        
        $db =  $_SESSION['db'];
        
        $datos = [];
        
        $datos =   Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDetalleProveedorPorNombre]  @proveedor = '$proveedor'")->queryAll(); 
        
        return $this->renderAjax('_listproveedores', [
            'datos'=>$datos
        ]);
        
    }
    
    private function getDocumento($codigo){
        
        $db =  $_SESSION['db'];
       
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDocumentoAutorizacion]  @codigo = '{$codigo}'")->queryOne();
        
    }

    private function obtenerAreayDepartamento($cedula){
        
        $db =  $_SESSION['db'];
       
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerAreayDepartamentoXCedula]  @cedula = '{$cedula}'")->queryOne();
        
    }

    private function existenciaProveedor($cedula){
        
        $db =  $_SESSION['db'];
       
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDatosProveedor]  @cedula = '{$cedula}'")->queryOne();
        
    }

    private function obtenerNivelRiesgo($cedula){
        
        $db =  $_SESSION['db'];
       
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDatosProveedor]  @cedula = '{$cedula}'")->queryOne();
        
    }

    private function obtenerFlujo($codigo,$idUsuario){
        
        $db =  $_SESSION['db'];
       
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDocumentoAutorizacion]  @codigo = '{$codigo}', @idUsuario = '{$idUsuario}'")->queryOne();
        
    }
    
    private function getAutorizacion($id_sys_documento, $id_usuario, $tipo){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerAutorizacionDocumentoUsuarioXTipo]  @id_sys_documento = {$id_sys_documento}, @id_usuario = {$id_usuario}, @tipo = {$tipo}")->queryOne();
        
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
        return Yii::$app->$db->createCommand("exec [dbo].[ObtenerDatosPermisosIngresos]")->queryAll();
        
    }
    /**
     * Finds the SysRrhhEmpleadosPermisosIngresos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SysRrhhEmpleadosPermisosIngresos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhEmpleadosPermisosIngresos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
