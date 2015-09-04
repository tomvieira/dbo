<?

	class painel
	{
		function initParam($param, $value)
		{
			if(!$_SESSION[sysId()]['painel_params'][$param])
			{
				$_SESSION[sysId()]['painel_params'][$param] = $value;
			}
		}

		function setParam($param, $value)
		{
			$_SESSION[sysId()]['painel_params'][$param] = $value;
		}

		function setParamMulti($param, $value)
		{
			$_SESSION[sysId()]['painel_params'][$param][$value] = $value;
		}

		function clearParam($param)
		{
			unset($_SESSION[sysId()]['painel_params'][$param]);
		}

		function clearParamMulti($param, $value = false)
		{
			if($value)
			{
				unset($_SESSION[sysId()]['painel_params'][$param][$value]);
			}
			else
			{
				unset($_SESSION[sysId()]['painel_params'][$param]);
			}
		}

		function getParam($param)
		{
			return $_SESSION[sysId()]['painel_params'][$param];
		}

		function getParams()
		{
			return $_SESSION[sysId()]['painel_params'];
		}
	}

?>