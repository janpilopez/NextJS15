<?php

namespace app\controllers;

use app\models\SysRrhhEmpleadosMarcacionesReloj;
use Yii;
use app\models\SysRrhhComedor;
use app\models\SysRrhhEmpleados;
use app\models\SysRrhhEmpleadosNovedades;
use app\models\SysRrhhEmpleadosSueldos;
use app\models\Search\SysRrhhComedoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\SysRrhhEntregaCanasta;
use app\models\SysRrhhEmpleadosLunch;
use Mpdf\Mpdf;
use app\models\SysRrhhComedorVisitas;
use app\models\SysEmpresa;
use kartik\mpdf\Pdf;

/**
 * ComendorController implements the CRUD actions for SysRrhhComedor model.
 */
class ComedorController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all SysRrhhComedor models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysRrhhComedoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhComedor model.
     * @param string $id_sys_rrhh_comedor
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
     * Creates a new SysRrhhComedor model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysRrhhComedor();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id_sys_rrhh_comedor' => $model->id_sys_rrhh_comedor]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SysRrhhComedor model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id_sys_rrhh_comedor
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id_sys_rrhh_comedor' => $model->id_sys_rrhh_comedor]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SysRrhhComedor model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id_sys_rrhh_comedor
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
       // $this->findModel($id_sys_rrhh_comedor, $id_sys_empresa)->delete();
        return $this->redirect(['index']);
    }

    public function actionLunch(){
        
        $horarios    = SysRrhhComedor::find()->all();
        $horarioss   = [];
        $empresas    = SysEmpresa::find()->where(['activo'=> 1])->all();
        $totalLunch  = 0;
        $fecha       = date('Y-m-d');
        $hora        = date('H:i:s');
        $idcomedor   = 0;

        foreach ($horarios as $horario){
            $rango =  ['id_sys_rrhh_comedor'=> $horario['id_sys_rrhh_comedor'], 'alimento'=> $horario['alimento'], 'h_desde'=> date('H:i:s',strtotime($horario['h_desde'])), 'h_hasta'=> date('H:i:s',strtotime($horario['h_hasta'])), 'id_sys_empresa'=> '001'];
            array_push($horarioss,$rango);
        }

        foreach ( $empresas as $empresa){
               
            foreach ($horarioss as $horar){
                if($hora > $horar['h_desde'] && $hora <= $horar['h_hasta']):
                    $idcomedor = $horar['id_sys_rrhh_comedor'];
                endif;
            }
            
            $totalLunch =   $totalLunch +  (new \yii\db\Query())
            ->select(["count(*)"])
            ->from("sys_rrhh_empleados_lunch")
            ->where("fecha = '{$fecha}'")
            ->andwhere("id_sys_rrhh_comedor = '{$idcomedor}'")
            ->scalar(Yii::$app->get($empresa->db_name));
            
        }
      
        return $this->render('_lunch',['horarios'=> $horarioss, 'totalLunchs' => $totalLunch]);
 
    }
    
    public function actionInfolunch(){
        
        
        $datos = [];
        
        $fechaini  =  date('Y-m-d');
        $fechafin  =  date('Y-m-d');
        $lunchh    =  SysRrhhComedor::find()->all();
        $lunch     =  [];

        foreach ($lunchh as $data){
            $lunch += [intval($data['id_sys_rrhh_comedor']) => $data['alimento']];
        }

        $info      =  [1 =>'Detallado', 2 => 'Resumido'];
        $tipo      =  '';
        $tipoinfo  =  '';
        
        if(Yii::$app->request->post()){
            
            $fechaini = $_POST['fechaini'] != null ? $_POST['fechaini'] : $fechaini; 
            $fechafin = $_POST['fechafin'] != null ? $_POST['fechafin'] : $fechafin; 
            $tipo     = $_POST['tipo'] != null ? $_POST['tipo'] : $tipo; 
            $tipoinfo = $_POST['tipoinfo'] != null ? $_POST['tipoinfo'] : $tipoinfo; 
            $datos    = $this->getLunchs($fechaini, $fechafin, $tipo);
            
        }
        
        
       return  $this->render('_infolunch', ['datos'=> $datos, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'tipo'=> $tipo,  'lunch'=> $lunch, 'info'=> $info, 'tipoinfo'=> $tipoinfo]);
        
      
    }
    
    public  function actionInfolunch2(){
        
        $datos = [];
        
        $fechaini  =  date('Y-m-d');
        $fechafin  =  date('Y-m-d');
        $lunch     =  [1 => 'Desayuno',3 => 'Merienda'];
        $tipo      =  '';
        $tipoinfo  =  '';
        
        if(Yii::$app->request->post()){
            
            $fechaini = $_POST['fechaini'] != null ? $_POST['fechaini'] : $fechaini;
            $fechafin = $_POST['fechafin'] != null ? $_POST['fechafin'] : $fechafin;
            $tipo     = $_POST['tipo'] != null ? $_POST['tipo'] : $tipo; 
            $datos    = $this->getLunchs2($fechaini, $fechafin, $tipo);
        }
        
        return  $this->render('_infolunch2', ['datos'=> $datos, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'tipo'=> $tipo,  'lunch'=> $lunch, 'tipoinfo'=> $tipoinfo]);
        
        
    }
   
    public function actionInfolunchpdf($fechaini, $fechafin, $tipo, $tipoinfo){
        
        $datos    = $this->getLunchs($fechaini, $fechafin, $tipo);
        
        $html =    $this->renderPartial('infolunchpdf',['datos'=> $datos, 'fechaini'=> $fechaini, 'fechafin'=> $fechafin, 'tipo' => $tipo, 'tipoinfo'=> $tipoinfo]);
        
        
       /* $mpdf = new Mpdf([
            'format' => 'A4',
            // 'orientation' => 'L'
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('InformeLunch.pdf', 'I');
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
            'marginBottom' => 19,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}  th, td{  padding:3px; } .text-center {text-align: center; margin: 1px;} ',
            
            // set mPDF properties on the fly
            //'options' => ['title' => 'Solicitud de Vacaciones'],
            // call mPDF methods on the fly
            'methods' => [
                'SetTitle' =>  'Informe Comedor',
                'SetHeader'=>['Sistema Gestión de Nómina - Informe Comedor||'],
                'SetFooter' => ['Impresión : '.Yii::$app->user->identity->username.' '.date('d/m/Y : H:i:s').'  || Página {PAGENO}']
            ]
        ]);
        
        // return the pdf output as per the destination setting
        return $pdf->render(); 
    }
    
    private function getLunchs($fechaini, $fechafin, $tipo){
        
        
        
        $datos = [];
        
        
        $lunchs =  (new \yii\db\Query())
        ->select([
            "emp.id_sys_rrhh_cedula",
            "emp.fecha",
            "emp.hora",
            "lunch.valor"])
        ->from("sys_rrhh_empleados_lunch emp")
        ->innerJoin('sys_rrhh_comedor lunch', 'emp.id_sys_rrhh_comedor = lunch.id_sys_rrhh_comedor')
        ->where("emp.id_sys_rrhh_comedor = '{$tipo}'")
        ->andwhere("fecha between '{$fechaini}' and '{$fechafin}'")
        ->all(SysRrhhEmpleados::getDb());
        
        foreach ($lunchs as $lunch):
        
        
             $emp = (new \yii\db\Query())
            ->select(["nombres", "area", "dep.id_sys_adm_area" , "departamento", "dep.id_sys_adm_departamento"])
            ->from("sys_rrhh_empleados emp")
            ->innerJoin("sys_adm_cargos cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
            ->innerJoin("sys_adm_departamentos dep","cargo.id_sys_adm_departamento =  dep.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","dep.id_sys_adm_area =  area.id_sys_adm_area ")
            ->where("id_sys_rrhh_cedula  = '{$lunch['id_sys_rrhh_cedula']}'")
            ->one(SysRrhhEmpleados::getDb());
             
            if($emp):
            
                  $datos[] = ['id_sys_rrhh_cedula' => $lunch['id_sys_rrhh_cedula'], 'nombres'=> $emp['nombres'], 'area'=> $emp['area'], 'id_sys_adm_area'=> $emp['id_sys_adm_area'], 'departamento'=> $emp['departamento'], 'id_sys_adm_departamento'=> $emp['id_sys_adm_departamento'], 'fecha'=> $lunch['fecha'], 'hora'=> $lunch['hora'], 'valor'=> $lunch['valor']]; 
            
              
            else:
                    
                     $visita = (new \yii\db\Query())
                    ->select(["area", "dep.id_sys_adm_area" , "departamento", "dep.id_sys_adm_departamento"])
                    ->from("sys_rrhh_comedor_visitas visita")
                    ->innerJoin("sys_adm_departamentos dep","visita.id_sys_adm_departamento =  dep.id_sys_adm_departamento")
                    ->innerJoin("sys_adm_areas area","dep.id_sys_adm_area =  area.id_sys_adm_area ")
                    ->where("codigo  = '{$lunch['id_sys_rrhh_cedula']}'")
                    ->one(SysRrhhEmpleados::getDb());
                    
                    
                    if($visita):
                    
                       $datos[] = ['id_sys_rrhh_cedula' => $lunch['id_sys_rrhh_cedula'], 'nombres'=> 'Visitas', 'area'=> $visita['area'], 'id_sys_adm_area'=> $visita['id_sys_adm_area'], 'departamento'=> 'VISITAS '.$visita['departamento'], 'id_sys_adm_departamento'=> $visita['id_sys_adm_departamento'], 'fecha'=> $lunch['fecha'], 'hora'=> $lunch['hora'],  'valor' => $lunch['valor']];
             
                    endif;
              
            endif;

        endforeach;
        
        return $datos;

        
    }
   
    private  function getLunchs2($fechaini, $fechafin, $tipo){
        
        $db    = $_SESSION['db'];
        return  Yii::$app->$db->createCommand("EXEC dbo.ObtenerLunchsComedor @fechaini = '{$fechaini}', @fechafin = '{$fechafin}', @tipo = {$tipo}")->queryAll();
    }
    
    public function actionAutorizacionlunch(){
        
        
        $cadena      =  json_decode(Yii::$app->request->post('cadena'));
        
        $empresas    = SysEmpresa::find()->where(['activo'=> 1])->all();
        
        $foto        =  '';
        
        $fotodefault =  '';
       
        $contlunch   =  0;
        
        $estado      =  false;
        
        $mensaje     = '';
  
        $ban        =  0;
        
        $fecha     = date('Y-m-d');
        
        foreach ($empresas as $empresa):
        
        
             $empleado =  (new \yii\db\Query())
            ->select(["id_sys_rrhh_cedula","desayuno", "almuerzo", "merienda", "id_sys_empresa"])
            ->from("sys_rrhh_empleados")
            ->where("estado = 'A'")
            ->andwhere("(codigo_temp = '{$cadena->codempleado}' or id_sys_rrhh_cedula = '{$cadena->codempleado}')")
            ->one(Yii::$app->get($empresa->db_name));
        
            //Valida autorizacion del empleado
            
            if($empleado):
                
                 $ban = 1;
                 $lunchs =  (new \yii\db\Query())
                ->select(["*"])
                ->from("sys_rrhh_empleados_lunch")
                ->where("fecha = '{$fecha}'")
                ->andwhere("id_sys_rrhh_cedula = '{$empleado['id_sys_rrhh_cedula']}'")
                ->andwhere("id_sys_rrhh_comedor = '{$cadena->id_sys_rrhh_comedor}'")
                ->all(Yii::$app->get($empresa->db_name));
                
              
                switch (intval($cadena->id_sys_rrhh_comedor)) {
                    
                    //Desayuno 
                    case 1:
                        
                           if(trim($empleado['desayuno']) == '1') :
                            
                                    if(count($lunchs) == 0):
                                        
                                        $this->registrarLunch($empleado['id_sys_rrhh_cedula'], $cadena->id_sys_rrhh_comedor, $empresa->id_sys_empresa, $empresa->db_name);
                                                           
                                        $estado = true;
        
                                    else:
                                    
                                        $mensaje = 'El empleador con cc ('.$empleado['id_sys_rrhh_cedula'].') no tiene autorización para un doble consumo!!';
          
                                    endif;
                            
                            else:
                            
                               $mensaje = 'El empleador con cc ('.$empleado['id_sys_rrhh_cedula'].') no tiene autorizado desayuno!!';
                            
                            endif;
                        
                        break;
                     
                    //Almuerzo   
                    case 2:    

                           if(trim($empleado['almuerzo']) == '1'):
                            
                                   if(count($lunchs) == 0):
                                    
                                        $this->registrarLunch($empleado['id_sys_rrhh_cedula'], $cadena->id_sys_rrhh_comedor, $empresa->id_sys_empresa, $empresa->db_name);
                                    
                                         $estado = true;
                                    
                                    else:
                                    
                                         $mensaje = 'El empleador con cc ('.$empleado['id_sys_rrhh_cedula'].') no tiene autorización para un doble consumo!!';
                                    
                                    endif;
                            
                            else:
                            
                                $mensaje = 'El empleador con cc ('.$empleado['id_sys_rrhh_cedula'].') no tiene autorizado almuerzo!!';
                            
                            endif;
                            
                            break;
                    
                    //Merienda
                    case 3:
                     
                        
                            if(trim($empleado['merienda']) == '1'):
                            
                                if(count($lunchs) == 0):
                                
                                        $this->registrarLunch($empleado['id_sys_rrhh_cedula'], $cadena->id_sys_rrhh_comedor, $empresa->id_sys_empresa, $empresa->db_name);
                                        
                                        $estado = true;
                                        
                                else:
                                
                                         $mensaje = 'El empleador con cc ('.$empleado['id_sys_rrhh_cedula'].') no tiene autorización para un doble consumo!!';
                                
                                endif;
                            
                            else:
                            
                              $mensaje = 'El empleador con cc ('.$empleado['id_sys_rrhh_cedula'].') no tiene autorizado merienda!!';
                            
                            endif;
                        
                      
                        break;

                    default:

                        if(count($lunchs) == 0):
                                                    
                            $this->registrarLunch($empleado['id_sys_rrhh_cedula'], $cadena->id_sys_rrhh_comedor, $empresa->id_sys_empresa, $empresa->db_name);
                                            
                            $estado = true;

                        else:
                        
                            $mensaje = 'El empleador con cc ('.$empleado['id_sys_rrhh_cedula'].') no tiene autorización para un doble consumo!!';

                        endif; 
                }
            
            
            else:
                
                    //Valida autorizacion credendencial visita 
        
                    $visita  =  (new \yii\db\Query())
                    ->select(["desayuno", "almuerzo", "merienda",  "tipo_visita", "codigo"])
                    ->from("sys_rrhh_comedor_visitas")
                    ->where("estado = 'A'")
                    ->andwhere("codigo = '{$cadena->codempleado}'")
                    ->andwhere("id_sys_empresa = '{$empresa->id_sys_empresa}'")
                    ->one(Yii::$app->get($empresa->db_name));
            
                    if($visita):
                    
                          
                    
                          if($visita['tipo_visita'] == 'A'):
                          
                              $ban = 1;
                              $this->registrarLunch($visita['codigo'], $cadena->id_sys_rrhh_comedor, $empresa->id_sys_empresa, $empresa->db_name);
                              $estado = true;
                                      
                          endif;
                    
                    
                    
                    endif;
                    
                    
            endif;
            
            
             $contlunch =   $contlunch +  (new \yii\db\Query())
            ->select(["count(*)"])
            ->from("sys_rrhh_empleados_lunch")
            ->where("fecha = '{$cadena->fecha}'")
            ->andwhere("id_sys_rrhh_comedor = '{$cadena->id_sys_rrhh_comedor}'")
            ->scalar(Yii::$app->get($empresa->db_name));
                               
        endforeach;
        
        if($ban==0):
        
            $mensaje = 'El Empleador no tiene acceso al comedor';
        
        endif;
  
  
        return json_encode([ 'data' => [ 'estado'=> $estado, 'mjs'=> $mensaje , 'foto'=> $foto, 'fotodefault' => $fotodefault, 'contlunch'=> $contlunch]]);
  
    
    }
    
    public function actionEntregacanasta(){
        
        $ncanastas  = (new \yii\db\Query())->select(["count(*)"])
        ->from("sys_rrhh_entrega_canasta")
        ->where("year(fecha) = year(getdate())")
        ->Scalar(SysRrhhEmpleados::getDb());
       
        return $this->render('_entregacanasta', ['ncanastas'=> $ncanastas]);
        
    }
    
    public function actionInformecanasta(){
        
        
        $area         =  '';
        $datos        = []; 
        $anio         = date('Y');
        
        if(Yii::$app->request->post()){
            
            $area  =  $_POST['area']== null ? '': $_POST['area'];
            $anio  =  $_POST['anio']== null ? $anio : $_POST['anio'];
            $datos = $this->getCanastasAreas($area, $anio);
        }
     
        return $this->render('_infocanastas', ['datos'=> $datos, 'area'=> $area, 'anio'=> $anio]);
       
    }
    
    public function actionInformecanastapdf($area, $anio){
       
        $datos = $this->getCanastasAreas($area, $anio);
        
        $html =    $this->renderAjax('infocanastaspdf',['datos'=> $datos,  'anio'=> $anio]);
        
        $mpdf = new Mpdf([
            'format' => 'A4',
            // 'orientation' => 'L'
        ]);
        $mpdf->WriteHTML($html);
        $mpdf->Output('InformeCanastas.pdf', 'I');
        exit();
        
    }

    public function actionMarcacionemanual(){
        
        $alimento = '';
        $fechaini = date('Y-m-d H:i');
        $cedula = '';
        $db    = $_SESSION['db'];
        
        if(Yii::$app->request->post()){
        
            $fechaini      = $_POST["fechainicio"] == null ? $fechaini:  $_POST["fechainicio"];
            $alimento      = $_POST["alimento"] == null ? '0' : $_POST['alimento'];
            $cedula        = $_POST["cedula"] == null ? '' :  $_POST["cedula"];
            
            if($alimento != '' ):

                $fecha = date('Y-m-d', strtotime($fechaini));

                $empleado =  (new \yii\db\Query())
                ->select(["id_sys_rrhh_cedula","desayuno", "almuerzo", "merienda", "id_sys_empresa"])
                ->from("sys_rrhh_empleados")
                ->where("estado = 'A'")
                ->andwhere("id_sys_rrhh_cedula = '{$cedula}'")
                ->one(Yii::$app->get($db));
        
                //Valida autorizacion del empleado
                
                if($empleado):
                    
                    $ban = 1;
                    $lunchs =  (new \yii\db\Query())
                    ->select(["*"])
                    ->from("sys_rrhh_empleados_lunch")
                    ->where("fecha = '{$fecha}'")
                    ->andwhere("id_sys_rrhh_cedula = '{$empleado['id_sys_rrhh_cedula']}'")
                    ->andwhere("id_sys_rrhh_comedor = '{$alimento}'")
                    ->all(Yii::$app->get($db));
                    
                
                    switch (intval($alimento)) {
                        
                        //Desayuno 
                        case 1:
                            
                            if(trim($empleado['desayuno']) == '1') :
                                
                                if(count($lunchs) == 0):
                                            
                                    $newmodel = new SysRrhhEmpleadosLunch();

                                    $newmodel->id_sys_rrhh_cedula = $cedula;
                                    $newmodel->id_sys_rrhh_comedor = $alimento;
                                    $newmodel->fecha = date('Y-m-d', strtotime($fechaini));
                                    $newmodel->hora = date('H:i:s', strtotime($fechaini));
                                    $newmodel->id_sys_empresa = '001';

                                    if($newmodel->save(false)):
            
                                        Yii::$app->getSession()->setFlash('info', [
                                                'type' => 'success','duration' => 1500,
                                                'icon' => 'glyphicons glyphicons-robot','message' => 'Se ha registro la alimentación con éxito!',
                                                'positonY' => 'top','positonX' => 'right']);
                                            
                                    else:
                                                
                                        Yii::$app->getSession()->setFlash('info', [
                                                'type' => 'warning','duration' => 1500,
                                                'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador!',
                                                'positonY' => 'top','positonX' => 'right']);
                                    
                                    endif;
            
                                else:
                                        
                                    Yii::$app->getSession()->setFlash('info', [
                                            'type' => 'warning','duration' => 1500,
                                            'icon' => 'glyphicons glyphicons-robot','message' => 'El empleador con cc ('.$empleado['id_sys_rrhh_cedula'].') no tiene autorización para un doble consumo!!',
                                            'positonY' => 'top','positonX' => 'right']);
            
                                endif;
                                
                            else:

                                Yii::$app->getSession()->setFlash('info', [
                                    'type' => 'warning','duration' => 1500,
                                    'icon' => 'glyphicons glyphicons-robot','message' => 'El empleador con cc ('.$empleado['id_sys_rrhh_cedula'].') no tiene autorizado desayuno!!',
                                    'positonY' => 'top','positonX' => 'right']);
                                
                            endif;
                            
                            break;
                        
                        //Almuerzo   
                        case 2:    

                            if(trim($empleado['almuerzo']) == '1'):
                                
                                if(count($lunchs) == 0):
                                        
                                    $newmodel = new SysRrhhEmpleadosLunch();

                                    $newmodel->id_sys_rrhh_cedula = $cedula;
                                    $newmodel->id_sys_rrhh_comedor = $alimento;
                                    $newmodel->fecha = date('Y-m-d', strtotime($fechaini));
                                    $newmodel->hora = date('H:i:s', strtotime($fechaini));
                                    $newmodel->id_sys_empresa = '001';
    
                                    if($newmodel->save(false)):
                
                                        Yii::$app->getSession()->setFlash('info', [
                                                'type' => 'success','duration' => 1500,
                                                'icon' => 'glyphicons glyphicons-robot','message' => 'Se ha registro la alimentación con éxito!',
                                                'positonY' => 'top','positonX' => 'right']);
                                                
                                    else:
                                                    
                                        Yii::$app->getSession()->setFlash('info', [
                                                'type' => 'warning','duration' => 1500,
                                                'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador!',
                                                'positonY' => 'top','positonX' => 'right']);
                                        
                                    endif;
                
                                else:
                                            
                                    Yii::$app->getSession()->setFlash('info', [
                                            'type' => 'warning','duration' => 1500,
                                            'icon' => 'glyphicons glyphicons-robot','message' => 'El empleador con cc ('.$empleado['id_sys_rrhh_cedula'].') no tiene autorización para un doble consumo!!',
                                            'positonY' => 'top','positonX' => 'right']);
                
                                endif;
                                    
                            else:
    
                                Yii::$app->getSession()->setFlash('info', [
                                        'type' => 'warning','duration' => 1500,
                                        'icon' => 'glyphicons glyphicons-robot','message' => 'El empleador con cc ('.$empleado['id_sys_rrhh_cedula'].') no tiene autorizado almuerzo!!',
                                        'positonY' => 'top','positonX' => 'right']);
                                    
                            endif;

                            break;
                        
                        //Merienda
                        case 3:
                        
                            if(trim($empleado['merienda']) == '1'):
                                
                                if(count($lunchs) == 0):
                                    
                                    $newmodel = new SysRrhhEmpleadosLunch();

                                    $newmodel->id_sys_rrhh_cedula = $cedula;
                                    $newmodel->id_sys_rrhh_comedor = $alimento;
                                    $newmodel->fecha = date('Y-m-d', strtotime($fechaini));
                                    $newmodel->hora = date('H:i:s', strtotime($fechaini));
                                    $newmodel->id_sys_empresa = '001';

                                    if($newmodel->save(false)):
            
                                        Yii::$app->getSession()->setFlash('info', [
                                                'type' => 'success','duration' => 1500,
                                                'icon' => 'glyphicons glyphicons-robot','message' => 'Se ha registro la alimentación con éxito!',
                                                'positonY' => 'top','positonX' => 'right']);
                                            
                                    else:
                                                
                                        Yii::$app->getSession()->setFlash('info', [
                                                'type' => 'warning','duration' => 1500,
                                                'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador!',
                                                'positonY' => 'top','positonX' => 'right']);
                                    
                                    endif;
            
                                else:
                                        
                                    Yii::$app->getSession()->setFlash('info', [
                                            'type' => 'warning','duration' => 1500,
                                            'icon' => 'glyphicons glyphicons-robot','message' => 'El empleador con cc ('.$empleado['id_sys_rrhh_cedula'].') no tiene autorización para un doble consumo!!',
                                            'positonY' => 'top','positonX' => 'right']);
            
                                endif;
                                
                            else:

                                Yii::$app->getSession()->setFlash('info', [
                                    'type' => 'warning','duration' => 1500,
                                    'icon' => 'glyphicons glyphicons-robot','message' => 'El empleador con cc ('.$empleado['id_sys_rrhh_cedula'].') no tiene autorizado merienda!!',
                                    'positonY' => 'top','positonX' => 'right']);
                                
                            endif;
                            
                        
                            break;

                    }

                endif;

            else:
              
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'warning','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'Seleccione el alimento!',
                    'positonY' => 'top','positonX' => 'right']);
              
            endif;
            
        }
        
        return $this->render('_asistenciamanual', ['fechaini'=> $fechaini,'alimento'=> $alimento, 'cedula' => $cedula]);
  
    }
    
    public function actionRegistracanasta(){
        
        
        $codempleado    =  trim(Yii::$app->request->get('codempleado'));
        
        $img            =  file_get_contents('img/sin_foto.jpg');
        $fotodefault    = 'data:image/jpeg;base64, '.base64_encode($img);
        $foto           =  '';
        $mensaje        =  '';
        $estado         =  false;
        $count          =  0;
       
        $empleado  = (new \yii\db\Query())
        ->select(["*"])
        ->from("sys_rrhh_empleados")
        ->where("estado = 'A'")
        ->andwhere("(codigo_temp = '{$codempleado}' or id_sys_rrhh_cedula = '{$codempleado}')")
        ->one(SysRrhhEmpleados::getDb());
        
        $count   =  (new \yii\db\Query())->select(["count(*)"])
        ->from("sys_rrhh_entrega_canasta")
        ->where("year(fecha) = year(getdate())")
        ->Scalar(SysRrhhEmpleados::getDb());
        
        if($empleado):
        
                $fotoemp  = (new \yii\db\Query())
                ->select(["foto","baze64"])
                ->from("sys_rrhh_empleados_foto cross apply (select foto as '*' for xml path('')) T (baze64)")
                ->where("id_sys_rrhh_cedula = '{$empleado['id_sys_rrhh_cedula']}'")
                ->one(SysRrhhEmpleados::getDb());
            
                if($fotoemp['baze64'] != null):
                    
                      $foto =  'data:image/jpeg;base64, '.$fotoemp['baze64'];
                
                endif;
             
                $model = (new \yii\db\Query())->select(["*"])
                ->from("sys_rrhh_entrega_canasta")
                ->where("year(fecha) = year(getdate())")
                ->andwhere("id_sys_rrhh_cedula = '{$empleado['id_sys_rrhh_cedula']}'")
                ->one(SysRrhhEmpleados::getDb()); 
             
             if(!$model):
             
                         $codigo   =  SysRrhhEntregaCanasta::find()->select(['max(CAST(id_sys_rrhh_entrega_canasta AS INT))'])->scalar() + 1;
             
                         $newmodel = new SysRrhhEntregaCanasta();
                         $newmodel->id_sys_rrhh_entrega_canasta = $codigo;
                         $newmodel->id_sys_rrhh_cedula          = $empleado['id_sys_rrhh_cedula'];
                         $newmodel->fecha                       = date('Y-m-d');
                         $newmodel->fecha_registro              = date('Ymd H:i:s');
                         $newmodel->usuarios_registro           = Yii::$app->user->username;
                         
                         if($newmodel->save(false)):
                         
                             $count   =  (new \yii\db\Query())->select(["count(*)"])
                             ->from("sys_rrhh_entrega_canasta")
                             ->where("year(fecha) = year(getdate())")
                             ->Scalar(SysRrhhEmpleados::getDb());
                         
                              $mensaje =  'Registro Exitoso!!';
                              $estado   = true;
                         
                         else:
                      
                             $mensaje =  'Ha Ocurrido un Error!!';
    
                         endif;
                                                 
             else:

                 $mensaje = 'El empleador ya registra entrega de Canasta Navideña!!';
       
             endif;
          
         
       else:
                $mensaje = 'El empleador no se encuentra registrado, Comuniquese con el departamento de DDOO!!';
        endif;
            
        
        return json_encode( [ 'data' => [ 'estado'=> $estado, 'mjs'=> $mensaje, 'foto'=> $foto, 'fotodefault' => $fotodefault, 'count'=> $count]]);
    }
    
    private function registrarLunch($id_sys_rrhh_cedula, $id_sys_rrhh_comedor,  $id_sys_empresa, $db){
        
        
        Yii::$app->$db->createCommand("INSERT INTO sys_rrhh_empleados_lunch (id_sys_rrhh_cedula, id_sys_rrhh_comedor, fecha, hora, id_sys_empresa) VALUES ('{$id_sys_rrhh_cedula}','{$id_sys_rrhh_comedor}', getdate(), getdate(), '$id_sys_empresa')")->execute(); 
    }
 
    private function getCanastasAreas($id_sys_adm_area, $anio){
        
      return   (new \yii\db\Query())->select(
            [
                "canastas.id_sys_rrhh_cedula",
                "emp.nombres",
                "area",
                "departamento"
            ])
            ->from("sys_rrhh_entrega_canasta canastas")
            ->innerJoin("sys_rrhh_empleados emp","canastas.id_sys_rrhh_cedula = emp.id_sys_rrhh_cedula")
            ->innerJoin("sys_adm_cargos cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
            ->innerJoin("sys_adm_departamentos dep","cargo.id_sys_adm_departamento  =  dep.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","dep.id_sys_adm_area =  area.id_sys_adm_area")
            ->where("year(fecha) = {$anio}")
            ->andfilterWhere(['like', "area.id_sys_adm_area",$id_sys_adm_area])
            ->orderBy("nombres")
            ->all(SysRrhhEmpleados::getDb());  
    }
      
    public  function actionAdddescuento(){
        
        if (Yii::$app->request->isAjax && Yii::$app->request->post()):
          
         $obj           =  json_decode( Yii::$app->request->post('data'));
        
         $horaDescuento = $obj->valor / 60 ;

         //obtenemos el sueldo del empleador
         $sueldoemp    =  SysRrhhEmpleadosSueldos::find()->select('sueldo')
         ->where(['id_sys_rrhh_cedula'=> $obj->id_sys_rrhh_cedula])
         ->andWhere(['estado'=> 'A'])
         ->scalar();
         
         $valorHora = $sueldoemp / 240;
         
         $novedad =  SysRrhhEmpleadosNovedades::find()->select('*')->Where(['id_sys_rrhh_concepto'=> 'DES_HORAS_NL'])->andWhere(['fecha' => $obj->fecha])->one();
         
         if(!$novedad):

             $db          = $_SESSION['db'];
    
             $transaction = \Yii::$app->$db->beginTransaction();
             
             $flag = true; 
             $codnovedad =  SysRrhhEmpleadosNovedades::find()->select(['max(CAST(id_sys_rrhh_empleados_novedad AS INT))'])->Where(['id_sys_empresa'=> '001'])->scalar();       
             $newnovedad = new SysRrhhEmpleadosNovedades();  
             $newnovedad->id_sys_rrhh_empleados_novedad = $codnovedad + 1 ;
             $newnovedad->id_sys_rrhh_cedula            = $obj->id_sys_rrhh_cedula;
             $newnovedad->id_sys_empresa                = '001';
             $newnovedad->id_sys_rrhh_concepto          = 'DES_HORAS_NL';
             $newnovedad->fecha                         = $obj->fecha;
             $newnovedad->cantidad                      = floatval($horaDescuento) * floatval($valorHora);
             $newnovedad->comentario                    = "DESCUENTO POR SOBRE TIEMPO COMEDOR";
             $newnovedad->transaccion_usuario           = Yii::$app->user->username;
             $flag = $newnovedad->save(false);
           
                 
             if($flag):
             
                 $transaction->commit();
                 echo  json_encode(['data' => [ 'estado' => false,'mensaje' => 'Los datos se ha registrado con éxito. Consulte el módulo de novedades ']]);
                 
             else:
             
                 $transaction->rollBack();
                 echo  json_encode(['data' => ['estado' => false, 'mensaje' => 'Ha ocurrido un error al guardar la noveadad!']]);
                 
             endif;
        else:
          echo  json_encode(['data' => ['estado' => false, 'mensaje' => 'Ya se aplico descuento para esta fecha. Consulte el módulo de novedades!']]);
        endif;
         
        else:
        
          echo  json_encode(['data' => ['estado' => false, 'mensaje' => 'Ha ocurrido un error!']]);
        
        endif;
    

}

public function actionCargarasistencialote(){
        
    return $this->render('_asistencialote');
}
    
public function actionGuardarconsumolote(){
        
    if (Yii::$app->request->isAjax && Yii::$app->request->post()) {
    
              $obj         =  json_decode(Yii::$app->request->post('cadena'));
            
              $novedades   =  $obj->{'empleados'};
              
              $db          = $_SESSION['db'];

                $error       =  [];  
                $error1      =  [];
                $error2      =  [];
                $error3      =  [];
              
              foreach ($novedades as $data ){
                  
                  //verificamos el  empleador 
                  $empleado =  (new \yii\db\Query())
                  ->select(["id_sys_rrhh_cedula","desayuno", "almuerzo", "merienda", "id_sys_empresa","id_sys_adm_ccosto"])
                  ->from("sys_rrhh_empleados")
                  ->where("estado = 'A'")
                  ->andwhere("id_sys_rrhh_cedula = '{$data->cedula}'")
                  ->one(Yii::$app->get($db));
                  
                  $fecha =  date('Y-m-d',strtotime($obj->{'fechaingreso'}));
                  
                   if ($empleado){

                    $lunchs =  (new \yii\db\Query())
                    ->select(["*"])
                    ->from("sys_rrhh_empleados_lunch")
                    ->where("fecha = '{$fecha}'")
                    ->andwhere("id_sys_rrhh_cedula = '{$empleado['id_sys_rrhh_cedula']}'")
                    ->andwhere("id_sys_rrhh_comedor = '{$obj->{'tipoalimento'}}'")
                    ->all(Yii::$app->get($db));
                    
                
                    switch (intval($obj->{'tipoalimento'})) {
                        
                        //Desayuno 
                        case 1:
                            
                            if(trim($empleado['desayuno']) == '1') :
                                
                                if(count($lunchs) == 0):
                                            
                                    $newnovedad = new SysRrhhEmpleadosLunch();
                       
                                    $newnovedad->id_sys_rrhh_cedula            =  $empleado['id_sys_rrhh_cedula'];
                                    $newnovedad->id_sys_empresa                = '001';
                                    $newnovedad->id_sys_rrhh_comedor           = $obj->{'tipoalimento'};
                                    $newnovedad->fecha                         = date('Y-m-d',strtotime($obj->{'fechaingreso'}));
                                    $newnovedad->hora                          = date('H:i:s', strtotime($obj->{'fechaingreso'}));
                                   
                                    if($newnovedad->save(false)){
                                        
                                        
                                    }else{

                                        $error [] = array('Empleado' => $empleado['id_sys_rrhh_cedula']);
                                    } 
                                    
                                    $newmarcacion = new SysRrhhEmpleadosMarcacionesReloj();

                                    $newmarcacion->id_sys_rrhh_cedula  = $empleado['id_sys_rrhh_cedula'];
                                    $newmarcacion->fecha_marcacion     = date('Ymd H:i:s',strtotime($obj->{'fechasalida'}));
                                    $newmarcacion->id_sys_empresa      = '001';
                                    $newmarcacion->tipo                = 'SD';
                                    $newmarcacion->fecha_jornada       = $fecha;
                                    $newmarcacion->validar             = 0;
                                    $newmarcacion->estado              = 'A';
                                    $newmarcacion->fecha_sistema       = date('Ymd H:i:s',strtotime($obj->{'fechasalida'}));
                                    $newmarcacion->id_sys_adm_ccostos  = $empleado['id_sys_adm_ccosto'];
                                    $newmarcacion->transaccion_usuario = Yii::$app->user->username;
            
                                    if($newmarcacion->save(false)){
                                        
                                        
                                    }else{

                                        $error3 [] = array('Empleado' => $empleado['id_sys_rrhh_cedula']);
                                    } 

                                else:

                                    $error1 [] = array('Empleado' => $empleado['id_sys_rrhh_cedula']);
                                        
                                endif;
                                
                            else:

                                $error2 [] = array('Empleado' => $empleado['id_sys_rrhh_cedula']);
                                
                            endif;
                            
                            break;
                        
                        //Almuerzo   
                        case 2:    

                            if(trim($empleado['almuerzo']) == '1'):
                                
                                if(count($lunchs) == 0):
                                        
                                    $newnovedad = new SysRrhhEmpleadosLunch();
                       
                                    $newnovedad->id_sys_rrhh_cedula            =  $empleado['id_sys_rrhh_cedula'];
                                    $newnovedad->id_sys_empresa                = '001';
                                    $newnovedad->id_sys_rrhh_comedor           = $obj->{'tipoalimento'};
                                    $newnovedad->fecha                         = date('Y-m-d',strtotime($obj->{'fechaingreso'}));
                                    $newnovedad->hora                          = date('H:i:s', strtotime($obj->{'fechaingreso'}));
                                   
                                    if($newnovedad->save(false)){
                     
                                        
                                    }else{

                                        $error [] = array('Empleado' =>  $empleado['id_sys_rrhh_cedula']);
                                    }   

                                      
                                    $newmarcacion = new SysRrhhEmpleadosMarcacionesReloj();

                                    $newmarcacion->id_sys_rrhh_cedula  = $empleado['id_sys_rrhh_cedula'];
                                    $newmarcacion->fecha_marcacion     = date('Ymd H:i:s',strtotime($obj->{'fechasalida'}));
                                    $newmarcacion->id_sys_empresa      = '001';
                                    $newmarcacion->tipo                = 'SA';
                                    $newmarcacion->fecha_jornada       = $fecha;
                                    $newmarcacion->validar             = 0;
                                    $newmarcacion->estado              = 'A';
                                    $newmarcacion->fecha_sistema       = date('Ymd H:i:s',strtotime($obj->{'fechasalida'}));
                                    $newmarcacion->id_sys_adm_ccostos  = $empleado['id_sys_adm_ccosto'];
                                    $newmarcacion->transaccion_usuario = Yii::$app->user->username;
            
                                    if($newmarcacion->save(false)){
                                        
                                        
                                    }else{

                                        $error3 [] = array('Empleado' => $empleado['id_sys_rrhh_cedula']);
                                    } 
            
                                else:

                                    $error1 [] = array('Empleado' =>  $empleado['id_sys_rrhh_cedula']);
                                        
                                endif;
                                
                            else:

                                $error2 [] = array('Empleado' =>  $empleado['id_sys_rrhh_cedula']);
                                
                            endif;
                            
                            break;
                        
                        
                        //Merienda
                        case 3:
                        
                            if(trim($empleado['merienda']) == '1'):
                                
                                if(count($lunchs) == 0):
                                    
                                    $newnovedad = new SysRrhhEmpleadosLunch();
                       
                                    $newnovedad->id_sys_rrhh_cedula            =  $empleado['id_sys_rrhh_cedula'];
                                    $newnovedad->id_sys_empresa                = '001';
                                    $newnovedad->id_sys_rrhh_comedor           = $obj->{'tipoalimento'};
                                    $newnovedad->fecha                         = date('Y-m-d',strtotime($obj->{'fechaingreso'}));
                                    $newnovedad->hora                          = date('H:i:s', strtotime($obj->{'fechaingreso'}));
                                   
                                    if($newnovedad->save(false)){
                                        
                                        
                                    }else{

                                        $error [] = array('Empleado' =>  $empleado['id_sys_rrhh_cedula']);
                                    }   

                                      
                                    $newmarcacion = new SysRrhhEmpleadosMarcacionesReloj();

                                    $newmarcacion->id_sys_rrhh_cedula  = $empleado['id_sys_rrhh_cedula'];
                                    $newmarcacion->fecha_marcacion     = date('Ymd H:i:s',strtotime($obj->{'fechasalida'}));
                                    $newmarcacion->id_sys_empresa      = '001';
                                    $newmarcacion->tipo                = 'SM';
                                    $newmarcacion->fecha_jornada       = $fecha;
                                    $newmarcacion->validar             = 0;
                                    $newmarcacion->estado              = 'A';
                                    $newmarcacion->fecha_sistema       = date('Ymd H:i:s',strtotime($obj->{'fechasalida'}));
                                    $newmarcacion->id_sys_adm_ccostos  = $empleado['id_sys_adm_ccosto'];
                                    $newmarcacion->transaccion_usuario = Yii::$app->user->username;
            
                                    if($newmarcacion->save(false)){
                                        
                                        
                                    }else{

                                        $error3 [] = array('Empleado' => $empleado['id_sys_rrhh_cedula']);
                                    } 
            
                                else:

                                    $error1 [] = array('Empleado' =>  $empleado['id_sys_rrhh_cedula']);
                                        
                                endif;
                                
                            else:

                                $error2 [] = array('Empleado' =>  $empleado['id_sys_rrhh_cedula']);
                                
                            endif;
                            
                            break;

                    }
                          
                }
                
             }

                if(count($error) > 0) :
                   
                    return json_encode(['data'=> ['estado'=>  false , 'mjs'=> 'No se pudo guardar los datos'.json_encode($error).' comuniquese con su administrador!']]);
              
                endif;
    
                if(count($error1) > 0) :
                   
                    return json_encode(['data'=> ['estado'=>  false , 'mjs'=> 'No se pudo guardar los datos'.json_encode($error1).' por que usted no tiene derecho a doble consumo']]);
              
                endif;
    
                if(count($error2) > 0) :
                   
                    return json_encode(['data'=> ['estado'=>  false , 'mjs'=> 'No se pudo guardar los datos'.json_encode($error2).' por que no tiene autorizado este alimento']]);
              
                endif;

                if(count($error3) > 0) :
                   
                    return json_encode(['data'=> ['estado'=>  false , 'mjs'=> 'No se pudo guardar los datos'.json_encode($error3).' comuniquese con su administrador!']]);
              
                endif;
            
                    return json_encode(['data'=> ['estado'=>  true , 'mjs'=> 'Registro con éxito']]);
                     
    }else{
        
         echo  json_encode(['data' => ['estado' => false, 'mensaje' => 'Ha ocurrido un error!']]);
    }
}
    
    /**
     * Finds the SysRrhhComedor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_sys_rrhh_comedor
     * @param string $id_sys_empresa
     * @return SysRrhhComedor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SysRrhhComedor::findOne(['id_sys_rrhh_comedor' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
