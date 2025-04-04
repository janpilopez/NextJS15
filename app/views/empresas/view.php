<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysEmpresa */

$this->title = $model->razon_social;
$this->params['breadcrumbs'][] = ['label' => 'Empresas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-empresa-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Actualizar', ['update', 'id' => $model->id_sys_empresa], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Eliminar', ['delete', 'id' => $model->id_sys_empresa], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Está seguro de realizar la siguiente acción?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_sys_empresa',
            'ruc',
            'representante',
            'razon_social',
            'pais',
            'direccion',
            'ciudad',
            'celular',
        ],
      ]) ?>
      <?php
       if ($model->logo !='') {
           echo '<br /><p><img src="'. Yii::$app->homeUrl.'/logo/'.$model->ruc.'/'.$model->logo.'"></p>';
       }    
    ?>

</div>
