<?php
    use kartik\grid\GridView;
    use yii\bootstrap\Modal;
    use yii\helpers\Html;
    use yii\helpers\Url;

?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong class="panel-title">Ordenes de trabajo</strong>
        </div>
        <div>
            <?php
                $columns = [
                    [
                        'header'=>'Correlativo',
                        'attribute'=>'correlativo',
                    ],
                    [
                        'header'=>'Responsable',
                        'attribute'=>'responsable',
                    ],
                    [
                        'header'=>'Telefono',
                        'attribute'=>'telefono',
                    ],
                    [
                        'header'=>'Operador',
                        'attribute'=>'nombreUsuario',
                        'value'=>function($model)
                        {
                            return $model->fkIdUserD->nombre." ".$model->fkIdUserD->apellido;
                        }
                    ],
                    [
                        'header'=>'Fecha',
                        'attribute'=>'fechaGenerada',
                        'value'=>function($model)
                        {
                            return date("Y-m-d H:i",strtotime($model->fechaGenerada));
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template'=>'{print} {view} {validate}',
                        'buttons'=>[
                            'validate'=>function($url,$model){
                                $options = array_merge([
                                                           //'class'=>'btn btn-success',
                                                           'data-original-title'=>'Validar',
                                                           'data-toggle'=>'tooltip',
                                                           'title'=>'',
                                                           'onclick'             => "validar(".$model->idOrdenCTP.",'".Url::to(['diseno/dependientes'])."'); return false;"
                                                       ]);
                                if(empty($model->fk_idUserD2))
                                    return Html::a('<span class="glyphicon glyphicon-check btn btn-success btn-sm"></span>', '#', $options);
                                else
                                    return "";
                            },
                            'print'=>function($url,$model){
                                $options = array_merge([
                                                           //'class'=>'btn btn-success',
                                                           'data-original-title'=>'Imprimir',
                                                           'data-toggle'=>'tooltip',
                                                           'title'=>''
                                                       ]);
                                $url = Url::to(['diseno/print','op'=>'orden','id'=>$model->idOrdenCTP]);
                                return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, $options);
                            },
                            'view'=>function($url,$model) {
                                $options = array_merge([
                                                           'data-original-title' => 'Ver Orden de Trabajo',
                                                           'data-toggle'         => 'tooltip',
                                                           'title'               => '',
                                                           'onclick'             => "
                                                        $.ajax({
                                                            type    :'get',
                                                            cache   : false,
                                                            url     : '" . Url::to(['diseno/review','op'=>'cliente','id'=>$model->idOrdenCTP]) . "',
                                                            success : function(data) {
                                                                if(data.length>0){
                                                                    $('#viewModal .modal-header').html('<h3 class=\"text-center\">Orden de Trabajo ".$model->correlativo."</h3>');
                                                                    $('#viewModal .modal-body').html(data);
                                                                    $('#viewModal').modal();
                                                                }
                                                            }
                                                        });return false;"
                                                       ]);
                                $url     = "#";
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, $options);
                            },
                        ]
                    ],
                ];
                echo GridView::widget([
                                          'dataProvider'=> $orden,
                                          'filterModel' => $search,
                                          'columns' => $columns,
                                          'responsive'=>true,
                                          'condensed'=>true,
                                          'hover'=>true,
                                          'bordered'=>false,
                                      ]);
            ?>
        </div>
    </div>
<?php
    Modal::begin([
                     'id'=>'viewModal',
                     'size'=>Modal::SIZE_LARGE,
                 ]);
    Modal::end();
?>
<?php
    $script = <<<js
function validar(idOrden,url){
var r = confirm("Desea Validar la Orden?");
    if (r == true) {
        $.ajax({
        type    :'post',
        cache   : false,
        data    :{id:idOrden},
        url     : url,
        success : function(data) {
            if(data=="done"){
                location.reload();
                }
        }
        });
    }
}
js;
    $this->registerJs($script, \yii\web\View::POS_HEAD);
?>