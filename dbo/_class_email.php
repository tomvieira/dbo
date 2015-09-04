<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'email' ============================================ AUTO-CREATED ON 03/10/2013 19:40:53 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('email'))
{
	class email extends dbo
	{
		/* smart constructor: will perform load() upon numeric argument and loadAll() upon string argument */
		function __construct($foo = '')
		{
			parent::__construct('email');
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
			return $this->subject;
		}

	} //class declaration
} //if ! class exists

/* class que disparara os emails */

?>