<?php

$tpl = eZTemplate::factory();

$tpl->TemplateFetchList = array();

$mytemplate = 'design:functionview/'.$Params["MyClassName"].'/'.$Params["MyFunctionName"].'/'.$Params["MyNodeView"] . '.tpl';

$resourceData=$tpl->loadURIRoot( $mytemplate, true );

if ($tpl->TemplateFetchList == array()) {
	echo '<h1>Error</h1><p>This class function has not been configured.</p><p>(missing functionview/'.$Params["MyClassName"].'/'.$Params["MyFunctionName"].'/'.$Params["MyNodeView"] . '.tpl)</p>';
	eZExecution::cleanExit();
}

$ClassName = $Params["MyClassName"];

$r=new ReflectionMethod(new $ClassName, $Params["MyFunctionName"]);
$params = $r->getParameters();

$func_params = array();

foreach ($params as $param) {
	if (array_key_exists($param->name, $_GET)) {
		$val = $_GET[$param->name];
		if ($val == 'true' || $val == 'false' || $val == 'null' || is_numeric($val)) {
			eval("\$val = $val;");
		} elseif (is_array(@unserialize(urldecode($val)))) {
			$val = @unserialize(urldecode($val));
		} else {
			$val = urldecode($val);
		}
		$func_params[] = $val;
	} else {
		$func_params[] = $param->getDefaultValue();
	}
}

$res = call_user_func_array(array(new $ClassName, $Params["MyFunctionName"]), $func_params);

$GLOBALS['eZDebugEnabled']=false;

$tpl->setVariable( 'nodes', $res );
$tpl->setVariable( 'view', $Params["MyNodeView"] );

echo($tpl->fetch($mytemplate));

eZExecution::cleanExit();

?>
