<?php

use app\models\SysMedPatologiaCategoria;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysMedPatologia */

$this->title = 'Patología';
$this->params['breadcrumbs'][] = ['label' => 'Patologías', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-med-patologia-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'nombre',
            [
                'label'=>'Categoria',
                'attribute'=>'id_sys_med_patologia_categoria',
                'value'=> function($model){
                    
                    $categoria  = SysMedPatologiaCategoria::find()->Where(['id'=> $model->id_sys_med_patologia_categoria])->one();
                    return $categoria->categoria;
                    
                } ,
            ],
            [
                'label'=>'Estado',
                'attribute'=>'activo',
                'value'=> function($model){
                    return $model->activo == true ? 'Activo' : 'Inactivo';
                } ,
            ]
        ],
    ]) ?>

</div>
