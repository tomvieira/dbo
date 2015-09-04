<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'categoria_servico' ================================ AUTO-CREATED ON 13/12/2012 10:55:42 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('categoria_servico'))
{
	class categoria_servico extends dbo
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

		function getBreadcrumbIdentifier()
		{
			return $this->nome;
		}

	} //class declaration
} //if ! class exists

?>