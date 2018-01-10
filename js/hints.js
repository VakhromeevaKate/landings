$().ready(function() {
    //Крутые селекты с полем для ввода
    $("#apteki_city").select2();
    $("#apteki_metro").select2();

    function findByAddress(str) {
        var url = "/search/?address="+str;

        var address = $.ajax({
            url: url,
            async: false
        }).responseText;

        return address;
    }

    $('#SearchButton').bind('click',function(){
        var city  = $('#apteki_city option:checked').val();
        var metro = $('#apteki_metro option:checked').val();
        var h24   = $('#apteki_24h').is(":checked");
        var address = $('#SearchBox').val();

        if(city==''){
            city='moskva';
        }

        var url = '/apteki/' + city + '/';

        if(metro!=''){
            url += 'metro-'+metro+'/';
        }

        if(h24){
            url += 'kruglosutochnye/';
        }

        if(address != ''){
            address='?street='+address;
            url+=address;
        }
        location.href=url;


    });
    $('#ClearButton').bind('click',function(){
        var url = '/apteki/';
        var city  = $('#apteki_city option:checked').val();
        if(!city==''){
            var url = '/apteki/' + city + '/';
        }
        location.href=url;
    });

    $( "#apteki_city" ).change(function() {
        var city  = $('#apteki_city option:checked').val();
        var url = "/apteki/"+city;
        location.href=url;
    });

    $('input[data-list]').each(function () {
        var availableTags = $('#' + $(this).attr("data-list")).find('option').map(function () {
            return this.innerHTML;
        }).get();

    });

});