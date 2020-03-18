<div class="row" style="margin-top: 15px; margin-bottom: 15px;">
	<div class="col-xs-12">
		<div id="oc-links" class="btn-group">

			<? 

			$estados = $this->Html->estadosOc();

			$sta = (isset($this->request->params['named']['sta'])) ? $this->request->params['named']['sta'] : '';

			$select_todo = (empty($sta)) ? 'selected' : '';

			echo $this->Html->link('<i class="fa fa-list"></i> <span>Todo</span>', array('action' => 'index'), array('class' => 'btn btn-block btn-xs btn-info ' . $select_todo, 'escape' => false,));

			foreach ($estados as $ie => $e): 

				$opts = $this->Html->estadoOcOpt($ie);

				if (!isset($opts['ico']))
					continue;

				$selected = ($sta == $ie) ? 'selected' : '';

				echo $this->Html->link('<i class="fa ' . $opts['ico'] . '"></i> <span>' . $e . '</span>', array('action' => 'index', 'sta' => $ie), array('style' => ' background-color:' . $opts['bgr'] . '; color: ' . $opts['txt'] . '; ', 'class' => 'btn btn-block btn-xs text-center ' . $selected, 'escape' => false));

			 endforeach ?>

			
		</div>
	</div>
</div>