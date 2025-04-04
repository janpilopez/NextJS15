<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysDocumentoAutorizacion */

$this->params['breadcrumbs'][] = ['label' => 'Autorización de Documentos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-documento-autorizacion-view">

    <h1><?= Html::encode($this->title) ?></h1>

 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id_sys_documento',
            'id_usuario',
            'id_sys_area',
            'id_sys_departamento',
            'estado',
        ],
    ]) ?>

</div>
