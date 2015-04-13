<?php
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;

?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong class="panel-title">Ordenes de Trabajo - Transaccionadas</strong>
        </div>
        <div>
            <?php
            $data =  new ActiveDataProvider([
                'query'      => $orden,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);

            $columns = [
                [
                    'header'=>'Correlativo',
                    'attribute'=>'correlativo',
                ],
                [
                    'header'=>'Codigo',
                    'attribute'=>'codigoServicio',
                ],
                [
                    'header'=>'Cliente',
                    'attribute'=>function($model){
                        return $model->fkIdCliente->nombreNegocio;
                    },
                ],
                [
                    'header'=>'Responsable',
                    'attribute'=>'responsable',
                ],
                [
                    'header'=>'Fecha Generada',
                    'attribute'=>'fechaGenerada',
                ],
                [
                    'header'=>'Fecha Venta',
                    'attribute'=>'fechaCobro',
                ],
                [
                    'header'=>'',
                    'attribute'=>'fechaCobro',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template'=>'{update} {aviso} {print} {factura}',
                    'buttons'=>[
                        'update'=>function($url,$model){
                            $options = array_merge([
                                //'class'=>'btn btn-success',
                                'data-original-title'=>'Modificar',
                                'data-toggle'=>'tooltip',
                                'title'=>''
                            ]);
                            $url = Url::to(['venta/venta','id'=>$model->idOrdenCTP]);
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, $options);
                        },
                        'print'=>function($url,$model){
                            $options = array_merge([
                                //'class'=>'btn btn-success',
                                'data-original-title'=>'Imprimir',
                                'data-toggle'=>'tooltip',
                                'title'=>''
                            ]);
                            $url = Url::to(['venta/print','op'=>'orden','id'=>$model->idOrdenCTP]);
                            return Html::a('<span class="glyphicon glyphicon-print"></span>', $url, $options);
                        }
                    ]
                ],
            ];
            echo GridView::widget([
                'dataProvider'=> $data,
                //'filterModel' => $searchModel,
                'columns' => $columns,
                'responsive'=>true,
                'hover'=>true
            ]);
            /*
                array(
                    'header'=>'',
                    'class'=>'booster.widgets.TbButtonColumn',
                    'template'=>'{update} {aviso} {print} {factura}',
                    'buttons'=>array(
                        'update'=>
                            array(
                                'url'=>'array("ctp/modificar","id"=>$data->idServicioVenta)',
                                'label'=>'Modificar',
                                'visible'=>'$data->estado >= 0',
                            ),
                        'aviso'=>
                            array(
                                'url'=>'"#"',
                                'label'=>'Anulado',
                                'icon'=>"pencil",
                                'visible'=>'$data->estado < 0',
                                'options'=>array('onclick'=>'alert("ANULADO")'),
                            ),
                        'print'=>
                            array(
                                'url'=>'array("ctp/preview","id"=>$data->idServicioVenta)',
                                'label'=>'imprimir',
                                'icon'=>'print',
                            ),
                        'factura'=>
                            array(
                                'label'=>'Introducir Nro de Factura',
                                'icon'=>'edit',
                                'url'=>'array("ctp/addFactura","id"=>$data->idServicioVenta)',
                                'visible'=>'$data->tipoVenta == 0',
                                'options'=>array(
                                    'ajax'=>array(
                                        'type'=>'POST',
                                        'url'=>"js:$(this).attr('href')",
                                        'success'=>'function(data) {if(data.length>0){ $("#viewModal .modal-body ").html(data); $("#viewModal").modal(); }}'
                                    ),
                                ),
                            ),
                    ),
                ),
            );

            $this->renderPartial('/baseTable',array('columns'=>$columns,'data'=>$ordenes->searchCliente('fechaVenta Desc',$condicion),'filter'=>$ordenes))
            //*/
            ?>
        </div>
    </div>

<?php
echo $this->render('../scripts/tooltip');
/*$this->beginWidget(
    'booster.widgets.TbModal',
    array('id' => 'viewModal','size'=>'small')

); ?>
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<h3 class="modal-title text-center" id="myModalLabel"><strong>Añadir Nº Factura</strong></h3>
</div>
<div class="modal-body" style="overflow:auto;">
</div>
<div class="modal-footer">
<?php $this->widget('booster.widgets.TbButton',
                    array(
                        'context' => 'primary',
                        'buttonType' => 'ajaxLink',
                        'label'=>'Guardar',
                        'url' => '#',
                        'htmlOptions'=>array('onclick' => 'formSubmit();'),
                    )
); ?>
<?php $this->widget('booster.widgets.TbButton',
                    array(
                        'label'=>'Cancelar',
                        'url' => '#',
                        'htmlOptions' => array('data-dismiss' => 'modal'),
                    )
); ?>
</div>
<?php $this->endWidget(); ?>

<?php Yii::app()->clientScript->registerScript("modalSubmit",'
function formSubmit()
{
    data=$("#form").serialize();
    $.ajax({
    type: "POST",
    data:data,
    url: $("#form").attr("action"),
    success: function(data){
    if(data=="done")
        location.reload();
    else
        $("#viewModal .modal-body ").html(data);
    }
    });
}
',CClientScript::POS_HEAD);
?>