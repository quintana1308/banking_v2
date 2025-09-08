<?php
class BncService {
    private $apiUrl = 'https://servicios.bncenlinea.com:16500/api';

    public function procesarSolicitud($opcion, $datos) {
        switch ($opcion) {
            case 'autenticacion':
                
               return  $this->autenticacion($datos);
                break;
            case 'vpos':
                $this->vpos($datos);
                break;

            case 'p2p':
                $this->p2p($datos);
                break;

            case 'c2p':
                $this->c2p($datos);
                break;

            case 'rc2p':
                $this->rc2p($datos);
                break;

            case 'saldoActual':
                $this->saldoActual($datos);
                break;

            case 'movimientos':
                $this->movimientos($datos);
                break;

            case 'movimientosRangoFecha':
                $this->movimientosRangoFecha($datos);
                break;

            default:
                echo "Opción no reconocida";
                break;
        }
    }

    private function autenticacion($datos) {
        $endpoint = $this->apiUrl . '/Auth/LogOn';

       
       return $this->enviarSolicitud($endpoint, $datos);
    }

    private function vpos($datos) {
        $endpoint = $this->apiUrl . '/Transaction/Send';
        $this->enviarSolicitud($endpoint, $datos);
    }

    private function p2p($datos) {
        $endpoint = $this->apiUrl . '/MobPayment/SendP2P';
        $this->enviarSolicitud($endpoint, $datos);
    }

    private function c2p($datos) {
        $endpoint = $this->apiUrl . '/MobPayment/SendC2P';
        $this->enviarSolicitud($endpoint, $datos);
    }

    private function rc2p($datos) {
        $endpoint = $this->apiUrl . '/MobPayment/ReverseC2P';
        $this->enviarSolicitud($endpoint, $datos);
    }

    private function saldoActual($datos) {
        $endpoint = $this->apiUrl . '/Position/Current';
        $this->enviarSolicitud($endpoint, $datos);
    }

    private function movimientos($datos) {
        $endpoint = $this->apiUrl . '/Position/History';
        $this->enviarSolicitud($endpoint, $datos);
    }

    private function movimientosRangoFecha($datos) {
        $endpoint = $this->apiUrl . '/Position/HistoryByDate';
        $this->enviarSolicitud($endpoint, $datos);
    }
    

    private function enviarSolicitud($url, $datos) {
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $datos);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
       
        if (curl_errno($ch)) {
		    echo 'Error:' . curl_error($ch);
		}
        curl_close($ch);


       
        return  $result;
    }
}

?>
