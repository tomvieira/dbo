<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'tipo_servico' ===================================== AUTO-CREATED ON 26/07/2012 09:48:51 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('tipo_servico'))
{
	class tipo_servico extends dbo
	{
		/* smart constructor: will perform load() upon numeric argument and loadAll() upon string argument */
		function __construct($foo = '')
		{
			parent::__construct(get_class($this));
			if($foo != '')
			{
				if(is_numeric($foo))
				{
					$this->id = $foo;
					$this->load();
				}
				elseif(is_string($foo))
				{
					$this->loadAll($foo);
				}
			}
		}

		//your methods here
		function checkFileExtension($file_name)
		{
			if(!strlen(trim($this->extensoes_permitidas))) 
				return true;
			$ext = dboGetExtension($file_name);
			$perms = explode(",", $this->extensoes_permitidas);
			$perms = array_map(trim, $perms);
			$perms = array_map(strtolower, $perms);
			if(in_array(strtolower($ext), $perms)) 
				return true;
			return false;
		}

	} //class declaration
} //if ! class exists

?>