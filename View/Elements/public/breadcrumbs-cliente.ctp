<?php
if ( BreadcrumbComponent::$visible && ! empty($breadcrumbs) )
{
foreach ( $breadcrumbs as $breadcrumb )
{
$this->Html->addCrumb($breadcrumb[0], $breadcrumb[1]);
}
?>
<?
    echo $this->Html->getCrumbList(array('id' => 'breadcrumb', 'class' => 'breadcrumb align-items-center d-flex m-0'));
 ?>  
<?
}
?>


