$(function(){

    /* Donut dashboard chart
    Morris.Donut({
        element: 'dashboard-donut-1',
        data: [
            {label: "En Carro", value: 213},
            {label: "Pendiente", value: 764},
            {label: "Pagado", value: 3311}
        ],
        colors: ['#B64645','#FEA223','#95B75D'],
        resize: true
    });
    /* END Donut dashboard chart

    /* Bar dashboard chart
    Morris.Bar({
        element: 'dashboard-bar-1',
        data: [
            { y: 'Oct 10', a: 75, b: 35 },
            { y: 'Oct 11', a: 64, b: 26 },
            { y: 'Oct 12', a: 78, b: 39 },
            { y: 'Oct 13', a: 82, b: 34 },
            { y: 'Oct 14', a: 86, b: 39 },
            { y: 'Oct 15', a: 94, b: 40 },
            { y: 'Oct 16', a: 96, b: 41 }
        ],
        xkey: 'y',
        ykeys: ['a', 'b'],
        labels: ['New Users', 'Returned'],
        barColors: ['#33414E', '#3FBAE4'],
        gridTextSize: '10px',
        hideHover: true,
        resize: true,
        gridLineColor: '#E5E5E5'
    });
    /* END Bar dashboard chart */

    /* Line dashboard chart */


    /* EMD Line dashboard chart */


    $(".x-navigation-minimize").on("click",function(){
        setTimeout(function(){
            rdc_resize();
        },200);
    });
});
