(function ($) {
    "use strict";

    if ($('#captchaImageComment').attr("src") === '') {
        refreshCaptcha();
    }

    $('body').on('click', '#refreshCaptcha', function (e) {
        e.preventDefault();
        refreshCaptcha();
    });

    const mapContainer = $('#contactMap');
    const mapOption = {
        dragging: false,
        zoomControl: false,
        scrollWheelZoom: false,
    };
    const lat = mapContainer.attr('data-latitude');
    const lng = mapContainer.attr('data-longitude');
    const zoom = mapContainer.attr('data-zoom');

    const contactMap = L.map('contactMap', mapOption).setView([lat, lng], zoom);

    L.tileLayer('https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
        maxZoom: 18,
        tileSize: 512,
        zoomOffset: -1,
        attribution: '© <a target="_blank" rel="nofollow" href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(contactMap);

    var myIcon = L.icon({
        iconUrl: '/assets/default/img/location.png',
        iconAnchor: [lat - 14, lng + 10],
    });
    L.marker([lat, lng], {color: '#43d477', icon: myIcon}).addTo(contactMap);

})(jQuery);
