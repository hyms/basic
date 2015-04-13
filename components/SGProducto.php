<?php
namespace app\components;


use app\models\MovimientoStock;
use app\models\Producto;
use app\models\ProductoStock;
use Yii;
use yii\base\Component;
use yii\data\ActiveDataProvider;

class SGProducto extends Component
{
    public function grabar()
    {

    }

    public function eliminar()
    {

    }

    public function getCodigo()
    {

    }

    static public function movimientoStockVenta($idMovimiento, $productoStock,$observaciones="", $dependiente = false)
    {
        if (empty($idMovimiento)) {
            $movimientoStock                   = new MovimientoStock;
            $movimientoStock->fk_idProducto    = $productoStock->fk_idProducto;
            $movimientoStock->fk_idStockOrigen = $productoStock->idProductoStock;
            //$movimientoStock->fk_idUser        = yii::$app->user->id;
            $movimientoStock->fk_idUser        = 1;
            $movimientoStock->observaciones    = $observaciones;
            $movimientoStock->time             = date("Y-m-d H:i:s");
            return $movimientoStock;
        }
        return MovimientoStock::findOne(['idMovimientoStock'=>$idMovimiento]);
    }

    static public function getProductos($dataProvider=true,$pager=5,$sucursal=false)
    {
        if (!$sucursal) {
            if ($dataProvider) {
                return new ActiveDataProvider([
                    'query'      => Producto::find(),
                    'pagination' => [
                        'pageSize' => $pager,
                    ],
                ]);
            }
            return Producto::find();
        }
        if ($dataProvider) {
            return new ActiveDataProvider([
                'query'      => ProductoStock::find(['fk_sucursal' => $sucursal,'enable'=>1]),
                'pagination' => [
                    'pageSize' => $pager
                ]
            ]);

        }
        ProductoStock::find()->where(['fk_sucursal' => $sucursal]);
    }

    static public function getOrden($data)
    {
        return Producto::findOne($data);
    }
}