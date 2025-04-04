<?php

namespace app\controllers;

use app\models\Model;
use app\models\Search\SysAccesoProveedoresSearch;
use app\models\Search\SysSsooEPPSearch;
use app\models\SysSsooEPP;
use Exception;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AreasController implements the CRUD actions for SysAccesoProveedores model.
 */
class MaestroeppController extends Controller
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
     * Lists all SysAccesoProveedores models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SysSsooEPPSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SysAccesoProveedores model.
     * @param string $id_sys_empresa
     * @param string $id_sys_adm_area
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
     * Creates a new SysAccesoProveedores model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    /**
     * Updates an existing SysAccesoProveedores model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id_sys_empresa
     * @param string $id_sys_adm_area
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {


            if ($model->save(false)) {

                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'success',
                    'duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot',
                    'message' => 'Los datos han sido actualizados con  éxito!',
                    'positonY' => 'top',
                    'positonX' => 'right'
                ]);
            } else {

                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger',
                    'duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot',
                    'message' => 'Ha ocurrido un error. Comuniquese con su administrador! ',
                    'positonY' => 'top',
                    'positonX' => 'right'
                ]);
            }
            return $this->redirect('index');
        }

        return $this->render('update', [
            'model' => $model,
            'inputDisable' => 1
        ]);
    }

    /**
     * Deletes an existing SysAccesoProveedores model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id_sys_empresa
     * @param string $id_sys_adm_area
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model =  $this->findModel($id);
        $model->estado = 'I';
        $model->save(false);
        return $this->redirect(['index']);
    }

    public function actionCreate()
    {
        $model          = new SysSsooEPP();

        $db = $_SESSION['db'];

        if ($model->load(Yii::$app->request->post())) {
            $transaction = \Yii::$app->$db->beginTransaction();

            try {

                $model->nombre  = strtoupper($model->nombre);
                $model->estado  = strtoupper($model->estado);
                // $model->estado  = Yii::$app->user->username;
                $model->vida_util    = $model->vida_util;
                $model->um    = $model->um;
                $model->save();

                $transaction->commit();
            } catch (Exception $e) {

                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('info', [
                    'type' => 'danger',
                    'duration' => 1500,
                    'icon' => 'glyphicons glyphicons-robot',
                    'message' => 'Ha ocurrido un error. Comuniquese con su administrador!' . $e->getMessage(),
                    'positonY' => 'top',
                    'positonX' => 'right'
                ]);
                return $e->getMessage();
            }


            Yii::$app->getSession()->setFlash('info', [
                'type' => 'success',
                'duration' => 3000,
                'icon' => 'glyphicons glyphicons-robot',
                'message' => 'El EPP ha sido registrado con éxito!',
                'positonY' => 'top',
                'positonX' => 'right'
            ]);

            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }



    protected function findModel($id)
    {
        if (($model = SysSsooEPP::findOne(['id_sys_ssoo_epp' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
