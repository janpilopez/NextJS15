<?php 
use yii\helpers\Html;
use kartik\growl\Growl;
 

//Get all flash messages and loop through them ?>
<?php foreach (Yii::$app->session->getAllFlashes() as $message):; ?>

                     
            <?php
            echo Growl::widget([
                'type' => (!empty($message['type'])) ? $message['type'] : 'danger',
                
                'icon' => (!empty($message['icon'])) ? $message['icon'] : 'glyphicon glyphicon-info',
                'body' => (!empty($message['message'])) ? Html::encode($message['message']) : 'Msj por definir!',
                'showSeparator' => true,
                'delay' => 1, //This delay is how long before the message shows
                'pluginOptions' => [
                    'delay' => (!empty($message['duration'])) ? $message['duration'] : 3000, //This delay is how long the message shows for
                    'placement' => [
                        'from' => (!empty($message['positonY'])) ? $message['positonY'] : 'top',
                        'align' => (!empty($message['positonX'])) ? $message['positonX'] : 'right',
                    ]
                ]
            ]);
            ?>
        <?php endforeach; ?>