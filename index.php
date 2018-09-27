<?php
namespace pixeless;

require_once('defines.php');
require_once('functions.php');
require_once('api.class.php');
require_once('plugins/smarty/libs/Smarty.class.php');
require_once('plugins/html2text/html2text.php');
$pixelessApi = new \pixeless\api(API_ENDPOINT, API_KEY, API_SECRET);
$pixelessVars = $pixelessApi->getVars($_GET, $_POST);
$_TEMPLATE = (isset($pixelessVars['themeVars']))? $pixelessVars['themeVars'] : false;
$_PAGE = (isset($_TEMPLATE['PAGE']))? $_TEMPLATE['PAGE'] : 'home';

// INSTANCIAR SMARTY
$smarty = new \Smarty();
$smarty->registerPlugin('modifier', 'seoText', 'generateSeoName');
$smarty->registerPlugin('modifier', 'removeQueryString', 'removeQueryString');
$smarty->registerPlugin('modifier', 'html2Text', 'convert_html_to_text');
$smarty->template_dir = 'theme_content/templates/';
$smarty->compile_dir = 'theme_content/templates/compiled/';

// reinstanciar variáveis locais
$_TEMPLATE['BASE_DIR'] = getExistentUrlPath();
$_TEMPLATE['THEME_DIR'] = getExistentUrlPath().'theme_content/';

// assimilar variáveis do template
if($_TEMPLATE){
    foreach ($_TEMPLATE as $key => $value) {
        $smarty->assign($key,$value);
    }
}

// INCLUIR PÁGINA DO TEMPLATE
if($_PAGE && file_exists('theme_content/templates/'.$_PAGE.'.html')){
    $smarty->display($_PAGE.'.html');
}else{
    header('HTTP/1.0 404 Not Found');
    if(file_exists('theme_content/templates/404.html')){
        $smarty->display('theme_content/templates/404.html');
    }else{
        echo "<h1>404 Not Found</h1>";
    }
}

