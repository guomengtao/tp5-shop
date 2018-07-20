<?php
$config = array (	
		//应用ID,您的APPID。
		'app_id' => "2016070501580903",

		//商户私钥
		'merchant_private_key' => "MIIEpAIBAAKCAQEAvmOcLqEOpjqoifB212Tbxahsvw1GstUSmXbGeG4vEfLGAi2Qx0URlKJMkAsca2k0BzJq0X38SuEhj/jE1XruXNkiM9DUoMy0sGLePZrtDJ+GmBznFjeZD+YF75HSu7b28efAm/SGcnMwYgDwY9zPORkn43Dpg77qoMi/IErJ0ZdRAUVwWykJywNHO0CWzcoHLJqYUVq4tRiOlXHkXOsMN2xjbosaOCDyhzYHVF6nE07p3PK56tzPdEFuVzTDNt56+Qj6gbUw3XwC0HxU1z0SxHeh2q3qARs+8as8W2QMhrZXTmvmIQol2Wxgob4iTtqoZPnkfyNuZgo7GvOKOvBltwIDAQABAoIBAEMTMuYWpeOYjOI0DppqFSd44/8XPU4p1Sit+NRa9UhcgLgpt3UM91mnKFQjJfsNkF/sukdeHHGBCWMAoNAZRDr/4JXiv9Z66DmwS13m5BbraO7UV/Gy2sEIKBU0CctKdYvaOzh2mJtfy67rZ8W2+GbEXxI5KHva2iyVLQjCcIHfMh6ph4EXrWSsF0ENurdD/RsIuxKdWuzglRmIl0AOmDx5XcbZrYtCxC011CwaqU2pvbLSLYE2xcztD+z4vDQZ6DYlX0ZNC2Am6VH3HfKrWMVel3WeNY3KIm3xrbReF12nZwI8w6nm1uqPNePxY/QlaCAucglRrXsDZTY8fde7OqkCgYEA9a+lgKDp+I4UDw1HyEbMVKRVFRVHtw5q2zJ4MP+sozAWN4EtLk6Qh8fKvnmYJyt6p72RlThmbAjrahI0LuoGKg16LuiUU4flZ7fVOMHUZyVBKBa+ztj+ylsB29f9XmBMJXciuo4RY5xRi0LW63OuubEpEDUbbz83WXgFog7rMQsCgYEAxmGxyRd34rMk81A123SWx2Bp5pYRJhq0MsJGseHlkAipEf23SFxiFqNSvl9ARPub2h2EpFrHeKe+DXjI6CgwOcTPvcw9BPhWx+FVDUXnB7T+6v0pEd2HXrvvzD090IkKfalXc7lwmxs7Izbt9ObMZjVYU9qVIdCQa5SbhpO4oYUCgYADWQJbT1YA0gbJ8bdhrj1Ihfi20dRpVSP1mqlymXlDu/sBhJwV/nIPt5Q2pjeBiSEhuf6K1vrmS/TRPDBD12KAHjDlBLxIqxhgImwgWtvHEAXkQvMUpHZMUZMj6LyGFa/T7tylSBxKjC28RQvip2/hDd3uX5rhGC5r5KvG+ocP4wKBgQCAGnYDtPPloGkr9Y/Rtufmt4urKxzHzlausGWWWJk0+WK0C3Jfd8ifdbfo1vlZkmCB6K3OtBA7CZbgfC7AO7Nomn7LwSdmHjdru0aA27Lkdyxl8jSjJLpVomanLKTOFLCBlOi0AfuFLYByrPXcP22eUrRG8c97loKr9bq4nIuNQQKBgQCRij3v5UDTbje4f1UJNX0xpC45RcvXje3BKn1iE7X0yWs2awc5uNeY5Je0m9QHT+RP3ONCQq5fjWpu7wSy/kdulm7Ln+q1kzMDMJjJE8T4xveG5uSg1G8yoKPXcsXzMDfhh3lF3E9aIGKjayZcl7r4y5L6PMBDln1g8WYlK+jU0w==",
		
		//异步通知地址
		'notify_url' => "http://open.gaoxueya.com/alipay/notify_url.php",
		
		//同步跳转
		'return_url' => "http://open.gaoxueya.com/alipay/return_url.php",

		//编码格式
		'charset' => "UTF-8",

		//签名方式
		'sign_type'=>"RSA2",

		//支付宝网关
		'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

		//支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
		'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvmOcLqEOpjqoifB212Tbxahsvw1GstUSmXbGeG4vEfLGAi2Qx0URlKJMkAsca2k0BzJq0X38SuEhj/jE1XruXNkiM9DUoMy0sGLePZrtDJ+GmBznFjeZD+YF75HSu7b28efAm/SGcnMwYgDwY9zPORkn43Dpg77qoMi/IErJ0ZdRAUVwWykJywNHO0CWzcoHLJqYUVq4tRiOlXHkXOsMN2xjbosaOCDyhzYHVF6nE07p3PK56tzPdEFuVzTDNt56+Qj6gbUw3XwC0HxU1z0SxHeh2q3qARs+8as8W2QMhrZXTmvmIQol2Wxgob4iTtqoZPnkfyNuZgo7GvOKOvBltwIDAQAB",
);