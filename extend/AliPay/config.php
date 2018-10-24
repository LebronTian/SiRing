<?php

$config = array (
    //应用ID,您的APPID。
    'app_id' => "2016112603335050",

    //商户私钥，您的原始格式RSA私钥
    'merchant_private_key' => "MIIEowIBAAKCAQEAz+SfWrndsOSD3AY3v5YtA9n+BoBcckMYfjgpIrT5Bu2YF2GR5oFCBJASSQeRRyDHPWL3i91lbyZeiBsE2l+rJcMTP+EfH6MpxMerwqfvOPw4p4OHHAnbI52xjdNZStBdIT7oEwEUsghuejCpWelL/b3CPFpW/1OpEVRnssw9gc0f1mius2eOXZ0+5JaJRZ/zJWxgyMHctF6NXcSG2oVOl0WyiNK/F4CuqdIcq1y8ZDiVvmRbyfzcEmbgob7MpwVFWw1Fge3z4fSnG7bicOJSXkPbWNhZmGe/yXCEXbA/8Kldp/nMkwnMGJ5A/3yFZTEUnmY60qnXA5T3R1KOnpXklwIDAQABAoIBAQCDq2VSbQ4AD3uES1vbuB3ipprBO2NR6zUEHEXReZWP0cPWazGhMJTDlww9vNFCn3wRYTEwIJUyBLcytQop1RXs4NS8TLUNsKWvwFcE/qABE54+WoukMonc0O+3x/hx7e5ONC2Ae9rDt5thQJjCHYTHvPvchcs8A5y9IRxcngcGweL6m6KUd4yT4yr5pPCXM8Q4B5cG/BM+MtLeqPJ1S7zheMKt4pN52M9pU9+n1V3nx1FgViv7ycOh8E+9L33S/Ri9HuLyIeV9zZ44g53ociUlSoQBnUIiDHWriHROWP0yxPdp0Et4oUPFcsDR1FVa8rFSmhZRauA6M7Um8SRXKVtBAoGBAPrhPqij8HfnOCJAGMcbwJnpQGZAypYBawEOSib3uIKyqEQmDlvzTjJgR2YbFUfGvgeAn0mX/Q4B/Vgffb1dqCJbU4McSE3GHJHCdBO6UqvUD4B8Qy6aJJomPGwgZAi+DAk9PtNDo2tC6DTZbd5UJMTqdpMq0776pjR6E3+7F2wZAoGBANQiyZTfw9qqf9xyQ4YKwu6v0165e+mnlycOTRkrBSESJUNSCH4aYHZnE4B9J1MU5fxxrZuk5qt6iu0N5AkUQY6xuLkKdjX8WJbHWgHHjvMxXsEqx1LQlQ2PSCCvF5jxB0xhzTjBa3uCzfabs3o+6MKh1QF1DuYMBE1B/rku8uwvAoGAdnuAIxbhj08EpLBOw2Ho8QdGocQBqRxcU7BS9tpRKnCDpUOvzl820/XCYodx4mcLAfINyCzelwn7gu3EbXVY3XjyFN57izd/8Jq8RUDeoEXTWGPXOqATnzVlnc8iTzqp5oclL5MnD5YWojb5e2GTx+fPPiuguvYXHnt00AMkyakCgYAkFOKqksDSUYu76Cd6BhyP0pImG3BrFplMCE+uxzVxIY/6+ln9cOkVWoTjpuXoaLaRkJhRz+N4KTi2B1XRAYQBDFN6DcB7gDdlNfUmNlYnIS+XtXn/qQChNMy02nMuDVkLcdshGyz37hCwMF1/nnGioToErG9jS4nzxhTYVJb2+wKBgGGU5XtXTZAeBtueAgwwPkdOe1pXHXjkeytG2cGeNJrBkMmj7B7eNt+3EkHw4yPgvj/e4OYNm4ojRH05FefZmb6dtLDUH0p1k9LeqEbGGbHn7cl7jDjTqaRznlODyaT3pJlRldZIaJ95VEwZtpMQCnItUAu5yGH3Vrgo2Y8eNpAn",

    //异步通知地址
    'notify_url' => "https://vip.gagaliang.com/Alipay_pay_code",

    //同步跳转
    'return_url' => "https://vip.gagaliang.com/Alipay_pay_code",

    //编码格式
    'charset' => "UTF-8",

    //签名方式
    'sign_type'=>"RSA2",

    //支付宝网关
    'gatewayUrl' => "https://openapi.alipay.com/gateway.do",

    //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
    'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAz+SfWrndsOSD3AY3v5YtA9n+BoBcckMYfjgpIrT5Bu2YF2GR5oFCBJASSQeRRyDHPWL3i91lbyZeiBsE2l+rJcMTP+EfH6MpxMerwqfvOPw4p4OHHAnbI52xjdNZStBdIT7oEwEUsghuejCpWelL/b3CPFpW/1OpEVRnssw9gc0f1mius2eOXZ0+5JaJRZ/zJWxgyMHctF6NXcSG2oVOl0WyiNK/F4CuqdIcq1y8ZDiVvmRbyfzcEmbgob7MpwVFWw1Fge3z4fSnG7bicOJSXkPbWNhZmGe/yXCEXbA/8Kldp/nMkwnMGJ5A/3yFZTEUnmY60qnXA5T3R1KOnpXklwIDAQAB",

);