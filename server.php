<?php
echo"Server Inicializando...";

// ---- Constantes ----
$PORTO = 55100;  // 
$BUFFSIZE = 1024;

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

    // ---- Definir conteúdo HTML ----
    $txt = file_get_contents('index.html');

    // ---- Cabeçalho HTTP (Content-Type) ----
    $http = "HTTP/1.1 200 OK\r\n";
    $http .= "Content-Type: text/html; charset=UTF-8\r\n";
    $http .= "Connection: close\r\n\r\n";  // Fecha a conexão após enviar a resposta

    // ---- Obter hora atual ----
    $t = date("r") . "\n";  // Formato "Mon, 14 Nov 2025 17:23:20 +0000"

    // ---- Criar a resposta completa ----
    $buf = $http . $txt . $t;

    // ---- Enviar resposta para o cliente ----
    socket_write($c, $buf, strlen($buf));

    // ---- Ler a resposta do cliente ---- (opcional)
    $msg = socket_read($c, 1024);
    echo "Mensagem do cliente: $msg\n";

    // ---- Fechar a conexão com o cliente ----
    socket_close($c);
}

socket_close($s); // Fechar o socket principal quando sair do loop (o que no seu caso nunca ocorrerá)
?>
