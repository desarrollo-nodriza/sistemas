
$(document).ready(function(){
    $("#TrackingTrackingForm").validate({
        rules: {
            'data[Tracking][tracking_number]': {
                required: true,
                number: true
            }
        },
        messages: {
            'data[Tracking][tracking_number]': {
                required: 'Campo requerido',
                number: 'Ingrese solo números'
            }
        }
    });
});