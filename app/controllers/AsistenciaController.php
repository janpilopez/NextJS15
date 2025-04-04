<?php

namespace app\controllers;

use Yii;
use kartik\mpdf\Pdf;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosMarcacionesReloj;
use app\models\SysRrhhMarcacionesEmpleados;
use app\models\SysRrhhHorarioCab;
use app\models\SysRrhhCuadrillasJornadasMov;

class AsistenciaController extends \yii\web\Controller
{
    
    public function behaviors()
    {
        
        return [
            'ghost-access'=> [
                'class' => 'webvimark\modules\UserManagement\components\GhostAccessControl',
            ],
        ];
    }
    
    public function actionIndex()
    {
        
        $this->layout = '@app/views/layouts/main_emplados';
        
        $fechaini = date('Y-m-d');
        $departamento = '';
        $area = '';
        $datos = [];
        $filtro = '';

        if (Yii::$app->request->post()){
 
            $departamento = $_POST['departamento']== 0 ? '': $_POST['departamento'];
            $area         = $_POST['area'] == null ? '' :$_POST['area'];
            $fechaini     = $_POST['fechainicio'];
            $filtro       = $_POST['nombres']== null ? '': trim($_POST['nombres']);
            
            $datos = $this->getAsistenciaLaboral($fechaini,$fechaini, $area, $departamento, $filtro);
            
        }
  
        return $this->render('asistencia', ['datos'=> $datos, 'fechaini'=> $fechaini, 'area'=> $area, 'departamento'=> $departamento, 'filtro'=> $filtro]);
       
    }
    
    public function actionVerasistencia(){
        
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
            
            $datos =  Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboralXCedula]  @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @id_sys_rrhh_cedula = '{$cedula}'")->queryAll(); 
           
        }
        return $this->render('empleadoasistencia', ['fechaini'=> $fechaini, 'fechafin' => $fechafin, 'cedula'=> $cedula, 'datos'=> $datos]);
    }

    public function actionVerasistenciahoras(){
        
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
            
            $datos =  Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboralXCedula]  @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @id_sys_rrhh_cedula = '{$cedula}'")->queryAll(); 
           
        }
        return $this->render('empleadoasistenciahoras', ['fechaini'=> $fechaini, 'fechafin' => $fechafin, 'cedula'=> $cedula, 'datos'=> $datos]);
    }

    public function actionAsistenciaempleadohorasxls($cedula, $fechaini, $fechafin){
         
        $db =  $_SESSION['db'];

        $datos = Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboralXCedula]  @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @id_sys_rrhh_cedula = '{$cedula}'")->queryAll(); 
         
        return $this->render('asistenciahorasxls', ['datos'=> $datos, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'cedula'=> $cedula]);
    }
    
    
    public function actionAsistenciaxls($departamento, $area, $fechaini, $filtro){
       
        $datos = [];
         
        $datos = $this->getAsistenciaLaboral($fechaini,$fechaini, $area, $departamento, $filtro);
         
        return $this->render('asistenciaxls', ['datos'=> $datos, 'fechaini'=> $fechaini, 'area'=> $area, 'departamento'=> $departamento, 'filtro'=> $filtro]);
    }
    
    public function actionInformeasistencia(){
        
        ini_set("pcre.backtrack_limit", "5000000");
        
        $this->layout    = '@app/views/layouts/main_emplados';
        $fechaini        =  date('Y-m-d');
        $fechafin        =  date('Y-m-d');
        $departamento    = '';
        $area            = '';
        $datos           =  [];
        $filtro          = ''; 
        $tipo            =  '';
       
            if(Yii::$app->request->post()){
                
                $fechaini        = $_POST['fechaini']== null ?  $fechaini : $_POST['fechaini'];
                $fechafin        = $_POST['fechafin']== null ?  $fechafin : $_POST['fechafin'];
                $departamento    = $_POST['departamento']== null ? '': $_POST['departamento'];
                $area            = $_POST['area']== null ? '':$_POST['area'];
                $filtro          = $_POST['nombres']== null ? '': trim($_POST['nombres']);
               // $datos           = $this->getAsistencia($area, $departamento, $filtro, $fechaini, $fechafin);
                
                $datos = $this->getAsistenciaLaboral($fechaini,$fechafin, $area, $departamento, $filtro);
                
                $tipo            = $_POST['tipo'];
                
            }
            
            return $this->render('_informeasistencia', ['fechaini'=> $fechaini, 'fechafin' => $fechafin, 'area'=> $area, 'departamento'=> $departamento, 'datos'=> $datos, 'filtro'=> $filtro, 'tipo'=> $tipo]);
            
    }

    public function actionInformeasistenciacomedor(){
        
        ini_set("pcre.backtrack_limit", "5000000");
        
        $this->layout    = '@app/views/layouts/main_emplados';
        $fechaini        =  date('Y-m-d');
        $fechafin        =  date('Y-m-d');
        $departamento    = '';
        $area            = '';
        $datos           =  [];
        $filtro          = '';
       
            if(Yii::$app->request->post()){
                
                $fechaini        = $_POST['fechaini']== null ?  $fechaini : $_POST['fechaini'];
                $fechafin        = $_POST['fechafin']== null ?  $fechafin : $_POST['fechafin'];
                $departamento    = $_POST['departamento']== null ? '': $_POST['departamento'];
                $area            = $_POST['area']== null ? '':$_POST['area'];
                $filtro          = $_POST['nombres']== null ? '': trim($_POST['nombres']);
               // $datos           = $this->getAsistencia($area, $departamento, $filtro, $fechaini, $fechafin);
                
                $datos = $this->getAsistenciaLaboralComedor($fechaini,$fechafin, $area, $departamento, $filtro);
                
                
            }
            
            return $this->render('_informeasistenciacomedor', ['fechaini'=> $fechaini, 'fechafin' => $fechafin, 'area'=> $area, 'departamento'=> $departamento, 'datos'=> $datos, 'filtro'=> $filtro]);
            
    }
    
    public function actionAusentismo(){
        
        ini_set("pcre.backtrack_limit", "5000000");
        
        $fechaini        =  date('Y-m-d');
        $fechafin        =  date('Y-m-d');
        $departamento    = '';
        $area            = '';
        $datos           =  [];

        if(Yii::$app->request->post()){
            
            $fechaini        = $_POST['fechaini']== null ?  $fechaini : $_POST['fechaini'];
            $fechafin        = $_POST['fechafin']== null ?  $fechafin : $_POST['fechafin'];
            $departamento    = $_POST['departamento']== null ? '': $_POST['departamento'];
            $area            = $_POST['area']== null ? '':$_POST['area'];
    
            $datos = $this->getAsistenciaLaboralResumen($fechaini,$fechafin, $area, $departamento);
            
            /*if (count($datos) > 0) :
          
                $html =  $this->renderPartial('_ausentismopdf',['fechaini'=> $fechaini, 'fechafin' => $fechafin, 'area'=> $area, 'departamento'=> $departamento, 'datos'=> $datos]);
                
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
                        'SetTitle' =>  'Informe de Asistencia',
                        'SetHeader'=>['Sistema Gestión de Nómina - Informe Asistencia Laboral||'],
                        'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}']
                    ]
                ]);
                
                // return the pdf output as per the destination setting
                return $pdf->render();
                
            endif;*/
            
        }
        return $this->render('_ausentismo', ['fechaini'=> $fechaini, 'fechafin' => $fechafin, 'area'=> $area, 'departamento'=> $departamento, 'datos'=> $datos]);
       
    }

    public function actionAusentismopdf($fechaini, $fechafin, $area, $departamento){
        
        ini_set("pcre.backtrack_limit", "50000000");
   
        $datos = $this->getAsistenciaLaboralResumen($fechaini,$fechafin, $area, $departamento);
      
        if (count($datos) > 0) :
          
            $html =  $this->renderPartial('_ausentismopdf',['fechaini'=> $fechaini, 'fechafin' => $fechafin, 'area'=> $area, 'departamento'=> $departamento, 'datos'=> $datos]);
            
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
                    'SetTitle' =>  'Informe de Asistencia',
                    'SetHeader'=>['Sistema Gestión de Nómina - Informe Asistencia Laboral||'],
                    'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}']
                ]
            ]);
            
            // return the pdf output as per the destination setting
            return $pdf->render();
            
        endif;
    }

    public function actionAusentismoxls($fechaini, $fechafin, $area, $departamento){
        
        ini_set("pcre.backtrack_limit", "50000000");
   
        $datos = $this->getAsistenciaLaboralResumen($fechaini,$fechafin, $area, $departamento);
      
        if (count($datos) > 0) :
          
            return $this->render('_ausentismooxls', ['datos'=> $datos, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin]);
            
        endif;
    }


    public function actionInformeasistenciacomedorpdf($fechaini, $fechafin, $area, $departamento, $filtro){
        
        ini_set("pcre.backtrack_limit", "50000000");
   
        $datos = $this->getAsistenciaLaboralComedor($fechaini,$fechafin, $area, $departamento, $filtro);
       
        $html =  $this->renderPartial('_informeasistenciacomedorpdf',['datos'=> $datos, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'area'=> $area, 'departamento'=> $departamento]);
      
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
                'SetTitle' =>  'Informe de Asistencia Comedor',
                'SetHeader'=>['Sistema Gestión de Nómina - Informe Asistencia Comedor||'],
                'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}']
            ]
            ]);
        
        // return the pdf output as per the destination setting
        return $pdf->render();
      
 }
    
    public function actionInformeasistenciapdf($fechaini, $fechafin, $area, $departamento, $filtro, $tipo){
        
           ini_set("pcre.backtrack_limit", "50000000");
      
           $datos = $this->getAsistenciaLaboral($fechaini,$fechafin, $area, $departamento, $filtro);
          
           $html =  $this->renderPartial('_informeasistenciapdf',['datos'=> $datos, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'area'=> $area, 'departamento'=> $departamento,  'tipo'=> $tipo]);
         
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
                   'SetTitle' =>  'Informe de Asistencia',
                   'SetHeader'=>['Sistema Gestión de Nómina - Informe Asistencia Laboral||'],
                   'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}']
               ]
               ]);
           
           // return the pdf output as per the destination setting
           return $pdf->render();
         
    }

    public function actionAsistenciaempleadopdf($fechaini, $fechafin, $cedula){
        
        $db =  $_SESSION['db'];
        
        $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $cedula])->andWhere(['id_sys_empresa'=> '001'])->one();
        $datos =  Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboralXCedula]  @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @id_sys_rrhh_cedula = '{$cedula}'")->queryAll();
        
        $html =  $this->renderPartial('_asistenciaempleadopdf',[
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
                'SetTitle' =>  'Informe de Asistencia',
                'SetHeader'=>['Sistema Gestión de Nómina - Informe Asistencia Laboral||'],
                'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}']
            ]
        ]);
        
        // return the pdf output as per the destination setting
        return $pdf->render();
        
    }
    
    public function actionAsistenciaempleadopdf2($fechaini, $fechafin, $cedula){
        
        $db =  $_SESSION['db'];
        
        $empleado = SysRrhhEmpleados::find()->where(['id_sys_rrhh_cedula'=> $cedula])->andWhere(['id_sys_empresa'=> '001'])->one();
        
        $datos =   Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboralXCedula] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @id_sys_rrhh_cedula = '{$empleado['id_sys_rrhh_cedula']}'")->queryAll(); 
        
       return  $this->render('_informeasistenciarol',[
            'empleado'=> $empleado, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'datos' => $datos
          ]);
      
    }
    
    public function actionUpdatemarcacion($cedula, $marcacion){
        
        $datos = SysRrhhEmpleadosMarcacionesReloj::find()->where(['id_sys_rrhh_cedula'=> $cedula, 'fecha_jornada'=> $marcacion])->all();
        
        $model = [];
        
        if ($datos) {
            
            foreach ($datos as $data){
                
                $obj = new SysRrhhEmpleadosMarcacionesReloj();
                $obj->id_sys_rrhh_cedula =  $data->id_sys_rrhh_cedula;
                $obj->fecha_marcacion    =  date('Y/m/d H:i:s', strtotime($data->fecha_marcacion));
                $obj->fecha_sistema      =  date('Y/m/d H:i:s', strtotime($data->fecha_sistema));
               /* $obj->fecha_sistema      =  date('Y-m-d', strtotime($data->fecha_sistema));
                $obj->horamarcacion      =  date('H:i:s', strtotime($data->fecha_sistema));*/
                $obj->tipo               =  $data->tipo;
                $obj->estado             =  $data->estado;
                $obj->id_sys_adm_ccostos =  $data->id_sys_adm_ccostos;
                
                array_push($model, $obj);
            }
            
        }else{
            
            array_push($model, new SysRrhhEmpleadosMarcacionesReloj());
        }
        
        
        if(Yii::$app->request->post()){
            
            $db = $_SESSION['db'];
            
            $hextras = SysRrhhMarcacionesEmpleados::find()->where(['id_sys_rrhh_cedula'=> $cedula])->andwhere(['id_sys_empresa'=> '001'])->andWhere(['fecha_laboral'=> $marcacion])->one();
           
            if($hextras):
            
                $hextras->pago25   = 0;
                $hextras->horas25  = 0;
                $hextras->pago50   = 0;
                $hextras->horas50  = 0;
                $hextras->pago100  = 0;
                $hextras->horas100 = 0;
                
                $hextras->save(false);
            
            endif;
            
            $datos = Yii::$app->request->post('SysRrhhEmpleadosMarcacionesReloj');
            
            foreach ($datos as $data){
                
                if($data['fecha_marcacion'] != null ){
                          
                    $upmodel = SysRrhhEmpleadosMarcacionesReloj::find()->where(['id_sys_rrhh_cedula'=> $cedula])->andWhere(['fecha_marcacion'=> date('Ymd H:i:s', strtotime( $data['fecha_marcacion']))])->one();

                     if($upmodel):
                     
                         Yii::$app->$db->createCommand("UPDATE sys_rrhh_empleados_marcaciones_reloj 
                          SET   fecha_marcacion ='".date('Ymd H:i:s', strtotime($data['fecha_marcacion']))."',
                                fecha_sistema = '".date('Ymd H:i:s', strtotime($data['fecha_sistema']))."', 
                                tipo = '".$data['tipo']."', 
                                estado = '".$data['estado']."', 
                                id_sys_adm_ccostos = '".$data['id_sys_adm_ccostos']."' 
                                 WHERE ( fecha_marcacion='".date('Ymd H:i:s', strtotime($data['fecha_marcacion']))."') AND (id_sys_empresa ='001') AND (id_sys_rrhh_cedula='{$cedula}')")->execute();
        
                        endif;
       
                }else{
                                        
                    $newmodel =  new SysRrhhEmpleadosMarcacionesReloj();
                    $newmodel->fecha_marcacion     =  date('Ymd H:i:s', strtotime($data['fecha_sistema']));
                    $newmodel->id_sys_rrhh_cedula  =  $cedula;
                    $newmodel->fecha_sistema       =  date('Ymd H:i:s', strtotime($data['fecha_sistema']));
                    $newmodel->tipo                =  $data['tipo'];
                    $newmodel->estado              =  $data['estado'];
                    $newmodel->fecha_jornada       =  $marcacion;
                    $newmodel->id_sys_empresa      =  '001';
                    $newmodel->id_sys_adm_ccostos  =  $data['id_sys_adm_ccostos'];
                    $newmodel->validar             =  1;
                    $newmodel->transaccion_usuario = Yii::$app->user->username;
                    $newmodel->save(false);
                    
                }
                
            }
           
        }
        return $this->renderAjax('_form', [
            'model' => $model,
            'cedula'=> $cedula,
        ]);
    }

    public function actionAdd25(){
        
        if (Yii::$app->request->isAjax && Yii::$app->request->post()):
            
            $obj          =  json_decode( Yii::$app->request->post('data'));
      
            $ban          =  true;
            
            $db           =  $_SESSION['db'];
            
            $rol          =  Yii::$app->$db->createCommand("SELECT * FROM sys_rrhh_empleados_rol_cab where  '{$obj[0]->fecha}' >= fecha_ini_liq and '{$obj[0]->fecha}' <= fecha_fin_liq and periodo = '2' and estado = 'P'")->queryOne();
            
            if(!$rol):
            
                    $transaction  =  Yii::$app->$db->beginTransaction();
                    
                    foreach ($obj as $data){                        
                        
                        $model = SysRrhhMarcacionesEmpleados::find()->where(['id_sys_rrhh_cedula'=> $data->id_sys_rrhh_cedula])->andwhere(['id_sys_empresa'=> '001'])->andWhere(['fecha_laboral'=> $data->fecha])->one();
                        
                        if($model):
                        
                            $model->user_apro25  = $data->pago25 == true ?  Yii::$app->user->username : null;
                            $model->pago25       = $data->pago25;
                            $model->horas25      = $data->h25;
                            
                            $model->user_apro50  = $data->pago50 == true ?  Yii::$app->user->username : null;
                            $model->pago50       = $data->pago50;
                            $model->horas50      = $data->h50;
                            
                            $model->user_apro100  = $data->pago100 == true ?  Yii::$app->user->username : null;
                            $model->pago100       = $data->pago100;
                            $model->horas100      = $data->h100;
                            
                            $model->valor_hora    = $this->getValorHora($data->id_sys_rrhh_cedula);
                                
                            if(!$ban = $model->save(false)):
                            
                                break;
                            
                            endif;
                            
                        else:
                        
                            $newmodel = new SysRrhhMarcacionesEmpleados();
                            $newmodel->id_sys_rrhh_cedula = $data->id_sys_rrhh_cedula;
                            $newmodel->fecha_laboral      = $data->fecha;
                            $newmodel->id_sys_empresa     = '001';
                              
                            $newmodel->user_apro25  = $data->pago25 == true ?  Yii::$app->user->username : null;
                            $newmodel->pago25       = $data->pago25;
                            $newmodel->horas25      = $data->h25;
                              
                            $newmodel->user_apro50  = $data->pago50 == true ?  Yii::$app->user->username : null;
                            $newmodel->pago50       = $data->pago50;
                            $newmodel->horas50      = $data->h50;
                              
                            $newmodel->user_apro100  = $data->pago100 == true ?  Yii::$app->user->username : null;
                            $newmodel->pago100       = $data->pago100;
                            $newmodel->horas100      = $data->h100;
                            
                            $newmodel->valor_hora    = $this->getValorHora($data->id_sys_rrhh_cedula);
                              
                            if(!$ban = $newmodel->save(false)):
                              
                                break;
                              
                            endif;
                        
                        endif;
                        
                    }
                    
                    if($ban){
                        $transaction->commit();
                        echo  json_encode(['data' => [ 'estado' => false,'mensaje' => 'El proceso de liquidación de horas extras se realizado con éxito!']]);
                        
                    }else{
                        $transaction->rollBack();
                        echo  json_encode(['data' => ['estado' => false, 'mensaje' => 'Ha ocurrido un error al guardar el permiso!']]);
                    }
                   
            else:
            
                echo  json_encode(['data' => [ 'estado' => false,'mensaje' => 'No se pudo realizar la liquidación, ya que el periodo se encuentra procesado!']]);
            
            endif;
           
        else:
             echo  json_encode(['data' => ['estado' => false, 'mensaje' => 'Ha ocurrido un error!']]);
        
        endif;
       
    }
    
    public function actionAdd50(){
        
        $cedula         = Yii::$app->request->post('cedula');
        $fechamarcacion = Yii::$app->request->post('fechamarcacion');
        $horamarcacion  = Yii::$app->request->post('horamarcacion');
        $pago           = Yii::$app->request->post('pago');
        $model = SysRrhhMarcacionesEmpleados ::find()->where(['id_sys_rrhh_cedula'=> $cedula])->andwhere(['id_sys_empresa'=> '001'])->andWhere(['fecha_laboral'=> $fechamarcacion])->one();
       
        $mjs = [];
        
        if($model){
            $model->user_apro50         = Yii::$app->user->username;
            $model->id_sys_rrhh_cedula  = $cedula;
            $model->fecha_laboral       = $fechamarcacion;
            $model->id_sys_empresa      = '001';
            $model->pago50              = $pago;
            $model->horas50             = $this->setHorasdecimal($horamarcacion);
            
            if($model->save(false)){
                
                $mjs = ['estado'=> true];
                
            }else{
                
                $mjs = ['estado'=> false];
                
            }
            
        }else{
            
            $newmodel = new SysRrhhMarcacionesEmpleados();
            $newmodel->user_apro50         = Yii::$app->user->username;
            $newmodel->id_sys_rrhh_cedula = $cedula;
            $newmodel->fecha_laboral      = $fechamarcacion;
            $newmodel->id_sys_empresa     = '001';
            $newmodel->pago50             = $pago;
            $newmodel->horas50            = $this->setHorasdecimal($horamarcacion);
            
            if($newmodel->save(false)){
                
                $mjs = ['estado'=> true];
                
            }else{
                
                $mjs = ['estado'=> false];
                
            }
            
        }
        
        return json_encode($mjs);
    }
    
    public function actionAdd100(){
        
        $cedula         = Yii::$app->request->post('cedula');
        $fechamarcacion = Yii::$app->request->post('fechamarcacion');
        $horamarcacion  = Yii::$app->request->post('horamarcacion');
        $pago           = Yii::$app->request->post('pago');
        $model = SysRrhhMarcacionesEmpleados ::find()->where(['id_sys_rrhh_cedula'=> $cedula])->andwhere(['id_sys_empresa'=> '001'])->andWhere(['fecha_laboral'=> $fechamarcacion])->one();
        
        $mjs = [];
        
        if($model){
            $model->user_apro100        = Yii::$app->user->username;
            $model->id_sys_rrhh_cedula =  $cedula;
            $model->fecha_laboral      = $fechamarcacion;
            $model->id_sys_empresa     = '001';
            $model->pago100             = $pago;
            $model->horas100            = $this->setHorasdecimal($horamarcacion);
            
            if($model->save(false)){
                
                $mjs = ['estado'=> true];
                
            }else{
                
                $mjs = ['estado'=> false];
                
            }
            
        }else{
            
            $newmodel = new SysRrhhMarcacionesEmpleados();
            $newmodel->user_apro25         = Yii::$app->user->username;
            $newmodel->id_sys_rrhh_cedula = $cedula;
            $newmodel->fecha_laboral      = $fechamarcacion;
            $newmodel->id_sys_empresa     = '001';
            $newmodel->pago100            = $pago;
            $newmodel->horas100           = $this->setHorasdecimal($horamarcacion);
            
            if($newmodel->save(false)){
                
                $mjs = ['estado'=> true];
                
            }else{
                
                $mjs = ['estado'=> false];
                
            }
            
        }
        
        return json_encode($mjs);
    }
    
    public function actionHorasextras(){
        
        ini_set("pcre.backtrack_limit", "5000000");
       
        $fechaini        =  date('Y-m-d');
        $fechafin        =  date('Y-m-d');
        $departamento    = '';
        $area            = '';
        $datos           =  [];
        $tipo            = 'R';
        if(Yii::$app->request->post()){
            
            $fechaini        = $_POST['fechaini']== null ?  $fechaini : $_POST['fechaini'];
            $fechafin        = $_POST['fechafin']== null ?  $fechafin : $_POST['fechafin'];
            $area            = $_POST['area'] == null ? '' : $_POST['area'];
            $departamento    = $_POST['departamento'] == null ? '' : $_POST['departamento'];
            $tipo            = $_POST['tipo'];
            if($tipo == 'R'):
                  $datos = $this->getHorasExtrasResumen($fechaini,$fechafin, $area, $departamento);
            else:
                  $datos = $this->getHorasExtrasDetalle($fechaini, $fechafin, $area, $departamento);
            endif;
            
            $datos = $this->getHorasExtrasDetalle($fechaini, $fechafin, $area, $departamento);
            
        }
        
        return $this->render('_horasextras', ['fechaini'=> $fechaini, 'fechafin' => $fechafin, 'area'=> $area, 'departamento'=> $departamento, 'tipo'=> $tipo, 'datos'=> $datos]);
        
    }
    
    public function actionResumen(){
        
        ini_set("pcre.backtrack_limit", "5000000");
        
        $this->layout    = '@app/views/layouts/main_emplados';
        $fechaini        =  date('Y-m-d');
        $fechafin        =  date('Y-m-d');
        $departamento    = '';
        $area            = '';
        $datos           =  [];
        
        if(Yii::$app->request->post()){
            
            $fechaini        = $_POST['fechaini']== null ?  $fechaini : $_POST['fechaini'];
            $fechafin        = $_POST['fechafin']== null ?  $fechafin : $_POST['fechafin'];
            $departamento    = $_POST['departamento'];
            $area            = $_POST['area'];
            
            $datos = $this->getAsistenciaLaboralResumen($fechaini,$fechafin, $area, $departamento);
            
            
        }
        
        return $this->render('_resumen', ['fechaini'=> $fechaini, 'fechafin' => $fechafin, 'area'=> $area, 'departamento'=> $departamento, 'datos'=> $datos]);
    }
    
    public  function actionResumenxls($fechaini=null, $fechafin=null, $area=null, $departamento=null){
        
        ini_set("pcre.backtrack_limit", "5000000");
        $datos = [];
        $datos = $this->getAsistenciaLaboralResumen($fechaini,$fechafin, $area, $departamento);
        
        return $this->render('_resumenxls', ['datos'=> $datos, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin]);
    }
   
    public  function actionHorasextraspdf($fechaini,$fechafin, $area, $departamento, $tipo){
        
        ini_set("pcre.backtrack_limit", "50000000");
        
        if ($tipo == 'R'):
             $datos = $this->getHorasExtrasResumen($fechaini,$fechafin, $area, $departamento);
        else:
             $datos = $this->getHorasExtrasDetalle($fechaini, $fechafin, $area, $departamento);
        endif;
        
        $html =  $this->renderPartial('_horasextraspdf',['datos'=> $datos, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'area'=> $area, 'departamento'=> $departamento, 'tipo'=> $tipo]);
          
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
                'SetHeader'=>['Sistema Gestión de Nómina - Reporte Horas Laboradas||'],
                'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}'],
            ]
        ]);
        
        // return the pdf output as per the destination setting
        return $pdf->render(); 
    }
    
    public  function actionHorasextrasxls($fechaini,$fechafin, $area, $departamento, $tipo){
        
        ini_set("pcre.backtrack_limit", "50000000");
        
        if ($tipo == 'R'):
             $datos = $this->getHorasExtrasResumen($fechaini,$fechafin, $area, $departamento);
        else:
             $datos = $this->getHorasExtrasDetalle($fechaini, $fechafin, $area, $departamento);
        endif;
        
        return $this->render('_horasextrasxls',['datos'=> $datos, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'area'=> $area, 'departamento'=> $departamento, 'tipo'=> $tipo]);
     
    }
    private function setHorasdecimal($hora){
        
        $array = explode(':', trim($hora));
        $h     = floatval($array[0]);
        $m     = floatval($array[1]);
      
        if($m > 0 ){
            $m = $m/60;
        }
        return $h+$m;
      
    }
     
    private function getEmpleadosAsistencia($fechaini, $fechafin){
        
        return  (new \yii\db\Query())
        ->select(["area.id_sys_adm_area","area","departamento.id_sys_adm_departamento", "departamento", "emp.id_sys_rrhh_cedula", "nombres", "tipo_jornada", "cargo.reg_ent_salida", "(select top(1) fecha_salida from sys_rrhh_empleados_contratos where id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula order by fecha_ingreso desc ) as fecha_salida", "(select top(1) fecha_ingreso from sys_rrhh_empleados_contratos where id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula order by fecha_ingreso desc ) fecha_ingreso"])
        ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
        ->innerJoin("sys_adm_departamentos as departamento","cargo.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->from("sys_rrhh_empleados emp")
        ->Where("emp.id_sys_rrhh_cedula in (SELECT distinct id_sys_rrhh_cedula
			FROM [dbo].[sys_rrhh_empleados]
			where estado = 'A'
			union
			SELECT distinct id_sys_rrhh_cedula
			FROM [dbo].[sys_rrhh_empleados_contratos]
			where fecha_salida >= '{$fechaini}' and fecha_salida <= '{$fechafin}')")
	     ->andwhere("emp.id_sys_empresa = '001'")
		 ->orderBy("area,departamento,nombres")
		 ->all(SysRrhhEmpleados::getDb());
       
    }
     
    private function getAsistencia($area, $departamento, $filtro, $fechaini, $fechafin){
        
        if($filtro != ''):
        
        return  (new \yii\db\Query())
        ->select(["area.id_sys_adm_area","area","departamento.id_sys_adm_departamento", "departamento", "emp.id_sys_rrhh_cedula", "nombres", "tipo_jornada", "(select top(1) fecha_salida from sys_rrhh_empleados_contratos where id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula order by fecha_ingreso desc ) as fecha_salida", "(select top(1) fecha_ingreso from sys_rrhh_empleados_contratos where id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula order by fecha_ingreso desc ) fecha_ingreso"])
        ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
        ->innerJoin("sys_adm_departamentos as departamento","cargo.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
        ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
        ->from("sys_rrhh_empleados emp")
        ->andWhere("emp.id_sys_rrhh_cedula in (SELECT distinct id_sys_rrhh_cedula
                FROM [dbo].[sys_rrhh_empleados]
                where estado = 'A'
                union
                SELECT distinct id_sys_rrhh_cedula
                FROM [dbo].[sys_rrhh_empleados_contratos]
                where fecha_salida >= '{$fechaini}' and fecha_salida <= '{$fechafin}')")
                ->andWhere("emp.nombres like '{$filtro}%'")
                ->andwhere("cargo.reg_ent_salida = 'S'")
                ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
                ->andfilterWhere(['like', "area.id_sys_adm_area",$area])
                ->andwhere("emp.id_sys_empresa = '001'")
                ->orderBy("area,departamento,nombres")
                ->all(SysRrhhEmpleados::getDb());
      
       else:
       
           return  (new \yii\db\Query())
           ->select(["area.id_sys_adm_area","area","departamento.id_sys_adm_departamento", "departamento", "emp.id_sys_rrhh_cedula", "nombres", "tipo_jornada", "(select top(1) fecha_salida from sys_rrhh_empleados_contratos where id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula order by fecha_ingreso desc ) fecha_salida", "(select top(1) fecha_ingreso from sys_rrhh_empleados_contratos where id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula order by fecha_ingreso desc ) fecha_ingreso"])
            ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
            ->innerJoin("sys_adm_departamentos as departamento","cargo.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->from("sys_rrhh_empleados emp")
            ->andWhere("emp.id_sys_rrhh_cedula in (SELECT distinct id_sys_rrhh_cedula
			FROM [dbo].[sys_rrhh_empleados]
			where estado = 'A'
			union
			SELECT distinct id_sys_rrhh_cedula
			FROM [dbo].[sys_rrhh_empleados_contratos]
			where fecha_salida >= '{$fechaini}' and fecha_salida <= '{$fechafin}')")
            ->andwhere("cargo.reg_ent_salida = 'S'")
            ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
            ->andfilterWhere(['like', "area.id_sys_adm_area",$area])
            ->andwhere("emp.id_sys_empresa = '001'")
            ->orderBy("area,departamento,nombres")
            ->all(SysRrhhEmpleados::getDb());
          
       endif;
        
    }
  
    public function actionMarcacionemanual(){
        
        $area = '';
        $departamento = '';
        $jornada = '';
        $fechaini = date('Y-m-d');
        
        if(Yii::$app->request->post()){
        
            $fechaini      = $_POST["fechainicio"] == null ? $fechaini:  $_POST["fechainicio"];
            $area          = $_POST["area"] == null ? '' : $_POST['area'];
            $departamento  = $_POST["departamento"] == null ? '' : $_POST['departamento'];            
            $jornada       = $_POST["jornada"] == null ? '0' : $_POST['jornada'];
            $empleados     = [];
            
            if($area != ''):
            
                $empleados = $this->getEmpleados($area, $departamento);
                  
                    foreach ($empleados as $empleado):
                   
                    $objjornada = null;
                   
                    $marcacionempleado = SysRrhhMarcacionesEmpleados::find()->where(['id_sys_rrhh_cedula'=> $empleado['id_sys_rrhh_cedula']])->andWhere(['fecha_laboral'=> $fechaini])->one();
                   
                       if(!$marcacionempleado):
              
                                $agendamiento = $this->getAgendamiento($empleado['id_sys_rrhh_cedula'], $fechaini);
                     
                                if(!$agendamiento):
                                
                                    $objjornada  = SysRrhhHorarioCab::find()->where(['id_sys_rrhh_horario_cab'=> $jornada])->one();
                     
                                else:
                                     
                                    if($agendamiento->id_sys_rrhh_jornada != null):
                                            
                                        $objjornada  = SysRrhhHorarioCab::find()->where(['id_sys_rrhh_horario_cab'=> $agendamiento->id_sys_rrhh_jornada ])->one();
                                            
                                    endif;
                                
                                endif; 
                                
                                if($objjornada != null):
                                
                                           //Inserta Marcaciones
                                            $upmodel = SysRrhhEmpleadosMarcacionesReloj::find()->where(['id_sys_rrhh_cedula'=> $empleado['id_sys_rrhh_cedula']])->andWhere(['fecha_marcacion'=>  date('Ymd', strtotime($fechaini)). " ".substr($objjornada->hora_inicio,0,8)])->one();
                                            
                                            if(!$upmodel):
                                            
                                                $permiso = $this->getPermiso($fechaini, $empleado['id_sys_rrhh_cedula']);
                                                    
                                                $vacaciones = $this->getVacaciones($fechaini,$empleado['id_sys_rrhh_cedula']);
                                                    
                                                    
                                                    if(!$permiso):
                                                    
                                                        if(!$vacaciones):
                                                    
                                                            $newmodel =  new SysRrhhEmpleadosMarcacionesReloj();
                                                            $newmodel->fecha_marcacion    =  date('Ymd', strtotime($fechaini)). " ".substr($objjornada->hora_inicio,0,8);
                                                            $newmodel->id_sys_rrhh_cedula =  $empleado['id_sys_rrhh_cedula'];
                                                            $newmodel->fecha_sistema      =  date('Ymd', strtotime($fechaini)). " ".substr($objjornada->hora_inicio,0,8);
                                                            $newmodel->tipo               =  'E';
                                                            $newmodel->validar            =   2;
                                                            $newmodel->estado             =  'A';
                                                            $newmodel->fecha_jornada      =   $fechaini;
                                                            $newmodel->id_sys_empresa     =  '001';
                                                            $newmodel->id_sys_adm_ccostos =  $empleado['id_sys_adm_ccosto'];
                                                            $newmodel->save(false);
                                                            
                                                        endif;
                                                          
                                                    endif;       
                                                    
                                            endif;
                                            
                                            $fechasalida =  date('Ymd', strtotime($fechaini));
                                            
                                            if(strtotime($objjornada->hora_fin) >=  strtotime("00:00:00") && strtotime($objjornada->hora_fin) <= strtotime("07:00:00")):
                                                  
                                                  $fechasalida  = date("Ymd",strtotime($fechaini."+ 1 days"));
                                            
                                            endif;
                                            
                                            $upmodel = SysRrhhEmpleadosMarcacionesReloj::find()->where(['id_sys_rrhh_cedula'=> $empleado['id_sys_rrhh_cedula']])->andWhere(['fecha_marcacion'=>  $fechasalida. " ".substr($objjornada->hora_fin,0,8)])->one();
                                            
                                            if(!$upmodel):
                                            
                                                    $permiso = $this->getPermiso($fechaini, $empleado['id_sys_rrhh_cedula']);
                                                    
                                                    $vacaciones = $this->getVacaciones($fechaini,  $empleado['id_sys_rrhh_cedula']);
                                                         
                                                    if(!$permiso):
                                                    
                                                            if(!$vacaciones):
                                                
                                                                    $newmodel =  new SysRrhhEmpleadosMarcacionesReloj();
                                                                    $newmodel->fecha_marcacion    =  $fechasalida. " ".substr($objjornada->hora_fin,0,8);
                                                                    $newmodel->id_sys_rrhh_cedula =  $empleado['id_sys_rrhh_cedula'];
                                                                    $newmodel->fecha_sistema      =  $fechasalida. " ".substr($objjornada->hora_fin,0,8);
                                                                    $newmodel->tipo               =  'S';
                                                                    $newmodel->validar            =   2;
                                                                    $newmodel->estado             =  'A';
                                                                    $newmodel->fecha_jornada      =   $fechaini;
                                                                    $newmodel->id_sys_empresa     =  '001';
                                                                    $newmodel->id_sys_adm_ccostos =  $empleado['id_sys_adm_ccosto'];
                                                                    $newmodel->save(false);
                                                                    
                                                                endif;
                                                      endif;  
                                            endif;
                                endif;
                                
                         endif;
                              
                  endforeach;
                  
                  Yii::$app->getSession()->setFlash('info', [
                      'type' => 'success','duration' => 1500,
                      'icon' => 'glyphicons glyphicons-robot','message' => 'Se ha registro la asistencia con éxito!',
                      'positonY' => 'top','positonX' => 'right']);
     
            else:
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'warning','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Seleccione el área y la jornada laboral!',
                    'positonY' => 'top','positonX' => 'right']);
                
            endif;
            
        }
        
        return $this->render('_asistenciamanual', ['fechaini'=> $fechaini,'area'=> $area, 'departamento' => $departamento, 'jornada'=> $jornada]);
  
    }
    
    private function getEmpleados($area, $departamento){
        
        //revisa el departmamento por el cargo
        return  (new \yii\db\Query())->select(
            [
                "emp.nombres",
                "emp.id_sys_rrhh_cedula",
                "emp.id_sys_adm_ccosto"
            ])
            ->from("sys_rrhh_empleados as emp")
            ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
            ->innerJoin("sys_adm_departamentos as departamento","cargo.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->where("cargo.reg_ent_salida = 'S'")
            ->andwhere("emp.estado = 'A'")
            ->andfilterWhere(['like','departamento.id_sys_adm_departamento', $departamento])
            ->andfilterWhere(['like','area.id_sys_adm_area', $area])
            ->orderBy("nombres")
            ->all(SysRrhhEmpleados::getDb());
        
    }
    
    private function getAgendamiento ($id_sys_rrhh_cedula, $fecha_laboral){
        
        return SysRrhhCuadrillasJornadasMov::find()->where(['id_sys_rrhh_cedula'=> $id_sys_rrhh_cedula])->andWhere(['fecha_laboral'=> $fecha_laboral])->orderBy(['fecha_registro'=> SORT_DESC])->one();
        
    }
    
    //Obtenemos permisos
    private function getPermiso ($fecha, $id_sys_rrhh_cedula){
        
        return  (new \yii\db\Query())
        ->select('*')
        ->from("sys_rrhh_empleados_permisos pemp")
        ->innerjoin("sys_rrhh_permisos p", "pemp.id_sys_rrhh_permiso = p.id_sys_rrhh_permiso")
        ->where("'{$fecha}' >= fecha_ini")
        ->andwhere("'{$fecha}' <= fecha_fin")
        ->andwhere("id_sys_rrhh_cedula like '%{$id_sys_rrhh_cedula}%'")
        ->orderby("nivel")
        ->one(SysRrhhEmpleados::getDb());

    }
    
    //Obrenemos vacaciones
    private  function getVacaciones($fecha, $cedula){
        
        return (new \yii\db\Query())
        ->select('*')
        ->from("sys_rrhh_vacaciones_solicitud")
        ->where("'{$fecha}' >= fecha_inicio and   '{$fecha}'<= fecha_fin")
        ->andwhere("id_sys_rrhh_cedula = '{$cedula}'")
        ->andwhere("tipo = 'G'")
        ->one(SysRrhhEmpleados::getDb());

    }
    private function getAsistenciaLaboralComedor ($fechaini, $fechafin, $area, $departamento, $filtro){
        
        $db =  $_SESSION['db'];
         
        if($area != "" &&  $departamento == "" && $filtro == ""):
          
            return  Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboralComedorXArea] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @id_sys_adm_area = '{$area}'")->queryAll(); 
            
        elseif($area != "" && $departamento != "" && $filtro == ""):
         
            return  Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboralComedorXAreaDepartamento] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @id_sys_adm_area = '{$area}', @id_sys_adm_departamento = '{$departamento}'")->queryAll(); 
         
        elseif ($area != "" && $departamento != "" && $filtro != ""):
         
            return  Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboralComedorXAreaDepartamentoEmpleado] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @id_sys_adm_area = '{$area}', @id_sys_adm_departamento = '{$departamento}', @nombre_empleado = '{$filtro}'")->queryAll(); 
              
        elseif ($area != "" && $departamento == "" && $filtro != ""):
         
            return  Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboralComedorXAreaEmpleado] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @id_sys_adm_area = '{$area}', @nombre_empleado = '{$filtro}'")->queryAll(); 
         
        elseif($area == "" && $departamento == "" && $filtro != ""):
         
            return  Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboralComedorXEmpleado] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @nombre_empleado = '{$filtro}'")->queryAll(); 
         
        else:
         
            return  Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboralComedor] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}'")->queryAll();
         
        endif;
        
    }

    private function getAsistenciaLaboral ($fechaini, $fechafin, $area, $departamento, $filtro){
        
        $db =  $_SESSION['db'];
         
        if($area != "" &&  $departamento == "" && $filtro == ""):
          
            return  Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboralXArea] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @id_sys_adm_area = '{$area}'")->queryAll(); 
            
        elseif($area != "" && $departamento != "" && $filtro == ""):
         
            return  Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboralXAreaDepartamento] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @id_sys_adm_area = '{$area}', @id_sys_adm_departamento = '{$departamento}'")->queryAll(); 
         
        elseif ($area != "" && $departamento != "" && $filtro != ""):
         
            return  Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboralXAreaDepartamentoEmpleado] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @id_sys_adm_area = '{$area}', @id_sys_adm_departamento = '{$departamento}', @nombre_empleado = '{$filtro}'")->queryAll(); 
              
        elseif ($area != "" && $departamento == "" && $filtro != ""):
         
            return  Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboralXAreaEmpleado] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @id_sys_adm_area = '{$area}', @nombre_empleado = '{$filtro}'")->queryAll(); 
         
        elseif($area == "" && $departamento == "" && $filtro != ""):
         
            return  Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboralXEmpleado] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @nombre_empleado = '{$filtro}'")->queryAll(); 
         
        else:
         
            return  Yii::$app->$db->createCommand("EXEC [dbo].[AsistenciaLaboral] @fechaini = '{$fechaini}', @fechafin = '{$fechafin}'")->queryAll();
         
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

    private  function getAsistenciaLaboralResumenn($fecha_ini, $fecha_fin, $id_sys_adm_area, $id_sys_adm_departamento,$tipogenero){
       
        $db =  $_SESSION['db'];
        
        if (($id_sys_adm_area != null) && ( $id_sys_adm_departamento == null)) :
        
            return  Yii::$app->$db->createCommand("exec dbo.ObtenerAsistenciaLaboralEmpleadosResumenHoras @fecha_ini = '{$fecha_ini}', @fecha_fin = '{$fecha_fin}', @id_sys_adm_area = {$id_sys_adm_area}, @genero = {$tipogenero}")->queryAll();
       
        elseif (($id_sys_adm_area != null) && ( $id_sys_adm_departamento != null)):
        
                return  Yii::$app->$db->createCommand("exec dbo.ObtenerAsistenciaLaboralEmpleadosResumenHoras @fecha_ini = '{$fecha_ini}', @fecha_fin = '{$fecha_fin}', @id_sys_adm_area = {$id_sys_adm_area}, @id_sys_adm_departamento = {$id_sys_adm_departamento}, @genero = {$tipogenero}")->queryAll();
        else:
        
                return  Yii::$app->$db->createCommand("exec dbo.ObtenerAsistenciaLaboralEmpleadosResumenHoras @fecha_ini = '{$fecha_ini}', @fecha_fin = '{$fecha_fin}', @genero = {$tipogenero}")->queryAll();
        
        endif;
       
    }
 
    private function getValorHora($id_sys_rrhh_cedula){
        
        $db =  $_SESSION['db'];
        return  Yii::$app->$db->createCommand("exec dbo.ObtenerValorHoraEmpleado @id_sys_rrhh_cedula = '{$id_sys_rrhh_cedula}'")->queryScalar();
    }
    
    private  function getHorasExtrasResumen($fechaini, $fechafin, $area, $departamento){
        
        $db =  $_SESSION['db'];  
        
        if ($area != '' && $departamento == ''):
           return  Yii::$app->$db->createCommand("exec dbo.ObtenerHorasExtrasResumenn @fecha_ini = '{$fechaini}', @fecha_fin = '{$fechafin}', @id_sys_adm_area = '{$area}'")->queryAll();
        endif;
        
        if ($area != '' && $departamento != ''):
          return  Yii::$app->$db->createCommand("exec dbo.ObtenerHorasExtrasResumenn @fecha_ini = '{$fechaini}', @fecha_fin = '{$fechafin}', @id_sys_adm_area = '{$area}', @id_sys_adm_departamento = {$departamento}")->queryAll();
        endif;
       
        return  Yii::$app->$db->createCommand("exec dbo.ObtenerHorasExtrasResumenn @fecha_ini = '{$fechaini}', @fecha_fin = '{$fechafin}'")->queryAll();
        
    }
    
    private function getHorasExtrasDetalle($fechaini, $fechafin, $area, $departamento){
        
        $db =  $_SESSION['db'];   
        
        if ($area != '' && $departamento == ''):
            return  Yii::$app->$db->createCommand("exec dbo.ObtenerHorasExtrasDetalle @fecha_ini = '{$fechaini}', @fecha_fin = '{$fechafin}', @id_sys_adm_area = '{$area}'")->queryAll();
        endif;
        
        if ($area != '' && $departamento != ''):
            return  Yii::$app->$db->createCommand("exec dbo.ObtenerHorasExtrasDetalle @fecha_ini = '{$fechaini}', @fecha_fin = '{$fechafin}', @id_sys_adm_area = '{$area}', @id_sys_adm_departamento = {$departamento}")->queryAll();
        endif;
        
        return  Yii::$app->$db->createCommand("exec dbo.ObtenerHorasExtrasDetalle @fecha_ini = '{$fechaini}', @fecha_fin = '{$fechafin}'")->queryAll();
            
    }
    
}