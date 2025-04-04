<?php

namespace app\controllers;

use app\models\SysRrhhEmpleadosSueldos;
use app\models\SysRrhhMarcacionesEmpleados;
use app\models\SysRrhhEmpleadosNovedades;
use Exception;
use Yii;
use app\models\SysRrhhSoextras;
use app\models\search\SysRrhhSoextrasSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\models\Model;
use app\models\SysAdmAreas;
use app\models\SysAdmDepartamentos;
use app\models\SysRrhhSoextrasEmpleados;
use app\models\SysRrhhEmpleados;
use app\models\SysEmpresa;
use app\models\User;
use app\models\SysAdmUsuariosDep;
use kartik\mpdf\Pdf;

/**
 * SoextrasController implements the CRUD actions for SysRrhhSoextras model.
 */
class SoextrasController extends Controller
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
     * Lists all SysRrhhSoextras models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysRrhhSoextrasSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhSoextras model.
     * @param string $id_sys_rrhh_soextras
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
     * Creates a new SysRrhhSoextras model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model    = new SysRrhhSoextras();
        $modeldet = [new SysRrhhSoextrasEmpleados()];
        
        $db  =  $_SESSION['db'];
                    
        $titulo  = "Solicitud de Aprobación Horas Extras";

        $empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();

        if ($model->load(Yii::$app->request->post())) {
            
            $modeldet = Model::createEmpleadosSoextras(SysRrhhSoextrasEmpleados::classname());
            Model::loadMultiple($modeldet, Yii::$app->request->post());
            
            $transaction = \Yii::$app->$db->beginTransaction();
            
            try {
                
                $codsoextra =  SysRrhhSoextras::find()->select(['max(CAST(id_sys_rrhh_soextras AS INT))'])->from('sys_rrhh_horas_extras_solicitud WITH (nolock)')
                ->Where(['id_sys_empresa'=> '001'])->scalar();
                
                
                if(empty($model->id_sys_adm_departamento)){
                    $model->id_sys_rrhh_soextras          = $codsoextra + 1;
                    $model->transaccion_usuario           = Yii::$app->user->username;
                    $model->id_sys_empresa                = '001';
                    $model->fecha_registro                = $model->fecha_registro;
                    $model->comentario                    = $model->comentario;
                    $model->estado                        = 'P';
                    $model->fecha_creacion                = date('Ymd H:i:s');
                }else{
                    $model->id_sys_rrhh_soextras          = $codsoextra + 1;
                    $model->transaccion_usuario           = Yii::$app->user->username;
                    $model->id_sys_empresa                = '001';
                    $model->fecha_registro                = $model->fecha_registro;
                    $model->comentario                    = $model->comentario;
                    $model->estado                        = 'P';
                    $model->id_sys_adm_departamento       = $model->id_sys_adm_departamento;
                    $model->fecha_creacion                = date('Ymd H:i:s');
                }
                

                if ($flag = $model->save(false)) {
                    
                   //Agregar Empleados       
                    foreach ($modeldet as $index => $modeldetalle) {
                            
                         
                            $md =  new SysRrhhSoextrasEmpleados();
                            $newcodigo =  SysRrhhSoextrasEmpleados::find()->select(['max(CAST(id_sys_rrhh_soextras_empleados AS INT))'])->from('sys_rrhh_horas_extras_solicitud_empleados WITH (nolock)')->Where(['id_sys_empresa'=> '001'])->scalar() + 1; 
                            $valor50 = 0;
                            $valor100 = 0;    
                        
                            if($modeldetalle->id_sys_rrhh_cedula == Null || empty($modeldetalle->id_sys_rrhh_cedula)){
                               
                            }else{

                                $data = $this->ObtenerDatosMarcacion($modeldetalle->id_sys_rrhh_cedula, $model->fecha_registro);
                                $h50            = '00:00:00';
							    $h100           = '00:00:00';
                                $salidadesayuno   = '00:00:00';
                                $salidaalmuerzo   = '00:00:00';
                                $salidamerienda   = '00:00:00';

                                if($data){

                                    if($data['salida_desayuno'] != NULL){
                                        $salidadesayuno  =  date('H:i:s', strtotime($data['salida_desayuno']));
                                    }

                                    if($data['salida_almuerzo'] != NULL){
                                        $salidaalmuerzo  =  date('H:i:s', strtotime($data['salida_almuerzo']));
                                    }
                                    

                                    if($data['salida_merienda'] != NULL){
                                        $salidamerienda  =  date('H:i:s', strtotime($data['salida_merienda']));
                                    }

                                    $h50  = $this->getRendonminutos($this->gethoras50(date('Ymd H:i:s', strtotime($data['entrada'])), date('Ymd H:i:s', strtotime($data['salida'])),$salidadesayuno,$salidaalmuerzo,$salidamerienda,$modeldetalle->id_sys_rrhh_cedula, $model->fecha_registro,$data['feriado']));
                                    
                                    $h100  = $this->getRendonminutos($this->gethoras100(date('Ymd H:i:s', strtotime($data['entrada'])), date('Ymd H:i:s', strtotime($data['salida'])),$salidadesayuno,$salidaalmuerzo,$salidamerienda,$modeldetalle->id_sys_rrhh_cedula, $model->fecha_registro, $data['feriado'],$data['agendamiento']));
                                }

                                if($h50 < '00:00:00'):
                                    $h50            = '00:00:00';
                                elseif($h100 < '00:00:00'):
                                    $h100           = '00:00:00';
                                endif;

                                if($this->HorasToDecimal($modeldetalle->horas50) > $this->HorasToDecimal($h50) && $this->HorasToDecimal($modeldetalle->horas100) > $this->HorasToDecimal($h100)){
                                    
                                    $transaction->rollBack();
                                    Yii::$app->getSession()->setFlash('info', [
                                        'type' => 'danger','duration' => 1500,
                                        'icon' => 'glyphicons glyphicons-robot','message' => 'El número de horas(50 y 100) a solicitar es mayor al número de horas realizados!'.$modeldetalle['id_sys_rrhh_cedula'],
                                        'positonY' => 'top','positonX' => 'right']);
                                    return $this->redirect(['index']);

                                }elseif($this->HorasToDecimal($modeldetalle->horas50) > $this->HorasToDecimal($h50)){
                                    
                                    $transaction->rollBack();
                                    Yii::$app->getSession()->setFlash('info', [
                                        'type' => 'danger','duration' => 1500,
                                        'icon' => 'glyphicons glyphicons-robot','message' => 'El número de horas(50) a solicitar es mayor al número de horas realizados!'.$modeldetalle['id_sys_rrhh_cedula'],
                                        'positonY' => 'top','positonX' => 'right']);
                                    return $this->redirect(['index']);


                                }elseif($this->HorasToDecimal($modeldetalle->horas100) > $this->HorasToDecimal($h100)){
                                    
                                    $transaction->rollBack();
                                    Yii::$app->getSession()->setFlash('info', [
                                        'type' => 'danger','duration' => 1500,
                                        'icon' => 'glyphicons glyphicons-robot','message' => 'El número de horas(100) a solicitar es mayor al número de horas realizados!'.$h100,
                                        'positonY' => 'top','positonX' => 'right']);
                                    return $this->redirect(['index']);

                                }else{

                                    $existe = $this->obtenerExistenteSolicitud($modeldetalle->id_sys_rrhh_cedula,$model->fecha_registro);

                                    if(!$existe){
                                        $md->id_sys_rrhh_soextras_empleados    = $newcodigo;
                                        $md->id_sys_rrhh_cedula                = $modeldetalle->id_sys_rrhh_cedula;
                                        $md->id_sys_empresa                    = '001';
                                        $md->comentario                        = $modeldetalle->comentario;
                                        $md->id_sys_rrhh_soextras              = $model->id_sys_rrhh_soextras;
                                        $md->horas50                           = $this->HorasToDecimal($modeldetalle->horas50);
                                        $md->horas100                          = $this->HorasToDecimal($modeldetalle->horas100);
                                        $md->pago50                            = $this->ObtenerValorDecimal50($modeldetalle->id_sys_rrhh_cedula,$modeldetalle->horas50);
                                        $md->pago100                           = $this->ObtenerValorDecimal100($modeldetalle->id_sys_rrhh_cedula,$modeldetalle->horas100);
    
                                        if (! ($flag = $md->save(false))) {
                                            $transaction->rollBack();
                                            break;
                                        }
                                    }else{
                                        $transaction->rollBack();
                                        Yii::$app->getSession()->setFlash('info', [
                                            'type' => 'danger','duration' => 1500,
                                            'icon' => 'glyphicons glyphicons-robot','message' => 'El colaborador '. $modeldetalle->id_sys_rrhh_cedula.' ya tiene solicitadas horas extras este día!',
                                            'positonY' => 'top','positonX' => 'right']);
                                        return $this->redirect(['index']);
                                    }
                                
                                }
                            }

                        }
                    
                    if ($flag) {

                        $area = SysAdmAreas::find()->where(['id_sys_adm_area' => $model->id_sys_adm_area])->one();

                    
                        $mensaje = $mensaje = "<p  style = 'margin:1px;'>Se ha generado una solicitud de horas extras #".str_pad($model->id_sys_rrhh_soextras, 5, "0", STR_PAD_LEFT)." del Área: <b>".$area->area."</b><p>Comentario: ".$model->comentario."</p><p>Puede consultar el documento en el siguiente link:</p><a href='http://".Yii::$app->params['ipServer']."/soextras/listar?id=".$model->id_sys_rrhh_soextras."' target='_blank'>Ver Solicitud</a>";
                       
                        $documento = $this->getDocumento('SOLICITUD_HORAS_EXTRAS');

                        $to = [];
                        $cc = [];

                        $mailUserCreate =  $this->getEmailCreacion($model->transaccion_usuario);
                        
                        if($mailUserCreate != ""):
                            array_push($cc, $mailUserCreate);
                        endif;
                        
                        //$this->EnviarCorreo($to, $cc, $mensaje, $titulo, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, $documento['mail_notificaciones'], "P");
                        
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

    public function actionCreatecomentario($id)
    {
        $model = $this->findModel($id);
        
        $estado        = 'R';
        
        $db            = $_SESSION['db'];
        
        $empresa       = SysEmpresa::find()->where(['db_name'=> $db])->one();
        
        $titulo        = 'Solicitud de Aprobación de Horas Extras';
        
        $tipousuario   = $this->getTipoUsuario(Yii::$app->user->identity->id);
        
        
            //Validar si el documento necesita autorizacion
            $documento = $this->getDocumento('SOLICITUD_HORAS_EXTRAS');
            
            if($documento):
            
                $autorizacion = $this->getAutorizacion($documento['id'], Yii::$app->user->identity->id);
                
                if($autorizacion):
                        
                    if($tipousuario == 'G'):
                    
                        $estado = 'N';
                    
                    endif;
                
                endif;
            
            endif;
            
            if($model->estado == 'R'):
                    
                if($estado == 'N'):

                    if ($model->load(Yii::$app->request->post())) :
                
                        $model->estado               = $estado;
                        $model->usuario_anulacion    = $estado != 'P' ? Yii::$app->user->username : null;
                        $model->fecha_anulacion      = date('Ymd H:i:s');
                        $model->comentario_anulacion = $model->comentario_anulacion;

                        if($model->save(false)):

                            $area = SysAdmAreas::find()->where(['id_sys_adm_area' => $model->id_sys_adm_area])->one();

                            $mensaje = "<p  style = 'margin:1px;'>La solicitud de horas extras #".str_pad($model->id_sys_rrhh_soextras, 5, "0", STR_PAD_LEFT)." del Área: <b>".$area->area."</b><p>No ha sido aprobada por motivo de: </p><p>".$model->comentario_anulacion."</p>";
                        
                            $to = [];
                            $cc = [];

                            $emailUser = $this->ObtenerUsuariosGruposAutorizacionAll($documento['id']);
                                
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
                            return $this->redirect(['index']);
                        endif; 
                    
                    endif;
                
                else:
                
                    Yii::$app->getSession()->setFlash('info', [
                    'type' => 'warning','duration' => 3000,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Usted no tiene permisos para aprobar permisos!',
                    'positonY' => 'top','positonX' => 'right']);
                    return $this->redirect(['index']);
                endif;


            else:
            
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'warning','duration' => 3000,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud ya ha sido aprobada!',
                    'positonY' => 'top','positonX' => 'right']);
                    return $this->redirect(['index']);
            endif;
       

        return $this->renderAjax('createcomentario', [
            'model' => $model,
            'update'=> 0,
            'esupdate'=> 0,
        ]);
    }

    public function HorasToDecimal($hora){
    
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

    public function ObtenerDatosMarcacion($cedula,$fecha){
        
        $db =  $_SESSION['db'];
        
        return Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerAsistenciaLaboralEmpleadosXCedula]  @fecha_ini = '{$fecha}',  @fecha_fin = '{$fecha}', @cedula = '$cedula'")->queryOne(); 
        
    }

    public function ObtenerValorDecimal50($cedula,$valor50){
        
        $valor_hora = 0;
        $newvalor50 = 0;
        $v50 = $this->HorasToDecimal($valor50);

        $sueldo = SysRrhhEmpleadosSueldos::find()->where(['id_sys_rrhh_cedula'=>$cedula])->andWhere(['estado'=>'A'])->one();

        if($sueldo){
            $valor_hora = number_format(($sueldo->sueldo / 240), 6,'.', '.');

            $newvalor50 = number_format(((($valor_hora * 0.50) + $valor_hora) *($v50)), 2,'.', '.' );

            return $newvalor50;
        }else{
            return 0;
        }  

        
    }

    public function obtenerExistenteSolicitud($cedula,$fecha){
        
        $db =  $_SESSION['db'];
        
        return Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerExistenciaSolicitudHorasExtrasXEmpleado]  @cedula = '$cedula', @fecha = '{$fecha}'")->queryAll(); 
    }

    public function ObtenerValorDecimal100($cedula,$valor100){
    
        $valor_hora = 0;
        $newvalor100 = 0;
        $v100 = $this->HorasToDecimal($valor100);

        $sueldo = SysRrhhEmpleadosSueldos::find()->where(['id_sys_rrhh_cedula'=>$cedula])->andWhere(['estado'=>'A'])->one();

        if($sueldo){
            $valor_hora = number_format(($sueldo->sueldo / 240), 6,'.', '.');

            $newvalor100 = number_format((($valor_hora * 2) * ($v100)), 2,'.', '.' );

            return $newvalor100;     
        }else{
            return 0;
        }                   
        
    }

    public function DecimaltoHoras($valor){
        
        if($valor > 0):
        
            $array = explode('.', trim($valor));
            $h     = floatval($array[0]);
            $m     = floatval($array[1]) * 0.60;
            return  str_pad($h, 2, "0", STR_PAD_LEFT).':'.str_pad(intval(round($m)), 2, "0", STR_PAD_LEFT).':00';
       
            
        else:
        
            return "00:00:00";
        
        endif;
    }
    
    public function actionEmpleadosdepartamento($departamento,$fecha){
        
        $db =  $_SESSION['db'];
        
        $datos = [];
        
        $datos =   Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboralXDepartamento]  @fechaini = '{$fecha}', @fechafin = '{$fecha}', @id_sys_adm_departamento = {$departamento}")->queryAll(); 
        
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
     * Updates an existing SysRrhhSoextras model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id_sys_rrhh_soextras
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $db    = $_SESSION['db'];
       
        $modeldet = [];        
        
        $datos= Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDetalladoSolicitudHorasExtras] @id = {$id}")->queryAll();
        
        if ($datos){
            foreach ($datos as $data){
                $obj                                   = new SysRrhhSoextrasEmpleados();
               // $emp                                   = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $data->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> $data->id_sys_empresa])->one();
                $obj->id_sys_rrhh_soextras_empleados   = $data['id_sys_rrhh_soextras_empleados'];
                $obj->id_sys_rrhh_cedula               = $data['id_sys_rrhh_cedula'];
                $obj->id_sys_empresa                   = $data['nombres'];
                $obj->horas50                          = $this->DecimaltoHoras($data['horas50']);
                $obj->horas100                         = $this->DecimaltoHoras($data['horas100']);
                $obj->pago50                           = $data['pago50'];
                $obj->pago100                          = $data['pago100'];
                $obj->comentario                       = $data['comentario'];
                array_push($modeldet, $obj);
            }
        }else{
            array_push($modeldet, new SysRrhhSoextrasEmpleados());
        }
    

        if($model->estado != 'A' && $model->estado != 'N' && $model->estado != 'R'){

            if ($model->load(Yii::$app->request->post())) {

                $model->fecha_actualizacion    = date('Ymd H:i:s');

                $oldIDs    = ArrayHelper::map($modeldet, 'id_sys_rrhh_soextras_empleados', 'id_sys_rrhh_soextras_empleados');
            
                $array  = Yii::$app->request->post('SysRrhhSoextrasEmpleados');
                
                if ($array){
                    
                    $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($array, 'id_sys_rrhh_soextras_empleados', 'id_sys_rrhh_soextras_empleados')));
                }
                
                if(!empty($deletedIDs)){
                    
                    SysRrhhSoextrasEmpleados::deleteAll(['id_sys_rrhh_soextras_empleados' => $deletedIDs]);
                }
                
                $transaction = \Yii::$app->$db->beginTransaction();
                
                try {
                    
                    if ($flag = $model->save(false)) {
                        
            
                        if ($array){
                            
                            foreach ($array as $index => $modeldetalle) {
                            
                                if($modeldetalle['id_sys_rrhh_soextras_empleados'] != ''){
                                    
                                    $newcodigo = SysRrhhSoextrasEmpleados::find()->select('id_sys_rrhh_soextras_empleados')->from('sys_rrhh_horas_extras_solicitud_empleados WITH (nolock)')->Where(['id_sys_empresa'=> '001', 'id_sys_rrhh_soextras_empleados'=> $modeldetalle['id_sys_rrhh_soextras_empleados']])->scalar();
                                    $md =  SysRrhhSoextrasEmpleados::find()->from('sys_rrhh_horas_extras_solicitud_empleados WITH (nolock)')->where(['id_sys_empresa'=> '001', 'id_sys_rrhh_soextras_empleados'=> $modeldetalle['id_sys_rrhh_soextras_empleados']])->one();
                                }
                                else{
                                    $md =  new SysRrhhSoextrasEmpleados();
                                    $newcodigo =  SysRrhhSoextrasEmpleados::find()->select(['max(CAST(id_sys_rrhh_soextras_empleados AS INT))'])->from('sys_rrhh_horas_extras_solicitud_empleados WITH (nolock)')->Where(['id_sys_empresa'=> '001'])->scalar() + 1;
                                }

                                $data = $this->ObtenerDatosMarcacion($modeldetalle['id_sys_rrhh_cedula'], $model->fecha_registro);
                               
                                $h50            = '00:00:00';
							    $h100           = '00:00:00';
                                $salidadesayuno   = '00:00:00';
                                $salidaalmuerzo   = '00:00:00';
                                $salidamerienda   = '00:00:00';
                               
                                if($data){

                                    if($data['salida_desayuno'] != NULL){
                                        $salidadesayuno  =  date('H:i:s', strtotime($data['salida_desayuno']));
                                    }

                                    if($data['salida_almuerzo'] != NULL){
                                        $salidaalmuerzo  =  date('H:i:s', strtotime($data['salida_almuerzo']));
                                    }
                                    

                                    if($data['salida_merienda'] != NULL){
                                        $salidamerienda  =  date('H:i:s', strtotime($data['salida_merienda']));
                                    }

                                    $h50  = $this->getRendonminutos($this->gethoras50(date('Ymd H:i:s', strtotime($data['entrada'])),date('Ymd H:i:s', strtotime($data['salida'])),$salidadesayuno,$salidaalmuerzo,$salidamerienda,$modeldetalle['id_sys_rrhh_cedula'], $model->fecha_registro,$data['feriado']));
                                    
                                    $h100  = $this->getRendonminutos($this->gethoras100(date('Ymd H:i:s', strtotime($data['entrada'])), date('Ymd H:i:s', strtotime($data['salida'])),$salidadesayuno,$salidaalmuerzo,$salidamerienda,$modeldetalle['id_sys_rrhh_cedula'], $model->fecha_registro, $data['feriado'],$data['agendamiento']));
                                }

                                if($modeldetalle['id_sys_rrhh_cedula'] != ''){

                                    if($this->HorasToDecimal($modeldetalle['horas50']) > $this->HorasToDecimal($h50) && $this->HorasToDecimal($modeldetalle['horas100']) > $this->HorasToDecimal($h100)){
                                        
                                        $transaction->rollBack();
                                        Yii::$app->getSession()->setFlash('info', [
                                            'type' => 'danger','duration' => 1500,
                                            'icon' => 'glyphicons glyphicons-robot','message' => 'El número de horas(50 y 100) a solicitar es mayor al número de horas realizados!'.$modeldetalle['id_sys_rrhh_cedula'],
                                            'positonY' => 'top','positonX' => 'right']);
                                        return $this->redirect(['index']);

                                    }elseif($this->HorasToDecimal($modeldetalle['horas50']) > $this->HorasToDecimal($h50)){
                                        
                                        $transaction->rollBack();
                                        Yii::$app->getSession()->setFlash('info', [
                                            'type' => 'danger','duration' => 1500,
                                            'icon' => 'glyphicons glyphicons-robot','message' => 'El número de horas(50) a solicitar es mayor al número de horas realizados!'.$modeldetalle['id_sys_rrhh_cedula'],
                                            'positonY' => 'top','positonX' => 'right']);
                                        return $this->redirect(['index']);


                                    }elseif($this->HorasToDecimal($modeldetalle['horas100']) > $this->HorasToDecimal($h100)){
                                        
                                        $transaction->rollBack();
                                        Yii::$app->getSession()->setFlash('info', [
                                            'type' => 'danger','duration' => 1500,
                                            'icon' => 'glyphicons glyphicons-robot','message' => 'El número de horas(100) a solicitar es mayor al número de horas realizados!'.$modeldetalle['id_sys_rrhh_cedula'],
                                            'positonY' => 'top','positonX' => 'right']);
                                        return $this->redirect(['index']);

                                    }else{

                                        $md->id_sys_rrhh_soextras_empleados    = $newcodigo;
                                        $md->id_sys_rrhh_cedula                = $modeldetalle['id_sys_rrhh_cedula'];
                                        $md->id_sys_rrhh_soextras              = $model->id_sys_rrhh_soextras;
                                        $md->id_sys_empresa                    = '001';
                                        $md->horas50                           = $this->HorasToDecimal($modeldetalle['horas50']);
                                        $md->horas100                          = $this->HorasToDecimal($modeldetalle['horas100']);
                                        $md->pago50                            = $this->ObtenerValorDecimal50($modeldetalle['id_sys_rrhh_cedula'],$modeldetalle['horas50']);
                                        $md->pago100                           = $this->ObtenerValorDecimal100($modeldetalle['id_sys_rrhh_cedula'],$modeldetalle['horas100']);
                                        $md->comentario                        = $modeldetalle['comentario'];

                                        if (! ($flag = $md->save(false))) {
                                            $transaction->rollBack();
                                            break;
                                        }

                                    }
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
     * Deletes an existing SysRrhhSoextras model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id_sys_rrhh_soextras
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model =$this->findModel($id);
        
        $estado        = 'R';
            
        $db            = $_SESSION['db'];
        
        $empresa       = SysEmpresa::find()->where(['db_name'=> $db])->one();
        
        $titulo        = 'Solicitud de Aprobación de Horas Extras';
        
        $tipousuario   = $this->getTipoUsuario(Yii::$app->user->identity->id);
       
        //Validar si el documento necesita autorizacion
        $documento = $this->getDocumento('SOLICITUD_HORAS_EXTRAS');
           
        if($documento):
           
            $autorizacion = $this->getAutorizacion($documento['id'], Yii::$app->user->identity->id);
               
            if($autorizacion):
                       
                if($tipousuario == 'G'):
                   
                    $estado = 'N';
                   
                endif;
               
            endif;

        endif;
           
        if($model->estado == 'R'):
                   
            if($estado == 'N'):
               
                $model->estado               = $estado;
                $model->usuario_anulacion    = $estado != 'P' ? Yii::$app->user->username : null;
                $model->fecha_anulacion      = date('Ymd H:i:s');
                $model->comentario_anulacion = $model->comentario_anulacion;
               
                if($model->save(false)):

                    $area = SysAdmAreas::find()->where(['id_sys_adm_area' => $model->id_sys_adm_area])->one();

                    $mensaje = "<p  style = 'margin:1px;'>La solicitud de horas extras #".str_pad($model->id_sys_rrhh_soextras, 5, "0", STR_PAD_LEFT)." del Área: <b>".$area->area."</b><p>No ha sido aprobada por motivo de: </p><p>".$model->comentario_anulacion."</p>";

                    $to = [];
                    $cc = [];

                    $emailUser = $this->ObtenerUsuariosGruposAutorizacionAll($documento['id']);
                           
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

    public function actionRevisar($id){
        
        $model         = SysRrhhSoextras::find()->where(['id_sys_rrhh_soextras'=> $id])->one();
        
        $estado        = 'P';
        
        $db            = $_SESSION['db'];
        
        $empresa       = SysEmpresa::find()->where(['db_name'=> $db])->one();
        
        $titulo        = 'Solicitud de Aprobación de Horas Extras';
        
        $tipousuario   = $this->getTipoUsuario(Yii::$app->user->identity->id);
        
        //Validar si el documento necesita autorizacion
        $documento = $this->getDocumento('SOLICITUD_HORAS_EXTRAS');
        
        if($documento):
                
            if($tipousuario == 'D' || $tipousuario == 'A'):
                
                $estado = 'R';
                
            endif;
            
        endif;
        
        if($model->estado == 'P'):
                
            if($estado == 'R'):
            
                $model->estado = $estado;
                $model->fecha_revision    = date('Ymd H:i:s');
               
                if($model->save(false)):
                                    
                    $area = SysAdmAreas::find()->where(['id_sys_adm_area' => $model->id_sys_adm_area])->one();

                    $mensaje = "<p  style = 'margin:1px;'>La solicitud de horas extras #".str_pad($model->id_sys_rrhh_soextras, 5, "0", STR_PAD_LEFT)." del Área: <b>".$area->area."</b><p>Ha sido revisada y lista para aprobar</p><p>Comentario: ".$model->comentario."</p>";
                
                    $to = [];
                    $cc = [];

                    $emailUser = $this->ObtenerUsuariosGruposAutorizacionAll($documento['id']);
                        
                    foreach ($emailUser as $user): 
                        array_push($to, $user['email']);
                    endforeach;
                    
                    $this->EnviarCorreo($to, $cc, $mensaje, $titulo, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, '', $estado);
                    
                    Yii::$app->getSession()->setFlash('info', [
                    'type' => 'success','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud ha sido revisada con éxito!',
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
                'icon' => 'glyphicons glyphicons-robot','message' => 'Usted no tiene permisos para revisar solicitud!',
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

    public function actionAnularsolicitud($id){
        
        $model         = SysRrhhSoextras::find()->where(['id_sys_rrhh_soextras'=> $id])->one();
        
        $estado        = 'P';
        
        $db            = $_SESSION['db'];
        
        $empresa       = SysEmpresa::find()->where(['db_name'=> $db])->one();
        
        $titulo        = 'Solicitud de Aprobación de Horas Extras';
        
        $tipousuario   = $this->getTipoUsuario(Yii::$app->user->identity->id);
        
        //Validar si el documento necesita autorizacion
        $documento = $this->getDocumento('SOLICITUD_HORAS_EXTRAS');
        
        if($documento):
                
            if($tipousuario == 'D' || $tipousuario == 'A'):
                
                $estado = 'N';
                
            endif;
            
        endif;
        
        if($model->estado == 'P'):
                
            if($estado == 'N'):
            
                $model->estado = $estado;
                $model->fecha_anulacion    = date('Ymd H:i:s');
                $model->usuario_anulacion  = Yii::$app->user->username;
                $model->comentario_anulacion = 'SOLICITUD ANULADA';
               
                if($model->save(false)):

                    Yii::$app->getSession()->setFlash('info', [
                    'type' => 'success','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud ha sido anulada con éxito!',
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
                'icon' => 'glyphicons glyphicons-robot','message' => 'Usted no tiene permisos para anular solicitud!',
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
        
        $model         = SysRrhhSoextras::find()->where(['id_sys_rrhh_soextras'=> $id])->one();
        
        $estado        = 'R';
        
        $db            = $_SESSION['db'];
        
        $empresa       = SysEmpresa::find()->where(['db_name'=> $db])->one();
        
        $titulo        = 'Solicitud de Aprobación de Horas Extras';
        
        $tipousuario   = $this->getTipoUsuario(Yii::$app->user->identity->id);
        
        //Validar si el documento necesita autorizacion
        $documento = $this->getDocumento('SOLICITUD_HORAS_EXTRAS');

        $transaction = \Yii::$app->$db->beginTransaction();
        
        if($documento):
        
            $autorizacion = $this->getAutorizacion($documento['id'], Yii::$app->user->identity->id);
            
            if($autorizacion):
                    
                if($tipousuario == 'G'):
                
                       $estado = 'A';
                
                endif;
            
            endif;

        endif;
        
        if($model->estado == 'R'):
                
            if($estado == 'A'):
            
                $model->estado     = $estado;
                $model->usuario_aprobacion  = $estado != 'P' ? Yii::$app->user->username : null;
                $model->fecha_aprobacion    = date('Ymd H:i:s');
               
                if($model->save(false)):

                    $datos= SysRrhhSoextrasEmpleados::find()
                    ->joinWith(['sysRrhhCedula'])
                    ->where(['sys_rrhh_horas_extras_solicitud_empleados.id_sys_rrhh_soextras'=> $id])
                    ->orderBy('nombres')
                    ->all();

                    if ($datos){
                        foreach ($datos as $data){

                            if($data->horas50 != 0 && $data->horas100 !=0){

                                $marcacion = SysRrhhMarcacionesEmpleados::find()->where(['id_sys_rrhh_cedula'=>$data->id_sys_rrhh_cedula])->andWhere(['fecha_laboral'=>$model->fecha_registro])->one();

                                if($marcacion):

                                    $newdata50 = 0;
                                    $newdata100 = 0;

                                    if($marcacion->pago50 == 1 && $marcacion->pago100 == 1):
                                        
                                        $newdatamarcacion = $this->ObtenerDatosMarcacion($data->id_sys_rrhh_cedula, $model->fecha_registro);
                                        $h50     = '00:00:00';
                                        $h100    = '00:00:00';
                                        $salidadesayuno   = '00:00:00';
                                        $salidaalmuerzo   = '00:00:00';
                                        $salidamerienda   = '00:00:00';
                                       
                                        if($newdatamarcacion != NULL){

                                            if($newdatamarcacion['salida_desayuno'] != NULL){
                                                $salidadesayuno  =  date('H:i:s', strtotime($newdatamarcacion['salida_desayuno']));
                                            }

                                            if($newdatamarcacion['salida_almuerzo'] != NULL){
                                                $salidaalmuerzo  =  date('H:i:s', strtotime($newdatamarcacion['salida_almuerzo']));
                                            }
                                            

                                            if($newdatamarcacion['salida_merienda'] != NULL){
                                                $salidamerienda  =  date('H:i:s', strtotime($newdatamarcacion['salida_merienda']));
                                            }
                                            
                                            
                                            $h50  = $this->getRendonminutos($this->gethoras50(date('Ymd H:i:s', strtotime($newdatamarcacion['entrada'])), date('Ymd H:i:s', strtotime($newdatamarcacion['salida'])), $salidadesayuno,$salidaalmuerzo,$salidamerienda, $data->id_sys_rrhh_cedula, $model->fecha_registro,$newdatamarcacion['feriado']));
                                            $h100  = $this->getRendonminutos($this->gethoras100(date('Ymd H:i:s', strtotime($newdatamarcacion['entrada'])), date('Ymd H:i:s', strtotime($newdatamarcacion['salida'])),$salidadesayuno,$salidaalmuerzo,$salidamerienda ,$data->id_sys_rrhh_cedula, $model->fecha_registro, $newdatamarcacion['feriado'], $newdatamarcacion['agendamiento']));
                                        
                                        }

                                        $newdata50 = floatval($data->horas50) + floatval($marcacion->horas50);
                                        $newdata100 = floatval($data->horas100) + floatval($marcacion->horas100);

                                        if($newdata50 > $this->HorasToDecimal($h50) || $newdata100 > $this->HorasToDecimal($h100)):

                                            $transaction->rollBack();
                                            Yii::$app->getSession()->setFlash('info', [
                                                'type' => 'danger','duration' => 1500,
                                                'icon' => 'glyphicons glyphicons-robot','message' => 'El número de horas(50 o 100) del colaborador '.$data->id_sys_rrhh_cedula.' a aprobar es mayor al número de horas realizadas!',
                                                'positonY' => 'top','positonX' => 'right']);
                                            return $this->redirect(['index']);
                                        
                                        else:
                                            
                                            $marcacion->horas50 = $newdata50;
                                            $marcacion->horas100 = $newdata100;
                                            $marcacion->pago50 = 1;
                                            $marcacion->pago100 = 1;
                                            $marcacion->user_apro50 = Yii::$app->user->username;
                                            $marcacion->user_apro100 = Yii::$app->user->username;
                                            $marcacion->valor_hora = $this->getValorHora($data->id_sys_rrhh_cedula);;
                                            
                                            $marcacion->save(false);
                                            //Yii::$app->$db->createCommand("UPDATE sys_rrhh_marcaciones_empleados set horas50= $data->horas50 ,horas100= $data->horas100 ,pago50=1 ,pago100=1 , user_apro50='".Yii::$app->user->username."',user_apro100='".Yii::$app->user->username."', valor_hora='{$valor_hora}' 
                                            //where id_sys_rrhh_cedula='{$data->id_sys_rrhh_cedula}' and fecha_laboral='{$model->fecha_registro}'")->execute();

                                        endif;
                                       
                                    else:

                                        
                                        $marcacion->horas50 = $data->horas50;
                                        $marcacion->horas100 = $data->horas100;
                                        $marcacion->pago50 = 1;
                                        $marcacion->pago100 = 1;
                                        $marcacion->user_apro50 = Yii::$app->user->username;
                                        $marcacion->user_apro100 = Yii::$app->user->username;
                                        $marcacion->valor_hora = $this->getValorHora($data->id_sys_rrhh_cedula);;
                                        
                                        $marcacion->save(false);

                                    endif;

                                else:

                                    $newmarcacion = new SysRrhhMarcacionesEmpleados();

                                    $newmarcacion->id_sys_empresa = '001';
                                    $newmarcacion->id_sys_rrhh_cedula = $data->id_sys_rrhh_cedula;
                                    $newmarcacion->fecha_laboral = $model->fecha_registro;
                                    $newmarcacion->horas50 = floatval($data->horas50);
                                    $newmarcacion->horas100 = floatval($data->horas100);
                                    $newmarcacion->pago50 = 1;
                                    $newmarcacion->pago100 = 1;
                                    $newmarcacion->user_apro50 = Yii::$app->user->username;
                                    $newmarcacion->user_apro100 = Yii::$app->user->username;
                                    $newmarcacion->valor_hora = $this->getValorHora($data->id_sys_rrhh_cedula);;

                                    $newmarcacion->save(false);

                                    /*Yii::$app->$db->createCommand("INSERT INTO sys_rrhh_marcaciones_empleados(id_sys_empresa,id_sys_rrhh_cedula,fecha_laboral,pago50,pago100,
                                    horas50,horas100,user_apro50,user_apro100,valor_hora)
                                    VALUES ('001','{$data->id_sys_rrhh_cedula}','{$model->fecha_registro}',1,1,$data->horas50,$data->horas100,'".Yii::$app->user->username."','".Yii::$app->user->username.",'$valor_hora') 
                                    ")->execute();*/

                                endif;
                                
                            }elseif($data->horas50 != 0){
                                
                                $marcacion = SysRrhhMarcacionesEmpleados::find()->where(['id_sys_rrhh_cedula'=>$data->id_sys_rrhh_cedula])->andWhere(['fecha_laboral'=>$model->fecha_registro])->one();

                                if($marcacion):

                                    $newdata = 0;

                                    if($marcacion->pago50 == 1):

                                        $newdatamarcacion = $this->ObtenerDatosMarcacion($data->id_sys_rrhh_cedula, $model->fecha_registro);
                                        $h50            = '00:00:00';
                                        $salidadesayuno   = '00:00:00';
                                        $salidaalmuerzo   = '00:00:00';
                                        $salidamerienda   = '00:00:00';
                                      
                                        if($newdatamarcacion != NULL){
                                            
                                            if($newdatamarcacion['salida_desayuno'] != NULL){
                                                $salidadesayuno  =  date('H:i:s', strtotime($newdatamarcacion['salida_desayuno']));
                                            }

                                            if($newdatamarcacion['salida_almuerzo'] != NULL){
                                                $salidaalmuerzo  =  date('H:i:s', strtotime($newdatamarcacion['salida_almuerzo']));
                                            }
                                            

                                            if($newdatamarcacion['salida_merienda'] != NULL){
                                                $salidamerienda  =  date('H:i:s', strtotime($newdatamarcacion['salida_merienda']));
                                            }

                                            $h50  = $this->getRendonminutos($this->gethoras50(date('Ymd H:i:s', strtotime($newdatamarcacion['entrada'])), date('Ymd H:i:s', strtotime($newdatamarcacion['salida'])), $salidadesayuno,$salidaalmuerzo,$salidamerienda,$data->id_sys_rrhh_cedula, $model->fecha_registro,$newdatamarcacion['feriado']));
                                        
                                        }
                                        
                                        $newdata = floatval($data->horas50) + floatval($marcacion->horas50);

                                        if($newdata > $this->HorasToDecimal($h50)):

                                            $transaction->rollBack();
                                            Yii::$app->getSession()->setFlash('info', [
                                                'type' => 'danger','duration' => 1500,
                                                'icon' => 'glyphicons glyphicons-robot','message' => 'El número de horas(50) del colaborador '.$data->id_sys_rrhh_cedula.' a aprobar es mayor al número de horas realizadas!',
                                                'positonY' => 'top','positonX' => 'right']);
                                            return $this->redirect(['index']);
                                        
                                        else:
                                            
                                            $marcacion->horas50 = $newdata;
                                            $marcacion->pago50 = 1;
                                            $marcacion->user_apro50 = Yii::$app->user->username;
                                            $marcacion->valor_hora = $this->getValorHora($data->id_sys_rrhh_cedula);;
                                            
                                            $marcacion->save(false);
                                            /*Yii::$app->$db->createCommand("UPDATE sys_rrhh_marcaciones_empleados set horas50= $data->horas50  , pago50=1, pago100=0, horas100 = 0, user_apro50='".Yii::$app->user->username."', valor_hora='{$valor_hora}' 
                                            where id_sys_rrhh_cedula='{$data->id_sys_rrhh_cedula}' and fecha_laboral='{$model->fecha_registro}'")->execute();*/

                                        endif;
                                       
                                    else:

                                        
                                        $marcacion->horas50 = floatval($data->horas50);
                                        $marcacion->pago50 = 1;
                                        $marcacion->user_apro50 = Yii::$app->user->username;
                                        $marcacion->valor_hora = $this->getValorHora($data->id_sys_rrhh_cedula);;
                                        
                                        $marcacion->save(false);

                                    endif;

                                    
                                
                                else:

                                    $newmarcacion = new SysRrhhMarcacionesEmpleados();

                                    $newmarcacion->id_sys_empresa = '001';
                                    $newmarcacion->id_sys_rrhh_cedula = $data->id_sys_rrhh_cedula;
                                    $newmarcacion->fecha_laboral = $model->fecha_registro;
                                    $newmarcacion->horas50 = floatval($data->horas50);
                                    $newmarcacion->horas100 = 0;
                                    $newmarcacion->pago50 = 1;
                                    $newmarcacion->pago100 = 0;
                                    $newmarcacion->user_apro50 = Yii::$app->user->username;
                                    $newmarcacion->valor_hora = $this->getValorHora($data->id_sys_rrhh_cedula);;

                                    $newmarcacion->save(false);

                                    /*Yii::$app->$db->createCommand("INSERT INTO sys_rrhh_marcaciones_empleados(id_sys_empresa,id_sys_rrhh_cedula,fecha_laboral,pago50,
                                    horas50,pago100,horas100,user_apro50,valor_hora)
                                    VALUES ('001','{$data->id_sys_rrhh_cedula}','{$model->fecha_registro}',1,$data->horas50,0,0,'".Yii::$app->user->username."','$valor_hora') 
                                    ")->execute();*/
                                
                                endif;

                            }else{

                                $marcacion = SysRrhhMarcacionesEmpleados::find()->where(['id_sys_rrhh_cedula'=>$data->id_sys_rrhh_cedula])->andWhere(['fecha_laboral'=>$model->fecha_registro])->one();

                                if($marcacion):

                                    $newdata = 0;

                                    if($marcacion->pago100 == 1):

                                        $newdatamarcacion = $this->ObtenerDatosMarcacion($data->id_sys_rrhh_cedula, $model->fecha_registro);
                                        $h100  = '00:00:00';
                                 
                                        $salidadesayuno   = '00:00:00';
                                        $salidaalmuerzo   = '00:00:00';
                                        $salidamerienda   = '00:00:00';

                                        if($newdatamarcacion != NULL){

                                            if($newdatamarcacion['salida_desayuno'] != NULL){
                                                $salidadesayuno  =  date('H:i:s', strtotime($newdatamarcacion['salida_desayuno']));
                                            }

                                            if($newdatamarcacion['salida_almuerzo'] != NULL){
                                                $salidaalmuerzo  =  date('H:i:s', strtotime($newdatamarcacion['salida_almuerzo']));
                                            }
                                            

                                            if($newdatamarcacion['salida_merienda'] != NULL){
                                                $salidamerienda  =  date('H:i:s', strtotime($newdatamarcacion['salida_merienda']));
                                            }
                                        
                                            $h100  = $this->getRendonminutos($this->gethoras100(date('Ymd H:i:s', strtotime($newdatamarcacion['entrada'])), date('Ymd H:i:s', strtotime($newdatamarcacion['salida'])),$salidadesayuno,$salidaalmuerzo,$salidamerienda,$data->id_sys_rrhh_cedula, $model->fecha_registro, $newdatamarcacion['feriado'], $newdatamarcacion['agendamiento']));
                                        
                                        }

                                        $newdata = floatval($data->horas100) + floatval($marcacion->horas100);

                                        if($newdata > $this->HorasToDecimal($h100)):

                                            $transaction->rollBack();
                                            Yii::$app->getSession()->setFlash('info', [
                                                'type' => 'danger','duration' => 1500,
                                                'icon' => 'glyphicons glyphicons-robot','message' => 'El número de horas(100) del colaborador '.$data->id_sys_rrhh_cedula.' a aprobar es mayor al número de horas realizadas!',
                                                'positonY' => 'top','positonX' => 'right']);
                                            return $this->redirect(['index']);
                                        
                                        else:

                                            $marcacion->horas100 = $newdata;
                                            $marcacion->pago100 = 1;
                                            $marcacion->user_apro100 = Yii::$app->user->username;
                                            $marcacion->valor_hora = $this->getValorHora($data->id_sys_rrhh_cedula);;
                                            
                                            $marcacion->save(false);
                                            /* Yii::$app->$db->createCommand("UPDATE sys_rrhh_marcaciones_empleados set horas100= $data->horas100  , pago100=1 , pago50=0, horas50 = 0,user_apro100='".Yii::$app->user->username."',valor_hora='{$valor_hora}' 
                                            where id_sys_rrhh_cedula='{$data->id_sys_rrhh_cedula}' and fecha_laboral='{$model->fecha_registro}'")->execute();*/
                                   
                                        endif;  

                                    else:

                                        $marcacion->horas100 = floatval($data->horas100);                              
                                        $marcacion->pago100 = 1;
                                        $marcacion->user_apro100 = Yii::$app->user->username;
                                        $marcacion->valor_hora = $this->getValorHora($data->id_sys_rrhh_cedula);;
                                        
                                        $marcacion->save(false);

                                    endif;
                            
                                   
                            
                                else:

                                    $newmarcacion = new SysRrhhMarcacionesEmpleados();

                                    $newmarcacion->id_sys_empresa = '001';
                                    $newmarcacion->id_sys_rrhh_cedula = $data->id_sys_rrhh_cedula;
                                    $newmarcacion->fecha_laboral = $model->fecha_registro;
                                    $newmarcacion->horas50 = 0;
                                    $newmarcacion->horas100 = floatval($data->horas100);
                                    $newmarcacion->pago50 = 0;
                                    $newmarcacion->pago100 = 1;
                                    $newmarcacion->user_apro100 = Yii::$app->user->username;
                                    $newmarcacion->valor_hora = $this->getValorHora($data->id_sys_rrhh_cedula);;

                                    $newmarcacion->save(false);

                                    /*Yii::$app->$db->createCommand("INSERT INTO sys_rrhh_marcaciones_empleados(id_sys_empresa,id_sys_rrhh_cedula,fecha_laboral,pago100,
                                    horas100,pago50,horas50,user_apro100,valor_hora)
                                    VALUES ('001','{$data->id_sys_rrhh_cedula}','{$model->fecha_registro}',1,$data->horas100,0,0,'".Yii::$app->user->username."','$valor_hora') 
                                    ")->execute();*/

                                endif;
                            }
                            
                        }
                    }else{
                        array_push($modeldet, new SysRrhhSoextrasEmpleados());
                    }
                    
                
                    $area = SysAdmAreas::find()->where(['id_sys_adm_area' => $model->id_sys_adm_area])->one();

                   
                    $mensaje = "<p  style = 'margin:1px;'>La solicitud de horas extras #".str_pad($model->id_sys_rrhh_soextras, 5, "0", STR_PAD_LEFT)." del Área: <b>".$area->area."</b><p>Ha sido aprobada con éxtio</p><p>Comentario: ".$model->comentario."</p>";
                
               
                    $to = [];
                    $cc = [];

                    $emailUser = $this->ObtenerUsuariosGruposAutorizacionAll($documento['id']);
                        
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
                'icon' => 'glyphicons glyphicons-robot','message' => 'Usted no tiene permisos para aprobar solicitud de horas extras!',
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

    public function actionListar($id)
    {

        ini_set("pcre.backtrack_limit", "5000000");

        $model = $this->findModel($id);
        
        $db    = $_SESSION['db'];
       
        $modeldet = [];        
        
        $datos= SysRrhhSoextrasEmpleados::find()
        ->joinWith(['sysRrhhCedula'])
        ->where(['sys_rrhh_horas_extras_solicitud_empleados.id_sys_rrhh_soextras'=> $id])
        ->orderBy('nombres')
        ->all();

        if ($datos){
            foreach ($datos as $data){
                $obj                                   = new SysRrhhSoextrasEmpleados();
                $emp                                   = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $data->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> $data->id_sys_empresa])->one();
                $obj->id_sys_rrhh_soextras_empleados   = $data->id_sys_rrhh_soextras_empleados;
                $obj->id_sys_rrhh_cedula               = $data->id_sys_rrhh_cedula;
                $obj->id_sys_empresa                   = $emp->nombres;
                $obj->horas50                          = $this->DecimaltoHoras($data->horas50);
                $obj->horas100                         = $this->DecimaltoHoras($data->horas100);
                $obj->pago50                           = number_format($data->pago50, 2,'.', '.');
                $obj->pago100                          = number_format($data->pago100, 2,'.', '.');
                array_push($modeldet, $obj);
            }
        }else{
            array_push($modeldet, new SysRrhhSoextrasEmpleados());
        }

   
        $ingreso = $this->actionEmpleadosXArea($model->id_sys_adm_area,$model->fecha_registro);
        return $this->render('solicitud', [
            'model'   => $model,
            'modeldet' => $modeldet,
            'ingreso' => $ingreso,
            'update'=> 1,
        ]);
        
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

    private function ObtenerUsuariosGruposAutorizacionAll($id_sys_documento){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerUsuariosGruposAutorizacionAll]  @id_sys_documento = {$id_sys_documento}")->queryAll();
    }

    private function getEmailCreacion($username){
        
        $user  = User::find()->where(['username'=> $username])->one();
        return  $user != null ? trim($user->email) : "";
        
    }

    public function actionAprobarsolicitudes(){

        ini_set("pcre.backtrack_limit", "50000000");

        $this->layout = '@app/views/layouts/main_emplados';
        
        $datos         =  [];
        $fechaini      = date('Y-m-d');
        $fechafin      = date('Y-m-d');
        $area          = '';
        $departamento  = '';
        $semana        = date('W');
        
        if(Yii::$app->request->post()){
            
            $fechaini         =  $_POST['fechaini']== null ?  '' : $_POST['fechaini'];
            $area             =  $_POST['area']== null ? '': $_POST['area'];
            $departamento     =  $_POST['departamento']== null ? '': $_POST['departamento'];
            $fechafin         =  $_POST['fechafin']== null ?  '' : $_POST['fechafin'];


            $datos = $this->obtenerSolicitudesPendientes($fechaini,$fechafin,$area,$departamento);
        
        }

        return $this->render('aprosolicitud', ['fechaini'=> $fechaini,'fechafin'=> $fechafin, 'area'=> $area,'departamento' =>$departamento,'datos' => $datos]);
        
        
    }

    public function actionRevisarsolicitudes(){

        ini_set("pcre.backtrack_limit", "50000000");

        $this->layout = '@app/views/layouts/main_emplados';
        
        $datos         =  [];
        $fechaini      = date('Y-m-d');
        $fechafin      = date('Y-m-d');
        $area          = '';
        $semana        = date('W');
        
        if(Yii::$app->request->post()){
            
            $fechaini         =  $_POST['fechaini']== null ?  '' : $_POST['fechaini'];
            $area             =  $_POST['area']== null ? '': $_POST['area'];
            $fechafin         =  $_POST['fechafin']== null ?  '' : $_POST['fechafin'];


            $datos = $this->obtenerSolicitudesPorRevisar($fechaini,$fechafin,$area);
        
        }

        return $this->render('revisionsolicitud', ['fechaini'=> $fechaini,'fechafin'=> $fechafin, 'area'=> $area,'datos' => $datos]);
        
        
    }


    public function actionVerhorasacumuladas(){

        $this->layout = '@app/views/layouts/main_emplados';
        
        $datos         =  [];
        $fechaini      = date('Y-m-d');
        $fechafin      = date('Y-m-d');
        $area          = '';
        
        if(Yii::$app->request->post()){
            
            $fechaini         =  $_POST['fechaini']== null ?  '' : $_POST['fechaini'];
            $area             =  $_POST['area']== null ? '': $_POST['area'];
            $fechafin         =  $_POST['fechafin']== null ?  '' : $_POST['fechafin'];


            $datos = $this->obtenerDatoSolicitudes($fechaini,$fechafin,$area);
        
        }

        return $this->render('informehoras', ['fechaini'=> $fechaini,'fechafin'=> $fechafin, 'area'=> $area,'datos' => $datos]);
        
        
    }

    public function actionVerhorasacumuladasporsolicitud(){

        $this->layout = '@app/views/layouts/main_emplados';
        
        $datosSolicitud         =  [];
        $fechaini      = date('Y-m-d');
        $fechafin      = date('Y-m-d');
        $area          = '';
        $departamento  = '';
        
        if(Yii::$app->request->post()){
            
            $fechaini         =  $_POST['fechaini']== null ?  '' : $_POST['fechaini'];
            $area             =  $_POST['area']== null ? '': $_POST['area'];
            $departamento     = $_POST['departamento']== 0 ? '': $_POST['departamento'];
            $fechafin         =  $_POST['fechafin']== null ?  '' : $_POST['fechafin'];


            $datosSolicitud = $this->getAsistenciaLaboralResumen($fechaini,$fechafin,$area,$departamento);
        
        }

        return $this->render('informehorasolicitud', ['fechaini'=> $fechaini,'fechafin'=> $fechafin, 'area'=> $area,'departamento' => $departamento,'datosSolicitud' => $datosSolicitud]);
        
        
    }

    public function actionVerhorasgeneradasvshorassolicitadas(){

        $this->layout = '@app/views/layouts/main_emplados';
        ini_set("pcre.backtrack_limit", "100000000");
        
        $datos         =  [];
        $fechaini      = date('Y-m-d');
        $fechafin      = date('Y-m-d');
        $area          = '';
        $departamento  = '';
        $tipo          = 'R';
        
        if(Yii::$app->request->post()){
            
            $fechaini         =  $_POST['fechaini']== null ?  '' : $_POST['fechaini'];
            $area             =  $_POST['area']== null ? '': $_POST['area'];
            $fechafin         =  $_POST['fechafin']== null ?  '' : $_POST['fechafin'];
            $departamento     =  $_POST['departamento']== null ? '': $_POST['departamento'];
            $tipo             =  $_POST['tipo'];


            $datos = $this->obtenerDatosMarcacionySolicitudes($fechaini,$fechafin,$area,$departamento);
        
        }

        return $this->render('informecomparacionhoras', ['fechaini'=> $fechaini,'fechafin'=> $fechafin, 'area'=> $area,'departamento'=> $departamento,'datos' => $datos,'tipo' =>$tipo]);
        
        
    }

    private  function obtenerDatosMarcacionySolicitudes($fecha_ini, $fecha_fin, $id_sys_adm_area, $id_sys_adm_departamento){
       
        ini_set("pcre.backtrack_limit", "50000000");

        $db =  $_SESSION['db'];
        
        if (($id_sys_adm_area != null) && ( $id_sys_adm_departamento == null)) :
        
            return  Yii::$app->$db->createCommand("exec [dbo].[ObtenerAsistenciaLaboralEmpleadosHoras] @fecha_ini = '{$fecha_ini}', @fecha_fin = '{$fecha_fin}', @id_sys_adm_area = {$id_sys_adm_area}")->queryAll();
       
        elseif (($id_sys_adm_area != null) && ( $id_sys_adm_departamento != null)):
        
                return  Yii::$app->$db->createCommand("exec [dbo].[ObtenerAsistenciaLaboralEmpleadosHoras] @fecha_ini = '{$fecha_ini}', @fecha_fin = '{$fecha_fin}', @id_sys_adm_area = {$id_sys_adm_area}, @id_sys_adm_departamento = {$id_sys_adm_departamento}")->queryAll();
        else:
        
                return  Yii::$app->$db->createCommand("exec [dbo].[ObtenerAsistenciaLaboralEmpleadosHoras] @fecha_ini = '{$fecha_ini}', @fecha_fin = '{$fecha_fin}'")->queryAll();
        
        endif;
       
    }

    public function actionInformecomparacionhorasxls($fechaini,$fechafin,$area,$departamento,$tipo){

        ini_set("pcre.backtrack_limit", "50000000");

        $datos = $this->obtenerDatosMarcacionySolicitudes($fechaini,$fechafin,$area,$departamento);
         
        return $this->render('informecomparacionhorasxls', ['datos'=> $datos,'fechaini'=>$fechaini,'fechafin'=>$fechafin,'area'=>$area,'departamento'=>$departamento,'tipo'=>$tipo]);
    }

    public  function actionInformecomparacionhoraspdf($fechaini,$fechafin, $area, $departamento, $tipo){
        
        ini_set("pcre.backtrack_limit", "50000000");

        $datos = $this->obtenerDatosMarcacionySolicitudes($fechaini,$fechafin,$area,$departamento);
        
        $html =  $this->renderPartial('informecomparacionhoraspdf',['datos'=> $datos, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'area'=> $area, 'departamento'=> $departamento, 'tipo'=> $tipo]);
          
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
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}  .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
            	border:0;
            	padding:0;
            	margin-left:-0.00001;
            }
            th, td {padding: 5px;} .fuente_table {font-size: 8px;}',
            
            // set mPDF properties on the fly
            'options' => ['title' => ''],
            // call mPDF methods on the fly
            'methods' => [
                'SetHeader'=>['Sistema Gestión de Nómina - Reporte Horas Generadas VS Horas Solicitadas||'],
                'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}'],
            ]
        ]);
        
        // return the pdf output as per the destination setting
        return $pdf->render(); 
    }
      
    function getFirstDayWeek($week, $year)
    {
        $dt = new \DateTime();
        $return['start'] = $dt->setISODate($year, $week)->format('Y-m-d');
        $return['end'] = $dt->modify('+6 days')->format('Y-m-d');
        return $return;
    }

    private function obtenerSolicitudesPendientes($fechaini,$fechafin,$area,$departamento){
        
        $db =  $_SESSION['db'];

       if($fechaini != NULL && $fechafin != NULL && $area != NULL && $departamento != NULL):
        
            return Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerSolicitudHorasExtrasPorSemanaFechayAreaXDepartamento]  @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @area = '{$area}', @tipo='R', @departamento='{$departamento}'")->queryAll(); 

        elseif($fechaini != NULL && $fechafin != NULL && $area != NULL):
        
            return Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerSolicitudHorasExtrasPorSemanaFechayArea]  @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @area = '{$area}', @tipo='R'")->queryAll();    

        elseif($fechaini != NULL && $fechafin != NULL):
        
            return Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerSolicitudHorasExtrasPorSemanaFecha]  @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @tipo='R'")->queryAll(); 
        
        endif;    

    }

    private function obtenerSolicitudesPorRevisar($fechaini,$fechafin,$area){
        
        $db =  $_SESSION['db'];

       if($fechaini != NULL && $fechafin != NULL && $area != NULL):
        
            return Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerSolicitudHorasExtrasPorSemanaFechayArea]  @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @area = '{$area}', @tipo='P'")->queryAll(); 

        elseif($fechaini != NULL && $fechafin != NULL):
        
            return Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerSolicitudHorasExtrasPorSemanaFecha]  @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @tipo='P'")->queryAll(); 
        
        endif;    

    }

    private function obtenerSolicitudesHoras($fechaini,$fechafin,$area){
        
        $db =  $_SESSION['db'];

       if($fechaini != NULL && $fechafin != NULL && $area != NULL):
        
            return Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerSolicitudHorasExtrasPorSemanaFechayAreaGeneral]  @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @area = '{$area}'")->queryAll(); 

        elseif($fechaini != NULL && $fechafin != NULL):
        
            return Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerSolicitudHorasExtrasPorSemanaFechaGeneral]  @fechaini = '{$fechaini}', @fechafin = '{$fechafin}'")->queryAll(); 
        
        endif;    

    }

    private function obtenerDatoSolicitudes($fechaini,$fechafin,$area){
        
        $db =  $_SESSION['db'];

       if($fechaini != NULL && $fechafin != NULL && $area != NULL):
        
            return Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDatosSolicitudHorasExtrasPorSemanaFechayArea]  @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @area = '{$area}'")->queryAll(); 

        elseif($fechaini != NULL && $fechafin != NULL):
        
            return Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDatosSolicitudHorasExtrasPorSemanaFecha]  @fechaini = '{$fechaini}', @fechafin = '{$fechafin}'")->queryAll(); 
        
        endif;    

    }

    private  function getAsistenciaLaboralResumen($fecha_ini, $fecha_fin, $id_sys_adm_area, $id_sys_adm_departamento){
       
        $db =  $_SESSION['db'];
        
        if (($id_sys_adm_area != null) && ( $id_sys_adm_departamento == null)) :
        
            return  Yii::$app->$db->createCommand("exec dbo.ObtenerAsistenciaLaboralEmpleados @fecha_ini = '{$fecha_ini}', @fecha_fin = '{$fecha_fin}', @id_sys_adm_area = {$id_sys_adm_area}")->queryAll();
       
        elseif (($id_sys_adm_area != null) && ( $id_sys_adm_departamento != null)):
        
                return  Yii::$app->$db->createCommand("exec dbo.ObtenerAsistenciaLaboralEmpleados @fecha_ini = '{$fecha_ini}', @fecha_fin = '{$fecha_fin}', @id_sys_adm_area = {$id_sys_adm_area}, @id_sys_adm_departamento = {$id_sys_adm_departamento}")->queryAll();
        else:
        
                return  Yii::$app->$db->createCommand("exec dbo.ObtenerAsistenciaLaboralEmpleados @fecha_ini = '{$fecha_ini}', @fecha_fin = '{$fecha_fin}'")->queryAll();
        
        endif;
       
    }

    public function actionAprobarsolicitudmasiva(){
        
        
        if (Yii::$app->request->isAjax && Yii::$app->request->post()) {
            
                   $obj         =  json_decode(Yii::$app->request->post('datos'));
                   $solicitudes   =  $obj->{'solicitudes'};
                   $error       =  [];  
                   $error1      =  [];
                   $error2      =  [];
                   $error4      =  [];
                   
                foreach($solicitudes as $data){
                    
                    $model = SysRrhhSoextras::find()->where(['id_sys_rrhh_soextras'=> $data->id_sys_rrhh_soextras])->one();

                    $estado        = 'R';
        
                    $db            = $_SESSION['db'];
                    
                    $empresa       = SysEmpresa::find()->where(['db_name'=> $db])->one();
                    
                    $titulo        = 'Solicitud de Aprobación de Horas Extras';
                    
                    $tipousuario   = $this->getTipoUsuario(Yii::$app->user->identity->id);
                    
                    //Validar si el documento necesita autorizacion
                    $documento = $this->getDocumento('SOLICITUD_HORAS_EXTRAS');

                    $transaction = \Yii::$app->$db->beginTransaction();
                    
                    if($documento):
                    
                        $autorizacion = $this->getAutorizacion($documento['id'], Yii::$app->user->identity->id);
                        
                        if($autorizacion):
                                
                            if($tipousuario == 'G'):
                            
                                $estado = 'A';
                            
                            endif;
                        
                        endif;

                    endif;
                    
                    if($model->estado == 'R'):
                            
                        if($estado == 'A'):
                        
                            $model->estado     = $estado;
                            $model->usuario_aprobacion  = $estado != 'P' ? Yii::$app->user->username : null;
                            $model->fecha_aprobacion    = date('Ymd H:i:s');
                        
                            if($model->save(false)):

                                $datos= SysRrhhSoextrasEmpleados::find()
                                ->joinWith(['sysRrhhCedula'])
                                ->where(['sys_rrhh_horas_extras_solicitud_empleados.id_sys_rrhh_soextras'=> $data->id_sys_rrhh_soextras])
                                ->orderBy('nombres')
                                ->all();

                                if ($datos){

                                    foreach ($datos as $data){

                                        if($data->horas50 != 0 && $data->horas100 !=0){

                                            $marcacion = SysRrhhMarcacionesEmpleados::find()->where(['id_sys_rrhh_cedula'=>$data->id_sys_rrhh_cedula])->andWhere(['fecha_laboral'=>$model->fecha_registro])->one();
            
                                            if($marcacion):
            
                                                $newdata50 = 0;
                                                $newdata100 = 0;

                                                if($marcacion->pago50 == 1 && $marcacion->pago100 == 1):

                                                    $newdatamarcacion = $this->ObtenerDatosMarcacion($data->id_sys_rrhh_cedula, $model->fecha_registro);
                                                    $h50     = '00:00:00';
                                                    $h100    = '00:00:00';
                                                    $salidadesayuno   = '00:00:00';
                                                    $salidaalmuerzo   = '00:00:00';
                                                    $salidamerienda   = '00:00:00';
            
                                                    if($newdatamarcacion != NULL){

                                                        if($newdatamarcacion['salida_desayuno'] != NULL){
                                                            $salidadesayuno  =  date('H:i:s', strtotime($newdatamarcacion['salida_desayuno']));
                                                        }
            
                                                        if($newdatamarcacion['salida_almuerzo'] != NULL){
                                                            $salidaalmuerzo  =  date('H:i:s', strtotime($newdatamarcacion['salida_almuerzo']));
                                                        }
                                                        
            
                                                        if($newdatamarcacion['salida_merienda'] != NULL){
                                                            $salidamerienda  =  date('H:i:s', strtotime($newdatamarcacion['salida_merienda']));
                                                        }
                                            
                                                        $h50  = $this->getRendonminutos($this->gethoras50(date('Ymd H:i:s', strtotime($newdatamarcacion['entrada'])), date('Ymd H:i:s', strtotime($newdatamarcacion['salida'])), $salidadesayuno,$salidaalmuerzo,$salidamerienda,$data->id_sys_rrhh_cedula, $model->fecha_registro,$newdatamarcacion['feriado']));
                                                        $h100  = $this->getRendonminutos($this->gethoras100(date('Ymd H:i:s', strtotime($newdatamarcacion['entrada'])), date('Ymd H:i:s', strtotime($newdatamarcacion['salida'])),$salidadesayuno,$salidaalmuerzo,$salidamerienda,$data->id_sys_rrhh_cedula, $model->fecha_registro, $newdatamarcacion['feriado'], $newdatamarcacion['agendamiento']));
                                                    
                                                    }

                                                    $newdata50 = floatval($data->horas50) + floatval($marcacion->horas50);
                                                    $newdata100 = floatval($data->horas100) + floatval($marcacion->horas100);

                                                    if($newdata50 > $this->HorasToDecimal($h50) || $newdata100 > $this->HorasToDecimal($h100)):

                                                        $transaction->rollBack();
                                                        $error4 [] = array('Mensaje'=>'El número de horas(50 o 100) del colaborador '.$data->id_sys_rrhh_cedula.' a aprobar es mayor al número de horas realizadas!');

                                                        return json_encode(['data'=> ['estado'=>  false , 'mjs'=> json_encode($error4).'. No se pudo aprobar la solicitud #'.$data->id_sys_rrhh_soextras]]);
                                                    
                                                    else:

                                                        $marcacion->horas50 = $newdata50;
                                                        $marcacion->horas100 = $newdata100;
                                                        $marcacion->pago50 = 1;
                                                        $marcacion->pago100 = 1;
                                                        $marcacion->user_apro50 = Yii::$app->user->username;
                                                        $marcacion->user_apro100 = Yii::$app->user->username;
                                                        $marcacion->valor_hora = $this->getValorHora($data->id_sys_rrhh_cedula);
                                                        
                                                        $marcacion->save(false);
                                                        //Yii::$app->$db->createCommand("UPDATE sys_rrhh_marcaciones_empleados set horas50= $data->horas50 ,horas100= $data->horas100 ,pago50=1 ,pago100=1 , user_apro50='".Yii::$app->user->username."',user_apro100='".Yii::$app->user->username."', valor_hora='{$valor_hora}' 
                                                        //where id_sys_rrhh_cedula='{$data->id_sys_rrhh_cedula}' and fecha_laboral='{$model->fecha_registro}'")->execute();
                                                    
                                                    endif;

                                                else:

                                                    
                                                    $marcacion->horas50 = $data->horas50;
                                                    $marcacion->horas100 = $data->horas100;
                                                    $marcacion->pago50 = 1;
                                                    $marcacion->pago100 = 1;
                                                    $marcacion->user_apro50 = Yii::$app->user->username;
                                                    $marcacion->user_apro100 = Yii::$app->user->username;
                                                    $marcacion->valor_hora = $this->getValorHora($data->id_sys_rrhh_cedula);
                                                    
                                                    $marcacion->save(false);

                                                endif;
            
                                            else:
            
                                                $newmarcacion = new SysRrhhMarcacionesEmpleados();

                                                $newmarcacion->id_sys_empresa = '001';
                                                $newmarcacion->id_sys_rrhh_cedula = $data->id_sys_rrhh_cedula;
                                                $newmarcacion->fecha_laboral = $model->fecha_registro;
                                                $newmarcacion->horas50 = floatval($data->horas50);
                                                $newmarcacion->horas100 = floatval($data->horas100);
                                                $newmarcacion->pago50 = 1;
                                                $newmarcacion->pago100 = 1;
                                                $newmarcacion->user_apro50 = Yii::$app->user->username;
                                                $newmarcacion->user_apro100 = Yii::$app->user->username;
                                                $newmarcacion->valor_hora = $this->getValorHora($data->id_sys_rrhh_cedula);

                                                $newmarcacion->save(false);

                                                /*Yii::$app->$db->createCommand("INSERT INTO sys_rrhh_marcaciones_empleados(id_sys_empresa,id_sys_rrhh_cedula,fecha_laboral,pago50,pago100,
                                                horas50,horas100,user_apro50,user_apro100,valor_hora)
                                                VALUES ('001','{$data->id_sys_rrhh_cedula}','{$model->fecha_registro}',1,1,$data->horas50,$data->horas100,'".Yii::$app->user->username."','".Yii::$app->user->username.",'$valor_hora') 
                                                ")->execute();*/
                        
                                            endif;
                                            
                                        }elseif($data->horas50 != 0){
                                            
                                            $marcacion = SysRrhhMarcacionesEmpleados::find()->where(['id_sys_rrhh_cedula'=>$data->id_sys_rrhh_cedula])->andWhere(['fecha_laboral'=>$model->fecha_registro])->one();
            
                                            if($marcacion):
            
                                                $newdata = 0;

                                                if($marcacion->pago50 == 1):
            
                                                    $newdatamarcacion = $this->ObtenerDatosMarcacion($data->id_sys_rrhh_cedula, $model->fecha_registro);
                                                    $h50            = '00:00:00';
                                                    $feriado = NULL;
                                                    $salidadesayuno   = '00:00:00';
                                                    $salidaalmuerzo   = '00:00:00';
                                                    $salidamerienda   = '00:00:00';
            
                                                    if($newdatamarcacion != NULL){

                                                        if($newdatamarcacion['salida_desayuno'] != NULL){
                                                            $salidadesayuno  =  date('H:i:s', strtotime($newdatamarcacion['salida_desayuno']));
                                                        }
            
                                                        if($newdatamarcacion['salida_almuerzo'] != NULL){
                                                            $salidaalmuerzo  =  date('H:i:s', strtotime($newdatamarcacion['salida_almuerzo']));
                                                        }
                                                        
            
                                                        if($newdatamarcacion['salida_merienda'] != NULL){
                                                            $salidamerienda  =  date('H:i:s', strtotime($newdatamarcacion['salida_merienda']));
                                                        }
                                            
                                                        $h50  = $this->getRendonminutos($this->gethoras50(date('Ymd H:i:s', strtotime($newdatamarcacion['entrada'])), date('Ymd H:i:s', strtotime($newdatamarcacion['salida'])),$salidadesayuno,$salidaalmuerzo,$salidamerienda ,$data->id_sys_rrhh_cedula, $model->fecha_registro,$newdatamarcacion['feriado']));
                                                    
                                                    }
                                                    
                                                    $newdata = floatval($data->horas50) + floatval($marcacion->horas50);
            
                                                    if($newdata > $this->HorasToDecimal($h50)):
            
                                                        $transaction->rollBack();
                                                        $error4 [] = array('Mensaje'=>'El numero de horas(50) del colaborador '.$data->id_sys_rrhh_cedula.' a aprobar es mayor al numero de horas realizadas!');

                                                        return json_encode(['data'=> ['estado'=>  false , 'mjs'=> json_encode($error4).'. No se pudo aprobar la solicitud #'.$data->id_sys_rrhh_soextras]]);
                                                    
                                                    else:
                                                        
                                                        $marcacion->horas50 = $newdata;
                                                        $marcacion->pago50 = 1;
                                                        $marcacion->user_apro50 = Yii::$app->user->username;
                                                        $marcacion->valor_hora = $this->getValorHora($data->id_sys_rrhh_cedula);
                                                        
                                                        $marcacion->save(false);
                                                        /*Yii::$app->$db->createCommand("UPDATE sys_rrhh_marcaciones_empleados set horas50= $data->horas50  , pago50=1, pago100=0, horas100 = 0, user_apro50='".Yii::$app->user->username."', valor_hora='{$valor_hora}' 
                                                        where id_sys_rrhh_cedula='{$data->id_sys_rrhh_cedula}' and fecha_laboral='{$model->fecha_registro}'")->execute();*/
            
                                                    endif;
                                                   
                                                else:
            
                                                    
                                                    $marcacion->horas50 = floatval($data->horas50);
                                                    $marcacion->pago50 = 1;
                                                    $marcacion->user_apro50 = Yii::$app->user->username;
                                                    $marcacion->valor_hora = $this->getValorHora($data->id_sys_rrhh_cedula);
                                                    
                                                    $marcacion->save(false);
            
                                                endif;
            
                                                
                                            
                                            else:
            
                                                $newmarcacion = new SysRrhhMarcacionesEmpleados();
            
                                                $newmarcacion->id_sys_empresa = '001';
                                                $newmarcacion->id_sys_rrhh_cedula = $data->id_sys_rrhh_cedula;
                                                $newmarcacion->fecha_laboral = $model->fecha_registro;
                                                $newmarcacion->horas50 = floatval($data->horas50);
                                                $newmarcacion->horas100 = 0;
                                                $newmarcacion->pago50 = 1;
                                                $newmarcacion->pago100 = 0;
                                                $newmarcacion->user_apro50 = Yii::$app->user->username;
                                                $newmarcacion->valor_hora = $this->getValorHora($data->id_sys_rrhh_cedula);
            
                                                $newmarcacion->save(false);
            
                                                /*Yii::$app->$db->createCommand("INSERT INTO sys_rrhh_marcaciones_empleados(id_sys_empresa,id_sys_rrhh_cedula,fecha_laboral,pago50,
                                                horas50,pago100,horas100,user_apro50,valor_hora)
                                                VALUES ('001','{$data->id_sys_rrhh_cedula}','{$model->fecha_registro}',1,$data->horas50,0,0,'".Yii::$app->user->username."','$valor_hora') 
                                                ")->execute();*/
                                            
                                            endif;
            
            
                                        }else{
            
                                            $marcacion = SysRrhhMarcacionesEmpleados::find()->where(['id_sys_rrhh_cedula'=>$data->id_sys_rrhh_cedula])->andWhere(['fecha_laboral'=>$model->fecha_registro])->one();
            
                                            if($marcacion):
                                        
                                                $newdata = 0;

                                                if($marcacion->pago100 == 1):

                                                    $newdatamarcacion = $this->ObtenerDatosMarcacion($data->id_sys_rrhh_cedula, $model->fecha_registro);
                                                    $h100  = '00:00:00';
                                                    $salidadesayuno   = '00:00:00';
                                                    $salidaalmuerzo   = '00:00:00';
                                                    $salidamerienda   = '00:00:00';

                                                    if($newdatamarcacion != NULL){

                                                        if($newdatamarcacion['salida_desayuno'] != NULL){
                                                            $salidadesayuno  =  date('H:i:s', strtotime($newdatamarcacion['salida_desayuno']));
                                                        }
            
                                                        if($newdatamarcacion['salida_almuerzo'] != NULL){
                                                            $salidaalmuerzo  =  date('H:i:s', strtotime($newdatamarcacion['salida_almuerzo']));
                                                        }
                                                        
            
                                                        if($newdatamarcacion['salida_merienda'] != NULL){
                                                            $salidamerienda  =  date('H:i:s', strtotime($newdatamarcacion['salida_merienda']));
                                                        }

                                                        $h100  = $this->getRendonminutos($this->gethoras100(date('Ymd H:i:s', strtotime($newdatamarcacion['entrada'])), date('Ymd H:i:s', strtotime($newdatamarcacion['salida'])),$salidadesayuno,$salidaalmuerzo,$salidamerienda,$data->id_sys_rrhh_cedula, $model->fecha_registro, $newdatamarcacion['feriado'], $newdatamarcacion['agendamiento']));
                                                    
                                                    }

                                                    $newdata = floatval($data->horas100) + floatval($marcacion->horas100);

                                                    if($newdata > $this->HorasToDecimal($h100)):

                                                        $transaction->rollBack();
                                                        $error4 [] = array('Mensaje'=>'El numero de horas(100) del colaborador '.$data->id_sys_rrhh_cedula.' a aprobar es mayor al numero de horas realizadas!');

                                                        return json_encode(['data'=> ['estado'=>  false , 'mjs'=> json_encode($error4).'. No se pudo aprobar la solicitud #'.$data->id_sys_rrhh_soextras]]);
                                                    
                                                    else:

                                                        $marcacion->horas100 = $newdata;
                                                        $marcacion->pago100 = 1;
                                                        $marcacion->user_apro100 = Yii::$app->user->username;
                                                        $marcacion->valor_hora = $this->getValorHora($data->id_sys_rrhh_cedula);
                                                        
                                                        $marcacion->save(false);
                                                        /* Yii::$app->$db->createCommand("UPDATE sys_rrhh_marcaciones_empleados set horas100= $data->horas100  , pago100=1 , pago50=0, horas50 = 0,user_apro100='".Yii::$app->user->username."',valor_hora='{$valor_hora}' 
                                                        where id_sys_rrhh_cedula='{$data->id_sys_rrhh_cedula}' and fecha_laboral='{$model->fecha_registro}'")->execute();*/
                                            
                                                    endif;  

                                                else:

                                                    $marcacion->horas100 = floatval($data->horas100);                              
                                                    $marcacion->pago100 = 1;
                                                    $marcacion->user_apro100 = Yii::$app->user->username;
                                                    $marcacion->valor_hora = $this->getValorHora($data->id_sys_rrhh_cedula);
                                                    
                                                    $marcacion->save(false);

                                                endif;
                                        
                                            
                                        
                                            else:

                                                $newmarcacion = new SysRrhhMarcacionesEmpleados();

                                                $newmarcacion->id_sys_empresa = '001';
                                                $newmarcacion->id_sys_rrhh_cedula = $data->id_sys_rrhh_cedula;
                                                $newmarcacion->fecha_laboral = $model->fecha_registro;
                                                $newmarcacion->horas50 = 0;
                                                $newmarcacion->horas100 = floatval($data->horas100);
                                                $newmarcacion->pago50 = 0;
                                                $newmarcacion->pago100 = 1;
                                                $newmarcacion->user_apro100 = Yii::$app->user->username;
                                                $newmarcacion->valor_hora = $this->getValorHora($data->id_sys_rrhh_cedula);

                                                $newmarcacion->save(false);

                                                /*Yii::$app->$db->createCommand("INSERT INTO sys_rrhh_marcaciones_empleados(id_sys_empresa,id_sys_rrhh_cedula,fecha_laboral,pago100,
                                                horas100,pago50,horas50,user_apro100,valor_hora)
                                                VALUES ('001','{$data->id_sys_rrhh_cedula}','{$model->fecha_registro}',1,$data->horas100,0,0,'".Yii::$app->user->username."','$valor_hora') 
                                                ")->execute();*/

                                            endif;
                                        }
                                        
                                    }
                                }else{
                                    array_push($modeldet, new SysRrhhSoextrasEmpleados());
                                }
                                
                            
                                $area = SysAdmAreas::find()->where(['id_sys_adm_area' => $model->id_sys_adm_area])->one();

                            
                                $mensaje = "<p  style = 'margin:1px;'>La solicitud de horas extras #".str_pad($model->id_sys_rrhh_soextras, 5, "0", STR_PAD_LEFT)." del Área: <b>".$area->area."</b><p>Ha sido aprobada con éxtio</p><p>Comentario: ".$model->comentario."</p>";
                            
                        
                                $to = [];
                                $cc = [];

                                $emailUser = $this->ObtenerUsuariosGruposAutorizacionAll($documento['id']);
                                    
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
                            else:

                                $error [] = array('Solicitud' => $data->id_sys_rrhh_soextras );

                            endif;

                        else:

                            $error1 [] = array('Solicitud' => $data->id_sys_rrhh_soextras );

                        endif;
                    
                    else:

                        $error2 [] = array('Solicitud' => $data->id_sys_rrhh_soextras );

                    endif;
                }

                if(count($error) > 0) :
                   
                    return json_encode(['data'=> ['estado'=>  false , 'mjs'=> 'No se puedo aprobar la/las solicitud/solicitudes'.json_encode($error).' comuniquese con su administrador!']]);
              
                endif;

                if(count($error1) > 0) :
                   
                    return json_encode(['data'=> ['estado'=>  false , 'mjs'=> 'No se puedo aprobar la/las solicitud/solicitudes'.json_encode($error1).' por que usted no tiene permisos de aprobación']]);
              
                endif;

                if(count($error2) > 0) :
                   
                    return json_encode(['data'=> ['estado'=>  false , 'mjs'=> 'No se puedo aprobar la/las solicitud/solicitudes'.json_encode($error2).' por que ya han sido aprobadas']]);
              
                endif;

                   return  json_encode(['data'=> ['estado'=>  true , 'mjs'=> 'Solicitudes Aprobadas']]);
                   
            }   
      }

      public function actionDesaprobarsolicitudmasiva(){
        
        
        if (Yii::$app->request->isAjax && Yii::$app->request->post()) {
            
                   $obj         =  json_decode(Yii::$app->request->post('datos'));
                   $solicitudes   =  $obj->{'solicitudes'};
                   $error       =  [];  
                   $error1      =  [];
                   $error2      =  [];
                   
                foreach($solicitudes as $data){
                    
                    $model = SysRrhhSoextras::find()->where(['id_sys_rrhh_soextras'=> $data->id_sys_rrhh_soextras])->one();

                    $estado        = 'R';
        
                    $db            = $_SESSION['db'];
                    
                    $empresa       = SysEmpresa::find()->where(['db_name'=> $db])->one();
                    
                    $titulo        = 'Solicitud de Aprobación de Horas Extras';
                    
                    $tipousuario   = $this->getTipoUsuario(Yii::$app->user->identity->id);
                    
                    //Validar si el documento necesita autorizacion
                    $documento = $this->getDocumento('SOLICITUD_HORAS_EXTRAS');
                    
                    if($documento):
                    
                        $autorizacion = $this->getAutorizacion($documento['id'], Yii::$app->user->identity->id);
                        
                        if($autorizacion):
                                
                            if($tipousuario == 'G'):
                            
                                $estado = 'N';
                            
                            endif;
                        
                        endif;

                    endif;
                    
                    if($model->estado == 'R'):
                            
                        if($estado == 'N'):
                        
                            $model->estado     = $estado;
                            $model->usuario_anulacion  = $estado != 'P' ? Yii::$app->user->username : null;
                            $model->fecha_anulacion    = date('Ymd H:i:s');
                            $model->comentario_anulacion = 'Solicitud Anulada. Revise por favor!';
               
                            if($model->save(false)):

                                $area = SysAdmAreas::find()->where(['id_sys_adm_area' => $model->id_sys_adm_area])->one();

                                $mensaje = "<p  style = 'margin:1px;'>La solicitud de horas extras #".str_pad($model->id_sys_rrhh_soextras, 5, "0", STR_PAD_LEFT)." del Área: <b>".$area->area."</b><p>No ha sido aprobada por motivo de: </p><p>".$model->comentario_anulacion."</p>";

                                $to = [];
                                $cc = [];

                                $emailUser = $this->ObtenerUsuariosGruposAutorizacionAll($documento['id']);
                                    
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
                            
                            else:

                                $error [] = array('Solicitud' => $data->id_sys_rrhh_soextras );

                            endif;

                        else:

                            $error1 [] = array('Solicitud' => $data->id_sys_rrhh_soextras );

                        endif;
                    
                    else:

                        $error2 [] = array('Solicitud' => $data->id_sys_rrhh_soextras );

                    endif;
                }

                if(count($error) > 0) :
                   
                    return json_encode(['data'=> ['estado'=>  false , 'mjs'=> 'No se puedo desaprobar la/las solicitud/solicitudes'.json_encode($error).' comuniquese con su administrador!']]);
              
                endif;

                if(count($error1) > 0) :
                   
                    return json_encode(['data'=> ['estado'=>  false , 'mjs'=> 'No se puedo desaprobar la/las solicitud/solicitudes'.json_encode($error1).' por que usted no tiene permisos de aprobación']]);
              
                endif;

                if(count($error2) > 0) :
                   
                    return json_encode(['data'=> ['estado'=>  false , 'mjs'=> 'No se puedo desaprobar la/las solicitud/solicitudes'.json_encode($error2).' por que ya han sido aprobadas']]);
              
                endif;

                   return  json_encode(['data'=> ['estado'=>  true , 'mjs'=> 'Solicitudes Desaprobadas']]);
                   
            }   
      }


      function getRendonminutos($hora){
        
        //revisar redondeo de horas extras 
       
        $array   = explode(':', $hora);
        $min     = intval($array[1]);
        $horas   = $array[0];
        
        if ($min >= 0){
            
            if ($min < 15){
                
                return $horas.':00:00';
            }
            elseif ($min >= 15 && $min < 30){
                
                return $horas.':15:00';
            }
            elseif ($min >= 30 && $min < 45){
                
                return $horas.':30:00';
            }
            elseif ($min >= 45 && $min < 60){
                
                return $horas.':45:00';
            }
        }else{
            
            if($horas > 0):
                 $horas = intval($horas) - 1;
                 return str_pad($horas, 2, "0", STR_PAD_LEFT).':45:00';
            endif;
            
        }
        return $horas.':00:00';
        
    }
    
    function gethoras50($entrada,$salida, $sdesayuno, $salmuerzo, $smerienda,$cedula, $fecha, $feriado){
    
        $ini50 = $fecha.' 06:00:00';
        $fin50 = $fecha.' 23:59:00';
        $entradadesayuno = '00:00:00';
        $thorasdesayuno  = '00:00:00';
        $entradaalmuerzo = '00:00:00';
        $thorasalmuerzo  = '00:00:00';
        $entradamerienda = '00:00:00';
        $thorasmerienda  = '00:00:00';
                       
        $horatrabas  =  date('Y-m-d', strtotime($fecha)).' '.$this->getTotalhoras($entrada, $salida);
        $horanormal  =  date('Y-m-d', strtotime($fecha)).' '.$this->gethora_normal($cedula, $fecha, $entrada, $salida);
    
        $dia             =  date("N", strtotime($fecha));
        $tiempo          =  0;
        
        if(date('Y-m-d', strtotime($entrada)) ==  date('Y-m-d', strtotime($salida))):
    
            $lunchs =  $this->obtenerComidasDiarias($cedula,$fecha);
    
        else:
    
            $lunchs =  $this->obtenerComidasDiarias($cedula,$salida);
        
        endif;
        
        
        foreach($lunchs as $item):

            if($dia == 6 || $dia == 7):
    
                $ingreso =  date('H:i:s', strtotime($entrada));
    
                if($ingreso < $item['hora'] && $item['id_sys_rrhh_comedor'] == 1):
    
                    $entradadesayuno = date('H:i:s', strtotime($item['hora']));
    
                    $id_lunch = $this->obtenerTiempoLunch($item['id_sys_rrhh_comedor']);
    
                    if($sdesayuno != "00:00:00"){
    
                        $thorasdesayuno = $this->getTotalhorascomedor($entradadesayuno, $sdesayuno);
    
                        if($thorasdesayuno > $id_lunch['tiempo_descuento']){
    
                            $tiempo += floatval($this->HorasToDecimal($thorasdesayuno));
    
                        }else{
    
                            $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));
    
                        }
    
                    }else{
    
                        $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));
    
                    }
    
                elseif($item['id_sys_rrhh_comedor'] == 2):
    
                    $entradaalmuerzo = date('H:i:s', strtotime($item['hora']));
    
                    $id_lunch = $this->obtenerTiempoLunch($item['id_sys_rrhh_comedor']);
    
                    if($salmuerzo != "00:00:00"){
    
                        $thorasalmuerzo = $this->getTotalhorascomedor($entradaalmuerzo, $salmuerzo);
    
                        if($thorasalmuerzo > $id_lunch['tiempo_descuento']){
    
                            $tiempo += floatval($this->HorasToDecimal($thorasalmuerzo));
    
                        }else{
    
                            $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));
    
                        }
    
                    }else{
    
                        $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));
    
                    }
    
                elseif($item['id_sys_rrhh_comedor'] == 3):
    
                    $entradamerienda = date('H:i:s', strtotime($item['hora']));
                    
                    $id_lunch = $this->obtenerTiempoLunch($item['id_sys_rrhh_comedor']);
    
                    if($smerienda != "00:00:00"){
    
                        $thorasmerienda = $this->getTotalhorascomedor($entradamerienda, $smerienda);
    
                        if($thorasmerienda > $id_lunch['tiempo_descuento']){
    
                            $tiempo += floatval($this->HorasToDecimal($thorasmerienda));
    
                        }else{
    
                            $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));
    
                        }
    
                    }else{
    
                        $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));
    
                    }
                
                endif;
    
            else:
    
                if($item['id_sys_rrhh_comedor'] == 1):
    
                    $entradadesayuno = date('H:i:s', strtotime($item['hora']));
    
                    $id_lunch = $this->obtenerTiempoLunch($item['id_sys_rrhh_comedor']);
    
                    if($sdesayuno != "00:00:00"){
    
                        $thorasdesayuno = $this->getTotalhorascomedor($entradadesayuno, $sdesayuno);
    
                        if($thorasdesayuno > $id_lunch['tiempo_descuento']){
    
                            $tiempo += floatval($this->HorasToDecimal($thorasdesayuno));
    
                        }else{
    
                            $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));
    
                        }
    
                    }else{
    
                        $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));
    
                    }
    
                elseif($item['id_sys_rrhh_comedor'] == 2):
    
                    $entradaalmuerzo = date('H:i:s', strtotime($item['hora']));
    
                    $id_lunch = $this->obtenerTiempoLunch($item['id_sys_rrhh_comedor']);
    
                    if($salmuerzo != "00:00:00"){
    
                        $thorasalmuerzo = $this->getTotalhorascomedor($entradaalmuerzo, $salmuerzo);
    
                        if($thorasalmuerzo > $id_lunch['tiempo_descuento']){
    
                            $tiempo += floatval($this->HorasToDecimal($thorasalmuerzo));
    
                        }else{
    
                            $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));
    
                        }
    
                    }else{
    
                        $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));
    
                    }
    
                elseif($item['id_sys_rrhh_comedor'] == 3):
    
                    $entradamerienda = date('H:i:s', strtotime($item['hora']));
                    
                    $id_lunch = $this->obtenerTiempoLunch($item['id_sys_rrhh_comedor']);
    
                    if($smerienda != "00:00:00"){
    
                        $thorasmerienda = $this->getTotalhorascomedor($entradamerienda, $smerienda);
    
                        if($thorasmerienda > $id_lunch['tiempo_descuento']){
    
                            $tiempo += floatval($this->HorasToDecimal($thorasmerienda));
    
                        }else{
    
                            $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));
    
                        }
    
                    }else{
    
                        $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));
    
                    }
                
                endif;
    
            endif;
    
        endforeach;
        
                  
        if($horatrabas > $horanormal):
                 
            $dia = date("N", strtotime($salida));
    
            $diaLibre = $this->getDiaLibre($cedula, $fecha);
    
            if($feriado == false):
    
                if(!$diaLibre):
                    
                    if($dia >= 1 && $dia <= 5):
                            
                        if(date('Y-m-d', strtotime($entrada)) == date('Y-m-d', strtotime($salida))):
    
                            if($lunchs):
    
                                return $this->restarMinutosLunch($this->DecimaltoHoras(number_format($tiempo, 2, '.', '')),$this->getTotalhoras($horanormal, $horatrabas));
                
                            else:
                
                                return $this->getTotalhoras($horanormal, $horatrabas);
                
                            endif;
                                    
                        else:
            
                            $ini50 =  date('Y-m-d', strtotime($fecha."+ 1 days")).' 06:00:00';
            
                            $agendamiento = $this->obtenerAgendamiento($cedula, $fecha);
                                        
                            if($agendamiento):
            
                                if($agendamiento['salida'] >= $ini50):
    
                                    if($lunchs):
    
                                        return $this->restarMinutosLunch($this->DecimaltoHoras(number_format($tiempo, 2, '.', '')),$this->getTotalhoras(date("Y-m-d H:i:s",strtotime( $agendamiento['salida'])),$salida));
                        
                                    else:
                        
                                        return  $this->getTotalhoras(date("Y-m-d H:i:s",strtotime( $agendamiento['salida'])),$salida);
                        
                                    endif;
                        
                                endif;   
                                
                                if($lunchs):
    
                                    return $this->restarMinutosLunch($this->DecimaltoHoras(number_format($tiempo, 2, '.', '')),$this->getTotalhoras($ini50,$salida));
                    
                                else:
                    
                                    return  $this->getTotalhoras($ini50,$salida);
                    
                                endif;
                                
                            endif;
                                
                                    
                            if ($salida > $fin50 && $salida < $ini50):
                                    
                                $salidanormal = $this->suma_horas(date('H:i:s', strtotime($entrada)), "08:30:00");
    
                                if($lunchs):
    
                                    return $this->restarMinutosLunch($this->DecimaltoHoras(number_format($tiempo, 2, '.', '')),$this->getTotalhoras($fecha.' '.$salidanormal, $fin50));
                    
                                else:
                    
                                    return $this->getTotalhoras($fecha.' '.$salidanormal, $fin50);
                    
                                endif;
                                            
                            elseif ($salida > $ini50):
                                    
                                //Aqui sumar la suma de horas extras del 50% hasta las 23.59 y despues de 06:00:00
                                //Revisar 
                                    
                                $salidanormal = $this->suma_horas(date('H:i:s', strtotime($entrada)), "08:30:00");
    
                                if($lunchs):
    
                                    return $this->restarMinutosLunch($this->DecimaltoHoras(number_format($tiempo, 2, '.', '')),$this->getTotalhoras($fecha.' '.$salidanormal, $fin50));
                    
                                else:
                    
                                    return $this->getTotalhoras($fecha.' '.$salidanormal, $fin50);
                    
                                endif;
                                    
                                //return getTotalhoras($ini50, $salida);
                                    
                            endif;  
                        endif;
                    endif; 
                endif;   
            endif;
    
        endif;
             
        return '00:00:00';
    }

    function suma_horas($hora1,$hora2){
        
        $hora1=explode(":",$hora1);
        $hora2=explode(":",$hora2);
        $temp=0;
       
        //sumo minutos
        $minutos=(int)$hora1[1]+(int)$hora2[1]+$temp;
        $temp=0;
        while($minutos>=60){
            $minutos=$minutos-60;
            $temp++;
        }
        
        //sumo horas
        $horas=(int)$hora1[0]+(int)$hora2[0]+$temp;
        
        if($horas<10):
            $horas= '0'.$horas;
        endif;
            
        if($minutos<10):
            $minutos= '0'.$minutos;
         endif;
                            
       $sum_hrs = $horas.':'.$minutos.':00';
                    
       return ($sum_hrs);
                    
    }
     //restar minutos lunchs   
     function restarMinutosLunch ($tiempo,$totalhoras){
    
        if($tiempo < $totalhoras):
         
            $fechaUno=new \DateTime($tiempo);
            $fechaDos=new \DateTime($totalhoras);
             
            $dateInterval = $fechaUno->diff($fechaDos);
             
            return  $dateInterval->format('%H:%i:%s');
        
        endif;
    
        return '00:00:00';
      }
     //calculo de horas extras del 100 %  
    function gethoras100($entrada, $salida,$sdesayuno, $salmuerzo, $smerienda, $cedula, $fecha, $feriado, $agendamiento){
            
        $horatrabas      =  date('Y-m-d', strtotime($entrada)).' '.$this->getTotalhoras($entrada, $salida); //horas trabajadas
        $horanormal      =  date('Y-m-d', strtotime($entrada)).' '."08:00:00"; //horas normal
        $hsalida         =  $this->getSalidaLaboral($entrada, $salida, $horanormal);
        $dia             =  date("N", strtotime($fecha));
        $ini100          =  date('Y-m-d', strtotime($fecha."+ 1 days")).' 00:00:00';
        $fin100          =  date('Y-m-d', strtotime($fecha."+ 1 days")).' 06:00:00';
        $tiempo          =  0;
        $lunchs          =  [];
        $entradadesayuno = '00:00:00';
        $thorasdesayuno  = '00:00:00';
        $entradaalmuerzo = '00:00:00';
        $thorasalmuerzo  = '00:00:00';
        $entradamerienda = '00:00:00';
        $thorasmerienda  = '00:00:00';
        
        if(date('Y-m-d', strtotime($entrada)) ==  date('Y-m-d', strtotime($salida))):
    
            $lunchs =  $this->obtenerComidasDiarias($cedula,$fecha);
    
        else:
    
            if($salida < $fin100):

                $lunchs =  $this->obtenerComidasDiarias($cedula,$fecha);
            
            else:
    
                $lunchs =  $this->obtenerComidasDiarias($cedula,$salida);
    
            endif;
        
        endif;
        
        if($lunchs):
    
            foreach($lunchs as $item):

                if($dia == 6 || $dia == 7):

                    $ingreso =  date('H:i:s', strtotime($entrada));

                    if($ingreso < $item['hora'] && $item['id_sys_rrhh_comedor'] == 1):

                        $entradadesayuno = date('H:i:s', strtotime($item['hora']));

                        $id_lunch = $this->obtenerTiempoLunch($item['id_sys_rrhh_comedor']);

                        if($sdesayuno != "00:00:00"){

                            $thorasdesayuno = $this->getTotalhorascomedor($entradadesayuno, $sdesayuno);

                            if($thorasdesayuno > $id_lunch['tiempo_descuento']){
        
                                $tiempo += floatval($this->HorasToDecimal($thorasdesayuno));
        
                            }else{
        
                                $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));
        
                            }

                        }else{

                            $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));

                        }

                    elseif($item['id_sys_rrhh_comedor'] == 2):

                        $entradaalmuerzo = date('H:i:s', strtotime($item['hora']));

                        $id_lunch = $this->obtenerTiempoLunch($item['id_sys_rrhh_comedor']);

                        if($salmuerzo != "00:00:00"){

                            $thorasalmuerzo = $this->getTotalhorascomedor($entradaalmuerzo, $salmuerzo);

                            if($thorasalmuerzo > $id_lunch['tiempo_descuento']){
        
                                $tiempo += floatval($this->HorasToDecimal($thorasalmuerzo));
        
                            }else{
        
                                $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));
        
                            }

                        }else{

                            $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));

                        }

                    elseif($item['id_sys_rrhh_comedor'] == 3):

                        $entradamerienda = date('H:i:s', strtotime($item['hora']));
                        
                        $id_lunch = $this->obtenerTiempoLunch($item['id_sys_rrhh_comedor']);

                        if($smerienda != "00:00:00"){

                            $thorasmerienda = $this->getTotalhorascomedor($entradamerienda, $smerienda);

                            if($thorasmerienda > $id_lunch['tiempo_descuento']){
        
                                $tiempo += floatval($this->HorasToDecimal($thorasmerienda));
        
                            }else{
        
                                $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));
        
                            }

                        }else{

                            $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));

                        }
                    
                    endif;

                else:

                    if($item['id_sys_rrhh_comedor'] == 1):

                        $entradadesayuno = date('H:i:s', strtotime($item['hora']));

                        $id_lunch = $this->obtenerTiempoLunch($item['id_sys_rrhh_comedor']);

                        if($sdesayuno != "00:00:00"){

                            $thorasdesayuno = $this->getTotalhorascomedor($entradadesayuno, $sdesayuno);

                            if($thorasdesayuno > $id_lunch['tiempo_descuento']){
        
                                $tiempo += floatval($this->HorasToDecimal($thorasdesayuno));
        
                            }else{
        
                                $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));
        
                            }

                        }else{

                            $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));

                        }

                    elseif($item['id_sys_rrhh_comedor'] == 2):

                        $entradaalmuerzo = date('H:i:s', strtotime($item['hora']));

                        $id_lunch = $this->obtenerTiempoLunch($item['id_sys_rrhh_comedor']);

                        if($salmuerzo != "00:00:00"){

                            $thorasalmuerzo = $this->getTotalhorascomedor($entradaalmuerzo, $salmuerzo);

                            if($thorasalmuerzo > $id_lunch['tiempo_descuento']){
        
                                $tiempo += floatval($this->HorasToDecimal($thorasalmuerzo));
        
                            }else{
        
                                $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));
        
                            }

                        }else{

                            $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));

                        }

                    elseif($item['id_sys_rrhh_comedor'] == 3):

                        $entradamerienda = date('H:i:s', strtotime($item['hora']));
                        
                        $id_lunch = $this->obtenerTiempoLunch($item['id_sys_rrhh_comedor']);

                        if($smerienda != "00:00:00"){

                            $thorasmerienda = $this->getTotalhorascomedor($entradamerienda, $smerienda);

                            if($thorasmerienda > $id_lunch['tiempo_descuento']){
        
                                $tiempo += floatval($this->HorasToDecimal($thorasmerienda));
        
                            }else{
        
                                $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));
        
                            }

                        }else{

                            $tiempo += floatval($this->HorasToDecimal($id_lunch['tiempo_descuento']));

                        }
                    
                    endif;

                endif;

            endforeach;
        
        endif;

           
        //Encaso de estar agendado calcular horas del 100%
        $agendamiento = $this->obtenerAgendamiento($cedula, $fecha);
    
        $diaLibre = $this->getDiaLibre($cedula, $fecha);
           
            //Horas del 100% horarios Lunea Vienres 
            if($feriado != null ):
            
                if($lunchs):
    
                    return $this->restarMinutosLunch($this->DecimaltoHoras(number_format($tiempo, 2, '.', '')),$this->getTotalhoras($entrada, $salida));
    
                else:
    
                    return $this->getTotalhoras($entrada, $salida);
    
                endif;
                
            // Horas del 100% sin agendamiento
            elseif($diaLibre):
                    
                if($dia == 6  || $dia == 7):
                        
                    if($lunchs):
                        return $this->restarMinutosLunch($this->DecimaltoHoras(number_format($tiempo, 2, '.', '')),$this->getTotalhoras($entrada, $salida));
                    else:
                        return $this->getTotalhoras($entrada, $salida);
                    endif;
                    
                else:
                
                    if($lunchs):
                        return $this->restarMinutosLunch($this->DecimaltoHoras(number_format($tiempo, 2, '.', '')),$this->getTotalhoras($entrada, $salida));
                    else:
                        return $this->getTotalhoras($entrada, $salida);
                    endif;
                    
                endif;
                
            //Horas del 100% sábados y domingos 
            elseif($dia == 6  || $dia == 7):
            
                if($agendamiento):
                
                    if($lunchs):
                        return $this->restarMinutosLunch($this->DecimaltoHoras(number_format($tiempo, 2, '.', '')),$this->getTotalhoras($horanormal, $horatrabas));
                    else:
                        return $this->getTotalhoras($horanormal, $horatrabas);
                    endif;
                
                else:
                    
                    if($lunchs):
                        return $this->restarMinutosLunch($this->DecimaltoHoras(number_format($tiempo, 2, '.', '')),$this->getTotalhoras($entrada, $salida));
                    else:
                        return $this->getTotalhoras($entrada, $salida);
                    endif;
                    
                endif;
                
                    
            else:
            
                    //Horas del 100% de lunes a Viernes 00:00:00  y 06:00:00 
                    /*   $calcular100 = true;
            
                    if($agendamiento):
                    
                        $calcular100 = false;
                        
                    endif;
                
                    
                    if($calcular100 == true):
                    */
                
                if($horatrabas > $horanormal && date('Y-m-d', strtotime($entrada)) !=  date('Y-m-d', strtotime($salida))):
                    
                    if($hsalida < $fin100):
                                
                        if($hsalida < $ini100):
                                    
                            if($salida > $fin100):
                                        
                                return  $this->getTotalhoras($ini100, $fin100);
                                        
                            elseif($salida < $fin100):
                                        
                                return $this->getTotalhoras($ini100, $salida);
                                        
                            endif;
                                
                        else: 
                                    
                            if($salida > $fin100):
                                        
                                return  $this->getTotalhoras($hsalida, $fin100);
                                    
                            elseif($salida < $fin100):
                                            
                                return $this->getTotalhoras($hsalida, $salida);
                                            
                            endif;
                                        
                        endif;
                                
                    endif;
                            
                            
                                /* if($hsalida > $ini100 && $hsalida < $fin100):
                                    
                                    
                                    if($salida > $ini100 && $salida < $fin100):
                                    
                                            return  getTotalhoras($hsalida, $salida);
                            
                                    elseif($salida > $fin100 ):
                                    
                                            return  getTotalhoras($hsalida, $fin100);
                                    
                                    endif;
                                    
                                    elseif($hsalida < $ini100):
                                    
                                    
                                        if($salida > $ini100 && $salida < $fin100):
                                        
                                            return  getTotalhoras($ini100, $salida);
                                        
                                        elseif($salida > $fin100 ):
                                        
                                            return  getTotalhoras($ini100, $fin100);
                                        
                                        endif;
                        
                                    elseif($hsalida > $fin100):
                                    
                                        
                                    return  getTotalhoras($ini100, $fin100);
                                    
                                    
                                    endif;
                            */
                endif;
                            
                            
            endif;
                     
            //endif;
            
        return "00:00:00";
        
    }

    function getTotalhoras($entrada, $salida){
        
        if ( $salida > $entrada): 
            
            $fechaini = date('Y-m-d', strtotime($entrada));
            $fechafin = date('Y-m-d', strtotime($salida));
    
            $arrayentrada =  explode(':', date('H:i:s', strtotime($entrada) ));
            $arraysalida =  explode(':', date('H:i:s', strtotime($salida) ));
                         
                         
            if ($fechaini ==  $fechafin):
                         
                $totalhoras = 0;
                $totalmin   = 0;
                         
                $horaentra  = $arrayentrada[0];
                $minentrada = $arrayentrada[1];
                $horasalida = $arraysalida[0];
                $minsalida  = $arraysalida[1];
                         
                $minentrada = 60 - $minentrada;
                $horaentra++;
                         
                $totalmin = $minentrada + $minsalida;
                         
                if ($totalmin >= 60):
                         
                    $totalmin   = $totalmin - 60;
                    $horasalida++;
                         
                endif;
                         
                    $totalhoras =  $horasalida - $horaentra;
                         
                    return str_pad($totalhoras, 2, "0", STR_PAD_LEFT).':'.str_pad($totalmin, 2, "0", STR_PAD_LEFT).':00';
            
                else:
                  
                    $totalhoras = 0;
                    $totalmin   = 0;
                         
                    $horaentra  = $arrayentrada[0];
                    $minentrada = $arrayentrada[1];
                    $horasalida = $arraysalida[0];
                    $minsalida  = $arraysalida[1];
                                
                    $minentrada = 60 - $minentrada;
                    $horaentra++;
                    $horaentra = 24 - $horaentra;
                    $totalmin = $minentrada + $minsalida;
                         
                    if ($totalmin >= 60):
                         
                        $totalmin   = $totalmin - 60;
                        $horasalida++;
                             
                    endif;
                         
                        $totalhoras = $horasalida + $horaentra;
                         
                        return str_pad($totalhoras, 2, "0", STR_PAD_LEFT).':'.str_pad($totalmin, 2, "0", STR_PAD_LEFT).':00';
                 
                endif;
                  
            endif;
             
        return "00:00:00";
           
    }  

    function gethora_normal($cedula,$fechaentrada, $entrada, $salida){
        
        /*  $datos = [];
         
         $entrada = date('H:i:s', strtotime($entrada));
         $salida =  date('H:i:s', strtotime($salida));
         
         
         $datos =  (new \yii\db\Query())
         ->select(['hora_normales'])
         ->from("sys_rrhh_cuadrillas_jornadas_mov mov")
         ->innerJoin("sys_rrhh_horario_cab hor","mov.id_sys_rrhh_jornada  = hor.id_sys_rrhh_horario_cab")
         ->where("fecha_laboral = '{$fechaentrada}'")
         ->andwhere("id_sys_rrhh_cedula ='{$cedula}'")
         ->orderBy("fecha_registro desc")
         ->all(SysRrhhCuadrillasJornadasMov::getDb());
        
         if(count($datos) > 0 ) {
             
             return $datos[0]['hora_normales'];
             
         }else{
             
              $datos =  (new \yii\db\Query())
             ->select(["abs(datediff(minute ,hora_inicio, '{$entrada}')) + abs(datediff(minute ,hora_fin, '{$salida}')) min","hora_normales"])
             ->from("sys_rrhh_empleados_horario horemp")
             ->innerJoin("sys_rrhh_horario_cab hor","horemp.id_sys_rrhh_horario  = hor.id_sys_rrhh_horario_cab")
             ->where("id_sys_rrhh_cedula ='{$cedula}'")
             ->orderby("min")
             ->all(SysRrhhCuadrillasJornadasMov::getDb());
             
             if(count($datos) > 0 ){
                 
                 return $datos[0]['hora_normales'];
                 
             }
             
             return '08:30:00';
         }
         */
       
          /*if ($cedula == '1310801707') :
             return '06:30:00';
          endif;
          */
         return '08:00:00';
     }
    
     function obtenerComidasDiarias($cedula,$fecha){
    
        $db =  $_SESSION['db'];
       
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerComidasDiariasEmpleado] @cedula = '{$cedula}', @fecha = '{$fecha}'")->queryAll(); 
    
    }

    function obtenerTiempoLunch($idcomedor){
    
        $db =  $_SESSION['db'];
       
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerTiempoComedor] @idcomedor = '{$idcomedor}'")->queryOne(); 
    
    }

    function getDiaLibre($cedula, $fecha_laboral){
    
        return  (new \yii\db\Query())->select(["*"])
        ->from("sys_rrhh_cuadrillas_jornadas_mov")
        ->where("id_sys_rrhh_cedula  = '{$cedula}'")
        ->andWhere("id_sys_rrhh_jornada is null")
        ->andwhere("fecha_laboral = '{$fecha_laboral}'")
        ->one(SysRrhhEmpleados::getDb());
       
    }

    function getSalidaLaboral($entrada, $salida, $horanormal){
        
        $arrayentrada   =  explode(':', date('H:i:s', strtotime($entrada)));
        $arraynormal    =  explode(':', date('H:i:s', strtotime($horanormal)));
                
                $horas          = intval($arrayentrada[0]) + intval($arraynormal[0]);
                $mintutos       = intval($arraynormal[1]) + intval($arrayentrada[1]);
            
                if($horas >= 24):
                
                    $horas   = $horas - 24;
                    $fecha  = date('Y-m-d', strtotime($salida));
                
                    if($mintutos >= 60) :
                    
                           $horas ++;
                           $mintutos = $mintutos - 60;
                    
                    endif;
                
            
                else:
            
                    if($mintutos >= 60) :
                    
                        $horas ++;
                        $mintutos = $mintutos - 60;
                    
                    endif;
                    
                    if($horas >= 24 ):
                        $horas   = $horas - 24;
                        $fecha  = date('Y-m-d', strtotime($salida));
                    else:
                         $fecha  = date('Y-m-d', strtotime($entrada));
                    endif;
                    
              endif;
            
           return  $fecha.' '.str_pad($horas, 2, "0", STR_PAD_LEFT).':'.str_pad($mintutos, 2, "0", STR_PAD_LEFT);
              
        }

        function obtenerAgendamiento($cedula, $fecha_laboral){
    
            return (new \yii\db\Query())
            ->select(["*"])
            ->from("[dbo].[agendamiento]")
            ->where("id_sys_rrhh_cedula ='{$cedula}'")
            ->andWhere("fecha_laboral = '{$fecha_laboral}'")
            ->orderBy("fecha_registro desc")
            ->one(SysRrhhEmpleados::getDb());
            
            
        }

        private function getValorHora($id_sys_rrhh_cedula){
        
            $db =  $_SESSION['db'];
            return  Yii::$app->$db->createCommand("exec dbo.ObtenerValorHoraEmpleado @id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")->queryScalar();
        }

        function getTotalhorascomedor($entrada, $salida){
        
            if ( $salida > $entrada): 
                
                $fechaini = date('Y-m-d', strtotime($entrada));
                $fechafin = date('Y-m-d', strtotime($salida));
        
                $arrayentrada =  explode(':', date('H:i:s', strtotime($entrada) ));
                $arraysalida =  explode(':', date('H:i:s', strtotime($salida) ));
                             
                             
                if ($fechaini ==  $fechafin):
                             
                    $totalhoras = 0;
                    $totalmin   = 0;
                             
                    $horaentra  = $arrayentrada[0];
                    $minentrada = $arrayentrada[1];
                    $segentrada = $arrayentrada[2];
                    $horasalida = $arraysalida[0];
                    $minsalida  = $arraysalida[1];
                    $segsalida  = $arraysalida[2];
                             
                    if($horaentra != $horasalida):
                    
                        $minentrada = 60 - $minentrada;
                        $horaentra++;
                
                        $totalmin = $minentrada + $minsalida;
                
                        $segentrada = 60 - $segentrada;
                            
                        $totalseg = $segentrada + $segsalida;
        
                    else:
        
                        if($minentrada != $minsalida):
        
                            $minentrada = 60 - $minentrada;
                            $horaentra++;
                
                            $totalmin = $minentrada + $minsalida;
                
                            $segentrada = 60 - $segentrada;
                            
                            $totalseg = $segentrada + $segsalida;
                        else:
        
                            $minentrada = 60 - $minentrada;
                            $horaentra++;
                
                            $totalmin = $minentrada + $minsalida;
                            
                            $totalseg = $segsalida - $segentrada;
        
                        endif;
        
                    endif;
        
                    if ($totalseg >= 60):
        
                        $totalseg = $totalseg - 60;
        
                        if ($totalmin >= 60):
                             
                            $totalmin   = $totalmin - 60;
                            $horasalida++;
                                 
                        endif;
        
                        $totalmin++;
        
                    endif;
                             
                    if ($totalmin >= 60):
                             
                        $totalmin   = $totalmin - 60;
                        $horasalida++;
                             
                    endif;
                             
                        $totalhoras =  $horasalida - $horaentra;
                             
                        return str_pad($totalhoras, 2, "0", STR_PAD_LEFT).':'.str_pad($totalmin, 2, "0", STR_PAD_LEFT).':'.str_pad($totalseg, 2, "0", STR_PAD_LEFT).'';
                 
                endif;
                      
            endif;
                 
            return "00:00:00";
               
        }
    /**
     * Finds the SysRrhhSoextras model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_sys_rrhh_soextras
     * @param string $id_sys_empresa
     * @return SysRrhhSoextras the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhSoextras::find()->from('sys_rrhh_horas_extras_solicitud WITH (nolock)')->where(['id_sys_rrhh_soextras' => $id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
