<?php

use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\Url;
use app\assets\VacacionesAsset;
VacacionesAsset::register($this);

$url = Yii::$app->urlManager->createUrl(['vacaciones']);
$inlineScript = "var url='$url';";
$this->registerJs($inlineScript, View::POS_HEAD);


$this->title = 'Vacaciones';
$this->params['breadcrumbs'][] = ['label' => 'Vacaciones', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Agenda';
?>

<div class = "row">
   <div class = "col-md-6">
    <?= edofre\fullcalendar\Fullcalendar::widget([
        'events'        => Url::to(['vacaciones/calendario']),
        'clientOptions' => [
            'eventClick' => new JsExpression('function (cellInfo, jsEvent) {
                getSolicitud(cellInfo.id);
             }'),
        ],
       ]);
     ?>
   </div>
   <div class = "col-md-6">
       <table id='table' class="table table-bordered table-condensed" style="background-color: white; font-size: 12px; width: 100%">
       <caption style="font-size:16px;"><b>Datos de la Solicitud</b></caption>
         <tbody>
         </tbody>
       </table>
   </div>
</div>

