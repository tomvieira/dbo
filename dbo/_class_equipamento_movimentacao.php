<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'equipamento_movimentacao' ========================== AUTO-CREATED ON 16/04/2013 09:15:31 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('equipamento_movimentacao'))
{
	class equipamento_movimentacao extends dbo
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
		function delete()
		{	
			$ret = parent::delete();
			$this->_equipamento->recoverLocal();
			return $ret;
		}

	} //class declaration
} //if ! class exists

?>