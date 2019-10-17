<?php
if(!defined("CLI")){
    define('CLI', (PHP_SAPI == 'cli') ? true : false);
}
if(!defined("EOL")){
    define('EOL', CLI ? PHP_EOL : '<br />');
}

if(!function_exists('is_cli')){
    /**
     * 判断是否通过命令行执行
     * @return bool
     */
    function is_cli(): bool
    {
        return preg_match("/cli/i", php_sapi_name()) ? true : false;
    }
}


if(!function_exists('p')){
    /**
     * @param $data
     * @param bool $notExitFlag
     */
    function p($data, $notExitFlag = false)
    {
        echo EOL;
        var_export($data);
        echo EOL;
        if (!$notExitFlag) {
            exit();
        }
    }
}


if(!function_exists('recursion')) {
    /**
     * @param array $arr
     * @param string $primary_key
     * @param string $father_key
     * @param string $children
     * @param int $num
     * @param bool $keyNeed
     * @param string $callback
     * @return array
     */
    function recursion(array $arr, $primary_key = 'id', $father_key = 'pid', $children = 'children', $num = 0, $keyNeed = true,$callback='')
    {
        $list = [];
        foreach ($arr as $val) {
            if ($val[$father_key] == $num) {
                $tmp = recursion($arr, $primary_key, $father_key, $children, $val[$primary_key], $keyNeed,$callback);
                $tmp && $val[$children] = $tmp;
                $val[$children] = $tmp??[];
                if($callback&&is_callable($callback)){
                    $val = $callback($val);
                }
                if ($keyNeed) {
                    $list[$val[$primary_key]] = $val;
                } else {
                    $list[] = $val;
                }
            }
        }
        return $list;
    }
}


if(!function_exists('itrim')){
    /**
     * @param string $string
     * @return string|string[]|null
     */
    function itrim(string $string)
    {
        return preg_replace('/^(\s| |　)*|(\s| |　)*$/', '',  trim($string));
    }
}

if(!function_exists('filterCAS')){
    /**
     * @param string $cas
     * @return bool|mixed
     */
    function filterCAS(string $cas)
    {
        $cas = itrim($cas);
        $reg = "/^[1-9]\d*-\d\d-\d$/";
        preg_match($reg, $cas, $match);
        if (isset($match[0]) && $match[0]) {
            $casArray = explode("-", $match[0]);
            $str = $casArray[0] . $casArray[1];
            $total = 0;
            $j = 1;
            for ($i = strlen($str) - 1; $i >= 0; $i--) {
                $total += $str[$i] * $j;
                $j++;
            }
            if ($total % 10 === intval($casArray[2])) {
                return $match[0];
            }
        }
        return false;
    }
}


if(!function_exists('filterMDL')){
    /**
     * @param string $mdl
     * @return mixed|string
     */
    function filterMDL(string $mdl)
    {
        $reg = "/MFCD\d{8}$/";
        preg_match($reg, itrim($mdl), $match);
        if ((isset($match[0]) && $match[1]) || !isset($match[0])) {
            return '';
        }
        return $match[0];
    }
}


if(!function_exists('filterSMILES')){
    /**
     * @param string $smiles
     * @param string $deep openabel
     * @return bool|string|string[]|null
     * @throws Exception
     */
    function filterSMILES(string $smiles,string $deep='')
    {
        $result = null;
        $smiles = itrim($smiles);
        if(preg_match("/^([^J][0-9BCOHNSOPrIFla@+\-\[\]\(\)\\/%=#$,.~&!]{6,})$/",$smiles)){
            if($deep=='openbabel'){
                if(!defined("OBABLE_PATH")){
                    if(PHP_OS=="Linux"||PHP_OS=="Darwin"){
                        $command = "which obabel";
                        exec($command,$resultOpenbabelPath);
                        if($resultOpenbabelPath){
                            define('OBABLE_PATH',$resultOpenbabelPath[0]);
                        }
                    }
                }
                if(defined('OBABLE_PATH')){
                    $commond = OBABLE_PATH . " -:\"$smiles\" -oreport";
                    exec($commond, $report);
                    if (isset($report[0]) && $report[0] === 'FILENAME:') {
                        $tmp = explode(":", $report[1]);
                        $result['mf'] = itrim($tmp[1]);
                        $tmp = explode(":", $report[2]);
                        $result['mw'] = itrim($tmp[1]);
                        $tmp = explode(":", $report[3]);
                        $result['exactmw'] = itrim($tmp[1]);
                        $result['smiles'] = $smiles;
                    }
                }else{
                    throw new Exception('Openbabel is not installed.');
                }
            }
            return $result??$smiles;
        }
        return false;
    }
}

if(!function_exists('smilesToIMG')){
    function smilesToIMG($SMILES, $filename, $path = "./") {
        $commond = OBABLE_PATH . " -:\"{$SMILES}\" -O " . $path . $filename . '.png';
        exec ( $commond, $report );
        if ($report) {
            return false;
        }
        $commond = OBABLE_PATH . " -:\"{$SMILES}\" -O " . $path . $filename . ".svg";
        exec ( $commond, $report );
        return true;
    }
}


if(!function_exists('_26to10')){
    function _26to10($char)
    {
        $chars = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
        $len = strlen($char);
        $sum = 0;
        for ($i = 0; $i < $len; $i ++) {
            $index = array_search($char[$i], $chars);
            $sum += ($index+1) * pow(26, $len - 1 - $i);
        }
        return $sum-1;
    }
}

if(!function_exists('_10to26')) {
    function _10to26($num, $plus = 0)
    {
        $chars = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        $num = intval($num) + $plus + 1;
        $char = '';
        do {
            $key = ($num - 1) % 26;
            $char = $chars[$key] . $char;
            $num = floor(($num - $key) / 26);
        } while ($num > 0);
        return $char;
    }
}

if(!function_exists('_10to26s')) {
    function _10to26s($num)
    {
        return _10to26($num,26);
    }
}


//bc start
if(!function_exists('bcround')){
    function bcround($number, $scale = 0)
    {
        if ($scale < 0) $scale = 0;
        $sign = '';
        if (bccomp('0', $number, 64) == 1) $sign = '-';
        $increment = $sign . '0.' . str_repeat('0', $scale) . '5';
        $number = bcadd($number, $increment, $scale + 1);
        return bcadd($number, '0', $scale);
    }
}
if(!function_exists('bcplus')) {
    function bcplus($left_operand, $right_operand, $scale = 0)
    {
        return bcround(bcadd($left_operand, $right_operand, $scale + 1), $scale);
    }
}

if(!function_exists('bcminus')) {
    function bcminus($left_operand, $right_operand, $scale = 0)
    {
        return bcround(bcsub($left_operand, $right_operand, $scale + 1), $scale);
    }
}

if(!function_exists('bcdivide')) {
    function bcdivide($left_operand, $right_operand, $scale = 0)
    {
        return bcround(bcdiv($left_operand, $right_operand, $scale + 1), $scale);
    }
}

if(!function_exists('bcmultiply')) {
    function bcmultiply($left_operand, $right_operand, $scale = 0)
    {
        return bcround(bcmul($left_operand, $right_operand, $scale + 1), $scale);
    }
}
//bc end

//money
if(!function_exists('moneyFormat')) {
    function moneyFormat($money,int $scale=0){
        $money = bcplus($money,0,$scale+1);
        $money = bcround($money,$scale);
        $money_parts = explode('.', $money);
        $money_parts[0]= strrev(implode(',', str_split(strrev($money_parts[0]), 3)));
        return implode('.', $money_parts);
    }
}


//seo
if(!function_exists('urlPushToBaidu')) {
    /**
     * 如果没有设置SEO_BAIDU_KEY则返回null
     * @param array $urls
     * @param string $type 1.urls新增 2.update更新 3.del 删除
     * @return array|mixed|null
     */
    function urlPushToBaidu($urls = [], $type = 'urls')
    {
        if (env('SEO_BAIDU_KEY') && env('SEO_BAIDU_PUSH')) {
            $api = "http://data.zz.baidu.com/{$type}?site=" . env('APP_URL') . "&token=" . env('SEO_BAIDU_KEY');
            $ch = curl_init();
            $options = array(
                CURLOPT_URL => $api,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => implode("\n", $urls),
                CURLOPT_HTTPHEADER => array('Content-Type: text/plain'),
            );
            curl_setopt_array($ch, $options);
            $result = curl_exec($ch);
            $result = @json_decode($result, 1);
            return $result ?? [];
        }
        return null;
    }
}




