@extends('layouts.app')

@section('title', 'Monitoring Lokasi')

@section('content')
<link href="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.css" rel="stylesheet">

<!--begin::Content-->
<div id="kt_app_content" class="app-content flex-column-fluid">
    <!--begin::Content container-->
    <div id="kt_app_content_container" class="app-container container-xxl">
        <!--begin::Row-->
        <div class="row g-5 gx-xl-10 mb-5 mb-xl-10">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">Peta Lokasi Karyawan</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Memantau lokasi karyawan berdasarkan titik kordinat terakhir (menggunakan Mapbox)</span>
                        </h3>
                    </div>
                    <div class="card-body py-4">
                        <div id="map" style="height: 600px; width: 100%; border-radius: 8px; border: 1px solid #E4E6EF; z-index: 1;"></div>
                    </div>
                </div>
            </div>
        </div>
        <!--end::Row-->
    </div>
    <!--end::Content container-->
</div>
<!--end::Content-->
@endsection

@push('scripts')
<script src="https://api.mapbox.com/mapbox-gl-js/v2.15.0/mapbox-gl.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // PERHATIAN: Masukkan API Key Mapbox Anda di file .env sebagai MAPBOX_TOKEN
        mapboxgl.accessToken = '{{ env('MAPBOX_TOKEN', 'YOUR_MAPBOX_ACCESS_TOKEN_HERE') }}';
        
        var map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v12', // style URL
            center: [106.8456, -6.2088], // starting position [lng, lat]
            zoom: 10 // starting zoom
        });

        // Add zoom and rotation controls to the map.
        map.addControl(new mapboxgl.NavigationControl());

        var markers = [];

        function loadLocations() {
            var token = localStorage.getItem('jwt_token');
            if (!token) {
                console.warn("Token not found, trying to fetch without token or waiting for login.");
            }

            fetch('/api/users-locations', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 401) {
                        alert("Sesi Anda telah habis atau Anda belum login. Silakan login kembali.");
                        window.location.href = '/login';
                    }
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Clear existing markers
                markers.forEach(marker => marker.remove());
                markers = [];
                
                var bounds = null;
                
                if (Array.isArray(data)) {
                    data.forEach(function(user) {
                        if (user.latitude && user.longitude) {
                            var lng = parseFloat(user.longitude);
                            var lat = parseFloat(user.latitude);
                            
                            var popupContent = `
                                <div style="text-align: center; padding: 5px;">
                                    <strong>${user.nama}</strong><br>
                                    <span style="color: #666;">@${user.username}</span><br>
                                    <small>${lat}, ${lng}</small>
                                </div>
                            `;
                            
                            var popup = new mapboxgl.Popup({ offset: 25 })
                                .setHTML(popupContent);
                                
                            var marker = new mapboxgl.Marker()
                                .setLngLat([lng, lat])
                                .setPopup(popup)
                                .addTo(map);
                                
                            markers.push(marker);
                            
                            if (!bounds) {
                                bounds = new mapboxgl.LngLatBounds([lng, lat], [lng, lat]);
                            } else {
                                bounds.extend([lng, lat]);
                            }
                        }
                    });
                } else {
                    console.error("Data is not an array:", data);
                }

                if (bounds) {
                    map.fitBounds(bounds, {
                        padding: 50,
                        maxZoom: 15
                    });
                }
            })
            .catch(error => console.error('Error fetching locations:', error));
        }

        map.on('load', function() {
            loadLocations();
            // Refresh every 30 seconds
            setInterval(loadLocations, 30000);
        });
    });
</script>
@endpush
