<?php
use kartik\helpers\Html;

?>
<div style="width:593px; position: relative; float: left;">
    <div class="col-xs-12">
        <div class="row">
            <div class="row col-xs-12">
                <h3 class="col-xs-offset-3 col-xs-4 text-center"><?= $orden->fkIdSucursal->nombre?> <?=$orden->correlativo;?></h3>
                <div class="text-right"><strong><?= "FECHA:";?></strong> <?= date("d-m-Y / H:i",strtotime((empty($nota->fechaVisto))?$orden->fechaCobro:$nota->fechaVisto));?></div>
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="col-xs-7"><strong><?= "CLIENTE:";?></strong> <span class="text-capitalize"><?= $orden->responsable;?></span></div>
            <div class="col-xs-3"><strong><?= "NIT:";?></strong> <?= (!empty($orden->fk_idCliente))?$orden->fkIdCliente->nitCi:"";?></div>
        </div>

        <div class="row well well-sm" style="height:170px; border-color: #000; background-color: #fff">
            <table class="table table-condensed">
                <thead><tr>
                    <?= Html::tag('th','Nº',['style'=>'border-bottom: solid; border-bottom-width: 1.5px;']);?>
                    <?= Html::tag('th','Formato',['style'=>'border-bottom: solid; border-bottom-width: 1.5px;']);?>
                    <?= Html::tag('th','Cant.',['style'=>'border-bottom: solid; border-bottom-width: 1.5px;']);?>
                    <?= Html::tag('th','Colores',['style'=>'border-bottom: solid; border-bottom-width: 1.5px;']);?>
                    <?= Html::tag('th','Trabajo',['style'=>'border-bottom: solid; border-bottom-width: 1.5px;']);?>
                    <?= Html::tag('th','Pinza',['style'=>'border-bottom: solid; border-bottom-width: 1.5px;']);?>
                    <?= Html::tag('th','Resol.',['style'=>'border-bottom: solid; border-bottom-width: 1.5px;']);?>
                    <?= Html::tag('th','Adicional',['style'=>'border-bottom: solid; border-bottom-width: 1.5px;']);?>
                </tr></thead>

                <tbody>
                <?php $i=0; foreach ($orden->ordenDetalles as $producto){ $i++;?>
                    <tr>
                        <?= Html::tag('td',$i,['style'=>'font-size:12px; padding-top: 4px;']);?>
                        <?= Html::tag('td',$producto->fkIdProductoStock->fkIdProducto->formato,['style'=>'font-size:12px; padding-top: 4px;']);?>
                        <?= Html::tag('td',$producto->cantidad,['style'=>'font-size:12px; padding-top: 4px;']);?>
                        <?= Html::tag('td',(($producto->C)?"<strong>C </strong>":"").(($producto->M)?"<strong>M </strong>":"").(($producto->Y)?"<strong>Y </strong>":"").(($producto->K)?"<strong>K </strong>":""),['style'=>'font-size:12px; padding-top: 4px;']);?>
                        <?= Html::tag('td',$producto->trabajo,['style'=>'font-size:12px; padding-top: 4px;']);?>
                        <?= Html::tag('td',$producto->pinza,['style'=>'font-size:12px; padding-top: 4px;']);?>
                        <?= Html::tag('td',$producto->resolucion,['style'=>'font-size:12px; padding-top: 4px;']);?>
                        <?= Html::tag('td',$producto->adicional,['style'=>'font-size:12px; padding-top: 4px;']);?>
                    </tr>
                <?php }?>
                </tbody>
            </table>
            <!--   </div> -->
        </div>

        <div class="row">
            <div class="col-xs-6">
                <div class="row col-xs-12" style="font-size: 11px"><strong>Diseñador/a:</strong> <?= $orden->fkIdUserD->nombre." ".$orden->fkIdUserD->apellido;?></div>
                <div class="row col-xs-12" style="font-size: 11px"><strong>Obs:</strong> <?= $orden->observaciones;?></div>
                <div class="row col-xs-12" style="font-size: 11px"><strong>Cajer@:</strong> <?= $orden->fkIdUserV->nombre." ".$orden->fkIdUserV->apellido;?></div>
                <div class="row col-xs-12" style="font-size: 11px"><strong>Obs:</strong> <?= $orden->observacionesCaja;?></div>
            </div>
            <div class="text-right">
                <div style="font-size: 11px"><strong>Impreso por:</strong> <?= Yii::$app->user->identity->nombre." ".Yii::$app->user->identity->apellido ?></div>
            </div>
        </div>
    </div>
</div>
<div style="width:123px; position: relative; float: right;">
    <div class="row" style="font-size: 10.5px">
        <div class="col-xs-12 row text-center"><h3><strong><?= $orden->correlativo;?></strong></h3></div>
        <div class="col-xs-12 row text-center" style="font-size: 8px"><strong><?= $orden->fkIdSucursal->nombre;?></strong></div>
        <div class="col-xs-12 row text-center"><h4><strong><?= $orden->codigoServicio;?></strong></h4></div>
        <div class="col-xs-12 row"><span class="row"><strong><?= "CLIENTE:";?></strong> <span class="col-xs-12"><?= (!empty($orden->fk_idCliente))?$orden->fkIdCliente->nombreNegocio:"";?></span></div>
        <div class="col-xs-12 row"><span class="row"><strong><?= "RESP:";?></strong> <span class="col-xs-12"><?= $orden->responsable;?></span></div>
        <div class="col-xs-12 row"><span class="row"><strong><?= "FECHA:";?></strong> <span class="col-xs-12"><?= date("d-m-Y / H:i",strtotime((empty($nota->fechaVisto))?$orden->fechaCobro:$nota->fechaVisto));?></span></span></div>
        <div class="col-xs-12 row" style="font-size: 10px"><strong>Diseñador/a:</strong> <span class="text-capitalize"><?= $orden->fkIdUserD->nombre." ".$orden->fkIdUserD->apellido;?></span></div>
        <div class="col-xs-12 row">
            <?php foreach ($orden->ordenDetalles as $producto){ ?>
                <div class="col-xs-12" style="border: 1.5px solid;">
                    <?= $producto->fkIdProductoStock->fkIdProducto->formato;?> /
                    <?= $producto->cantidad; ?> /
                    <?= (($producto->C)?"C":"").(($producto->M)?"M":"").(($producto->Y)?"Y":"").(($producto->K)?"K":"");?>
                    <br>
                    <?= $producto->trabajo;?> /
                    <?= $producto->pinza;?> /
                    <?= $producto->resolucion;?>
                </div>
            <?php }?>
        </div>
    </div>
</div>
