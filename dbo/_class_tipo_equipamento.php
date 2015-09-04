<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'tipo_equipamento' ================================= AUTO-CREATED ON 31/10/2013 09:49:02 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('tipo_equipamento'))
{
	class tipo_equipamento extends dbo
	{
		/* smart constructor: will perform load() upon numeric argument and loadAll() upon string argument */
		function __construct($foo = '')
		{
			parent::__construct('tipo_equipamento');
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

	} //class declaration
} //if ! class exists

?>