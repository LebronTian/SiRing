<?php
$config = array (
    //应用ID,您的APPID。
    'app_id' => "2018082761132725",

    //商户私钥，您的原始格式RSA私钥
    'merchant_private_key' => "MIIEowIBAAKCAQEAq2KGCUdWRFqi0KaALNAlPulSp2gMViM8E05Srq/r3EOux8ZMLIBYdTuDtTjfnfEdGg450S19FRh4i4CBxF0ToprIVVbXSacriUQB8t6Pu9O4GqAqOKm5uFlUV+GsBt6ImHFkBU8Azik0fAF6saer1RTAYuYfy2+jhRwISo5bvdQYGaWEnOmgwzZ0EV2/B/KxmGwmCNSnrrPxit7jlWCvOk5UPKfNeA9y+39w++EEHXafFGNO8YlR85LBpwyAZhdjcjA1pi8L1LeJF3O/965QEfAxreEUs0yshIInrVeAsj+gz8Q2qJPQkRow6lM1irELP061ZXZKP9+kKqTpt7iioQIDAQABAoIBAQCPXT1OabRKPZ9Q9tblpcBiXf9cNneLXrIUXEJiCps8iAme58w0tbBJcN1+LPMyRc3YS+olhu3JRc0gtQDYaBvSu7O1X417+TE8A/21UmPd9P9elnh7Kc9H3MHnOcoTfPe6va+zmSDNVD6pNPuTvPTKrC87C9Gw9dRNtuNgqrEnmvOCUE81WmCQwX7iNuxmXXqfsPNpjv2VbrVDW/rRKBngBHWzXRFbck+F8Hf3sBvSPc1zHe89D5IknjhMG213Tk1QQr9qKxUuIQKuhtsQHvg+LtuojDL7lFn6U+4iy8N5lFxoYvslpNAvYf48c5jZbZlp+VC37+vTbbuS+wNvPaP5AoGBANXkBJH43IQ0NuTFj82W4umF+IHSaM92z0FeBqYD3XapivcxValIxWYbzby+6xZjmSECxRLgPTBCZZnqAi+U5pfgXllXZ4RtbBHslI51TF/5S4JZrzZFF0JhjvNjYMLuj2JIYy1enrAqxUq3O8PJDf7lCflnOnPIt2bXEssCJjUTAoGBAM0gO7F0N5izaXgizkbHPtQRUHblVd+J02XaQroACgKvuK47UkTLky6UBuz+D1yjW/UkDCW7jKH1Fxdrzuak9e+ZGql8/O8l9yMUE6UY82yOUqiNIySZCrehiRkeEcvGxW0GmiE0Nz2JmJek/qIQWxznZDkpf/NTtQsy8iBOGyP7AoGAa2Tjzo9P2amF7nQr8iRSpsI3tqd5LMIQ6ldVq0HBjvUt61QAGNGLG+vV73FFBKbZmjOT1Bh3YKXV8eQHWPDAn31uohk6xslSO+W36ZeH06COg1KYoP0r4o6tghNh4D58C/MgqQUbVIUFLrC192YZ+uPxkCJ+vOgI/j/7Fadsm7UCgYAaWdCxTC+0MyASac47826TyaGflHiCne8FP5Og105x5+b+ouo/ojNHIYb+POj2SpoOlNHmqwA28ghEXvoWUQyy+eUd7suDYUotPHAFnn3u7R2bP35LPknNKzg0fNNmbSOBjP6R02ZhRmLO4EhBw34g6WzLlxQzywYP6TyHf+EmrwKBgBqJs9lZmLySy7FwM21fNVsJeKpMYH8hVvHi+xcafHRy7L7qz3K7Kl4QN4wHU+xCFkeRBRg6FybOBhpVurwGr1epg5UgYnnu1x3Y2Gmm75CCWpbkLQZqopGkWnRKNguZY65IXnVPEBCefugoWWxd7u/fNfDLKO1Sb6Z3FnHNQthK",

    //异步通知地址
    'notify_url' => "http://工程公网访问地址/alipay.trade.wap.pay-PHP-UTF-8/notify_url.php",

    //同步跳转
    'return_url' => "http://mitsein.com/alipay.trade.wap.pay-PHP-UTF-8/return_url.php",

    //编码格式
    'charset' => "UTF-8",

    //签名方式
    'sign_type'=>"RSA2",

    //支付宝网关
    'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

    //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
    'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAq2KGCUdWRFqi0KaALNAlPulSp2gMViM8E05Srq/r3EOux8ZMLIBYdTuDtTjfnfEdGg450S19FRh4i4CBxF0ToprIVVbXSacriUQB8t6Pu9O4GqAqOKm5uFlUV+GsBt6ImHFkBU8Azik0fAF6saer1RTAYuYfy2+jhRwISo5bvdQYGaWEnOmgwzZ0EV2/B/KxmGwmCNSnrrPxit7jlWCvOk5UPKfNeA9y+39w++EEHXafFGNO8YlR85LBpwyAZhdjcjA1pi8L1LeJF3O/965QEfAxreEUs0yshIInrVeAsj+gz8Q2qJPQkRow6lM1irELP061ZXZKP9+kKqTpt7iioQIDAQAB",


);