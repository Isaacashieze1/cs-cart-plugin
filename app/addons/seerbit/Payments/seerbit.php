<?php
///***************************************************************************
// *                                                                          *
// *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
// *                                                                          *
// * This  is  commercial  software,  only  users  who have purchased a valid *
// * license  and  accept  to the terms of the  License Agreement can install *
// * and use this program.                                                    *
// *                                                                          *
// ****************************************************************************
// * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
// * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
// ****************************************************************************/
//

use Tygh\Enum\OrderStatuses;
use Tygh\Enum\YesNo;
use Tygh\Http;
//
/** @var array $order_info */
/** @var array $processor_data */

// Preventing direct access to the script, because it must be included by the "include" directive.
defined('BOOTSTRAP') or die('Access denied');
$order_tk_url = "https://seerbitapi.com/api/v2/encrypt/keys";
// Here are two different contexts for running the script.
/**
 * @param $data
 * @param string $payment_reference
 * @param string $order_id
 * @return void
 */
function order_status($data, string $payment_reference, string $order_id): void
{
    if ($data['code'] === '00') {
        $pp_response = [
            'order_status' => 'P',
            'transaction_id' => $payment_reference,
        ];
        fn_change_order_status($order_id, OrderStatuses::PAID);
    } else {
        $pp_response = [
            'order_status' => 'F',
            'transaction_id' => $payment_reference,
            'reason_text' => $data['message'] . '.',
        ];

        fn_change_order_status($order_id, OrderStatuses::FAILED);
    }
}


/**
 * @param string $public_key
 * @param string $secret_key
 * @return array
 */
function get_key(string $secret_key, string $public_key ): array
{
    $response = Http::post(
        "https://seerbitapi.com/api/v2/encrypt/keys",
        "{
           'key': {$secret_key}.{$public_key}
           }",
        [
            'headers' => [
                'Content-Type: application/json',
            ],
        ]
    );

    return json_decode($response, true);
}

if (defined('PAYMENT_NOTIFICATION')) {
    if (empty($_REQUEST['order_id'])) {
        exit;
    }

    $order_id = $_REQUEST['order_id'];
    $payment_reference = $_REQUEST['reference'];


    if (!fn_check_payment_script('seerbit.php', $order_id)) {
        die('Access denied');
    }


    if($_REQUEST['code'] === "S19"){
        fn_change_order_status($order_id, OrderStatuses::CANCELED);
    } else {
        $order_info = fn_get_order_info($order_id);
        $processor = $order_info['payment_method']['processor_params'];

        if ($processor['mode'] === "test"){
            $secret_key = $processor['test_secret_key'];
            $public_key = $processor['test_public_key'];
        } else if($processor['mode'] === "live"){
            $secret_key = $processor['live_secret_key'];
            $public_key = $processor['live_public_key'];
        }


        $response_data = get_key($secret_key, $public_key);



        $verify = $payment_reference ? Http::get(
            "https://seerbitapi.com/api/v3/payments/query/{$payment_reference}",
            "",
            [
                'headers' => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $response_data['data']['EncryptedSecKey']['encryptedKey'],
                ],
            ]
        ) : "";

        $verify_payment_data = json_decode($verify, true);

        if($verify_payment_data['data']['code']) {
            order_status($verify_payment_data['data'], $payment_reference, $order_id);
        }
    }

    fn_order_placement_routines('route', $order_id);

} else {
    $payment_url = "https://seerbitapi.com/api/v2/payments";

    if ($processor_data['processor_params']['mode'] === "test"){
        $secret_key = $processor_data['processor_params']['test_secret_key'];
        $public_key = $processor_data['processor_params']['test_public_key'];
    } else if($processor_data['processor_params']['mode'] === "live"){
        $secret_key = $processor_data['processor_params']['live_secret_key'];
        $public_key = $processor_data['processor_params']['live_public_key'];
    }

    if(!$secret_key || !$public_key){
    die('Invalid payment configuration');
    }



    $response_data = get_key($secret_key, $public_key);


    if($response_data['data']['code'] === "00"){
        $order_tk = $response_data['data']['EncryptedSecKey'];
        $return_url = fn_url("payment_notification.return?payment=seerbit&order_id={$order_info['order_id']}", AREA, 'current');

        $payment_request_data = [
            'publicKey' => $public_key,
            'amount'=> "{$order_info['total']}",
            'currency' =>  $processor_data['processor_params']['currency'] ?? 'NGN',
            'country' =>  $processor_data['processor_params']['country'] ?? 'NG',
            'paymentReference' => "{$order_info['timestamp']}",
            'email' => "{$order_info['email']}",
            'fullName' => "{$order_info['firstname']} {$order_info['lastname']}",
            'callbackUrl' => $return_url,
        ];


        $res = Http::post(
            "{$payment_url}",
            json_encode($payment_request_data),
            [
                'headers' => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $order_tk['encryptedKey'],
                ],
            ]
        );

        $response_payment_data = json_decode($res, true);

        if ($response_payment_data['data']['code'] == "00" && $response_payment_data['data']['payments']) {
            fn_create_payment_form($response_payment_data['data']['payments']['redirectLink'], [], 'Seerbit', true, 'GET');
        } else {
            die("Error Processing Payment");
        }
    } else{
        die("Error Processing Payment");
    }
}