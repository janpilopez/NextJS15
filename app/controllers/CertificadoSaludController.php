<?php

namespace app\controllers;

use Yii;
use app\models\SysMedCertificadoSalud;
use app\models\SysRrhhEmpleados;
use app\models\search\SysMedCertificadoSaludSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Mpdf\Mpdf;
use kartik\mpdf\Pdf;
/**
 * CertificadoSaludController implements the CRUD actions for SysMedCertificadoSalud model.
 */
class CertificadoSaludController extends Controller
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
     * Lists all SysMedCertificadoSalud models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = '@app/views/layouts/main_emplados';
        $searchModel = new SysMedCertificadoSaludSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysMedCertificadoSalud model.
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
     * Creates a new SysMedCertificadoSalud model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysMedCertificadoSalud();

        if ($model->load(Yii::$app->request->post())) {
            
            
            $model->usuario_creacion = Yii::$app->user->username;          
            $model->fecha_creacion = date('Ymd H:i:s');
            $model->fecha_vencimiento = date("Y-m-d", strtotime($model->fecha_emision."+ 1 year"));;
           
      
           
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

    /**
     * Updates an existing SysMedCertificadoSalud model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            
          
            $model->usuario_actualizacion = Yii::$app->user->username;
            $model->fecha_actualizacion = date('Ymd H:i:s');
            
            $model->fecha_vencimiento = date("Y-m-d", strtotime($model->fecha_emision."+ 1 year"));
            
             
           if($model->save(false)){
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'success','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Los datos han sido actualizados  con éxito!',
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

    
    public function actionCertificadosvencidos($anio=null, $mes=null){
        
        $anio = date('Y');
        $mes  =  date('n');
        $datos = [];
        
        $this->layout = '@app/views/layouts/main_emplados';
        
        if(Yii::$app->request->post()):
            
            $anio = $_POST['anio'] == null ? date('Y') : $_POST['anio'];
            $mes  = $_POST['mes'] == null ? date('n') : $_POST['mes'];
            
            $datos = $this->getCertificadosVencidos($anio, $mes);
        
        
        endif;
        
        return $this->render('certificados-vencidos',['datos'=> $datos,'anio'=> $anio, 'mes'=> $mes]) ;
        
    }
    public function actionCertificadosvencidospdf($anio=null, $mes=null){
        
        $paramentroAnio = $anio == null ? date('Y'): $anio;
        $paramentroMes  = $mes  == null ? date('n'): $mes;
        
        $datos = $this->getCertificadosVencidos($paramentroAnio, $paramentroMes);
        
        $html =  $this->renderPartial('certificados-medicospdf',['datos'=> $datos, 'anio'=> $paramentroAnio, 'mes'=> $paramentroMes]);
        
       /* $footer = "<table name='footer' width=\"1000\">
           <tr>
             <td style='font-size: 10px; padding-bottom: 20px;' align=\"left\"><b>Usuario Impresión</b> : ".Yii::$app->user->identity->username." ".date('d/m/Y H:i:s')."</td><td style='font-size: 18px; padding-bottom: 15px;' align=\"right\">{PAGENO}</td>
           </tr>
         </table>";
        
        $mpdf = new Mpdf([
            'format' => 'A4',
            // 'orientation' => 'L'
        ]);
        $mpdf->SetFooter($footer);
        $mpdf->WriteHTML($html);
        
        $mpdf->Output('CertificadosVencidos.pdf', 'I');
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
            'cssInline' => '.kv-heading-1{font-size:18px}   .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 { border:0; padding:0; margin-left:-0.00001; } .fuente_table { font-size: 8px; } .title{ font-size: 16px;} .negrita { font-weight: bold;} .subtitle{ font-size: 12px;} table { border-collapse: collapse;}',
            
            // set mPDF properties on the fly
            //'options' => ['title' => 'Solicitud de Vacaciones'],
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' =>  'Atenciones Médicas',
                'SetHeader'=>['Sistema Gestión de Nómina - Certificados de Salud Vencidos||'],
                'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}']
            ]
        ]);
        // return the pdf output as per the destination setting
        return $pdf->render(); 
        
    }
    
    private function getCertificadosVencidos($anio, $mes){
        
        $db    = $_SESSION['db'];
        return Yii::$app->$db->createCommand("EXEC dbo.[MedObtenerCertificadosMedicosVencidos] @anio = '{$anio}', @mes= '{$mes}'")->queryAll();
        
    }
    
    /**
     * Deletes an existing SysMedCertificadoSalud model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
     
        $model = $this->findModel($id);
        $model->anulado = 1;
        $model->save();
        
        return $this->redirect(['index']);
    }

    /**
     * Finds the SysMedCertificadoSalud model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SysMedCertificadoSalud the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysMedCertificadoSalud::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
