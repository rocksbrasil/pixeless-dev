<?php

function getExistentUrlPath() {
    if (!isset($_SERVER['HTTP_HOST'])) {
        return '';
    }
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        $folderDivisor = '\\';
    } else {
        $folderDivisor = '/';
    }
    $urlPath = $_SERVER['REQUEST_URI'];
    if ($urlPath[0] == '/') {
        $urlPath = substr($urlPath, 1);
    }
    $urlPath = explode('/', $urlPath);
    $serverPath = getcwd();
    if ($serverPath[0] == $folderDivisor) {
        $serverPath = substr($serverPath, 1);
    }
    $serverPath = explode($folderDivisor, $serverPath);
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        $realPath = 'HTTPS://';
    }elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
        $realPath = 'HTTPS://';
    }else {
        $realPath = 'HTTP://';
    }
    $realPath = $realPath . $_SERVER['HTTP_HOST'];

    $index = 0;
    foreach ($serverPath as $key => $value) {
        if (isset($urlPath[$index]) && $value == $urlPath[$index] && ($index != 0 || $urlPath[$index] != 'home')) {
            $realPath = $realPath . '/' . $value;
            $index++;
        }
    }
    $realPath = $realPath . '/';
    return $realPath;
}
function gerarKey($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false) {
	$lmin = 'abcdefghijklmnopqrstuvwxyz';
	$lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$num = '1234567890';
	$simb = '!@#$%*-';

	$retorno = '';
	$caracteres = '';

	$caracteres .= $lmin;
	if ($maiusculas)
		$caracteres .= $lmai;
	if ($numeros)
		$caracteres .= $num;
	if ($simbolos)
		$caracteres .= $simb;

	$len = strlen($caracteres);

	for ($n = 1; $n <= $tamanho; $n++) {
		$rand = mt_rand(1, $len);
		$retorno .= $caracteres[$rand - 1];
	}
	return $retorno;
}

function generateSeoName($text) {
	if (!$text || !is_string($text)) {
		return false;
	}
	$map = array('á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'é' => 'e', 'ê' => 'e', 'í' => 'i', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ú' => 'u', 'ü' => 'u', 'ç' => 'c', 'Á' => 'A', 'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'É' => 'E', 'Ê' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ç' => 'C');
	$text = strtr($text, $map);
	$text = strtolower($text);
	$text = preg_replace('/([^\w]+)/im', '-', $text);
	return $text;
}

function r_copy($src,$dst,$mod = 0777) {
    $dir = opendir($src);
    @mkdir($dst);
	chmod($dst,$mod);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                r_copy($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
				chmod($dst . '/' . $file,$mod);
            }
        }
    }
    closedir($dir);
}



function buscaCep($cep) {
	$resultado = @file_get_contents('http://republicavirtual.com.br/web_cep.php?cep=' . urlencode($cep) . '&formato=query_string');
	if (!$resultado) {
		$resultado = "&resultado=0&resultado_txt=erro+ao+buscar+cep";
	}
	parse_str($resultado, $retorno);
	return utf8_converter($retorno);
}

function detectMobile() {
	if (isset($_SERVER['HTTP_USER_AGENT'])) {
		return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER['HTTP_USER_AGENT']);
	} else {
		return false;
	}
}

function relativeTime($time) {
	$delta = time() - $time;
	if ($delta < 1 * MINUTE) {
		return $delta == 1 ? "um segundo" : $delta . " segundos";
	}
	if ($delta < 2 * MINUTE) {
		return "alguns minutos";
	}
	if ($delta < 45 * MINUTE) {
		return floor($delta / MINUTE) . " minutos";
	}
	if ($delta < 90 * MINUTE) {
		return "uma hora";
	}
	if ($delta < 24 * HOUR) {
		return floor($delta / HOUR) . " horas";
	}
	if ($delta < 48 * HOUR) {
		return "1 dia";
		// ontem
	}
	if ($delta < 30 * DAY) {
		return floor($delta / DAY) . " dias";
	}
	if ($delta < 12 * MONTH) {
		$months = floor($delta / DAY / 30);
		return $months <= 1 ? "um mês" : $months . " meses";
	} else {
		$years = floor($delta / DAY / 365);
		return $years <= 1 ? "um ano" : $years . " anos";
	}
}

function latlonDistancia($lat1, $long1, $lat2, $long2) {
	$d2r = 0.017453292519943295769236;

	$dlong = ($long2 - $long1) * $d2r;
	$dlat = ($lat2 - $lat1) * $d2r;

	$temp_sin = sin($dlat / 2.0);
	$temp_cos = cos($lat1 * $d2r);
	$temp_sin2 = sin($dlong / 2.0);

	$a = ($temp_sin * $temp_sin) + ($temp_cos * $temp_cos) * ($temp_sin2 * $temp_sin2);
	$c = 2.0 * atan2(sqrt($a), sqrt(1.0 - $a));

	return 6368.1 * $c;
}

function aasort(&$array, $key) {
	$sorter = array();
	$ret = array();
	if ($array) {
		reset($array);
		foreach ($array as $ii => $va) {
			$sorter[$ii] = $va[$key];
		}
		asort($sorter);
		foreach ($sorter as $ii => $va) {
			$ret[$ii] = $array[$ii];
		}
	}
	$array = $ret;
}

function validarCPF($cpf = false) {
	$cpf = str_pad(preg_replace('/[^0-9]/', '', $cpf), 11, '0', STR_PAD_LEFT);
	// Verifica se nenhuma das sequências abaixo foi digitada, caso seja, retorna falso
	if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {
		return FALSE;
	} else {// Calcula os números para verificar se o CPF é verdadeiro
		for ($t = 9; $t < 11; $t++) {
			for ($d = 0, $c = 0; $c < $t; $c++) {
				$d += $cpf{$c} * (($t + 1) - $c);
			}
			$d = ((10 * $d) % 11) % 10;
			if ($cpf{$c} != $d) {
				return FALSE;
			}
		}
		return TRUE;
	}
}

function validarCNPJ($cnpj = false) {
	$cnpj = preg_replace('/[^0-9]/', '', $cnpj);
	$cnpj = (string)$cnpj;
	$cnpj_original = $cnpj;
	$primeiros_numeros_cnpj = substr($cnpj, 0, 12);
	if (!function_exists('multiplica_cnpj')) {
		function multiplica_cnpj($cnpj, $posicao = 5){
			$calculo = 0;
			for ($i = 0; $i < strlen($cnpj); $i++) {
				$calculo = $calculo + ($cnpj[$i] * $posicao);
				$posicao--;
				if ($posicao < 2) {
					$posicao = 9;
				}
			}
			return $calculo;
		}
	}
	$primeiro_calculo = multiplica_cnpj($primeiros_numeros_cnpj);
	$primeiro_digito = ($primeiro_calculo % 11) < 2 ? 0 : 11 - ($primeiro_calculo % 11);
	$primeiros_numeros_cnpj .= $primeiro_digito;
	$segundo_calculo = multiplica_cnpj($primeiros_numeros_cnpj, 6);
	$segundo_digito = ($segundo_calculo % 11) < 2 ? 0 : 11 - ($segundo_calculo % 11);
	$cnpj = $primeiros_numeros_cnpj . $segundo_digito;
	if ($cnpj === $cnpj_original) {
		return true;
	} else {
		return false;
	}
}
function validarEmail($email, $checkDomain = true){
	//verifica se e-mail esta no formato correto de escrita
	if (filter_var($email, FILTER_VALIDATE_EMAIL)){
		//Valida o dominio
		if($checkDomain){
			$dominio = explode('@',$email);
			if(!checkdnsrr($dominio[1],'A')){
				return false;
			}else{
				return true;
			}
		}else{
			return true;
		}
    }else{
		return false;
	}
}
function validarData($dat){
	if(substr_count($dat, '/') != 2){
		return false;
	}
	$data = explode("/","$dat"); // fatia a string $dat em pedados, usando / como referência
	$d = $data[0];
	$m = $data[1];
	$y = $data[2];
	return checkdate($m,$d,$y);
}
function dataToTimestamp($data){
	if(!validarData($data)){return false;}
	return strtotime(str_replace('/','-', $data));
}
function dataToInsertDate($data){
	return date('Y-m-d H:i:s', dataToTimestamp($data));
}
function getStringNumber($string) {
	return preg_replace('/([^0-9])/i', '', $string);
}
function getStringFloat($string) {
	$string = preg_replace('/([^0-9\.\,])/i', '', $string);
	$string = str_replace(',','.',$string);
	$string = strval($string);
	return $string;
}

function utf8_converter($array) {
	return $array;
	if(is_array($array)){
		array_walk_recursive($array, function(&$item, $key) {
			if (!mb_detect_encoding($item, 'utf-8', true)) {
				$item = utf8_encode($item);
			}
		});
	}else{
		if (!mb_detect_encoding($array, 'utf-8', true)) {
			$array = utf8_encode($array);
		}
	}
	return $array;
}

function removeQueryString($query, $string) {// remove uma variável de um query string
	if (!$string){return $query;}
	if(!$stringPos = strpos($query, '?')){
		$stringPos = strpos($query, '&');
	}
	if($stringPos > 0){
		$postVars = false;
		$page = substr($query, 0, $stringPos);
		$vars = substr($query, $stringPos+1);
		$vars = explode('&', $vars);
		if($vars){
			foreach ($vars as $key => $value) {
				if(strpos($value, '=')){
					$var = explode('=',$value);
					if($var[0] != $string){
						$postVars[$var[0]] = $var[1];
					}
				}
			}
		}
		$query = $page;
		if($postVars){
			$query .= '?'.http_build_query($postVars);
		}
	}
	return $query;
}

function strtoupperplus($string){
	return strtr(strtoupper($string), "àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ", "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞß");
}

function strtolowerplus($string) {
	return strtr(strtolower($string), "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞß", "àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ");
}

function getDatabaseTableTree($tablename, $tablename_id, $tablename_parentid, $start_subid = 0, $recursivelimit = 50, $pagelimit = 50, $pageid = 1, $arrayConditions = Array()) {
	if ($pageid < 1) {$pageid = 1;
	}
	if (!isset($GLOBALS['getDatabaseTableTree']['Level'])) {
		$GLOBALS['getDatabaseTableTree']['Level'] = 0;
	}
	$database = $GLOBALS['database'];
	$database -> startCondGroup();
	$database -> addCond($tablename_parentid, $start_subid);
	if ($start_subid == 0 || $start_subid == '0') {$database -> addCond($tablename_parentid, 'NULL', 'IS', 'OR');
	}
	$database -> closeCondGroup();

	if (is_array($arrayConditions) && $arrayConditions) {
		foreach ($arrayConditions as $key => $value) {
			if ($key && $value) {
				$database -> addCond($key, $value);
			}
		}
	}

	$limit = ($GLOBALS['getDatabaseTableTree']['Level'] == 0) ? (($pageid - 1) * $pagelimit) . ',' . ($pageid * $pagelimit) : '';
	$return = $database -> select($tablename, $tablename_id . ' ASC', $limit);
	if ($return) {
		foreach ($return as $key => $value) {
			$GLOBALS['getDatabaseTableTree']['Level']++;
			$tempreturn = getDatabaseTableTree($tablename, $tablename_id, $tablename_parentid, $value[$tablename_id]);
			if ($tempreturn && $GLOBALS['getDatabaseTableTree']['Level'] <= $recursivelimit) {
				$return[$key]['sublevel'] = $tempreturn;
			}
			$GLOBALS['getDatabaseTableTree']['Level']--;
			unset($tempreturn);
		}
	}
	if ($GLOBALS['getDatabaseTableTree']['Level'] == 0) {
		unset($GLOBALS['getDatabaseTableTree']['Level']);
	}
	return $return;
}

function getDatabaseTableTreeParents($tablename, $tablename_id, $tablename_parentid, $start_subid = 0, $arrayConditions = Array()) {
	$database = $GLOBALS['database'];
	$return = false;
	while ($start_subid) {
		if (is_array($arrayConditions) && $arrayConditions) {
			foreach ($arrayConditions as $key => $value) {
				if ($key && $value) {
					$database -> addCond($key, $value);
				}
			}
		}
		$database -> addCond($tablename_id, $start_subid);
		$result = $database -> selectOneReg($tablename);
		if ($result) {
			$return[] = $result;
			$start_subid = $result[$tablename_parentid];
		} else {
			$start_subid = false;
			break;
		}
	}
	/*
	 if($return && ($return[0][$tablename_parentid] > 0)){
	 $database->addCond('DT_DEL', 'NULL', 'IS');
	 $database->addCond('ID', $return[0][$tablename_parentid]);
	 $subreturn = $database->selectOneReg($tablename);
	 if($subreturn){
	 foreach ($subreturn as $key => $value) {
	 $return[] = $subreturn[$key];
	 }
	 }
	 }

	 */
	return @array_reverse($return);
}

function currentURL() {
	$pageURL = 'http';
	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {$pageURL .= "s";
	}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function objectToArray($obj){
    if(is_object($obj)) $obj = (array) $obj;
    if(is_array($obj)) {
        $new = array();
        foreach($obj as $key => $val) {
            $new[$key] = objectToArray($val);
        }
    }
    else $new = $obj;
    return $new;       
}

function hrefEncode($href){
	$href = utf8_converter($href);
	if(!$href || !is_string($href)){return false;}
	return urlencode(urlencode($href));
}
function hrefDecode($href){
	$href = utf8_converter($href);
	if(!$href || !is_string($href)){return false;}
	return urldecode(urldecode($href));
}

function truepath($path){
    // whether $path is unix or not
    $unipath=strlen($path)==0 || $path{0}!='/';
    // attempts to detect if path is relative in which case, add cwd
    if(strpos($path,':')===false && $unipath)
        $path=getcwd().DIRECTORY_SEPARATOR.$path;
    // resolve path parts (single dot, double dot and double delimiters)
    $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
    $absolutes = array();
    foreach ($parts as $part) {
        if ('.'  == $part) continue;
        if ('..' == $part) {
            array_pop($absolutes);
        } else {
            $absolutes[] = $part;
        }
    }
    $path=implode(DIRECTORY_SEPARATOR, $absolutes);
    // resolve any symlinks
    if(file_exists($path) && linkinfo($path)>0)$path=readlink($path);
    // put initial separator that could have been lost
    $path=!$unipath ? '/'.$path : $path;
    return $path;
}
function mask($val, $mask){
	$maskared = '';
	$k = 0;
 	for($i = 0; $i<=strlen($mask)-1; $i++){
   		if($mask[$i] == '#'){
      		if(isset($val[$k])){
       			$maskared .= $val[$k++];
			}
		}else{
     		if(isset($mask[$i])){
     			$maskared .= $mask[$i];
     		}
 		}
	}
 	return $maskared;
}
function getClientIp() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

