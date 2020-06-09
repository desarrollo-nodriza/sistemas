<a href="#" data-toggle="dropdown" class="btn btn-info dropdown-toggle" aria-expanded="false"><i class="fa fa-list"></i> Facturas por página <span class="caret"></span></a>
							
<ul class="dropdown-menu" role="menu">                                
<? 

$options = $this->Html->items_per_page();

foreach ($options as $name => $opts) : ?>
	<li><?=$this->Html->link($name, $opts)?></li>
<?
endforeach;
?>                                               
</ul>