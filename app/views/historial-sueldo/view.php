<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\SysAdmHistorialSueldo */

$this->title = 'Ver Registro';
$this->params['breadcrumbs'][] = ['label' => 'Historial Sueldo', 'url' => ['index']];
\yii\web\YiiAsset::register($this);
?>
<div class="sys-adm-historial-sueldo-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'anio',
            'sueldo_sectorial',
            'sueldo_basico',
            'user_autorization',
            'date_autorization',
        ],
    ]) ?>
</div>
