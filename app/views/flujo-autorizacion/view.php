<?php

use app\models\SysGrupoAutorizacion;
use app\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysUserGrupoAutorizacion */
$this->title = 'Ver Flujo';
$this->params['breadcrumbs'][] = ['label' => 'Flujo de Autorizaciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-user-grupo-autorizacion-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
           // 'id_usuario',
            
            [
                
                'attribute'=>'id_usuario',
                'value'=> function($model){
                
                $user =  User::find()->where(['id'=>$model->id_usuario])->one();
                if($user):
                     return $user->username;
                endif;
                
                }
             ],
             [ 
                 'attribute'=>'id_sys_grupo_autorizacion',
                 'value'=> function($model){
                 
                 $grupo =  SysGrupoAutorizacion::find()->where(['id'=>$model->id_sys_grupo_autorizacion])->one();
                 if($grupo):
                    return $grupo->nombre;
                 endif;
                 
                 }
             ],
            'nivel_autorizacion',
        ],
    ]) ?>

</div>
