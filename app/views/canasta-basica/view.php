<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmHistorialSueldo */

$this->title = 'Ver Registro';
$this->params['breadcrumbs'][] = ['label' => 'Canasta Básica', 'url' => ['index']];
\yii\web\YiiAsset::register($this);
?>
<div class="sys-adm-canasta-basica-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'anio',
            'canasta_basica',
            'usuario_creacion',
            'fecha_creacion',
        ],
    ]) ?>
</div>
