var backend_clientes = function(){
	return {
		dashboard: function(){
			return {
				ajustar_pantalla: function(){
					let sidebarWidth = $('#sidebar').width(),
					documentWidth    = $(window).width(),
					contentWidth     = documentWidth - sidebarWidth,
					content          = $('#content'),
					breadcrumb       = $('#breadcrumb');
					content.css('width', contentWidth);	
					content.css('top', contentWidth);	

				},
				init: function(){

					/*backend_clientes.dashboard.ajustar_pantalla();

					$(window).on('resize', function(){
						backend_clientes.dashboard.ajustar_pantalla();
					});*/	

				}
			}
		}(),
		clientes: function(){
			return {
				bind: function(){

				},
				init: function(){
					backend_clientes.clientes.bind();
				}
			}
		}()
	}
}();



$(document).ready(function () {

    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
        $(this).find('span').toggleClass('d-none');
    });


    backend_clientes.dashboard.init();

});