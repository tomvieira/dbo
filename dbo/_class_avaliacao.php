<?

/* ================================================================================================================== */
/* DBO CLASS FILE FOR MODULE 'avaliacao' ======================================== AUTO-CREATED ON 01/08/2013 15:05:56 */
/* ================================================================================================================== */

/* IMPORTANT: This file is generated only in the first DBO sync, what means you should edit only via text editor. */

if(!class_exists('avaliacao'))
{
	class avaliacao extends dbo
	{
		/* smart constructor: will perform load() upon numeric argument and loadAll() upon string argument */
		function __construct($foo = '')
		{
			parent::__construct('avaliacao');
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

		function getFormAvaliacao()
		{
			global $_conf;
			ob_start();
			?>
				<div class="wrapper-form-avaliacao">
					<form method="post" action="ajax-avaliacao.php" class="form-avaliacao">
						<div class='row'>
							<div class='large-5 columns'><p>O serviço foi realizado?</p></div>
							<div class='large-7 columns text-center'>
								<input type='button' class="button secondary radius trigger-servico-realizado" name='' value="Sim" data-real-value='sim'/>
								<input type='button' class="button secondary radius trigger-servico-realizado" name='' value="Não" data-real-value='nao'/>
								<input type='hidden' name='servico_realizado' value=""/>
							</div><!-- col -->
						</div><!-- row -->

						<div class='row'>
							<div class='large-5 columns'><p>Qualidade do Serviço</p></div>
							<div class='large-7 columns'><?= $this->getStarRating('qualidade') ?></div><!-- col -->
						</div><!-- row -->
						
						<div class='row'>
							<div class='large-5 columns'><p>Tempo para execução</p></div>
							<div class='large-7 columns'><?= $this->getStarRating('tempo') ?></div><!-- col -->
						</div><!-- row -->
						
						<div class='row'>
							<div class='large-5 columns'><p>Feedback da Seção <i class="help icon" title="Qualidade do atendimento, educação e informações que <?= (($_conf->genero)?('o'):('a')) ?> <?= $_conf->nome_secao ?> lhe ofereceu."></i></p></div>
							<div class='large-7 columns'><?= $this->getStarRating('feedback') ?></div><!-- col -->
						</div><!-- row -->

						<div class='row'>
							<div class='large-5 columns'><p>Comentários / Críticas / Sugestões</p></div>
							<div class='large-7 columns'><textarea name="comentario"></textarea></div><!-- col -->
						</div><!-- row -->

						<div class='row'>
							<div class='large-12 columns text-right'><input type='button' name='' value="Finalizar avaliação" class="button radius submitter no-margin"/></div>
						</div><!-- row -->
		
						<input type='hidden' name='action' value="save-avaliacao"/>
						<input type='hidden' name='submit_token' value="<?= time().rand(1,1000) ?>"/>
						<input type='hidden' name='token' value="<?= $this->token ?>"/>
						<input type='hidden' name='id' value="<?= $this->id ?>"/>

					</form>
				</div>
			<?
			$ob_result = ob_get_clean();
			return $ob_result;
		}

		function getStarRating($field_name, $value = '')
		{
			ob_start();
			?>
			<div class="wrapper-star-rating">
				<div class="star-rating">
					<div class="star star-1"></div>
					<div class="star star-2"></div>
					<div class="star star-3"></div>
					<div class="star star-4"></div>
					<div class="star star-5"></div>
					<input type='hidden' name='<?= $field_name ?>' value=""/>
				</div>
			</div>
			<?
			$ob_result = ob_get_clean();
			return $ob_result;
		}

		function getResumo()
		{
			$message = "
				<ul>
					<li>Serviço realizado: <strong>".(($this->servico_realizado == 'nao')?('<span style="color: crimson;">Não</span>'):('<span style="color: olivedrab;">Sim</span>'))."</strong></li>
					<li>Qualidade: <strong><span style=\"color: ".(($this->qualidade >= 3)?('olivedrab'):('crimson')).";\">".$this->qualidade."</span></strong></li>
					<li>Tempo: <strong><span style=\"color: ".(($this->tempo >= 3)?('olivedrab'):('crimson')).";\">".$this->tempo."</strong></span></li>
					<li>Feedback: <strong><span style=\"color: ".(($this->feedback >= 3)?('olivedrab'):('crimson')).";\">".$this->feedback."</span></strong></li>
					".((strlen($this->comentario))?("<li>Comentário: <strong>".$this->comentario."</strong></li>"):(''))."
				</ul>
			";
			return $message;
		}

		function getPermalink($params = array())
		{
			extract($params);
			return DBO_URL."/../servico-user-view.php?&id=".$this->requisicao_item."&token=".(($requisicao_item_token)?($requisicao_item_token):($this->_requisicao_item->token))."&token_avaliacao=".$this->token;
		}

	} //class declaration
} //if ! class exists

?>