<?php

use app\models\SysGrupoAutorizacion;
use app\models\SysUserGrupoAutorizacion;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flujo de Autorizaciones';
$this->params['breadcrumbs'][] = $this->title;
$this->render('../_alertFLOTADOR'); 
?>
<div class="sys-user-grupo-autorizacion-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Registrar Flujo', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                
                'label'=> 'Usuarios',
                'filter'=> ArrayHelper::map(User::find()->where(['empresa'=> $_SESSION['empresa']])->andwhere(['status'=> '1'])->addOrderBy('username')->all(), 'id', 'username'),
                'attribute'=>'id_usuario',
                'format' => 'raw',
                'value'=> function($model){
                $user =  User::find()->where(['id'=>$model->id_usuario])->one();
                if($user):
                 return $user->username;
                endif;
                } ,
            ],
           [
              'label'=> 'Flujos',
               'filter'=> ArrayHelper::map(SysGrupoAutorizacion::find()->all(), 'id', 'nombre'),
              'attribute'=>'id_sys_grupo_autorizacion',
              'format' => 'raw',
              'value'=> function($model){
                    $grupo =  SysGrupoAutorizacion::find()->where(['id'=>$model->id_sys_grupo_autorizacion])->one();
                   if($grupo):
                         return $grupo->nombre;
                    endif;
                } ,
             ],
             [
                'label'=>'Nivel de Autorización',
                //'filter'=> ArrayHelper::map(SysUserGrupoAutorizacion::find()->all(),'id','nivel_autorizacion'),
                'attribute'=>'nivel_autorizacion',
                'format' => 'raw',
                'value'=> function($model){
                    $tipos = Yii::$app->params['niveles_autorizacion'];
                    return $tipos[$model->nivel_autorizacion];
                } ,
            ],
            [
                
                'label'=> 'Activo',
                'attribute'=>'activo',
                'format' => 'raw',
                'value'=> function($model){
                    return $model->activo == 1 ? "SI": "NO";
                 } ,
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
