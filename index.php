<?php

require_once('bootstrap.php');

use Src\Controller\USSDHandler;
use Predis\Client;

switch ($_SERVER["REQUEST_METHOD"]) {
    case 'POST':
        $_POST = json_decode(file_get_contents("php://input"), true);
        $response = array();

        if (!empty($_POST)) {
            $ussd = new USSDHandler($_POST);
            $response = $ussd->run();

            if (isset($response["data"]) && !empty($response["data"]))
                try {
                    $redis = new Client(['host' => REDIS_HOST, 'port' => REDIS_PORT]);
                    $redis->publish('livePaymentChannel', json_encode($response["data"]));
                    logData("Successful.", $response["data"]);
                } catch (\Exception $e) {
                    logData($e->getMessage());
                }
            else logData("No payment data available.", $response);

            header("Content-Type: application/json");
            echo json_encode($response);
        }
        break;

    default:
        header("HTTP/1.1 403 Forbidden");
        header("Content-Type: text/html");
        break;
}

exit();

// Helper Functions

function logData($message, $data = array())
{
    file_put_contents(
        'processUSSD.log',
        date('Y-m-d H:i:s') . " - " . $message . ".\n" . empty($data) ? $message : json_encode($data) . "\n",
        FILE_APPEND
    );
}
