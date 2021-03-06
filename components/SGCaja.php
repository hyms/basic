<?php
namespace app\components;

use app\models\MovimientoCaja;
use Yii;
use yii\base\Component;

class SGCaja extends Component
{
    static public function movimientoCajaVenta($idmovimiento, $idCaja, $Observaciones = "", $idParent = null, $tipo = 1)
    {
        if (empty($idmovimiento)) {
            $movimientoCaja                   = new MovimientoCaja;
            $movimientoCaja->fk_idCajaDestino = $idCaja;
            $movimientoCaja->fk_idUser        = yii::$app->user->id;
            $movimientoCaja->time             = date("Y-m-d H:i:s");
            $movimientoCaja->tipoMovimiento   = $tipo;
            $movimientoCaja->observaciones    = $Observaciones;
            if (!empty($idParent))
                $movimientoCaja->idParent = $idParent;
        }
        else {
            $movimientoCaja = MovimientoCaja::findOne(['idMovimientoCaja' => $idmovimiento]);
            $movimientoCaja->fk_idCajaDestino = $idCaja;
        }
        return $movimientoCaja;

    }

    static public function movimientoCajaCompra($idmovimiento, $idCaja, $Observaciones = "", $idParent = null, $tipo = 1)
    {
        if (empty($idmovimiento)) {
            $movimientoCaja                  = new MovimientoCaja;
            $movimientoCaja->fk_idCajaOrigen = $idCaja;
            $movimientoCaja->fk_idUser       = yii::$app->user->id;
            $movimientoCaja->time            = date("Y-m-d H:i:s");
            $movimientoCaja->tipoMovimiento  = $tipo;
            $movimientoCaja->observaciones   = $Observaciones;
            if (!empty($idParent))
                $movimientoCaja->idParent = $idParent;
        }
        else {
            $movimientoCaja = MovimientoCaja::findOne(['idMovimientoCaja' => $idmovimiento]);
            $movimientoCaja->fk_idCajaOrigen = $idCaja;
        }
        return $movimientoCaja;
    }

    static public function movimientoCajaTraspaso($idmovimiento, $idCajaFrom, $idCajaTo, $Observaciones = "", $time = null, $tipo = 1)
    {
        if (empty($idmovimiento)) {
            $movimientoCaja                   = new MovimientoCaja;
            $movimientoCaja->fk_idCajaOrigen  = $idCajaFrom;
            $movimientoCaja->fk_idCajaDestino = $idCajaTo;
            $movimientoCaja->fk_idUser        = yii::$app->user->id;
            if ($time == null)
                $movimientoCaja->time = date("Y-m-d H:i:s");
            else
                $movimientoCaja->time = $time;
            $movimientoCaja->tipoMovimiento = $tipo;
            $movimientoCaja->observaciones  = $Observaciones;
            return $movimientoCaja;
        }
        return MovimientoCaja::findOne(['idMovimientoCaja' => $idmovimiento]);
    }

    static public function getSaldo($idCaja, $fechaMovimientos, $array = false, $get = null,$admin=false)
    {
        if ($array || isset($get['deudas']))
            $deudas = array();
        else
            $deudas = 0;

        if ($array || isset($get['ventas']))
            $ventas = array();
        else
            $ventas = 0;

        if ($array || isset($get['recibos']))
            $recibos = array();
        else
            $recibos = [0,0];

        if ($array || isset($get['cajas']))
            $cajas = array();
        else
            $cajas = 0;

        if (isset($get['movimientos']))
            $movimientosAll = array();
        else
            $movimientosAll = null;

        $arqueos     = array();
        $movimientos = MovimientoCaja::find()
            ->andWhere('`fk_idCajaOrigen`=' . $idCaja . ' or `fk_idCajaDestino`=' . $idCaja);
        if(!$admin) {
            if (isset($get['arqueo']))
                $movimientos->andFilterWhere(['like', 'fechaCierre', $get['arqueo']]);
            else
                $movimientos->andWhere(['is', 'fechaCierre', null]);
        }

        $movimientos->andWhere(['<=', 'time', date("Y-m-d", strtotime($fechaMovimientos)) . " 23:59:59"]);
        if($admin) {
            $movimientos->andWhere(['>=', 'time', date("Y-m-d", strtotime($fechaMovimientos)) . " 00:00:00"]);
        }
        $movimientos = $movimientos->all();
        $total       = 0;

        foreach ($movimientos as $key => $movimiento) {
            switch ($movimiento->tipoMovimiento) {
                case 0:
                    if (isset($get['movimientos'])) {
                        array_push($movimientosAll, $movimiento);
                    }
                    if ($array || isset($get['deudas'])) {
                        if (isset($movimiento->idParent0)) {
                            if (isset($movimiento->idParent0->ordenCTPs[0]))
                                $orden = $movimiento->idParent0->ordenCTPs[0];
                            array_push($deudas, $orden);
                        }
                    } else {
                        $deudas += $movimiento->monto;
                    }
                    $total += $movimiento->monto;
                    break;
                case 1:
                    if (!empty($movimiento->ordenCTPs)) {
                        if (isset($get['movimientos'])) {
                            array_push($movimientosAll, $movimiento);
                        }
                        if ($array || isset($get['ventas'])) {
                            array_push($ventas, $movimiento->ordenCTPs[0]);
                        } else {
                            $ventas += $movimiento->monto;
                        }
                    }
                    $total += $movimiento->monto;
                    break;
                case 2:
                    if (isset($get['movimientos'])) {
                        array_push($movimientosAll, $movimiento);
                    }
                    if ($array || isset($get['cajas'])) {
                        array_push($cajas, $movimiento);
                    } else {
                        $cajas += $movimiento->monto;
                    }
                    $total += $movimiento->monto;
                    break;
                case 3:
                    array_push($arqueos, $movimiento);
                    $total += $movimiento->monto;
                    break;
                case 4:
                    if(!empty($movimiento->recibos)) {
                        if (isset($get['movimientos'])) {
                            array_push($movimientosAll, $movimiento);
                        }
                        if ($array || isset($get['recibos'])) {
                            $tmp = $movimiento->recibos;
                            array_push($recibos, $movimiento->recibos[0]);
                        } else {
                            if ($movimiento->recibos[0]->tipoRecibo)
                                $recibos[1]+=$movimiento->monto;
                            else
                                $recibos[0]+=$movimiento->monto;
                        }
                        $total += $movimiento->monto;
                    }
                    break;
            }
        }


        $saldos = MovimientoCaja::find()
            ->andWhere(['tipoMovimiento'=>3])
			->andWhere('`fk_idCajaOrigen`=' . $idCaja . ' or `fk_idCajaDestino`=' . $idCaja)
            ->andWhere(['<', 'time', date("Y-m-d H:i", strtotime($fechaMovimientos))])
            ->orderBy(['time'=>SORT_DESC])
            ->one();
        $saldo=0;
        if (!empty($saldos)) {
            $saldo = $saldos->saldoCierre;
        }

        $datos = array('ventas' => $ventas, 'deudas' => $deudas, 'recibos' => $recibos, 'cajas' => $cajas, 'saldo' => $saldo, 'movimientos' => $movimientosAll,'arqueos'=>$arqueos);
        return $datos;
    }

    public $success = false;

    public function cajaChica($data)
    {
        if ($data['cajaChica'] && $data['post'] && $data['caja']) {
            $data['cajaChica'] = SGCaja::movimientoCajaCompra((isset($data['cajaChica']->idMovimientoCaja)) ? $data['cajaChica']->idMovimientoCaja : null, $data['caja']->idCaja, "", null, 2);
            if (!$data['cajaChica']->isNewRecord) {
                $data['caja']->monto -= $data['cajaChica']->monto;
            }
            $data['cajaChica']->attributes = $data['post'];
            if (!$data['cajaChica']->validate()) {
                return $data;
            }
            $data['caja']->monto += $data['cajaChica']->monto;
            if ($data['cajaChica']->save()) {
                $data['caja']->save();
                $this->success = true;
            }
            return $data;
        }
    }
}