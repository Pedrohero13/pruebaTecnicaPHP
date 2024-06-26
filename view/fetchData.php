<?php
$datos = [
    "user" => "csm",
    "password" => "exam1csm",
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://104.154.142.250/apis/exam/auth");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$datos_consulta = http_build_query($datos);
curl_setopt($ch, CURLOPT_POSTFIELDS, $datos_consulta);

$respuesta = curl_exec($ch);
$json = json_decode($respuesta, true);
$user = $json["data"];
$tokenBearer = $user["jwt"];
curl_close($ch);

$url = "http://104.154.142.250/apis/exam/positions";

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$headers = [
    "Authorization: Bearer $tokenBearer",
];

curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$respuesta = curl_exec($curl);
$json = json_decode($respuesta, true);
$data = $json["data"];
echo $data;