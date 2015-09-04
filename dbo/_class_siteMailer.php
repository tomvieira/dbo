<?
class siteMailer {

	var $__data = array();
	var $message;
	var $subject;
	var $to = array();
	var $from_name;
	var $from_email;

    public function __set($name, $attr)
	{
        $this->__data[$name] = $attr;
    }

	public function __get ($name)
	{
		return $this->__data[$name];
	}

	function __construct($val = false)
	{
		global $_conf;

		if(is_numeric($val))
		{
			$email = new email($val);
			$this->message = $email->message;
			$this->subject = $email->subject;
		}
		elseif(strlen($val))
		{
			$email = new email("WHERE identificador = '".$val."'");
			$this->message = $email->message;
			$this->subject = $email->subject;
		}
		$this->from_name = $_conf->from_name;
		$this->from_email = $_conf->from_mail;
	}

	function prepare()
	{
		global $_conf;

		/* trocando palavras chaves */
		$txt = $this->subject.' '.$this->message;

		$re1='\\[';	# Any Single Character 1
		$re2='\\[';	# Any Single Character 2
		$re3='((?:[a-z][a-z0-9_]*))';	# Variable Name 1
		$re4='\\]';	# Any Single Character 3
		$re5='\\]';	# Any Single Character 4

		if ($c=preg_match_all ("/".$re1.$re2.$re3.$re4.$re5."/is", $txt, $matches))
		{
			if(sizeof($matches[1]))
			{
				foreach($matches[1] as $key => $value)
				{
					if(strlen($this->{$matches[1][$key]}))
					{
						$this->message = str_replace($matches[0][$key], $this->{$value}, $this->message);
						$this->subject = str_replace($matches[0][$key], $this->{$value}, $this->subject);
					}
				}
			}
		}

		if($_conf->id)
		{
			$this->message .= $_conf->mail_footer;
		}
	}

	function error($msg)
	{
		echo('<div style="color: red">Erro: '.$msg.'</div>');
	}

	function parseTo()
	{
		$to = (array)$this->to;
		if(!sizeof($to))
		{
			$this->error("Sem destinatários");
		}
		else
		{
			return $to;
		}
	}

	function send()
	{
		$this->prepare();

		$to = $this->parseTo();
		$message = $this->message;
		$subject = $this->subject;
		$from_name = $this->from_name;
		$from_email = $this->from_email;

		if(!sizeof($to)) { $this->error("Sem destinatários"); }
		if(!strlen($message)) { $this->error("Sem mensagem"); }
		if(!strlen($subject)) { $this->error("Sem assunto"); }
		if(!strlen($from_name)) { $this->error("Remetente sem nome"); }
		if(!strlen($from_email)) { $this->error("Remetente sem e-mail"); }
		
		$to = (array)$to;
		foreach($to as $address)
		{
			@mail($address, $subject, $message, "From: ".$from_name." <".$from_email.">\r\nMIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\n", "-r ".$from_email);
		}
	}

	function preview()
	{
		$this->prepare();

		$to = $this->parseTo();
		$message = $this->message;
		$subject = $this->subject;
		$from_name = $this->from_name;
		$from_email = $this->from_email;

		if(!sizeof($to)) { $this->error("Sem destinatários"); }
		if(!strlen($message)) { $this->error("Sem mensagem"); }
		if(!strlen($subject)) { $this->error("Sem assunto"); }
		if(!strlen($from_name)) { $this->error("Remetente sem nome"); }
		if(!strlen($from_email)) { $this->error("Remetente sem e-mail"); }

		ob_start();
		?>
		<h5>De: <?= $from_name ?> (<?= $from_email ?>)</h5>
		<h5>Para: <?= $to ?></h5>
		<h5>Assunto: <?= $subject ?></h5>
		<h5>Mensagem:</h5><?= $message ?>
		<?
		return ob_get_clean();
	}
}

?>