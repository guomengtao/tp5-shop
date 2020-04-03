<?php

namespace App\index\Controller;

use app\common\controller\Frontend;
use app\index\model\Order;
use think\Controller;
use think\Cookie;
use think\Session;
use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

class Alipay extends Frontend
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

    /**
     * 积分充值
     * @return \think\response\View
     */
    public function money()
    {
        Session::set('return_url','index/member/money');
        $this->assign('title','积分充值');
        return view();
    }


    public function index()
    {
        $total      = input('price');
        $title      = input('title');

        // 如果存在新的同步返回地址，就直接到新的返回地址
        //    if ($return_url){
        //        $this->config['return_url'] = 'http://open.gaoxueya.com/'.$return_url ;
        //    }


        if (!$total) {
            return;
        }

        $order = [
            'out_trade_no' => time(),
            'total_amount' => $total,
            'subject'      => $title,
            'user_id'      => $this->user_id,
        ];

        // 记录这个单号。但是未完成付款
        $orderSave = new Order();
        $orderSave->data($order);
        $orderSave->save();


        $alipay = Pay::alipay($this->config)->web($order);

        return $alipay->send();// laravel 框架中请直接 `return $alipay`
    }

    public function scan()
    {
        $order  = [
            'out_trade_no' => time().'666',
            'total_amount' => '0.01',
            'subject'      => 't 扫码',
        ];
        $alipay = Pay::alipay($this->config)->scan($order);
        // $result = $alipay->scan($order);
        //二维码内容： $qr = $result->qr_code;
        return $alipay->send();// laravel 框架中请直接 `return $alipay`
    }


    /**
     * 一级回调地址
     * @throws \Yansongda\Pay\Exceptions\InvalidConfigException
     * @throws \Yansongda\Pay\Exceptions\InvalidSignException
     */
    public function returns()
    {
        $data = Pay::alipay($this->config)->verify(); // 是的，验签就这么简单！


        // 订单号：$data->out_trade_no
        // 支付宝交易号：$data->trade_no
        // 订单总金额：$data->total_amount


        $out_trade_no = $data->out_trade_no;


        // 查询详细业务需求的回调地址
        $return_url = Session::get('return_url');
        Session::set('return_url','');
        $return_url = $return_url?:'index/index/order';

        // 更新支付状态

        // 判断这个订单已更新

        // $order = [
        //     'status'       => 1,
        //     'out_trade_no' => $data->out_trade_no,
        //     'trade_no'     => $data->trade_no,
        //     'total_amount' => $data->total_amount,
        //     'buyer_email' => 'from_returns',
        // ];


        // 更新的命名
        // $orderUpdate = Order::Where('out_trade_no', $order['out_trade_no'])
        //     ->update($order);


        // $this->redirect('index/index/order');
        // 重定向方式直接跳转到用户的会员里的订单管理处
        // 并且发送订单号


        $this->redirect($return_url, ['out_trade_no'=>$out_trade_no]);

        // echo "订单号：".$data->out_trade_no;
        // echo "支付宝交易号：".$data->trade_no;
        // echo "订单总金额：".$data->total_amount;
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
            $json = '{"gmt_create":"2020-04-02 23:39:31","charset":"utf-8","gmt_payment":"2020-04-02 23:39:38","notify_time":"2020-04-02 23:39:39","subject":"02 Composer 安装与使用 简介","sign":"Xn13nFxt71LKytqit322Ahcu60DJYaCi377LHyXD4nETT90810oYN+q9rJR61Iff4yVuxRZRLlLpM7C+COVmEMEdj5DIV9sCP62yW5cikXSVerFbqO4wrUkw77rs1uIPvOCZ70nyseTPDaThKGaVf9S8ShFBU8m+RpSvyyZslXTISgrr+oghdfkMy5yCTGVVmtlfOpZfkzOxAHn6YrdrP29BLjrvA+DpDre\/l6AvfklsD\/JFcHcaM5xFguNKCnHNd15foVE\/HYAd7BTrx+dEyZlxlWgTIj9R9EUHdmPVm5QfoSiVSLTqwF7Tju8d52XmWIb1L\/ZPP\/ioAyTxIpRRmg==","buyer_id":"2088822675183141","invoice_amount":"0.01","version":"1.0","notify_id":"2020040200222233938083141409415349","fund_bill_list":"[{\"amount\":\"0.01\",\"fundChannel\":\"PCREDIT\"}]","notify_type":"trade_status_sync","out_trade_no":"1585841964666ac订单号c","total_amount":"0.01","trade_status":"TRADE_SUCCESS","trade_no":"2020040222001483141439290278","auth_app_id":"2016070501580903","receipt_amount":"0.01","point_amount":"0.00","buyer_pay_amount":"0.01","app_id":"2016070501580903","sign_type":"RSA2","seller_id":"2088002229990889"}';
            // 存储一下异步给返回的数据情况
            // $info['body'] = $data->toJson();
            // 临时存入order表的body里，做一个体验
            // $orderAll = new Order();
            // $orderAll->data($info);
            // $orderAll->save();


            $app_id = $data->app_id;
            // 不是本商户的，直接忽略
            if ($app_id <> $this->config['app_id']) {
                return "SUCCESS";
            }


            $out_trade_no = $data->out_trade_no;

            // 检查是不是未支付订单
            $check_out_trade_no = Order::where('out_trade_no ', $out_trade_no)
                ->where('status ', 0)
                ->count();
            if (!$check_out_trade_no) {
                return "SUCCESS";
            }


            if ($check_out_trade_no) {
                // 如果查询是丢单，就保存进去
                $order = [
                    'status'       => 1,
                    'out_trade_no' => $data->out_trade_no,
                    'trade_no'     => $data->trade_no,
                    'total_amount' => $data->total_amount,
                    'body'         => 'from_notify',
                ];


                // 更新的命名
                $orderUpdate = Order::Where('out_trade_no', $order['out_trade_no'])
                    ->update($order);

                return "SUCCESS";
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        // return $alipay->success()->send();// laravel 框架中请直接 `return $alipay->success()`
    }
}