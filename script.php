<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $secretKey = 'sk_live_YfvvweLHTDcns2It9GnJp4NHiwmayALYKM5ckW9w5d';  // token da ativopay
    $authorization = base64_encode($secretKey . ":x");

    // Dados recebidos via POST
	$cpf = $_POST['cpf']; // tem que receber o cpf via post
	$nomeCompleto = $_POST['nome_completo']; // nome completo via post
	$email = $_POST['email']; // email via post
    $description = $_POST['descricao']; // aqui é o que mostra la na descrição do pagamento, geralmente sobre o que é .. ex: iPhone 15 pro max 256GB
    $amount = $_POST['valor_transacao']; // O valor é importante enviar ja com os centavos sem , ou . exemplo, se deu 5 reais envia ja assim: 500 se deu R$1.000,90 envia: 100090 entendeu? eles pedem assim.
	$quanty = $_POST['quantidade']; // aqui eles pedem a quantidade do item pedido no caso se foi seila 2 iphone envia 2
	$amountUnity = $_POST['priceUnit']; // aqui você poe o preço de cada um dos itens exemplo se foi 2 iphones e o total em amount deu 5 mil aqui voce poe 2500
	
    // Dados da transação
    $data = json_encode([
        "customer" => [
            "document" => [
                "number" => "15896074654",
                "type" => "cpf"
            ],
            "name" => "Pedro Lucas Mendes Souza",
            "email" => "pedro@gmail.com",
            "externalRef" => "custom_id"
        ],
        "payer"=> "id_unico",
        "paymentMethod" => "pix", // metodo de pagamento
        "amount" => 500000, // valor total ex: 500000 (R$5.000,00)
        "pix" => [
            "expiresInDays" => 2 // tempo em dias pra expirar o qrcode pix
        ],
        "items" => [
            [
                "tangible" => false, // se o item é fisico ou digital false para digital e true para fisico
                "title" => "Descrição", // descrição ex: iPhone 15 pro max 256GB
                "unitPrice" => 500000, // preço individual ex: 2500 (R$2.500,00)
                "quantity" => 1, // quantidade de itens ex 2
                "externalRef"=> 'user_id'
            ]
        ]
    ]);

    // Inicializa cURL
    $curl = curl_init();

	curl_setopt_array($curl, [
        CURLOPT_URL => 'https://api.conta.ativopay.com/v1/transactions',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Basic ' . $authorization
        ],
    ]);

    // Executa a requisição
    $response = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);

    if ($error) {
        echo json_encode(['success' => false, 'message' => 'Erro ao realizar a requisição: ' . $error]);
    } else {
        $responseArray = json_decode($response, true);
        
        // Verifica se a resposta contém o QR Code
        echo json_encode(['sucess'=> true, 'data'=> $responseArray]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de requisição inválido.']);
}
?>