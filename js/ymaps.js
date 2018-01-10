$(document).ready(function(){

    ymaps.ready(function(){
    console.log("Подключились");

    var city = document.getElementById("SelectedCity").innerHTML;
    var metro = $('#apteki_metro option:checked').val();
    var address = $('#SearchBox').val();
    var search = 'Россия '+ city.toLowerCase()+ ' ' + address.toLowerCase();

    //Создаем карту
    var map = new ymaps.Map('YMapsID', {
        center: [55.751574, 37.573856],
        zoom: 9
    }, {
        searchControlProvider: 'yandex#search',
        autoFitToViewport: 'always',
        controls: ['default', 'routeButtonControl','geolocationControl']
    });

    // Создаем объект ObjectManager
    objectManager = new ymaps.ObjectManager({
        // Чтобы метки начали кластеризоваться, выставляем опцию.
        clusterize: true,
        // ObjectManager принимает те же опции, что и кластеризатор.
        gridSize: 32,
        clusterDisableClickZoom: false,
        zoomMargin: 1
    });

    //Ставим центр карты в выбранный город, если он задан
    if (mapcenter){
        console.log('mapcenter:' + mapcenter + ', zoom:'+zoom);
        map.setCenter(mapcenter);
        if(zoom){
            if (metro.length > 0 || address.length > 0){
                zoom = 14;
                setMapCenter(region, city, map, zoom, metro, search);
            }
        } else {
            zoom = 10;
            map.setCenter(mapcenter);
        }
    } else {
        setMapCenter(region, city, map, zoom, metro, search);
    }

    objectManager.objects.options.set('preset', 'islands#redIcon');
    objectManager.clusters.options.set('preset', 'islands#redClusterIcons');

    // Список аптек из админки
    fetchStoresData(search, city);

    // Добавляем метки аптек на карте
    map.geoObjects.add(objectManager);

    //Получаем координаты объекта поиска по заданному в поиске адресу
    getSearchCoordinates(search, map)
    });
});

    // Функции для работы с картой на сайте
    function getSearchCoordinates(search, map) {
        var myGeocoder = ymaps.geocode(search);
        myGeocoder.then(
            function (res) {
                //alert('Координаты объекта :' + res.geoObjects.get(0).geometry.getCoordinates());
                myPoint = new ymaps.Placemark(
                    res.geoObjects.get(0).geometry.getCoordinates(),
                    {
                        balloonContent: search +', '+ res.geoObjects.get(0).geometry.getCoordinates(),
                        iconCaption: 'Точка, которую Вы искали',
                        draggable: true
                    }
                );
                // Точку ставим ТОЛЬКО ЗДЕСЬ, т.к. получение отложенное!
                map.geoObjects.add(myPoint);
            },
            function (err) {
                console.log('Ошибка: геолокатор потерялся :(');
            }
        );
    }

    function setMapCenter(region, city, map, zoom, metro, address) {
        console.log("region "+region+",city "+ city+",map "+ map+",zoom"+ zoom+",metro"+ metro+", address"+address);
        if (city.length > 0 || region.length > 0){
            city = "Россия " + region + ' ' + city + ' '+ metro + ' ' + address;
        } else {
            city = "Россия " + "Москва" + ' '+ metro + ' ' + address;
        }
        //console.log(city);
        if(zoom){
            //zoom ++;
            if (metro.length > 0 || address.length > 0){
                zoom = 14;
            }
        } else {
            zoom = 12;
        }

        var myGeocoder = ymaps.geocode(city);
        myGeocoder.then(
            function(res) {
                map.setCenter(res.geoObjects.get(0).geometry.getCoordinates(),zoom);
            },
            function (err) {
                console.log('Ошибка: город не найден');
            }
        );
    }

    function fetchStoresData(search){
        //здесь нужна выборка из json-а перед установкой точек
        var output = '';
        var coordinates = '';
        var str;
        output = JSON.parse(ymaps_json);

        /*myjson = JSON.parse(ymaps_json);
        if (search != ''){
            for (var i in myjson.features){
                str = myjson.features[i].geometry.balloonContentBody.toLowerCase();
                if(str.indexOf(search.toLowerCase()) > -1){
                    coordinates = myjson.features[i];
                    if (output.length > 0){
                        output += ','+JSON.stringify(coordinates);
                    } else {output += JSON.stringify(coordinates);}

                }
            }
            output = '{"type":"FeatureCollection","features":[' + output + ']}';
        } else { output = myjson };*/
        objectManager.add(output);
        storesList(storesList, output);
        //console.log(output);
    }

    function getNearestStores(JSON) {
        // Зная позицию и зону досягаемости, определяем 5 ближайших аптек
        var coordinates = [];
        for (var i in JSON.features[i].geometry.coordinates) {
            console.log(i + ' ' + JSON.features[i].geometry.coordinates);
            coordinates = JSON.features[i].geometry.coordinates;
        }
        return coordinates;
    }

    function storesList(list, ymapsJSON) {
        for (var i in ymapsJSON.features){
            list.innerHTML = "<div>"+ymapsJSON.features[i].geometry.balloonContentBody+"</div>";
        }
    }