<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'pessoa' =========================================== AUTO-CREATED ON 04/05/2012 16:21:58 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('pessoa'))
{
	class pessoa extends dbo
	{
		/* smart constructor: will perform load() upon numeric argument and loadAll() upon string argument */
		function __construct($foo = '')
		{
			parent::__construct('pessoa');
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
		//encriptando o password
		function save()
		{
			if(strlen(trim($this->pass)) != '128' && strlen(trim($this->pass)) > 0)
			{
				$this->pass = dbo::cryptPassword($this->pass);
			}
			return parent::save();
		}

		//encriptando o password
		function update($rest = '')
		{
			if(strlen(trim($this->pass)) != '128' && strlen(trim($this->pass)) > 0)
			{
				$this->pass = dbo::cryptPassword($this->pass);
			}
			return parent::update();
		}

		//mostrando a foto do usuário
		function foto($params = array())
		{
			extract($params);
			//params
			//size - small, medium, large
			//gravatar_size - px value, default 200
			if(strlen(trim($this->foto)))
			{
				return $this->_foto->url($params);
			}
			else
			{
				return getGravatar($this->email, ($gravatar_size ? $gravatar_size : 200));
			}
		}


	} //class declaration
} //if ! class exists

?>