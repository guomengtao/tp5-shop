<?php

namespace App\index\Controller;

use think\Controller;
use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

class Alipay extends Controller
{

    protected $config = [
        'app_id'         => '2016070501580903',
        'notify_url'     => 'http://open.gaoxueya.com/index/alipay/notify',
        'return_url'     => 'http://open.gaoxueya.com/index/alipay/returns',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEApwNWNNugEQsxKKKC50u+PO9Y3xLOzfwtwg/ZGbdEccStrGAr+e5rRK3qS7bhD8aCMtMaNljKTPGko4c4zEDE+Kjb3V/RwptQj26SacfvX7LdqfYk7H/KCEla1bHfKkbACqW+tCLROWeWU0pLgRoJLnJtTmxvCFZTYzKtkb5+rpzgkboBvKlxFkKi0V3JqA2nXURUrtmvXzq8QmBTn01zD4baBqzhj2CsZO9uHnfndtETDodcQQebC7dveII4Kf9naIumzbGkIxPboHsXtVhKcVVZG0+THMMmBOivNPazDtQnB7gbrZEZkQei4CnpJfiaZZbMlMhHPS1UA1jVWV1hWQIDAQAB',
        // 加密方式： **RSA2**
        'private_key'    => 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC1zlOIjdciyl9fO6RGP71T1Y+f9XikpgBhnNkv084WPZIi3tJYQ/zyVvBLPiIYS72jpgpFC3I3Wv8uV5NxVR7TY36uvb2psZ4vJ2MTAYjf4kS1ANKZjmgllG1BBzaO8cz+866/E4YvYXxxcwH8+P1WPIcwzF/5vQjK1A4UHTpjqrZUrR4wLUqxwTHRPetiJKSiRMfFkKk/8t4ExpA/S3DJkFNd+antz/TwjCw5DMeGaW3ZLxaQC8CUGO17CeWsXchoQIboLqyb9tr753ALS0W5FgpOTpAmunvIN78i6gWuJiB+dzeJFDOMQZmktOfW9xDFjkPClceupM4O53Iq2laxAgMBAAECggEAA0uuL/eCQtswR2WpYKyfHfntFJU6jqGDuEFDnp5USgrrrz4iyf7RgwMYcAS8UXxt/51u6jl+5In9vjVQMum8GVDgZwKBgaX6nBg2r42DkatkW3OcXbQ7JxM9t8QNtSGk+aopLMWJ1SoLO3M+Qfuxe/K08KmDw98tXZ/icXLmMBBMU4U2b+9UZlV5I4tSzKAshhgk7Vgk6o75/sogk6yGloFiHHXpfZ1zf/crlJ0SxLQzZ0+PO/bxlgTDX23bG0lYAFAMaXhprTCiq4sYdPAPzMmqf5KpEPO5GFORVAECd9BHYQTrmw0uV44t9TDQnuhB0/Awe0M+DvdfVSy3XoOPEQKBgQDYKbWl2YzMhPm++W86zfc+mJugW4sKvMNjLuR3AYwdg6feLMPfq3vaPZiVDbiDgcH9650Pa8wlkZmQTAP+ejEWmnJWnSWAjnNB5IshQ5MWtAZzZ1OY3q0YAQz09+bQOmu5JahzH94LVZ10l1KDkblKhxo2nICWwphRdsM+LKrWtwKBgQDXT7L0NlqaQslJQAEKO54RE9w4VRquTqEjZc2MWtDwW3aMEVAXEyYuN/qVfe+M5rU0jvqjQfKOvCxREy0Bq5zm41fx57vHuItiv+xA+1MtHOEyphWMe5lya6hHPlVscIzk6Y0fyLgC/aPRlB0LjJge+6X0RSwt3Dmsvusyb1UV1wKBgF3TDNMl8GU9OqHX5p2hlLWIy9P4qAtLD4vXaLb25vhQkuZui75j0mhR4A6iW/pIsgki4ZM1+PA9mGf3dqxYIUJsW0CZCRQZwJFTP8h6ajeqgDpLGQ/7ZypKGnOhvn+XO/arD8iYhmppCOT4YYpWEBT3OzuFBpNpd2+0mt47yRNVAoGBAL3VYRx8R8m2lK3mpoQVKDo6XnG0Zz/Dx8Lj4SScdZdVrOG16f1OPt2FMYRYcrqyNpXOciE65db/Bbu9wnK0kjPnwgRgomlmxk4clPp+HEmsKslzMZCY8SO207lstfhUC4VQfcLGP6czZhpEEo+6N+0pRppl2pvcjWVHNytSiZwlAoGBAJD5m4FSh9wleLt3ikDNqiFERDrtth4ZZsjfhlxB2nsG85rAv3+mE5Td2SNaAFus1Lq5dTc8tDMGWSWpFRlf0QmT0VCHQ6wDH3raxHXQgOz6U7ngrUooRcu99Lz6fmvOk49cbHNDsoNGIqsE5yH4hYFtYkKjuajfHtbHJws6+4qD',
        'log'            => [ // optional
                              'file'     => './logs/alipay.log',
                              'level'    => 'info', // 建议生产环境等级调整为 info，开发环境为 debug
                              'type'     => 'single', // optional, 可选 daily.
                              'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        ],
        'http'           => [ // optional
                              'timeout'         => 5.0,
                              'connect_timeout' => 5.0,
                              // 更多配置项请参考 [Guzzle](https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html)
        ],
    ];

    /**
     * 支付金额设置
     * @return \think\response\View
     */
    public function userPay()
    {
        return view();
    }


    public function index()
    {
        $total = input('price');
        $title = input('title');

        if (!$total) {
            return;
        }

        $order = [
            'out_trade_no' => time().'666',
            'total_amount' => $total,
            'subject'      => $title,
        ];

        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send();// laravel 框架中请直接 `return $alipay`
    }

    public function scan()
    {

        $order = [
            'out_trade_no' => time().'666',
            'total_amount' => '0.01',
            'subject'      => 't 扫码',
        ];
        $alipay = Pay::alipay($this->config)->scan($order);
        // $result = $alipay->scan($order);
        //二维码内容： $qr = $result->qr_code;
        return $alipay->send();// laravel 框架中请直接 `return $alipay`
    }

    public function returns()
    {
        $data = Pay::alipay($this->config)->verify(); // 是的，验签就这么简单！

        // 订单号：$data->out_trade_no
        // 支付宝交易号：$data->trade_no
        // 订单总金额：$data->total_amount
        echo "订单号：".$data->out_trade_no;
        echo "支付宝交易号：".$data->trade_no;
        echo "订单总金额：".$data->total_amount;
        echo dump($data);

    }

    public function notify()
    {
        $alipay = Pay::alipay($this->config);

        try {
            $data = $alipay->verify(); // 是的，验签就这么简单！

            // 请自行对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            // 1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号；
            // 2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额）；
            // 3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）；
            // 4、验证app_id是否为该商户本身。
            // 5、其它业务逻辑情况

            Log::debug('Alipay notify', $data->all());
        } catch (\Exception $e) {
            // $e->getMessage();
        }

        return $alipay->success()->send();// laravel 框架中请直接 `return $alipay->success()`
    }
}