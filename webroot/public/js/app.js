function validaRut(campo){
    if ( campo.length == 0 ){ return false; }
    if ( campo.length < 8 ){ return false; }

    campo = campo.replace('-','')
    campo = campo.replace(/\./g,'')

    if ( campo.length == 0 ){ return false; }
    if ( campo.length < 8 ){ return false; }

        campo = campo.replace('-','')
        campo = campo.replace(/\./g,'')

    if ( campo.length > 9 ){ return false; }

    var suma = 0;
    var caracteres = "1234567890kK";
    var contador = 0;    
    for (var i=0; i < campo.length; i++){
        u = campo.substring(i, i + 1);
        if (caracteres.indexOf(u) != -1)
        contador ++;
    }
    if ( contador==0 ) { return false }
    
    var rut = campo.substring(0,campo.length-1)
    var drut = campo.substring( campo.length-1 )
    var dvr = '0';
    var mul = 2;
    
    for (i= rut.length -1 ; i >= 0; i--) {
        suma = suma + rut.charAt(i) * mul
                if (mul == 7)   mul = 2
                else    mul++
    }
    res = suma % 11
    if (res==1)     dvr = 'k'
                else if (res==0) dvr = '0'
    else {
        dvi = 11-res
        dvr = dvi + ""
    }
    if ( dvr != drut.toLowerCase() ) { return false; }
    else { return true; }
}

$.validator.addMethod("rut", function(value, element) { 
        return this.optional(element) || validaRut(value); 
}, "Revise el RUT");

$.validator.addMethod( "time", function( value, element ) {
	return this.optional( element ) || /^([01]\d|2[0-3]|[0-9])(:[0-5]\d){1,2}$/.test( value );
}, "Please enter a valid time, between 00:00 and 23:59" );


var backend_clientes = function(){
	return {
		formulario: function(){
			return {
				validacionSimple: function(selector, opts = {}) {
					$(selector).each(function(){

						let form = $(this);
						let bootstrapize = {
							errorElement: "em",
							errorPlacement: function ( error, element ) {
								// Add the `invalid-feedback` class to the error element
								error.addClass( "invalid-feedback" );

								if ( element.prop( "type" ) === "checkbox" ) {
									error.insertAfter( element.next( "label" ) );
								} else {
									error.insertAfter( element );
								}
							},
							highlight: function ( element, errorClass, validClass ) {
								$( element ).addClass( "is-invalid" ).removeClass( "is-valid" );
							},
							unhighlight: function (element, errorClass, validClass) {
								$( element ).addClass( "is-valid" ).removeClass( "is-invalid" );
							}
						};

						$.extend( opts, bootstrapize );
						
						form.validate(opts);

						form.find('input, select, textarea').each(function(){

							var $ths = $(this);

							if ($ths.hasClass('not-blank')) {
								$ths.rules("add", {
							        required: true,
							        messages: {
							        	required: 'Campo requerido'
							        }
							    });
							}

							if ($ths.hasClass('is-rut')) {
								$ths.rules("add", {
							        rut: true,
							        messages: {
							        	rut: 'Ingrese un rut válido'
							        }
							    });
							}

							if ($ths.hasClass('is-email')) {
								$ths.rules("add", {
							        email: true,
							        messages: {
							        	email: 'Formato de email no válido'
							        }
							    });
							}

							if ($ths.hasClass('is-date')) {
								$ths.rules("add", {
							        date: true,
							        messages: {
							        	date: 'Formato de fecha no válido (ej:2001-01-30)'
							        }
							    });
							}

							if ($ths.hasClass('is-digit')) {
								$ths.rules("add", {
							        digits: true,
							        min: 0,
							        messages: {
							        	digits: 'Ingrese solo números',
							        	min: '0 es el mínimo'
							        }
							    });
							}

							if ($ths.hasClass('is-number')) {
								$ths.rules("add", {
							        number: true,
							        min: 0,
							        messages: {
							        	number: 'Ingrese solo números',
							        	min: '0 es el mínimo'
							        }
							    });
							}


							if ($ths.hasClass('is-date')) {
								$ths.rules("add", {
									required: true,
							        date: true,
							        messages: {
							        	required: 'Seleccione fecha',
							        	date: 'Formato de fecha no válido'
							        }
							    });
							}

							if ($ths.hasClass('is-time')) {
								$ths.rules("add", {
									required: true,
							        time: true,
							        messages: {
							        	required: 'Seleccione hora',
							        	time: 'Formato de hora no válido'
							        }
							    });
							}

						});

					});
				},
				init: function(){
					if ($('.js-formulario').length) {
						backend_clientes.formulario.validacionSimple('.js-formulario');
					}
				}
			}
		}(),
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
    backend_clientes.formulario.init();

});