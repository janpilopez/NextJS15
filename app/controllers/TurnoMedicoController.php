<?php

namespace app\controllers;

use Yii;
use app\models\sysMedTurnoMedico;
use app\models\search\sysMedTurnoMedicoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TurnoMedicoController implements the CRUD actions for sysMedTurnoMedico model.
 */
class TurnoMedicoController extends Controller
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
     * Lists all sysMedTurnoMedico models.
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = '@app/views/layouts/main_emplados';
        $searchModel = new sysMedTurnoMedicoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * Displays a single sysMedTurnoMedico model.
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
     * Creates a new sysMedTurnoMedico model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new sysMedTurnoMedico();

        if ($model->load(Yii::$app->request->post())) {
           
            $numTurno =  sysMedTurnoMedico::find()->select(['max(numero)'])->scalar() + 1;
            $model->usuario_creacion = Yii::$app->user->username;
            $model->numero = $numTurno;
            $model->fecha  = date('Y-m-d');
            $model->fecha_creacion = date('Ymd H:i:s');
            $model->ini_atencion = date('Ymd H:i:s');
            $model->anulado = 0;
            $model->atendido = 0;
            if($model->save(false)){
                
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'success','duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot','message' => 'El turno  # '.$model->numero.' han sido generado con éxito!',
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
     * Updates an existing sysMedTurnoMedico model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        
        if($model->atendido == 0):
        
        
           if($model->id_sys_med_tipo_motivo != 1):
        
                if ($model->load(Yii::$app->request->post())) {
                    $model->medico = Yii::$app->user->username;
                    $model->atendido = 1;
                    $model->comentario = strtoupper($model->comentario);
                    $model->fecha_atencion = date('Ymd H:i:s');
                    $model->fin_atencion = date('Ymd H:i:s');
                    if($model->save(false)){
                        
                        Yii::$app->getSession()->setFlash('info', [
                            'type' => 'success','duration' => 1500,
                            'icon' => 'glyphicons glyphicons-robot','message' => 'El turno  # '.$model->numero.' han sido atendido con éxito!',
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
            
            else:
                 return Yii::$app->response->redirect(['consulta-medica/create', 'turno' => $model->id]);
            endif;
            
            
        else:
        
           Yii::$app->getSession()->setFlash('info', [
            'type' => 'warning','duration' => 1500,
            'icon' => 'glyphicons glyphicons-robot','message' => 'El turno ya ha sido atendido!',
            'positonY' => 'top','positonX' => 'right']);
    
           return $this->redirect('index');
           
        endif;
        
    }

    /**
     * Deletes an existing sysMedTurnoMedico model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model =  $this->findModel($id);
        $model->anulado = 1;
        $model->save();
        
        return $this->redirect(['index']);
    }

    public function actionTurnosnoatendidos()
    {
        $db =  $_SESSION['db'];
        $datos = Yii::$app->$db->createCommand("EXEC [dbo].[ObtenerTurnosMedicosNoAtendidos]")->queryAll();
    
        return json_encode($datos);
    }

    /**
     * Finds the sysMedTurnoMedico model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = sysMedTurnoMedico::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
