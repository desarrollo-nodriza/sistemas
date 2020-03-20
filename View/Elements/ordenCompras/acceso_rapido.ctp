<div class="row" style="margin-top: 15px; margin-bottom: 15px;">
	<div class="col-xs-12">
		<div id="oc-links" class="btn-group">

			<? 

			$estados = $this->Html->estadosOc();

			$sta = (isset($this->request->params['named']['sta'])) ? $this->request->params['named']['sta'] : '';

			$select_todo = (empty($sta)) ? 'selected' : '';

			$total = $this->Html->estado_oc_total();

			echo $this->Html->link('<i class="fa fa-list"></i> <span>Todo</span> <span>(' . $total . ')</span>', array('action' => 'index'), array('class' => 'btn btn-block btn-xs btn-info ' . $select_todo, 'escape' => false,));

			foreach ($estados as $ie => $e): 

				$opts = $this->Html->estadoOcOpt($ie);

				if (!isset($opts['ico']))
					continue;

				$selected = ($sta == $ie) ? 'selected' : '';

				$total_estado = $this->Html->estado_oc_total($ie);

				echo $this->Html->link('<i class="fa ' . $opts['ico'] . '"></i> <span>' . $e . '</span> <span>(' . $total_estado . ')</span>', array('action' => 'index', 'sta' => $ie), array('style' => ' background-color:' . $opts['bgr'] . '; color: ' . $opts['txt'] . '; ', 'class' => 'btn btn-block btn-xs text-center ' . $selected, 'escape' => false));

			 endforeach ?>

			
		</div>
	</div>
</div>