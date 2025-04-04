<?php

namespace app\controllers;

use Yii;
use app\models\SysEmpresa;
use app\models\SysRrhhComedorVisitas;
use app\models\SysRrhhEmpleados;
use app\models\Search\SysRrhhComedorVisitasSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\SysAdmAreas;
use app\models\SysAdmDepartamentos;

/**
 * ComedorVisitasController implements the CRUD actions for SysRrhhComedorVisitas model.
 */
class ComedorVisitasController extends Controller
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
     * Lists all SysRrhhComedorVisitas models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysRrhhComedorVisitasSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysRrhhComedorVisitas model.
     * @param string $id_sys_rrhh_comedor_visita
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id_sys_rrhh_comedor_visita, $id_sys_empresa)
    {
        return $this->render('view', [
            'model' => $this->findModel($id_sys_rrhh_comedor_visita, $id_sys_empresa),
        ]);
    }

    /**
     * Creates a new SysRrhhComedorVisitas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SysRrhhComedorVisitas();
        $empresa = SysEmpresa::find()->where(['db_name'=> $_SESSION['db']])->one();

        if ($model->load(Yii::$app->request->post())) {
            
                    $codigo =  SysRrhhComedorVisitas::find()->select(['max(CAST(id_sys_rrhh_comedor_visita AS INT))'])->Where(['id_sys_empresa'=> '001'])->scalar() +1 ;
                    
                    $model->id_sys_rrhh_comedor_visita = $codigo;
                    
                    $model->id_sys_empresa = $empresa->id_sys_empresa;
                    
                    $model->codigo = $this->getCodigoCredencial($codigo, $model->id_sys_adm_departamento);
                    
                    if($model->save(false)):
    
                             Yii::$app->getSession()->setFlash('info', [
                            'type' => 'success','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'La credencial se ha registrado con éxito!',
                            'positonY' => 'top','positonX' => 'right']);
                        
                    
                    else:
                        
                             Yii::$app->getSession()->setFlash('info', [
                            'type' => 'danger','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                            'positonY' => 'top','positonX' => 'right']);
                        
                        
                    endif;
                
                    return $this->redirect('index');
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SysRrhhComedorVisitas model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id_sys_rrhh_comedor_visita
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id_sys_rrhh_comedor_visita, $id_sys_empresa)
    {
        $model = $this->findModel($id_sys_rrhh_comedor_visita, $id_sys_empresa);

        if ($model->load(Yii::$app->request->post())) {
            
            if($model->save(false)):
            
                Yii::$app->getSession()->setFlash('info', [
                'type' => 'success','duration' => 1500,
                'icon' => 'glyphicons glyphicons-robot','message' => 'La credencial ha sido actualizada  con éxito!',
                'positonY' => 'top','positonX' => 'right']);
            
            
            else:
            
               Yii::$app->getSession()->setFlash('info', [
               'type' => 'danger','duration' => 1500,
               'icon' => 'glyphicons glyphicons-robot','message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
               'positonY' => 'top','positonX' => 'right']);
                
            
            endif;
          
            return $this->redirect('index');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
    
    

    /**
     * Deletes an existing SysRrhhComedorVisitas model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id_sys_rrhh_comedor_visita
     * @param string $id_sys_empresa
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id_sys_rrhh_comedor_visita, $id_sys_empresa)
    {
       // $this->findModel($id_sys_rrhh_comedor_visita, $id_sys_empresa)->delete();

        $model  = $this->findModel($id_sys_rrhh_comedor_visita, $id_sys_empresa);
                
        $model->estado = 'I';
        
        $model->save(false);

        return $this->redirect(['index']);
    }
    
    public function actionImprimecredencial($id_sys_rrhh_comedor_visita, $id_sys_empresa){
        
        $datos =  (new \yii\db\Query())->select(
            [
                "codigo", 
                "id_sys_rrhh_comedor_visita",
                "departamento",
                "cre.id_sys_empresa"
                
            ])
            ->from("sys_rrhh_comedor_visitas cre")
            ->innerJoin("sys_adm_departamentos as dep","cre.id_sys_adm_departamento = dep.id_sys_adm_departamento")
            ->where("id_sys_rrhh_comedor_visita = '{$id_sys_rrhh_comedor_visita}'")
            ->andwhere("cre.id_sys_empresa =  '{$id_sys_empresa}'")
            ->one(SysRrhhEmpleados::getDb());
       
        return   $this->render('_credencial',['datos'=>$datos]);
        
    }
    
    

    /**
     * Finds the SysRrhhComedorVisitas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id_sys_rrhh_comedor_visita
     * @param string $id_sys_empresa
     * @return SysRrhhComedorVisitas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id_sys_rrhh_comedor_visita, $id_sys_empresa)
    {
        if (($model = SysRrhhComedorVisitas::findOne(['id_sys_rrhh_comedor_visita' => $id_sys_rrhh_comedor_visita, 'id_sys_empresa' => $id_sys_empresa])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
    private function getCodigoCredencial($id, $departamento){
        
        $secuencia =  str_pad($id, 3, "0", STR_PAD_LEFT); 
        $departa   = str_pad($departamento, 3, "0", STR_PAD_LEFT);
        $area      = str_pad( SysAdmDepartamentos::find()->select('id_sys_adm_area')->where(['id_sys_adm_departamento'=> $departamento])->scalar(), 2, "0", STR_PAD_LEFT);
       
        
        return $area.''.$departa.''.$secuencia;
        
        
    }
    
}
