<?php

namespace app\controllers;

use Yii;
use app\models\SysRrhhEmpleados;
use app\models\SysSsooIncidente;
use app\models\search\SysSsooIncidenteSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use app\models\SysMedConsultaMedica;
use app\models\SysAdmCargos;
use Mpdf\Mpdf;
use app\models\SysAdmAreas;
use kartik\mpdf\Pdf;

/**
 * IncidenteController implements the CRUD actions for SysSsooIncidente model.
 */
class IncidenteController extends Controller
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
     * Lists all SysSsooIncidente models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = '@app/views/layouts/main_emplados';
        $searchModel = new SysSsooIncidenteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysSsooIncidente model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        ini_set("pcre.backtrack_limit", "5000000");
        
        $model =  $this->findModel($id);
        $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=>  $model->id_sys_rrhh_cedula])->one();
        $cargo = SysAdmCargos::find()->where(['id_sys_adm_cargo'=> $empleado->id_sys_adm_cargo])->one();
        $area = SysAdmAreas::find()->where(['id_sys_adm_area' => $model->id_sys_adm_area])->one();
        
        $path= $model->adjunto;
        $base64 = "";
        
        if($model->adjunto != ""):
        
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path); 
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            
        endif;
        
        $html =    $this->renderPartial('view',[
            'model' => $model,
            'empleado' => $empleado,
            'cargo' => $cargo,
            'area' => $area,
            'base64' => $base64
        ]);
        
        
        
        
       /* $footer = "<table name='footer' width=\"1000\">
           <tr>
             <td style='font-size: 10px; padding-bottom: 20px;' align=\"left\"></td><td style='font-size: 18px; padding-bottom: 15px;' align=\"right\"><b>{$model->codigo}</b></td>
           </tr>
         </table>";
        
        $mpdf = new Mpdf([
            'format' => 'A4',
            // 'orientation' => 'L'
            'line' => 0, // That's the relevant parameter
        ]);
        $mpdf->SetFooter($footer);
        $mpdf->WriteHTML($html);
        
        $mpdf->Output('InformeIncidente.pdf', 'I');
        exit();
        */
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
            'marginTop' => 20,
            'marginBottom' => 20,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}  .title{ font-size: 14px;} .negrita { font-weight: bold;} .subtitle{ font-size: 12px;} .text-center { text-align: center; } .text-right { text-align: right;} .text-left { text-align: left; } table { border-collapse: collapse;}',
            
            // set mPDF properties on the fly
            //'options' => ['title' => 'Solicitud de Vacaciones'],
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' =>  'Control de Incidentes',
               // 'SetHeader'=>['Sistema Gestión de Nómina - Control de Incidentes||'],
                'SetFooter' => '||'.$model->codigo
            ]
        ]);
        
        // return the pdf output as per the destination setting
        return $pdf->render(); 
       
    }
    /**
     * Creates a new SysSsooIncidente model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $this->layout = '@app/views/layouts/main_emplados';
        $model = new SysSsooIncidente();
    
        $secuencial = SysSsooIncidente::find()->select("max(secuencial)")->scalar() + 1 ;
        
        $model->secuencial = $secuencial;
        

        if ($model->load(Yii::$app->request->post())) {
            
            
            $model->file =  UploadedFile::getInstance($model, 'file');
           
            if($model->file):
            
                    $ruta =  "C:/ssoo/incidentes/".$model->id_sys_rrhh_cedula.'_'.date('Y-m-d').'.'.$model->file->extension;
                    $model->file->saveAs($ruta);
                    $model->adjunto = $ruta;
            
            endif;
            
            
            $model->secuencial = $secuencial;
            $model->codigo = "PSCA-M-SSO-16-P7-F3";
            $model->fecha = date('Ymd H:i:s', strtotime($model->fecha));
            $model->puesto_trabajo = $model->puesto_trabajo != null ? strtoupper($model->puesto_trabajo) : 'N/A';
            $model->lugar =  $model->lugar != null ? strtoupper($model->lugar) : 'N/A';
            $model->lesion_corporal =  $model->lesion_corporal != null ? strtoupper($model->lesion_corporal) : 'N/A';
            $model->danio_maquinaria = $model->danio_maquinaria != null ? strtoupper($model->danio_maquinaria) : 'N/A';
            $model->danio_instalaciones = $model->danio_instalaciones != null ? strtoupper($model->danio_instalaciones) : 'N/A';
            $model->danio_epp = $model->danio_epp != null ? strtoupper($model->danio_epp) : 'N/A';
            $model->observacion = $model->observacion != null ? strtoupper($model->observacion) : 'N/A';
            $model->descripcion_incidente = $model->descripcion_incidente != null  ? strtoupper($model->descripcion_incidente) : 'N/A';
            $model->analisis_problema = $model->analisis_problema != null ? strtoupper($model->analisis_problema) : 'N/A';
            $model->correcion = $model->correcion != null ? strtoupper($model->correcion): 'N/A';
            $model->accion_preventiva = $model->accion_preventiva != null ? strtoupper($model->accion_preventiva): 'N/A';
            $model->usuario_creacion = Yii::$app->user->username;
            $model->fecha_creacion = date('Ymd H:i:s');
            $model->notifica_incidente_nombre = $model->notifica_incidente_nombre !=null ? strtoupper($model->notifica_incidente_nombre): 'N/A';
            $model->notifica_incidente_cargo = $model->notifica_incidente_cargo != null ? strtoupper($model->notifica_incidente_cargo) : 'N/A';
            
            $model->anulado = 0;
            $model->save();
            
            if($model->save(false)){
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'success','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos han sido registrados  con éxito!',
                    'positonY' => 'top','positonX' => 'right']);
            }
            else{
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                    'positonY' => 'top','positonX' => 'right']);
            }
            return $this->redirect('index');
            
           
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }
   
    public  function actionObtenerempleado(){
       
        $value = Yii::$app->request->get('q','');      
        $datos = SysRrhhEmpleados::find()->select(['id_sys_rrhh_cedula as value', 'nombres'])
        ->where(['or',
            ['like','id_sys_rrhh_cedula',$value.'%',false],
            ['like','nombres',$value.'%',false]])
            ->andWhere(['sys_rrhh_empleados.estado'=> 'A'])
            ->orderBy(['nombres'=>SORT_ASC])
            ->asArray()
            ->limit(5)
            ->all();
        
            return json_encode($datos);
    }
    
    public function actionObtenerprescripcionmedica($id_consulta_medica){
        
        $datos = SysMedConsultaMedica::find()
        ->where(['id'=> $id_consulta_medica])
        ->asArray()
        ->all();
        
        return json_encode($datos);
        
    }
    
    public  function actionObtenerconsulta(){
        
        $value = Yii::$app->request->get('q','');
        $datos = SysMedConsultaMedica::find()->select(['id as value', 'numero'])
        ->where(['like','numero',$value.'%',false])
        //->andWhere(['tipo' => 'I'])
        ->orderBy(['numero'=>SORT_ASC])
        ->asArray()
        ->limit(5)
        ->all();
            
        return json_encode($datos);
    }
    /**
     * Updates an existing SysSsooIncidente model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $this->layout = '@app/views/layouts/main_emplados';
        $model = $this->findModel($id);
        
        $num_consulta = SysMedConsultaMedica::find()->select("numero")->where(['id'=> $model->id_sys_med_consulta_medica])->scalar();

        $model->numero_consulta = $num_consulta;
        
        
        
        if ($model->load(Yii::$app->request->post())) {
            
            
            $model->file =  UploadedFile::getInstance($model, 'file');
            
            if($model->file):
            
                $ruta =  "C:/ssoo/incidentes/".$model->id_sys_rrhh_cedula.'_'.date('Y-m-d').'.'.$model->file->extension;
                $model->file->saveAs($ruta);
                $model->adjunto = $ruta;
            
            endif;
            
            
            $model->fecha = date('Ymd H:i:s', strtotime($model->fecha));
            $model->puesto_trabajo = $model->puesto_trabajo != null ? strtoupper($model->puesto_trabajo) : 'N/A';
            $model->lugar =  $model->lugar != null ? strtoupper($model->lugar) : 'N/A';
            $model->lesion_corporal =  $model->lesion_corporal != null ? strtoupper($model->lesion_corporal) : 'N/A';
            $model->danio_maquinaria = $model->danio_maquinaria != null ? strtoupper($model->danio_maquinaria) : 'N/A';
            $model->danio_instalaciones = $model->danio_instalaciones != null ? strtoupper($model->danio_instalaciones) : 'N/A';
            $model->danio_epp = $model->danio_epp != null ? strtoupper($model->danio_epp) : 'N/A';
            $model->observacion = $model->observacion != null ? strtoupper($model->observacion) : 'N/A';
            $model->descripcion_incidente = $model->descripcion_incidente != null  ? strtoupper($model->descripcion_incidente) : 'N/A';
            $model->analisis_problema = $model->analisis_problema != null ? strtoupper($model->analisis_problema) : 'N/A';
            $model->correcion = $model->correcion != null ? strtoupper($model->correcion): 'N/A';
            $model->accion_preventiva = $model->accion_preventiva != null ? strtoupper($model->accion_preventiva): 'N/A';
            $model->usuario_actualizacion = Yii::$app->user->username;
            $model->fecha_actualizacion = date('Ymd H:i:s');
            $model->notifica_incidente_nombre = $model->notifica_incidente_nombre !=null ? strtoupper($model->notifica_incidente_nombre): 'N/A';
            $model->notifica_incidente_cargo = $model->notifica_incidente_cargo != null ? strtoupper($model->notifica_incidente_cargo) : 'N/A';
          
            if($model->save(false)){
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'success','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos han sido actualizados con éxito!',
                    'positonY' => 'top','positonX' => 'right']);
            }
            else{
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                    'positonY' => 'top','positonX' => 'right']);
            }
            return $this->redirect('index');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SysSsooIncidente model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->anulado = 1;
        
        if($model->save(false)):
        
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'success','duration' => 1500,
                'icon' => 'glyphicons glyphicons-robot','message' => 'El documento ha sido anulado con éxito!',
                'positonY' => 'top','positonX' => 'right']);
        
        else:
        
            Yii::$app->getSession()->setFlash('info', [
                'type' => 'danger','duration' => 1500,
                'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                'positonY' => 'top','positonX' => 'right']);
        
        endif;
        
        

        return $this->redirect(['index']);
    }

    /**
     * Finds the SysSsooIncidente model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SysSsooIncidente the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysSsooIncidente::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
