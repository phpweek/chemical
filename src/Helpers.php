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
     * @return array
     */
    function recursion(array $arr, $primary_key = 'id', $father_key = 'pid', $children = 'children', $num = 0, $keyNeed = true)
    {
        $list = [];
        foreach ($arr as $val) {
            if ($val[$father_key] == $num) {
                $tmp = recursion($arr, $primary_key, $father_key, $children, $val[$primary_key], $keyNeed);
                $tmp && $val[$children] = $tmp;
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

