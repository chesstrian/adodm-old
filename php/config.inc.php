<?php

$config = array();

// MySQL Connection
$config['db']['host'] = "";
$config['db']['user'] = "";
$config['db']['pass'] = "";
$config['db']['name'] = "";

// AMI Connection
$config['asterisk']['host'] = ""; // server:port if port is not 5038
$config['asterisk']['user'] = "";
$config['asterisk']['pass'] = "";

// Messages Error Configuration
$config['error_msg']['email'] = "";
$config['error_msg']['header'] = "";

$config['error_msg'][-1]['subject'] = "Error en Parámetros de BD";

$config['error_msg'][-2]['subject'] = "Campaña Corriendo";
$config['error_msg'][-2]['message'] = "Existe una instancia llevando a cabo la Campaña.";

$config['error_msg'][-3]['subject'] = "Campaña no Creada";
$config['error_msg'][-3]['message'] = "Nombre de la campaña no registra en Base de Datos.";

$config['error_msg'][-4]['subject'] = "Campaña no Activa";
$config['error_msg'][-4]['message'] = "Campaña está desactivada o ya hay una instancia corriendo.";

$config['error_msg'][-5]['subject'] = "Campaña Fuera de Horario";
$config['error_msg'][-5]['message'] = "Campaña debe encontrarse en el horario registrado en la Base de Datos.";

$config['error_msg'][-6]['subject'] = "Error de Conexión en AMI";
$config['error_msg'][-6]['message'] = "Conexión a Asterisk Manager Interface no exitosa.";

?>
