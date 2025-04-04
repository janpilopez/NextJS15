<?php

namespace app\controllers;

use Yii;
use app\models\SysRrhhEmpleadosPermisos;
use app\models\SysRrhhVacacionesSolicitud;
use app\models\search\SysRrhhVacacionesSolicitudSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use app\models\SysRrhhEmpleadosPeriodoVacaciones;
use Exception;
use Mpdf\Mpdf;
use kartik\mpdf\Pdf;
use app\models\SysAdmCargos;
use app\models\SysAdmUsuariosDep;
use app\models\SysEmpresa;
use app\models\sysAdmMandos;
use app\models\SysRrhhEmpleados;
use app\models\SysAdmDepartamentos;
use app\models\User;
use yii\db\Query;

/**
 * SolicitudVacacionesController implements the CRUD actions for SysRrhhVacacionesSolicitud model.
 */
class SolicitudVacacionesController extends Controller
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
     * Lists all SysRrhhVacacionesSolicitud models.
     * @return mixed
     */
    public function actionIndex()
    {
        
        $this->layout = '@app/views/layouts/main_emplados';
        $searchModel = new SysRrhhVacacionesSolicitudSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhVacacionesSolicitud model.
     * @param string $id_sys_rrhh_vacaciones_solicitud
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id_sys_rrhh_vacaciones_solicitud, $id_sys_empresa)
    {
           $model =   $this->findModel($id_sys_rrhh_vacaciones_solicitud, $id_sys_empresa);
 
            $html =    $this->renderPartial('view',['model'=> $model ]);

            $empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();
        
           /* $mpdf = new Mpdf([
                'format' => 'A4',
                // 'orientation' => 'L'
            ]);
            $mpdf->WriteHTML($html);
             $mpdf->Output('SolicitudVacaciones.pdf', 'I');
            exit();
           */
            if($empresa->id_sys_empresa == '001'):
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
                    'cssInline' => '.kv-heading-1{font-size:18px} .text_left{text-align: left;} .sin_margen{margin: 0px;} .line{ border-bottom: 1px solid black; margin-top: 20px;} .title{font-size: 14px;} .subtitle{font-size: 12px;} .negrita{font-weight: bold;} table {width: 100%} td { margin: 1px;} .margen-left{ margin: 20px;}',
            
                    // set mPDF properties on the fly
                    'options' => ['title' => 'Solicitud de Vacaciones'],
                    // call mPDF methods on the fly
                    'methods' => [
                        'SetHeader'=>[''],
                        'SetFooter'=>['<img src="C:/xampp/htdocs/proyectonomina/web/logo/1391744064001/end.PNG">'],
                    ]
                ]);
            else:
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
                    'cssInline' => '.kv-heading-1{font-size:18px} .text_left{text-align: left;} .sin_margen{margin: 0px;} .line{ border-bottom: 1px solid black; margin-top: 20px;} .title{font-size: 14px;} .subtitle{font-size: 12px;} .negrita{font-weight: bold;} table {width: 100%} td { margin: 1px;} .margen-left{ margin: 20px;}',
            
                    // set mPDF properties on the fly
                    'options' => ['title' => 'Solicitud de Vacaciones'],
                    // call mPDF methods on the fly
                    'methods' => [
                        'SetHeader'=>[''],
                        'SetFooter'=>[''],
                    ]
                ]);

            endif;
            // return the pdf output as per the destination setting
            return $pdf->render(); 
        
    }

    /**
     * Creates a new SysRrhhVacacionesSolicitud model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysRrhhVacacionesSolicitud();
        
        if ($model->load(Yii::$app->request->post())) {
            
            $codsolicitud   =  SysRrhhVacacionesSolicitud::find()->select(['max(CAST(id_sys_rrhh_vacaciones_solicitud AS INT))'])->Where(['id_sys_empresa'=> '001'])->scalar();
                
            $db = $_SESSION['db'];
              
            $empresa       = SysEmpresa::find()->where(['db_name'=> $db])->one();
              
            $titulo = 'Notificación de vacaciones';
              
            $transaction    = \Yii::$app->$db->beginTransaction();

            $documento = $this->getDocumento('SOLICITUD_VACACIONES');

            $username = Yii::$app->user->username;

            $grupo_autorizacion = $this->ObtenerGrupoAutorizacionUsername($username);
            
            //Obtenemos datos del empleados 
              
            $empleado       =  SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->one();
 
            $nivelempleado  =  $this->getNivelcargo($empleado->id_sys_adm_cargo, $empleado->id_sys_empresa);
                
            $departamento = $this->getDepartamento($empleado->id_sys_adm_cargo);
  
            $periodo = SysRrhhEmpleadosPeriodoVacaciones::find()->where(['id_sys_adm_periodo_vacaciones'=> $model->id_sys_rrhh_vacaciones_periodo])->andWhere(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->one();
            
            $date1 = new \DateTime($model->fecha_inicio);
            $date2 = new \DateTime($model->fecha_fin);
            $diff  = $date1->diff($date2);
            // will output 2 days
            $dias  = $diff->days + 1;
                
           
                $diasOtorgados  = 0;
                $diasDisponibles = 0;
                 
                if ($periodo):
                        
                    $diasOtorgados   =  $periodo->dias_otorgados + intval($dias);
                    $diasDisponibles =  $periodo->dias_disponibles;
                    
                    //Yii::$app->$db->createCommand("update sys_rrhh_empleados_periodo_vacaciones set dias_otorgados='$diasOtorgados' where id_sys_rrhh_cedula='{$empleado['id_sys_rrhh_cedula']}' AND id_sys_adm_periodo_vacaciones='{$model->id_sys_rrhh_vacaciones_periodo}'")->execute();
                
                else:
    
                    //$diasOtorgados   =  intval($dias);
                    $codigo = Yii::$app->$db->createCommand("select ISNULL(max(id_sys_rrhh_empleados_periodo_vacaciones),0) + 1 from sys_rrhh_empleados_periodo_vacaciones")->queryScalar();
                    Yii::$app->$db->createCommand("INSERT INTO sys_rrhh_empleados_periodo_vacaciones(id_sys_rrhh_empleados_periodo_vacaciones, dias_disponibles, dias_otorgados, estado, id_sys_rrhh_cedula, id_sys_empresa, id_sys_adm_periodo_vacaciones, dias_laborados, valor)
                    SELECT * FROM (SELECT '{$codigo}' as id_sys_rrhh_empleados_periodo_vacaciones, '$diasDisponibles' as dias_disponibles, '$diasOtorgados' as dias_otorgados,'A' as estado,'{$empleado['id_sys_rrhh_cedula']}' as id_sys_rrhh_cedula,'001' as id_sys_empresa, '{$model->id_sys_rrhh_vacaciones_periodo}' as id_sys_adm_periodo_vacaciones, 360 as dias_laborados,  0 as valor) AS new_value
                    WHERE NOT EXISTS (
                     SELECT id_sys_rrhh_cedula,id_sys_adm_periodo_vacaciones FROM sys_rrhh_empleados_periodo_vacaciones WHERE id_sys_rrhh_cedula = '{$empleado['id_sys_rrhh_cedula']}' AND id_sys_adm_periodo_vacaciones='{$model->id_sys_rrhh_vacaciones_periodo}'
                    )")->execute();
            
                endif;

                try {
                
                    if($diasOtorgados <= $diasDisponibles):
                    
                                
                        $model->id_sys_rrhh_vacaciones_solicitud = $codsolicitud + 1;
                        $model->usuario_transaccion              = Yii::$app->user->username;
                        $model->id_sys_empresa                   = '001';                       
                        $model->estado                           = 'A';
                        $model->estado_solicitud                 = 'P';
                        $model->comentario                       = $model->comentario;
                        $model->fecha_registro                   = date('Y-m-d');
                        $model->tipo                             = $model->tipo != null ? $model->tipo : 'G';
                                
                        if($model->save(false)):
    
                            $ipserver  = Yii::$app->params['ipServer']; //ipserver
                                           
                            $mensaje   = "<p>Se ha generado la solicitud de vacaciones #".str_pad($model->id_sys_rrhh_vacaciones_solicitud, 10, "0", STR_PAD_LEFT)." del Sr/Sr(a): <b>".$empleado->nombres."</b> con fecha de registro ".$model->fecha_registro."</p><p>Puede consultar el documento en el siguiente link:</p><a href='http://".$ipserver."/vacaciones/vercalendario' target='_blank'>Ver Solicitud</a>";                                   
                                    
                            $to = [];
                                           
                            $cc = [];
                                           
                            if($model->tipo == 'G'):
                                          
                                                        
                                $emailUser = $this->ObtenerUsuariosGruposAutorizacionXDepartamento($nivelempleado, $departamento->id_sys_adm_area, $departamento->id_sys_adm_departamento);
                                                   
                                foreach ($emailUser as $user):
                                    array_push($to, $user['email']);
                                endforeach;
              
                                $this->EnviarCorreo($to, $cc, $mensaje, $titulo,  $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, "", "P");
                                     
                            elseif($model->tipo == 'P'):

                                $emailUser = $this->ObtenerUsuariosGrupoAutorizacionGerencia($documento['documento']);
                                      
                                $emailJefeDdoo = $this->ObtenerUsuarioJefeNomina();

                                foreach ($emailUser as $user):
                                    array_push($to, $user['email']);
                                endforeach;

                                if($emailJefeDdoo != ""):
                                    array_push($cc, $emailJefeDdoo['email']);
                                endif;
              
                                $this->EnviarCorreo($to, $cc, $mensaje, $titulo,  $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, "", "P");
                            endif;
                                            
                                $transaction->commit();
                                            
                                Yii::$app->getSession()->setFlash('info', [
                                'type' => 'success','duration' => 1500,
                                'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud se  ha sido registro con exito!',
                                'positonY' => 'top','positonX' => 'right']);
        
                                return $this->redirect(['index']);
                                           
                                    
                        endif;
                    else:
                      
                              
                            if($diasOtorgados >= $diasDisponibles):
                            
                                        
                                $model->id_sys_rrhh_vacaciones_solicitud = $codsolicitud + 1;
                                $model->usuario_transaccion              = Yii::$app->user->username;
                                $model->id_sys_empresa                   = '001';                       
                                $model->estado                           = 'A';
                                $model->estado_solicitud                 = 'P';
                                $model->comentario                       = $model->comentario;
                                $model->fecha_registro                   = date('Y-m-d');
                                $model->tipo                             = $model->tipo != null ? $model->tipo : 'A';
                                        
                                if($model->save(false)):
            
                                    $ipserver  = Yii::$app->params['ipServer']; //ipserver
                                                
                                    $mensaje   = "<p>Se ha generado la solicitud de vacaciones #".str_pad($model->id_sys_rrhh_vacaciones_solicitud, 10, "0", STR_PAD_LEFT)." del Sr/Sr(a): <b>".$empleado->nombres."</b> con fecha de registro ".$model->fecha_registro."</p><p>Puede consultar el documento en el siguiente link:</p><a href='http://".$ipserver."/vacaciones/vercalendario' target='_blank'>Ver Solicitud</a>";                                   
                                            
                                    $to = [];
                                                
                                    $cc = [];
                                                
                                    if($model->tipo == 'A'):
                                                 
                                                                
                                        $emailUser = $this->ObtenerUsuariosGruposAutorizacionXDepartamento($nivelempleado, $departamento->id_sys_adm_area, $departamento->id_sys_adm_departamento);
                                                        
                                        foreach ($emailUser as $user):
                                            array_push($to, $user['email']);
                                        endforeach;
                    
                                        $this->EnviarCorreo($to, $cc, $mensaje, $titulo,  $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, "", "P");
                                                       
                                        endif;
                                                    
                                        $transaction->commit();
                                                    
                                        Yii::$app->getSession()->setFlash('info', [
                                        'type' => 'success','duration' => 1500,
                                        'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud se  ha sido registro con exito!',
                                        'positonY' => 'top','positonX' => 'right']);
                
                                        return $this->redirect(['index']);
                                                
                                            
                                    endif;
                            
                                endif;
                            endif;                          
                  
                } catch (Exception $e) {
                    
                    $transaction->rollBack();
                 
                    Yii::$app->getSession()->setFlash('info', [
                        'type' => 'danger','duration' => 3000,
                        'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador!'.$e->getMessage(),
                        'positonY' => 'top','positonX' => 'right']);
                          return $this->redirect(['index']);
                    
                }
                      
           
        }
    

        return $this->render('create', [
            'model' => $model,
            'update'=> 0
        ]);
    }

    /**
     * Updates an existing SysRrhhVacacionesSolicitud model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id_sys_rrhh_vacaciones_solicitud
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id_sys_rrhh_vacaciones_solicitud, $id_sys_empresa)
    {
        $model = $this->findModel($id_sys_rrhh_vacaciones_solicitud, $id_sys_empresa);
        
        $model->comentario = utf8_encode($model->comentario);

        if ($model->load(Yii::$app->request->post())) {
                
        }
        
        return $this->render('update', [
            'model' => $model,
            'update'=> 1
        ]);
    }

    /**
     * Deletes an existing SysRrhhVacacionesSolicitud model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id_sys_rrhh_vacaciones_solicitud
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id_sys_rrhh_vacaciones_solicitud, $id_sys_empresa)
    {
        //$this->findModel($id_sys_rrhh_vacaciones_solicitud, $id_sys_empresa)->delete();
        
        $model = $this->findModel($id_sys_rrhh_vacaciones_solicitud, $id_sys_empresa);
        $model->comentario = '';
        
        $db      = $_SESSION['db'];
        
        $empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();
        
        $periodo = SysRrhhEmpleadosPeriodoVacaciones::find()->where(['id_sys_adm_periodo_vacaciones'=> $model->id_sys_rrhh_vacaciones_periodo])->andWhere(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> $model->id_sys_empresa])->one();
        
        //Obtenemos datos del empleados
        $empleado =  SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->one();
        
        //nivel del empleado
        $nivelempleado =   $this->getNivelcargo($empleado->id_sys_adm_cargo, $empleado->id_sys_empresa);
        
        $estado        =  '';

        $username = Yii::$app->user->username;

        $grupo_autorizacion = $this->ObtenerGrupoAutorizacionUsername($username);
        
        $tipousuario   = $this->getTipoUsuario(Yii::$app->user->identity->id);
            
        if($model->tipo == 'P'):

            if($tipousuario == 'G'):

                $estado = 'N';
            
            endif;
        
        elseif($model->tipo == 'G' || $model->tipo == 'A'):

            if($tipousuario == 'D'):
                        
            $estado = 'N';
                
            endif;
        
        endif;
  
        $departamento = $this->getDepartamento($empleado->id_sys_adm_cargo);
        
        if($estado == 'N' && $model->id_sys_rrhh_cedula != Yii::$app->user->identity->cedula):
        
            if ($model->load(Yii::$app->request->post())){
        
                $transaction    = \Yii::$app->$db->beginTransaction();
                
                try {
                    
                    
                    if ($model->estado_solicitud == 'A'):
                    
                         if($periodo):
                         
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
                    $model->comentario        =   $model->comentario;
                    $model->usuario_anulacion =  Yii::$app->user->username;
                    $model->fecha_anulacion   =  date('Ymd H:i:s');
                    
                    if ($periodo):
                          $periodo->save(false);
                    endif;
                    
                    if($model->save(false)):
                    
                        if($model->tipo == 'G' || $model->tipo == 'A'):
                        
                            $cc      = [];
                            $to      = [];
                            $mensaje = "<p  style = 'margin:1px;'>La solicitud de vacaciones #".str_pad($model->id_sys_rrhh_vacaciones_solicitud, 10, "0", STR_PAD_LEFT)." del Sr/Sr(a): <b>".$empleado->nombres."</b> ha sido anulada.</p><p  style = 'margin:1px;'>Comentario: ".$model->comentario."</p>";
                            $titulo  = "Notificación de vacaciones";
                            
                            $mailempleado = $this->getEmailEmpleado($empleado->email);
                            
                            if($mailempleado != ""):
                                array_push($to, $mailempleado);
                            endif;
                            
                            $emailUser = $this->ObtenerUsuariosGruposAutorizacionXDepartamento($nivelempleado, $departamento->id_sys_adm_area, $departamento->id_sys_adm_departamento);
                            
                            foreach ($emailUser as $user):
                                 array_push($to, $user['email']);
                            endforeach;
 
                            $addCC = false;
                            $mailUserCreate =  $this->getEmailCreacion($model->usuario_transaccion);
                            
                            foreach ($to as $item):
                                if($item ==  $mailUserCreate):
                                    $addCC = true;
                                    break;
                                endif;
                            endforeach;
                            
                            if(!$addCC):
                                array_push($cc, $mailUserCreate);
                            endif;
                            
                            $transaction->commit();
 
                            $this->EnviarCorreo($to, $cc, $mensaje, $titulo, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, "", "P");
                            
                            
                            Yii::$app->getSession()->setFlash('info', [
                                'type' => 'success','duration' => 3000,
                                'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud de vacaciones ha sido anulada con éxito!',
                                'positonY' => 'top','positonX' => 'right']);
                            
       
                        elseif($model->tipo == 'P'):
                            
                            $cc      = [];
                            $to      = [];
                            $mensaje = "<p  style = 'margin:1px;'>La solicitud de vacaciones #".str_pad($model->id_sys_rrhh_vacaciones_solicitud, 10, "0", STR_PAD_LEFT)." del Sr/Sr(a): <b>".$empleado->nombres."</b> ha sido anulada.</p><p  style = 'margin:1px;'>Comentario: ".$model->comentario."</p>";
                            $titulo  = "Notificación de vacaciones";
                            
                            $mailempleado = $this->getEmailEmpleado($empleado->email);
                            
                            if($mailempleado != ""):
                                array_push($to, $mailempleado);
                            endif;
                            
                            $emailUser = $this->ObtenerUsuariosGrupoAutorizacionGerencia('SOLICITUD_VACACIONES');
                                      
                            $emailJefeDdoo = $this->ObtenerUsuarioJefeNomina();

                            foreach ($emailUser as $user):
                                array_push($to, $user['email']);
                            endforeach;

                            if($emailJefeDdoo != ""):
                                array_push($cc, $emailJefeDdoo['email']);
                            endif;
                           
                            $addCC = false;
                            $mailUserCreate =  $this->getEmailCreacion($model->usuario_transaccion);
                            
                            foreach ($to as $item):
                                if($item ==  $mailUserCreate):
                                    $addCC = true;
                                    break;
                                endif;
                            endforeach;
                            
                            if(!$addCC):
                                array_push($cc, $mailUserCreate);
                            endif;
                            
                            $transaction->commit();
 
                            $this->EnviarCorreo($to, $cc, $mensaje, $titulo, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, "", "P");
                            
                            
                            Yii::$app->getSession()->setFlash('info', [
                                'type' => 'success','duration' => 3000,
                                'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud de vacaciones ha sido anulada con éxito!',
                                'positonY' => 'top','positonX' => 'right']);
                            


                        endif;
                            
                    endif;
                    
                    
                    
                }catch (Exception $e) {
                    
                    $transaction->rollBack();
                    
                    Yii::$app->getSession()->setFlash('info', [
                        'type' => 'danger','duration' => 3000,
                        'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador!'.$e->getMessage(),
                        'positonY' => 'top','positonX' => 'right']);
           
                    
                }
                   
             return  $this->redirect('index');
     
        }
        return $this->render('delete', ['model'=> $model, 'update'=> 2]);

    
        
        else:
            
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'warning','duration' => 3000,
                'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud no se puede anular, porque no tiene permisos de anulacion!',
                'positonY' => 'top','positonX' => 'right']);
            
            return  $this->redirect('index');
        
       endif;
    }
    
    public function actionAprobarsolicitud($id_sys_rrhh_vacaciones_solicitud, $id_sys_empresa){
        
        //revisar los nivel en caso de enviar correos
        
        $db      = $_SESSION['db'];
        
        $empresa = SysEmpresa::find()->where(['db_name'=> $db])->one();
        
        $model   =  SysRrhhVacacionesSolicitud::find()->where(['id_sys_rrhh_vacaciones_solicitud'=> $id_sys_rrhh_vacaciones_solicitud])->andWhere(['id_sys_empresa'=> $id_sys_empresa])->one();
        
        $periodo = SysRrhhEmpleadosPeriodoVacaciones::find()->where(['id_sys_adm_periodo_vacaciones'=> $model->id_sys_rrhh_vacaciones_periodo])->andWhere(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> $model->id_sys_empresa])->one();
        
        //Obtenemos datos del empleados
        $empleado =  SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $model->id_sys_rrhh_cedula])->andWhere(['id_sys_empresa'=> '001'])->one();
        
        //nivel del empleado 
        $nivelempleado =   $this->getNivelcargo($empleado->id_sys_adm_cargo, $empleado->id_sys_empresa);
        
        $estado =  'P';
        
        $titulo = 'Notificación de vacaciones';
        
        //Validar si el documento necesita autorizacion
        $documento = $this->getDocumento('SOLICITUD_VACACIONES');
        
        $departamento = $this->getDepartamento($empleado->id_sys_adm_cargo);
        
        $username = Yii::$app->user->username;

        $grupo_autorizacion = $this->ObtenerGrupoAutorizacionUsername($username);

        $tipousuario  = $this->getTipoUsuario(Yii::$app->user->identity->id);
        
        if($documento):
        
            $autorizacion = $this->getAutorizacion($documento['id'], Yii::$app->user->identity->id);
            
            if($autorizacion):
            
                if(trim(Yii::$app->user->identity->cedula) != trim($empleado->id_sys_rrhh_cedula)):
                
                    if($model->tipo == 'P'):

                        if($tipousuario == 'G'):

                            $estado = 'A';
                        
                        endif;
                    
                    elseif($model->tipo == 'G' || $model->tipo == 'A'):
                    
                        if($tipousuario == 'D'):
                        
                                $estado = 'A';       
                        
                        else:

                            foreach($autorizacion as $aut0):
                                //Todos los departamentos
                                if(($aut0['id_sys_area']) == 0 && ($aut0['id_sys_departamento'] == 0)):
                                    
                                    $estado = $this->AprobarPermiso($aut0['nivel_autorizacion'], $nivelempleado);
                                    
                                elseif(($aut0['id_sys_area']) != 0 && ($aut0['id_sys_departamento'] == 0)):
                                
                                    //Area del empleado
                                    if(($aut0['id_sys_area'] == $departamento->id_sys_adm_area)):
                                    
                                    $estado = $this->AprobarPermiso($aut0['nivel_autorizacion'], $nivelempleado);
                                    
                                    endif;
                                
                                elseif(($aut0['id_sys_area']) != 0 && ($aut0['id_sys_departamento'] != 0)):
                                
                                    //Departamento del empleado
                                    if($aut0['id_sys_departamento'] == $departamento->id_sys_adm_departamento):
                                    
                                    $estado = $this->AprobarPermiso($aut0['nivel_autorizacion'], $nivelempleado);
                                    
                                    endif;
                                
                                endif;
                            endforeach;
                        
                        endif;
                    
                    endif;

                endif;
            
            endif;
        else:
            $estado = 'A';
        endif;
        
  
        $transaction  = \Yii::$app->$db->beginTransaction();
       
        $date1 = new \DateTime($model->fecha_inicio);
        $date2 = new \DateTime($model->fecha_fin);
        $diff  = $date1->diff($date2);
        // will output 2 days
        $dias  = $diff->days + 1;
        
        
        $diasOtorgados   = 0;
        $diasDisponibles = 0;
        
        if($periodo):
        
             $diasOtorgados = $periodo->dias_otorgados + intval($dias);
             $diasDisponibles =  $periodo->dias_disponibles;
        
        endif;
   
        if ($periodo):
        
            if($diasOtorgados == $diasDisponibles):
                
                $periodo->estado = 'T';
            
            endif;
        
        endif;
        
        if($diasOtorgados <= $diasDisponibles):
                
                if($model->estado_solicitud == 'P'):
                 
                        if($estado == 'A'):
                        
                                $model->estado_solicitud   = $estado;
                                $model->usuario_aprobacion =  Yii::$app->user->username;
                                $model->fecha_aprobacion   =  date('Ymd H:i:s');
                        
                                if ($periodo):
                                    $periodo->dias_otorgados = $diasOtorgados;
                                    $periodo->save(false);
        
                                endif;
                        
                                if($model->save(false)):
                                
                                    if($model->tipo == 'G'):
                                        
                                        $to = [];
                                        $cc = [];
                                        
                                        $mensaje = "<p  style = 'margin:1px;'>La solicitud de vacaciones #".str_pad($model->id_sys_rrhh_vacaciones_solicitud, 10, "0", STR_PAD_LEFT)." del Sr/Sr(a): <b>".$empleado->nombres."</b> ha sido aprobada.</p><p  style = 'margin:1px;'><b>Fecha inicio: </b>".$model->fecha_inicio."</p><p  style = 'margin:1px;'><b>Fecha culminación: </b>".$model->fecha_fin."</p><p  style = 'margin:1px;'><b>Fecha reintegro de labores: </b>".$this->fechafinalizacion($model->fecha_fin)."</p>";
                                        
                                        $mailUserCreate =  $this->getEmailCreacion($model->usuario_transaccion);
                                        
                                        if($mailUserCreate != ""):
                                             array_push($cc, $mailUserCreate);
                                        endif;
                                        
                                        $mailempleado = $this->getEmailEmpleado($empleado->email);
                                        
                                        if($mailempleado != ""):
                                             array_push($to, $mailempleado);        
                                        endif;
                                        
                                        
                                        $emailUser = $this->ObtenerUsuariosGruposAutorizacionXDepartamento($nivelempleado, $departamento->id_sys_adm_area, $departamento->id_sys_adm_departamento);
                                        
                                        foreach ($emailUser as $user):
                                            
                                            if($mailUserCreate != $user['email']):
                                                 array_push($to, $user['email']);
                                            endif;
                                            
                                        endforeach;
                                       
                                        $this->EnviarCorreo($to, $cc, $mensaje, $titulo, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, $documento['mail_notificaciones'], $estado);
                                        
                                    
                                    elseif($model->tipo == 'P'):

                                        $to = [];
                                        $cc = [];
                                        
                                        $mensaje = "<p  style = 'margin:1px;'>La solicitud de vacaciones #".str_pad($model->id_sys_rrhh_vacaciones_solicitud, 10, "0", STR_PAD_LEFT)." del Sr/Sr(a): <b>".$empleado->nombres."</b> ha sido aprobada.</p><p  style = 'margin:1px;'><b>Fecha inicio: </b>".$model->fecha_inicio."</p><p  style = 'margin:1px;'><b>Fecha culminación: </b>".$model->fecha_fin."</p><p  style = 'margin:1px;'><b><p>Vacaciones Pagadas</p>";
                                        
                                        $mailUserCreate =  $this->getEmailCreacion($model->usuario_transaccion);
                                        
                                        if($mailUserCreate != ""):
                                             array_push($cc, $mailUserCreate);
                                        endif;
                                        
                                        $mailempleado = $this->getEmailEmpleado($empleado->email);
                                        
                                        if($mailempleado != ""):
                                             array_push($to, $mailempleado);        
                                        endif;
                                        
                                        $emailUser = $this->ObtenerUsuariosGrupoAutorizacionGerencia($documento['documento']);
                                      
                                        $emailJefeDdoo = $this->ObtenerUsuarioJefeNomina();
        
                                        foreach ($emailUser as $user):
                                            array_push($to, $user['email']);
                                        endforeach;
        
                                        if($emailJefeDdoo != ""):
                                            array_push($cc, $emailJefeDdoo['email']);
                                        endif;
                                       
                                        $this->EnviarCorreo($to, $cc, $mensaje, $titulo, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, $documento['mail_notificaciones'], $estado);

                                    endif;
                                    
                                    $transaction->commit();
                                    
                                    Yii::$app->getSession()->setFlash('info', [
                                        'type' => 'success','duration' => 3000,
                                        'icon' => 'glyphicons glyphicons-robot','message' => 'La Solicitud ha sido aprobada con exito!',
                                        'positonY' => 'top','positonX' => 'right']);
                                    
                                endif;
                        
                            else:
                                
                                $transaction->rollBack();
                                Yii::$app->getSession()->setFlash('info', [
                                'type' => 'warning','duration' => 3000,
                                'icon' => 'glyphicons glyphicons-robot','message' => 'Usted no tiene permisos para aprobar la solicitud!',
                                'positonY' => 'top','positonX' => 'right']);
                                    
                            endif;
                            
                       else:
                            
                            $transaction->rollBack();
                            Yii::$app->getSession()->setFlash('info', [
                           'type' => 'warning','duration' => 3000,
                           'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud no se puede aprobar, porque no se encuentra pendiente!',
                           'positonY' => 'top','positonX' => 'right']);
                            
                       endif;
              else:
              
                if($diasOtorgados >= $diasDisponibles):
                
                    if($model->estado_solicitud == 'P'):
                     
                            if($estado == 'A'):
                            
                                    $model->estado_solicitud   = $estado;
                                    $model->usuario_aprobacion =  Yii::$app->user->username;
                                    $model->fecha_aprobacion   =  date('Ymd H:i:s');
                            
                                    if ($periodo):
                                        $periodo->dias_otorgados = $diasOtorgados;
                                        $periodo->save(false);
            
                                    endif;
                            
                                    if($model->save(false)):
                                    
                                        if($model->tipo == 'G'):
                                           
                                            $to = [];
                                            $cc = [];
                                            
                                            $mensaje = "<p  style = 'margin:1px;'>La solicitud de vacaciones #".str_pad($model->id_sys_rrhh_vacaciones_solicitud, 10, "0", STR_PAD_LEFT)." del Sr/Sr(a): <b>".$empleado->nombres."</b> ha sido aprobada.</p><p  style = 'margin:1px;'><b>Fecha inicio: </b>".$model->fecha_inicio."</p><p  style = 'margin:1px;'><b>Fecha culminación: </b>".$model->fecha_fin."</p><p  style = 'margin:1px;'><b>Fecha reintegro de labores: </b>".$this->fechafinalizacion($model->fecha_fin)."</p>";
                                            
                                            $mailUserCreate =  $this->getEmailCreacion($model->usuario_transaccion);
                                            
                                            if($mailUserCreate != ""):
                                                 array_push($cc, $mailUserCreate);
                                            endif;
                                            
                                            $mailempleado = $this->getEmailEmpleado($empleado->email);
                                            
                                            if($mailempleado != ""):
                                                 array_push($to, $mailempleado);        
                                            endif;
                                            
                                            
                                            $emailUser = $this->ObtenerUsuariosGruposAutorizacionXDepartamento($nivelempleado, $departamento->id_sys_adm_area, $departamento->id_sys_adm_departamento);
                                            
                                            foreach ($emailUser as $user):
                                                
                                                if($mailUserCreate != $user['email']):
                                                     array_push($to, $user['email']);
                                                endif;
                                                
                                            endforeach;
                                           
                                            $this->EnviarCorreo($to, $cc, $mensaje, $titulo, $empresa->mail_host, $empresa->mail_username, $empresa->mail_password, $empresa->mail_port, $empresa->razon_social, $documento['mail_notificaciones'], $estado);
                                            
                                            
                                        endif;
                                        
                                        $transaction->commit();
                                        
                                        Yii::$app->getSession()->setFlash('info', [
                                            'type' => 'success','duration' => 3000,
                                            'icon' => 'glyphicons glyphicons-robot','message' => 'La Solicitud ha sido aprobada con exito!',
                                            'positonY' => 'top','positonX' => 'right']);
                                        
                                    endif;
                            
                                else:
                                    
                                    $transaction->rollBack();
                                    Yii::$app->getSession()->setFlash('info', [
                                    'type' => 'warning','duration' => 3000,
                                    'icon' => 'glyphicons glyphicons-robot','message' => 'Usted no tiene permisos para aprobar la solicitud!',
                                    'positonY' => 'top','positonX' => 'right']);
                                        
                                endif;
                                
                           else:
                                
                                $transaction->rollBack();
                                Yii::$app->getSession()->setFlash('info', [
                               'type' => 'warning','duration' => 3000,
                               'icon' => 'glyphicons glyphicons-robot','message' => 'La solicitud no se puede aprobar, porque no se encuentra pendiente!',
                               'positonY' => 'top','positonX' => 'right']);
                                
                           endif;
                  else:
                  
                  
                      $transaction->rollBack();
                      Yii::$app->getSession()->setFlash('info', [
                      'type' => 'warning','duration' => 3000,
                      'icon' => 'glyphicons glyphicons-robot','message' => 'El número de días a gozar  es mayor a los dias disponibles del periodo. Revisar el periodo del empleado!',
                      'positonY' => 'top','positonX' => 'right']);
                  
            endif;
              
        endif;
        
        return $this->redirect(['index']);
    }
        
    public function actionListperiodos($id_sys_rrhh_cedula)
    {
        
        $datos= [];
        
        if(trim($id_sys_rrhh_cedula) != ''):
    
                 $datos = (new \yii\db\Query())->select(['a.id_sys_adm_periodo_vacaciones', 'dias_disponibles', 'dias_otorgados', 'estado', 'periodo', '(dias_disponibles - dias_otorgados) as dias_pendientes'])
                ->from('sys_rrhh_empleados_periodo_vacaciones a')
                ->innerjoin('sys_adm_periodo_vacaciones b', 'a.id_sys_adm_periodo_vacaciones  = b.id_sys_adm_periodo_vacaciones')
                ->where("a.id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")
                ->andwhere("a.id_sys_empresa= '001'")
                ->orderby('anio_vac desc')
                ->all(SysRrhhEmpleadosPeriodoVacaciones::getDb());
  
        endif;
        
       return json_encode($datos);
    }
    
    private function getNivelcargo($codcargo, $codempresa){
        
        $cargo =  SysAdmCargos::find()->where(['id_sys_adm_cargo'=> $codcargo])->andWhere(['id_sys_empresa'=> $codempresa])->one();
        
        if($cargo){
            
            $mando = sysAdmMandos::find()->where(['id_sys_adm_mando'=>$cargo->id_sys_adm_mando])->andWhere(['id_sys_empresa'=> $cargo->id_sys_empresa])->one();
            
            return $mando->nivel;
            
        }
        return 0; 
    }
    
    private function  getEmaiUser($id){        

        $user  = User::find()->where(['id'=> $id])->one();
        
        if($user):
        
            return  trim($user->email);
        else:
                $empleado      =  SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $id])->one();
        
               if($empleado):
               
                 return trim($empleado->email);
               
               endif;
        endif;
        
         return "";
        
        
    }

    private function getTipoUsuario($id_usuario){
        
        $usertipo = SysAdmUsuariosDep::find()->where(['id_usuario'=> $id_usuario])->andwhere(['estado'=> 'A'])->one();
        
        if($usertipo):
        
        return $usertipo->usuario_tipo;
        
        endif;
        
        return 'N';
        
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
                 
                     $email    = $emailEmpleado;
                 
                 endif;
         
         endif;
        
         return $email;
        
    }
    //obtener usuario gerente
 
    private  function fechafinalizacion($fechafin){        
        return    $fechafin = date("Y-m-d", strtotime($fechafin . " + 1 day"));
        
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
  
    private function getDepartamento($id_sys_adm_cargo){
        
        //cargo usuario
        $cargo =  SysAdmCargos::find()->where(['id_sys_adm_cargo'=> $id_sys_adm_cargo])->one();
        //departamento usuario
        return  SysAdmDepartamentos::find()->where(['id_sys_adm_departamento'=> $cargo->id_sys_adm_departamento])->one();
        
        
    }
    
    private function getDocumento($codigo){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerDocumentoAutorizacion]  @codigo = '{$codigo}'")->queryOne();
    }
    
    private function getAutorizacion($id_sys_documento, $id_usuario){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerAutorizacionDocumentoUsuario]  @id_sys_documento = {$id_sys_documento}, @id_usuario = {$id_usuario}")->queryAll();
        
    }
    
    private function ObtenerUsuariosGruposAutorizacionXDepartamento($nivel_empleado, $id_sys_area, $id_sys_departamento){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerUsuariosGruposAutorizacionXDeparamento]  @nivel_empleado = {$nivel_empleado}, @id_sys_area = {$id_sys_area}, @id_sys_departamento = {$id_sys_departamento}")->queryAll();
    }

    private function ObtenerUsuariosGrupoAutorizacionGerencia($documento){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerUsuariosGrupoAutorizacionGerencia]  @id_grupo_autorizacion = 1, @documento = {$documento}")->queryAll();
    }

    private function ObtenerGrupoAutorizacionUsername($username){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerGrupoAutorizacionUsername] @username = {$username}")->queryOne();
    }

    private function    ObtenerUsuarioJefeNomina(){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerJefeNomina]")->queryOne();
    }
    
    private  function AprobarPermiso($nivelUsuario, $nivelEmpleado){
        
        $estado = "P";
        
        if($nivelUsuario == 1):
        
           $estado = "A";
        
        elseif($nivelUsuario == 2 || $nivelUsuario == 3):
        
            if($nivelEmpleado > 2):
                 $estado = "A";
            endif;
        
        endif;
        
        return $estado;
    }
    
    public function actionUltimafechaculminacionvacaciones($id_sys_rrhh_cedula){
        
        $db =  $_SESSION['db'];     
        echo json_encode(['ultima_fecha_culminacion_vacaciones' => Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerUltimaFechaCulminacionVacaciones]  @id_sys_rrhh_cedula = {$id_sys_rrhh_cedula}")->queryScalar()]);
    }

    public function actionSolicitudespendientes($id_sys_rrhh_cedula){
        
        $db =  $_SESSION['db'];     
        echo json_encode(Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerSolicitudesPendientesVacaciones]  @id_sys_rrhh_cedula = {$id_sys_rrhh_cedula}")->queryOne());
    }

    public function actionSolicitudespendientesempleados($id_sys_rrhh_cedula,$id_adm_periodo){
        
        $db =  $_SESSION['db'];     
        echo json_encode(Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerSolicitudesPendientesVacacionesEmpleados]  @id_sys_rrhh_cedula = {$id_sys_rrhh_cedula}, @id_adm_periodo = {$id_adm_periodo}")->queryAll());
    }
    
    private function getEmailCreacion($username){
        
        $user  = User::find()->where(['username'=> $username])->one();
        
        return  $user != null ? trim($user->email) : "";
        
    }
    /**
     * Finds the SysRrhhVacacionesSolicitud model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_sys_rrhh_vacaciones_solicitud
     * @param string $id_sys_empresa
     * @return SysRrhhVacacionesSolicitud the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id_sys_rrhh_vacaciones_solicitud, $id_sys_empresa)
    {
        if (($model = SysRrhhVacacionesSolicitud::findOne(['id_sys_rrhh_vacaciones_solicitud' => $id_sys_rrhh_vacaciones_solicitud, 'id_sys_empresa' => $id_sys_empresa])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
