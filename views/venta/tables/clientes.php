<?php
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="panel panel-default">
    <div class="panel-heading">
        <strong class="panel-title">Ordenes de trabajo Pendientes</strong>
    </div>
    <div class="panel-body">
        <?=
        Html::a('Nuevo Cliente', "#", [
            'class'=>'btn btn-default',
            'onclick'  => "
                    $.ajax({
                        type    :'POST',
                        cache   : false,
                        url     : '" . Url::to(['venta/cliente', 'op' => 'new']) . "',
                        success : function(data) {
                            if(data.length>0){
                                $('#viewModal .modal-header').html('<h3 class=\"text-center\">Nuevo Cliente</h3>');
                                $('#viewModal .modal-body').html(data);
                                $('#viewModal').modal();
                            }
                        }
                    });return false;"
        ]);
        ?>
    </div>
    <div style="overflow: auto">
        <?php
        $columns=[
            [
                'header'=>'Codigo',
                'attribute'=>'codigoCliente',
            ],
            [
                'header'=>'Nit/Ci',
                'attribute'=>'nitCi',
            ],
            [
                'header' => 'Negocio',
                'attribute'=>'nombreNegocio',
            ],
            [
                'header' => 'Dueño',
                'attribute'=>'nombreCompleto',
            ],
            [
                'header' => 'Responsable',
                'attribute'=>'nombreResponsable',
            ],
            [
                'header' => 'Telefono',
                'attribute'=>'telefono',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{update}',
                'buttons'=>[
                    'update'=>function($url,$model) {
                        $options = array_merge([
                            //'class'=>'btn btn-success',
                            'data-original-title' => 'Modificar',
                            'data-toggle'         => 'tooltip',
                            'title'               => '',
                            'onclick'             => "
                                                        $.ajax({
                                                            type     :'POST',
                                                            cache    : false,
                                                            url  : '" . Url::to(['venta/cliente','id'=>$model->idCliente]) . "',
                                                            success  : function(data) {
                                                                if(data.length>0){
                                                                    $('#viewModal .modal-header').html('<h3 class=\"text-center\">Cliente: ".$model->nombreNegocio."</h3>');
                                                                    $('#viewModal .modal-body').html(data);
                                                                    $('#viewModal').modal();
                                                                }
                                                            }
                                                        });return false;"
                        ]);
                        $url     = "#";
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                    },
                ]
            ],
        ];
        echo GridView::widget([
            'dataProvider'=> $clientes,
            'filterModel' => $search,
            'columns' => $columns,
            'responsive'=>true,
            'hover'=>true,
            'bordered'=>false
        ]);
        ?>
    </div>
</div>