<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\SysEmpresa;
use app\models\SysRrhhEmpleados;
use yii\web\Session;
use app\models\User;


class SiteController extends Controller
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
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {

        $db =  $_SESSION['db'];

        if($db != 'DB_Indicadores'):
        
            $empActivos = $this->getEmpleadosActivos();
            $empXAreas = $this->getEmpleadosAreas();
            $empIngresos = $this->IngresoPersonalAnioActualVsAnioAnterior();
            $empSalida = $this->SalidaPersonalAnioActualVsAnioAnterior();
            $tipoContrato = $this->getTipodeContrato();
            
            return $this->render('index', ['empActivos'=> $empActivos, 'empXAreas'=> $empXAreas, 'empIngresos' => $empIngresos, 'empSalida' => $empSalida, 'tipoContrato' => $tipoContrato]);
        else:

            return $this->render('index');

        endif;
    
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
     
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            
            $id = Yii::$app->user->id;
            Yii::$app->db->createCommand("update [dbo].[user] set intentos = 0 where id ='{$id}' ")->execute();
            $empresa = SysEmpresa::find()->where(['id_sys_empresa'=> $model->empresa])->one();
            Yii::$app->session->set('db',$empresa->db_name);
            Yii::$app->session->set('empresa',$model->empresa);
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
 
        return $this->goHome();

    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
    
    
    private function getEmpleadosActivos(){
        $db =  $_SESSION['db'];
        $arraydata = [];
        
        $datos =  Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerEmpleadosActivos]")->queryAll();
	
	     foreach ( $datos as $data):
	        array_push($arraydata,  ["name"=> $data["name"]== "M"? "HOMBRES": "MUJERES", "y" => floatval($data["y"])]);
	     endforeach;
	     
	     return $arraydata;
    }

    private function getTipodeContrato(){        
        $db =  $_SESSION['db'];
        $arraydata = [];
        
        $datos = Yii::$app->$db->createCommand("EXEC [dbo].[PersonalContrato]")->queryAll();
       
        foreach ( $datos as $data):
           array_push($arraydata,  ["name"=> $data["name"], "y" => floatval($data["y"])]);
        endforeach;
        
        return $arraydata;
        
    }
    
    private function getEmpleadosAreas(){
        
        $arraydata = [];
        
        $datos = (new \yii\db\Query())->select(
            [
                "area.area as name",
                "count(*) as y",
            
            ])
            ->from("sys_rrhh_empleados emp")
            ->innerJoin("sys_adm_cargos as cargo","emp.id_sys_adm_cargo = cargo.id_sys_adm_cargo")
            ->innerJoin("sys_adm_departamentos as departamento","cargo.id_sys_adm_departamento = departamento.id_sys_adm_departamento")
            ->innerJoin("sys_adm_areas area","departamento.id_sys_adm_area = area.id_sys_adm_area")
            ->andWhere("emp.estado = 'A'")
            ->groupBy("area.area")
            ->all(SysRrhhEmpleados::getDb());
        
            
            foreach ( $datos as $data):
                 array_push($arraydata,  ["name"=> $data["name"], "y" => floatval($data["y"])]);
            endforeach;
            
            return $arraydata;
        
    }
    private  function IngresoPersonalAnioActualVsAnioAnterior(){
        
        $arrayData = [];
        $data1 = [];
        $data2 = [];
        $data3 = [];
        $data4 = [];
        $monthNames = [1,2,3,4,5,6,7,8,9,10,11,12];
        
        $anioAct = date('Y');
        $anioAnt = date('Y') - 1;
        
        //Ingreso Personal Año Anterior 
        $ingresoAnt  = (new \yii\db\Query())->select(
            [
                "month(fecha_ingreso) as mes",
                "count(*) as ingreso"
                
            ])
         ->from("dbo.sys_rrhh_empleados_contratos")
         ->andWhere("year(fecha_ingreso) = {$anioAnt}")
         ->groupBy("month(fecha_ingreso)")
         ->all(SysRrhhEmpleados::getDb());
            
         
         
         //Ingreso Personal Año Actual
           $ingresoAct  = (new \yii\db\Query())->select(
                [
                    "month(fecha_ingreso) as mes",
                    "count(*) as ingreso"
                    
                ])
           ->from("dbo.sys_rrhh_empleados_contratos")
           ->andWhere("year(fecha_ingreso) = {$anioAct}")
           ->groupBy("month(fecha_ingreso)")
           ->all(SysRrhhEmpleados::getDb());
              
         
        foreach ($ingresoAnt as $i):
                
            $data4[] = $i['mes'];
        
        endforeach;
        
        foreach ($monthNames as $mes):
             
            if(in_array($mes, $data4)):
                foreach ($ingresoAnt as $i):

                    if($mes == $i['mes']):
        
                        $data1[]= floatval($i['ingreso']);
                        
                    endif;
                    
                endforeach;
                    
            else:
                $data1[] = 0;
            endif;

        endforeach;
        
            array_push($arrayData, ["name" => $anioAnt, "data"=> $data1]);
        
        foreach ($ingresoAct as $i):
        
            $data3[] = $i['mes'];
        
        endforeach;
        
        foreach ($monthNames as $mes):
             
            if(in_array($mes, $data3)):
                foreach ($ingresoAct as $i):

                    if($mes == $i['mes']):
        
                        $data2[] = floatval($i['ingreso']);
                        
                    endif;
                    
                endforeach;
                    
            else:
                $data2[] = 0;
            endif;

        endforeach;

          
          array_push($arrayData, ["name" => $anioAct, "data"=> $data2]);
          
          return $arrayData;
      
    }
    private  function SalidaPersonalAnioActualVsAnioAnterior(){
        
        $arrayData = [];
        $data1     = [];
        $data2     = [];
        $data3 = [];
        $data4 = [];
        $monthNames = [1,2,3,4,5,6,7,8,9,10,11,12];
        
        $anioAct = date('Y');
        $anioAnt = date('Y') - 1;
        
        //Ingreso Personal Año Anterior
        $ingresoAnt  = (new \yii\db\Query())->select(
            [
                "month(fecha_salida) as mes",
                "count(*) as salida"
                
            ])
            ->from("dbo.sys_rrhh_empleados_contratos")
            ->andWhere("year(fecha_salida) = {$anioAnt}")
            ->groupBy("month(fecha_salida)")
            ->all(SysRrhhEmpleados::getDb());
            
            
            
            //Ingreso Personal Año Actual
            $ingresoAct  = (new \yii\db\Query())->select(
                [
                    "month(fecha_salida) as mes",
                    "count(*) as salida"
                    
                ])
                ->from("dbo.sys_rrhh_empleados_contratos")
                ->andWhere("year(fecha_salida) = {$anioAct}")
                ->groupBy("month(fecha_salida)")
                ->all(SysRrhhEmpleados::getDb());
                
                
                foreach ($ingresoAnt as $i):
                
                    $data3[] = $i['mes'];
                
                endforeach;
                
                foreach ($monthNames as $mes):
                     
                    if(in_array($mes, $data3)):
                        foreach ($ingresoAnt as $i):

                            if($mes == $i['mes']):
                
                                $data1[]= floatval($i['salida']);
                                
                            endif;
                            
                        endforeach;
                            
                    else:
                        $data1[] = 0;
                    endif;

                endforeach;
                
                array_push($arrayData, ["name" => $anioAnt, "data"=> $data1]);
                
                foreach ($ingresoAct as $i):
                
                    $data4[] = $i['mes'];
                
                endforeach;
                
                foreach ($monthNames as $mes):
                     
                    if(in_array($mes, $data4)):
                        foreach ($ingresoAct as $i):

                            if($mes == $i['mes']):
                
                                $data2[]= floatval($i['salida']);
                                
                            endif;
                            
                        endforeach;
                            
                    else:
                        $data2[] = 0;
                    endif;

                endforeach;
                
                
                array_push($arrayData, ["name" => $anioAct, "data"=> $data2]);
                
                return $arrayData;
                
    }
    
    
}
