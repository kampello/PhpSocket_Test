<?php
echo"Server Inicializando...";

// ---- Constantes ----
$PORTO = 55100;  
$BUFFSIZE = 1024;
//onde vai ser incializado
echo "http://localhost:".$PORTO."/";
// ---- Criar socket ----
$s = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($s === false) {
    die("socket_create: " . socket_strerror(socket_last_error()) . "\n");
}

// ---- Associar ao endereço ----
if (!socket_bind($s, "0.0.0.0", $PORTO)) {
    die("socket_bind: " . socket_strerror(socket_last_error($s)) . "\n");
}

// ---- Colocar à escuta ----
if (!socket_listen($s, 5)) {
    die("socket_listen: " . socket_strerror(socket_last_error($s)) . "\n");
    
}

// ---- HTTP header equivalente ----
$http = "HTTP/1.1 200 OK\r\nContent-Length: 25\r\n\r\n";
$clientes = 0;

while (true) {
    // Aceitar conexão do cliente
    $c = socket_accept($s);
    if ($c === false) {
        die("socket_accept: " . socket_strerror(socket_last_error($s)) . "\n");
    } else {
        $clientes++;  // Incrementa contador de clientes
        echo "\n" . $clientes . " - Cliente entrou!\n";
       
    }
    
    $txt = file_get_contents('index.html');


    $http = "HTTP/1.1 200 OK\r\n";
    $http .= "Content-Type: text/html; charset=UTF-8\r\n";
    $http .= "Connection: close\r\n\r\n";  // Fecha a conexão após enviar a resposta

    $t = date("r") . "\n";

    // Oque deve ser mostrado ao cliente
    $buf = $t.$http . $txt;

    socket_write($c, $buf, strlen($buf));

    
    $msg = socket_read($c, 1024);
    /*echo "Mensagem do cliente: $msg\n";*/
    socket_close($c);
}

socket_close($s); 
?>
