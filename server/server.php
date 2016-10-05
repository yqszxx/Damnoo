<?php
echo "Damoo Server v0.0.1\nauthor:yqszxx [yqszxx.org]\nEmail:hby@itfls.com\nstart at ".time()."\n";

file_put_contents("iptable.table", "");
file_put_contents("banned_ip.list", "");

$server = new swoole_websocket_server("0.0.0.0", 1919);

$server->on('open', function (swoole_websocket_server $server, $request) {
    $ip = $server->connection_info($request->fd)['remote_ip'];
    $bannedArr = explode("\n", file_get_contents("banned_ip.list"));
    if (in_array($ip, $bannedArr)) {
        echo "Banned ip {$ip} tried to connect but refused\n";
        $server->close($request->fd, true);
        return;
    }
    echo "Handshake success with {$ip} using fd{$request->fd}\n";
});

$server->on('message', function (swoole_websocket_server $server, $frame) {
    $bannedArr = explode("\n", file_get_contents("banned_ip.list"));
    $ip = $server->connection_info($frame->fd)['remote_ip'];
    if (in_array($ip, $bannedArr)) {
        echo "Recived a message from banned ip {$ip} with data '{$frame->data}' and dropped it\n";
        $server->push($frame->fd, "F**K, YOU ARE BANNED!hahaha");
        return;
    }
    if ($frame->data == null) {
        return;
    }
    if (strlen($frame->data) > 150) {
        return;
    }
    echo "Receive from {$ip} with data '{$frame->data}'\n";
    $data = $frame->data;
    foreach($server->connections as $fd){
        $server->push($fd , $data);
    }
});

$server->on('close', function ($ser, $fd) {
    $ip = $ser->connection_info($fd)['remote_ip'];
    echo "Client {$ip} with fd{$fd} closed\n";
});

$server->start();


/*
echo "author:yqszxx [yqszxx.org]\nEmail:hby@itfls.com\nstart at ".time()."\n";

file_put_contents("iptable.table", "");
file_put_contents("banned_ip.list", "");

global $names;
global $nameMap;
$names = explode("\n", file_get_contents("names.list"));

$nameMap[1] = "SYS";

$server = new swoole_websocket_server("0.0.0.0", 1919);

$server->on('open', function (swoole_websocket_server $server, $request) {
    $ip = $server->connection_info($request->fd)['remote_ip'];
    $bannedArr = explode("\n", file_get_contents("banned_ip.list"));
    if (in_array($ip, $bannedArr)) {
        echo "Banned ip {$ip} tried to connect but refused\n";
        $server->close($request->fd, true);
        return;
    }
    echo "Handshake success with {$ip} using fd{$request->fd}\n";
});

$server->on('message', function (swoole_websocket_server $server, $frame) {
    global $names;
    global $nameMap;
    $bannedArr = explode("\n", file_get_contents("banned_ip.list"));
    $ip = $server->connection_info($frame->fd)['remote_ip'];
    if (in_array($ip, $bannedArr)) {
        echo "Recived a message from banned ip {$ip} with data '{$frame->data}' and dropped it\n";
        $server->push($frame->fd, "F**K, YOU ARE BANNED!hahaha");
        return;
    }
    if ($frame->data == null) {
        return;
    }
    if (strlen($frame->data) > 150) {
        return;
    }
    echo "Receive from {$ip} with data '{$frame->data}'\n";
    $data = $frame->data;
    if ($frame->data == "LoGiN2000919" && $frame->fd != 1) {
        $nameMap[$frame->fd] = next($names);
        $data = "【SYS】{$nameMap[$frame->fd]}上线了！";
        $server->push($frame->fd, "c0fab17579f5be4b{$nameMap[$frame->fd]}");
        $server->push($frame->fd, "Welcome to DanMu by yqszxx  Web:yqszxx.org");
        file_put_contents("iptable.table", time()."\t".$ip."\t".$frame->fd."\t".$nameMap[$frame->fd]."\n", FILE_APPEND);
        echo "Binded name {$nameMap[$frame->fd]} to IP {$ip}\n";
    } else if ($frame->data == "LoGiN2000919" && $frame->fd == 1) {
        $server->push($frame->fd, "c0fab17579f5be4b{$nameMap[$frame->fd]}");
        $server->push($frame->fd, "Welcome to DanMu by yqszxx  Web:yqszxx.org");
    } else {
        $data = "【{$nameMap[$frame->fd]}】".$data;
    }
    foreach($server->connections as $fd){
        $server->push($fd , $data);
    }
});

$server->on('close', function ($ser, $fd) {
    $ip = $ser->connection_info($fd)['remote_ip'];
    echo "Client {$ip} with fd{$fd} closed\n";
});

$server->start();
*/
