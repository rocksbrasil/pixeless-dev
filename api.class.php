<?php
namespace pixeless;
require_once('defines.php');
require_once('plugins/filecache/filecache.class.php');

class api{
    private $endpoint, $user, $pass;
    private $filecache, $cacheLifetime = 3600;
    private $errorFunc, $lastError = Array('code' => 0, 'msg' => null);
    public $enableCache = true, $internalErrors = false;
    function __construct($endpoint, $user, $password){
        $this->endpoint = $endpoint;
        $this->user = $user;
        $this->pass = md5($password);
        $this->filecache = new \filecache\filecache();
        $this->filecache->cacheDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'pixeless-dev'.DIRECTORY_SEPARATOR;
        return true;
    }
    function setCacheDir($dir){
        if(!is_writable($dir)){
            throw new \Exception("Cache directory '".$dir."' is not writable!");
            return false;
        }
        $this->filecache->cacheDir = $dir;
        return true;
    }
    function getVars($GET = false, $POST = false){
        $retorno = false;
        $params = Array(
            'GET' => $GET,
            'POST' => $POST,
        );
        // REALIZAR CONSULTA NO CACHE
        $cacheHash = $this->user.'-'.md5($this->endpoint.json_encode($params));
        if($this->enableCache && $retorno = $this->filecache->cache_get($cacheHash, $cacheChangeTime)){
            if((time() - $cacheChangeTime) > $this->cacheLifetime){
                $this->filecache->cache_unset($cacheHash);
                $retorno = false;
            }else{
                $retorno = false;
            }
        }
        // REALIZAR CONSULTA NO ENDPOINT
        if(!$retorno){
            if($params){
                foreach($params as $key => $value){
                    $params[$key] = @json_encode($value);
                }
            }
            if($retorno = $this->request($this->endpoint, $params, $httpCode)){
                if($retornoDecoded = json_decode($retorno, true)){
                    $retorno = $retornoDecoded;
                    // SALVAR CONSULTA EM CACHE
                    if($this->enableCache){
                        $this->filecache->cache_set($cacheHash, $retorno);
                    }
                }else{
                    return $this->throwError(null, 'Invalid Endpoint Result: HTTP Response Code: '.$httpCode.' Message: '.$retorno);
                }
            }
        }
        // RETORNAR O RESULTADO / ERRO
        if($retorno){
            if(isset($retorno['status']) && $retorno['status'] == 'ok' && array_key_exists('response', $retorno)){// OK
                return $retorno['response'];
            }elseif(isset($retorno['status']) && $retorno['status'] == 'error' && isset($retorno['error']['message'])){
                return $this->throwError($retorno['error']['code'], $retorno['error']['message']);
            }else{
                $eMessage = (isset($retorno['error']['message']))? $retorno['error']['message'] : '';
                $eMessage .= (isset($retorno['status']))? '(Status: '.$retorno['status'].')': '';
                $eMessage .= (isset($retorno['response']))? 'Response: '.$retorno['response']: '';
                $eCode = (isset($retorno['error']['code']))? $retorno['error']['code'] : 'n/a';
                return $this->throwError($eCode, 'Unknown Error['.$eCode.']: '.$eMessage);
            }
        }else{
            return $this->throwError(null, "Invalid Endpoint: ".$this->endpoint);
        }
    }
    private function throwError($code, $message){
        $lastError = Array('code' => $code, 'msg' => $message);
        if($this->errorFunc && is_callable($this->errorFunc)){
            call_user_func($this->errorFunc, $code, $message);
        }
        if(!$this->internalErrors){
            throw new \Exception($message, $code);
        }
        return false;
    }
    function errorHandler($function){
        if(is_callable($function)){
            $this->errorFunc = $function;
            return true;
        }else{
            $this->errorFunc = null;
            return false;
        }
    }
    private function request($endpoint, $data, &$httpCode = 0){
        $data['user'] = $this->user;
        $data['pass'] = $this->pass;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,5); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); //timeout in seconds
        $retorno = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $retorno;
    }
}
