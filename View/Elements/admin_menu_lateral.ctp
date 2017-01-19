<div class="page-sidebar">
	<ul class="x-navigation x-navigation-custom">
		<li class="xn-logo">
			<?= $this->Html->link(
				'<span class="fa fa-dashboard"></span> <span class="x-navigation-control">Backend</span>',
				'/admin',
				array('escape' => false)
			); ?>
			<a href="#" class="x-navigation-control"></a>
		</li>
		<li class="xn-profile">
			<a href="#" class="profile-mini">
            <? if ( empty($avatar['Administrador']['google_imagen']) ) : ?>
    			<?=$this->Html->image('logo_user.jpg', array('alt' => 'Perfil','class' => 'mCS_img_loaded')); ?>
    			
    		<? else : ?>
    			<? $avatarSz =  str_replace('sz=50', 'sz=200', $avatar['Administrador']['google_imagen']);?>
				<img class="mCS_img_loaded" src="<?=$avatarSz;?>" alt="Perfil">
    		<? endif; ?>
            </a>
            <div class="profile">
            	<div class="profile-image">
            		<? if ( empty($avatar['Administrador']['google_imagen']) ) : ?>
            			<?=$this->Html->image('logo_user.jpg', array('alt' => 'Perfil','class' => 'mCS_img_loaded')); ?>
            			
            		<? else : ?>
            			<? $avatarSz =  str_replace('sz=50', 'sz=200', $avatar['Administrador']['google_imagen']);?>
						<img class="mCS_img_loaded" src="<?=$avatarSz;?>" alt="Perfil">
            		<? endif; ?>
                </div>
                <div class="profile-data">
                    <div class="profile-data-name"><?=$this->Session->read('Auth.Administrador.nombre');?></div>
                    <div class="profile-data-title"><?=$this->Session->read('Auth.Administrador.Rol.nombre');?></div>
                </div>
            </div>                                                                        
        </li>

		<!-- Get Modules View -->	
		<?= $this->element('modulos'); ?>

	</ul>
</div>
