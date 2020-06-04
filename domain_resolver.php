<?php
set_time_limit(0);
x00x_Domain_Resolver(file_get_contents($argv[1]));

function x00x_Domain_Resolver($domains){
$domains=explode("\n",$domains);
$c=0;
$d=0;
$f=0;
foreach(array_unique($domains) as $dom){
$dom=remove_http(trim($dom));
$ip=@gethostbyname($dom);
$hostname = @gethostbyaddr($ip);
if(!empty($ip) OR !empty($hostname)){
$opened_ports=web_is_up($dom);
if($opened_ports){
echo "DOMAIN: http://{$dom} -- HOSTNAME: {$hostname} -- IP: {$ip} -- OPENED PORTS: "; foreach($opened_ports as $opened_port){echo "{$opened_port},";}; echo " ==> HTTP WORKS !\n";
file_put_contents("domains_http.txt","{$dom}|{$hostname}|{$ip}\n",FILE_APPEND);
$c++;
}else{
if(empty($hostname)){$hostname="TBD MANUALLAY";}
if(empty($ip)){$ip="TBD MANUALLAY";}
echo "DOMAIN: {$dom} -- HOSTNAME: {$hostname} -- IP: {$ip} ==> NO HTTP !\n";
file_put_contents("domains_no_http.txt","{$dom}|{$hostname}|{$ip}\n",FILE_APPEND);
$d++;
}
}else{
if(empty($hostname)){$hostname="TBD MANUALLAY";}
if(empty($ip)){$ip="TBD MANUALLAY";}
echo "DOMAIN: {$dom} -- HOSTNAME: {$hostname} -- IP: {$ip} ==> LOOKS DOWN !\n";
file_put_contents("domains_down.txt","{$dom}|{$hostname}|{$ip}\n",FILE_APPEND);
$f++;
}
}
if($c+$d+$f == 0){
echo '\nNOTHING FOUND  !!\n';
}else{
echo "\nTHERE IS : ($c) WITH HTTP/HTTPS RESPONSE ( 80/81/443/8000/8008/8080 USED AS PORTS ) AND ($d) RESPONDING BUT NO HTTP/HTTPS
AND ($f) ARE DEAD !! \n";
}
}

function web_is_up($host){
$ports= array(80,81,443,8000,8008,8080);
$opened_ports= array();
foreach($ports as $port){
if($socket=@fsockopen($host, $port, $errno, $errstr, 10)){
array_push($opened_ports,$port);
@fclose($socket);
}
}
return $opened_ports;
}

function remove_http($url) {
$disallowed = array('http://', 'https://','/');
foreach($disallowed as $d) {
if(strpos($url, $d) === 0) {
return str_replace($d, '', $url);
}
}
return $url;
}
?>
