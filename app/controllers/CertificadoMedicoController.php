<?php

namespace app\controllers;

use Yii;
use app\models\SysEmpresa;
use app\models\SysMedCertificadoMedico;
use app\models\search\SysMedCertificadoMedicoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use app\models\SysRrhhEmpleadosPermisos;
use app\models\SysRrhhPermisos;
use app\models\SysAdmUsuariosDep;
use app\models\SysAdmUsuariosPer;

/**
 * CertificadoMedicoController implements the CRUD actions for SysMedCertficadoMedico model.
 */
class CertificadoMedicoController extends Controller
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
     * Lists all SysMedCertficadoMedico models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = '@app/views/layouts/main_emplados';
        $searchModel = new SysMedCertificadoMedicoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysMedCertficadoMedico model.
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
     * Creates a new SysMedCertficadoMedico model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysMedCertificadoMedico();

        $db = $_SESSION['db'];

        $tipousuario  = $this->getTipoUsuario(Yii::$app->user->identity->id);
        
        $listpermisos = [];
        
        $permisos = SysAdmUsuariosPer::find()->where(['usuario_tipo'=> $tipousuario])->all();

        foreach($permisos as $index){

            if($index['permiso'] != ''){

                if($index['estado'] == 'A'):

                    array_push($listpermisos, $index['permiso']);

                endif;

            }else{

                if($index['estado'] == 'A'):

                    $listpermisos =  SysRrhhPermisos::find()->select('id_sys_rrhh_permiso')->where(['estado'=>'A'])->column();
            
                endif;
            
            }

        }        
          
        if ($model->load(Yii::$app->request->post())) {
            
            
            $model->usuario_creacion = Yii::$app->user->username;
            $model->fecha_creacion = date('Ymd H:i:s');
            $model->diagnostico =  strtoupper($model->diagnostico);
            $model->anulado = 0;
            if($model->tipo == 'D'):
              
                $model->fecha_ini = date('Ymd', strtotime($model->fecha_ini));
                $model->fecha_fin = date('Ymd', strtotime($model->fecha_fin));
               
            else:
            
                $model->fecha_ini = date('Ymd H:i', strtotime($model->fecha_ini));
                $model->fecha_fin = date('Ymd H:i', strtotime($model->fecha_fin));
                
            endif;
              
            $codpermiso =  SysRrhhEmpleadosPermisos::find()->select(['max(CAST(id_sys_rrhh_empleados_permiso AS INT))'])->Where(['id_sys_empresa'=> '001'])->scalar();
            $codnuevo = $codpermiso + 1;
            $id_sys_rrhh_cedula = $model->id_sys_rrhh_cedula;
            $transaccion_usuario =  Yii::$app->user->username;
            $hora_ini  = date('H:i:s', strtotime($model->fecha_ini));
            $fecha_ini = date('Y-m-d', strtotime($model->fecha_ini));
            $hora_fin  = date('H:i:s', strtotime($model->fecha_fin));
            $fecha_fin = date('Y-m-d', strtotime($model->fecha_fin));
            $tipo = $model->tipo == 'H' ? 'P' : 'C';
            $comentario          = $model->diagnostico;

            $permiso = $this->Obtenerdatospermisos($model->id_sys_rrhh_cedula,$model->id_sys_rrhh_permiso,$model->fecha_ini);

            if($permiso):
                if($permiso['id_sys_rrhh_permiso'] != $model->id_sys_rrhh_permiso && $permiso['fecha_ini'] != $fecha_ini && $permiso['fecha_fin'] != $fecha_fin):

                    Yii::$app->$db->createCommand("EXEC [dbo].[GenerarPermisoMedicoEmpleado] @codnuevo = {$codnuevo},@id_sys_rrhh_permiso= {$model->id_sys_rrhh_permiso}, @fecha_ini= '{$fecha_ini}',
                    @fecha_fin='{$fecha_fin}',@comentario ='{$comentario}',@id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}',@username='{$transaccion_usuario}',@hora_ini='{$hora_ini}',@hora_fin='{$hora_fin}',
                    @estado = 'A',@tipo = '{$tipo}', @userapro='{$transaccion_usuario}'")->execute();
                
                endif;
            else:
                Yii::$app->$db->createCommand("EXEC [dbo].[GenerarPermisoMedicoEmpleado] @codnuevo = {$codnuevo},@id_sys_rrhh_permiso= {$model->id_sys_rrhh_permiso}, @fecha_ini= '{$fecha_ini}',
                @fecha_fin='{$fecha_fin}',@comentario ='{$comentario}',@id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}',@username='{$transaccion_usuario}',@hora_ini='{$hora_ini}',@hora_fin='{$hora_fin}',
                @estado = 'A',@tipo = '{$tipo}', @userapro='{$transaccion_usuario}'")->execute();
            endif;

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
            'inputDisable' => false,
            'listpermisos'=> $listpermisos,
        ]);
    }

    /**
     * Updates an existing SysMedCertficadoMedico model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $db = $_SESSION['db'];

        $tipousuario  = $this->getTipoUsuario(Yii::$app->user->identity->id);
        
        $listpermisos = [];
        
        $permisos = SysAdmUsuariosPer::find()->where(['usuario_tipo'=> $tipousuario])->all();

        foreach($permisos as $index){

            if($index['permiso'] != ''){

                if($index['estado'] == 'A'):

                    array_push($listpermisos, $index['permiso']);

                endif;

            }else{

                if($index['estado'] == 'A'):

                    $listpermisos =  SysRrhhPermisos::find()->select('id_sys_rrhh_permiso')->where(['estado'=>'A'])->column();
            
                endif;
            
            }

        }  

        $id_sys_rrhh_cedula = $model->id_sys_rrhh_cedula;
        $hora_ini  = date('H:i:s', strtotime($model->fecha_ini));
        $fecha_ini = date('Y-m-d', strtotime($model->fecha_ini));
        $hora_fin  = date('H:i:s', strtotime($model->fecha_fin));
        $fecha_fin = date('Y-m-d', strtotime($model->fecha_fin));

        if ($model->load(Yii::$app->request->post())) {
            
            $model->usuario_actualizacion = Yii::$app->user->username;
            $model->fecha_actualizacion = date('Ymd H:i:s');
            $model->diagnostico =  strtoupper($model->diagnostico);

            $id_permiso = SysRrhhEmpleadosPermisos::find()->select('id_sys_rrhh_empleados_permiso')->where(['id_sys_rrhh_cedula'=>$id_sys_rrhh_cedula])->andWhere(['fecha_ini'=>$fecha_ini])
            ->andWhere(['fecha_fin'=>$fecha_fin])->andWhere(['hora_ini'=>$hora_ini])->andWhere(['hora_fin'=>$hora_fin])->scalar();

            if($model->tipo == 'D'):
            
                $model->fecha_ini = date('Ymd', strtotime($model->fecha_ini));
                $model->fecha_fin = date('Ymd', strtotime($model->fecha_fin));
                    
            else:
            
                $model->fecha_ini = date('Ymd H:i', strtotime($model->fecha_ini));
                $model->fecha_fin = date('Ymd H:i', strtotime($model->fecha_fin));
                
            endif;

            $id_sys_rrhh_cedula = $model->id_sys_rrhh_cedula;
            $hora_ini  = date('H:i:s', strtotime($model->fecha_ini));
            $fecha_ini = date('Y-m-d', strtotime($model->fecha_ini));
            $hora_fin  = date('H:i:s', strtotime($model->fecha_fin));
            $fecha_fin = date('Y-m-d', strtotime($model->fecha_fin));
            $tipo = $model->tipo == 'H' ? 'P' : 'C';
            $comentario          = $model->diagnostico;

            Yii::$app->$db->createCommand("EXEC [dbo].[ActualizarPermisoMedicoEmpleado] @id_sys_rrhh_permiso= {$model->id_sys_rrhh_permiso}, @fecha_ini= '{$fecha_ini}',
            @fecha_fin='{$fecha_fin}',@comentario ='{$comentario}',@id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}',@hora_ini='{$hora_ini}',@hora_fin='{$hora_fin}',
            @tipo = '{$tipo}', @id_sys_rrhh_empleados_permiso = '{$id_permiso}'")->execute();
            
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
            'inputDisable' => true,
            'listpermisos'=> $listpermisos,
        ]);
    }

    
    private function getTipoUsuario($id_usuario){        
        
        $usertipo = SysAdmUsuariosDep::find()->where(['id_usuario'=> $id_usuario])->andwhere(['estado'=> 'A'])->one();
       
       if($usertipo):
        
            return $usertipo->usuario_tipo;
        
        endif;
        
       return 'N';    
     
    }

    public  function actionInforme(){
        
        
        $anio = date('Y');
        $mes  =  date('n');
        $datos = [];
        
        $this->layout = '@app/views/layouts/main_emplados';
        
        if(Yii::$app->request->post()):
        
        $anio = $_POST['anio'] == null ? date('Y') : $_POST['anio'];
        $mes  = $_POST['mes'] == null ? date('n') : $_POST['mes'];
        
        $datos = $this->getCertificadosMedicos($anio, $mes);
        
        
        endif;
        
        return $this->render('info-certificados',['datos'=> $datos,'anio'=> $anio, 'mes'=> $mes]) ;
        
    }
    
    public  function actionInformexls($anio=null, $mes = null){
        
        
         $empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();
         
         
         $meses =  Yii::$app->params['meses'];
        
         $datos = $this->getCertificadosMedicos($anio, $mes);
        
         
         $objPHPExcel =  new Spreadsheet();
         
         $titulo= "Listado de Certificados Médicos";
         
         $objPHPExcel->getProperties()
         ->setCreator("Gestion")
         ->setLastModifiedBy("Gestion")
         ->setTitle($titulo)
         ->setSubject($titulo);
         
         
         $hojita = $objPHPExcel->getActiveSheet();
         $hojita->setTitle($titulo);
         $hojita->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
         $hojita->getPageMargins()->setRight(0.30);
         $hojita->getPageMargins()->setLeft(0.30);
         
         $imagenLogo = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
         $imagenLogo->setName('Logo');
         $imagenLogo->setDescription('pespesca');
         $imagenLogo->setPath('logo/'.$empresa->ruc.'/'.$empresa->logo);
         $imagenLogo->setCoordinates('B1');
         $imagenLogo->setWidthAndHeight(250,250);
         $imagenLogo->setWorksheet($hojita);
         
         $hojita->setCellValue('A5', "Area");
         $hojita->getStyle('A5')->getFont()->setSize(12);
         
         $hojita->setCellValue('B5', "Departamento");
         $hojita->getStyle('B5')->getFont()->setSize(12);
         
         $hojita->setCellValue('C5', "Cédula");
         $hojita->getStyle('C5')->getFont()->setSize(12);
         
         $hojita->setCellValue('D5', "Nombres");
         $hojita->getStyle('D5')->getFont()->setSize(12);
         
         $hojita->setCellValue('E5', "Fecha Inicio");
         $hojita->getStyle('E5')->getFont()->setSize(12);
         
         $hojita->setCellValue('F5', "Fecha Fin");
         $hojita->getStyle('F5')->getFont()->setSize(12);
         
         $hojita->setCellValue('G5', "Ausentismo");
         $hojita->getStyle('G5')->getFont()->setSize(12);
         
         $hojita->setCellValue('H5', "Diagnóstico");
         $hojita->getStyle('H5')->getFont()->setSize(12);
         
         $hojita->setCellValue('I5', "Entidad Emisora");
         $hojita->getStyle('I5')->getFont()->setSize(12);
         
    
         $hojita->getStyle('A5:I5')->getFont()->setBold(true);
         $hojita->getStyle('A5:I5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
         
         $hojita->setAutoFilter("A5:I5");
         
         if ($datos):
         
             $i = 5;
             foreach ($datos  as $data):
             
                 $i++;
                 $hojita->setCellValue('A'.$i,  $data['area']);
                 $hojita->setCellValue('B'.$i,  $data['departamento']);
                 $hojita->setCellValue('C'.$i,  $data['identificacion']);
                 $hojita->setCellValue('D'.$i,  $data['nombres']);
                 $hojita->setCellValue('E'.$i,  $data['inicio']);
                 $hojita->setCellValue('F'.$i,  $data['fin']);
                 $hojita->setCellValue('G'.$i,  $data['ausentismo']);
                 $hojita->setCellValue('H'.$i,  $data['diagnostico']);
                 $hojita->setCellValue('I'.$i,  $data['entidad']);
           
             endforeach;  
             
             foreach(range('A','I') as $columnID) {
                 $hojita->getColumnDimension($columnID)->setAutoSize(true);
             }
             
             $hojita->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(25,25);
             
             
         endif;
         
         $nombreArchivo='Certificados_medicos_'.$meses[$mes].'_'.$anio.'.xlsx';
         
         $writer = new Xlsx($objPHPExcel);
         $writer->save($nombreArchivo);
         $objPHPExcel->disconnectWorksheets();
         unset($objPHPExcel);
         header("Location: ".Yii::$app->getUrlManager()->getBaseUrl()."/".$nombreArchivo);
         exit;
          
    }
    
    private  function getCertificadosMedicos($anio, $mes){
       
        $db    = $_SESSION['db'];
        return Yii::$app->$db->createCommand("exec [dbo].[MedObtenerCertificadosMedicos] @anio = '{$anio}', @mes= '{$mes}'")->queryAll();
        
    }

    private function Obtenerdatospermisos($cedula, $permiso, $fechaini){
        $db    = $_SESSION['db'];
        return Yii::$app->$db->createCommand("exec [dbo].[ObtenerDatosPermisoEmpleado] @cedula = '{$cedula}', @permiso= '{$permiso}', @fecha = '{$fechaini}'")->queryOne();
    }
    
    /**
     * Deletes an existing SysMedCertficadoMedico model.
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
     * Finds the SysMedCertficadoMedico model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SysMedCertificadoMedico the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysMedCertificadoMedico::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
