@extends('layouts.app')

@section('title', 'Monitoring Lokasi')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

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
                            <span class="text-muted mt-1 fw-semibold fs-7">Memantau lokasi karyawan berdasarkan titik kordinat terakhir</span>
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
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('map').setView([-6.2088, 106.8456], 10);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        var markersGroup = L.layerGroup().addTo(map);

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
            .then(response => response.json())
            .then(data => {
                markersGroup.clearLayers();
                
                var bounds = [];
                var count = 0;
                
                if (Array.isArray(data)) {
                    data.forEach(function(user) {
                        if (user.latitude && user.longitude) {
                            var latlng = [user.latitude, user.longitude];
                            var popupContent = `
                                <div style="text-align: center;">
                                    <strong>${user.nama}</strong><br>
                                    <span style="color: #666;">@${user.username}</span><br>
                                    <small>${user.latitude}, ${user.longitude}</small>
                                </div>
                            `;
                            L.marker(latlng).bindPopup(popupContent).addTo(markersGroup);
                            bounds.push(latlng);
                            count++;
                        }
                    });
                }

                if (bounds.length > 0) {
                    // Only fit bounds if there's multiple locations or we want to zoom in on them
                    // map.fitBounds(bounds); 
                }
            })
            .catch(error => console.error('Error fetching locations:', error));
        }

        loadLocations();
        // Refresh every 30 seconds
        setInterval(loadLocations, 30000);
    });
</script>
@endpush
