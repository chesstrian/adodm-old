<?php

/**
 * Muestra los detalles del evento.
 *
 * @param $ecode
 * @param $data
 * @param $server
 * @param $port
 *
 */
function EventPrint($ecode, $data, $server, $port) {
    echo "\n";
    foreach($data as $key=>$val)
        echo "$key = $val\n";
    echo "\n";
}
?>
