@extends('layouts.app')
@section('title', 'Seller Map — TABUAN')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    #seller-map { height: 100%; min-height: 500px; z-index: 1; }
    .leaflet-popup-content-wrapper { border-radius: 16px; box-shadow: 0 8px 30px rgba(0,0,0,.15); padding: 0; overflow: hidden; }
    .leaflet-popup-content { margin: 0; width: 240px !important; }
    .leaflet-popup-tip-container { display: none; }

    .seller-marker {
        width: 42px; height: 42px;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        border: 3px solid white;
        box-shadow: 0 3px 10px rgba(0,0,0,.25);
        display: flex; align-items: center; justify-content: center;
    }
    .seller-marker-inner { transform: rotate(45deg); font-size: 18px; line-height: 1; }

    .live-pulse {
        position: absolute; top: -4px; right: -4px;
        width: 14px; height: 14px;
        background: #ef4444; border-radius: 50%; border: 2px solid white;
        animation: livepulse 1.5s infinite;
    }
    @keyframes livepulse {
        0%,100% { transform: scale(1); opacity: 1; }
        50%      { transform: scale(1.4); opacity: .7; }
    }

    .user-dot {
        width: 18px; height: 18px;
        background: #2563eb; border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 0 0 4px rgba(37,99,235,.25);
        animation: userpulse 2s infinite;
    }
    @keyframes userpulse {
        0%,100% { box-shadow: 0 0 0 4px rgba(37,99,235,.25); }
        50%      { box-shadow: 0 0 0 8px rgba(37,99,235,.1); }
    }

    .transport-btn {
        flex: 1; display:flex; flex-direction:column; align-items:center; gap:3px;
        padding: 7px 4px; border-radius: 10px; border: 2px solid #e5e7eb;
        cursor: pointer; background: white; transition: all .15s; font-size: 11px;
        font-family: Inter,sans-serif; font-weight: 600; color: #374151;
    }
    .transport-btn:hover  { border-color: #93c5fd; background: #eff6ff; }
    .transport-btn.active { border-color: #3b82f6; background: #eff6ff; color: #1d4ed8; }

    #route-info-bar {
        position: absolute; bottom: 12px; left: 50%; transform: translateX(-50%);
        z-index: 500; background: white; border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0,0,0,.18);
        padding: 10px 16px; display:flex; align-items:center; gap:12px;
        font-family: Inter,sans-serif; min-width: 260px;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Seller Map</h1>
            <p class="text-sm text-gray-500 mt-0.5">Find local farmers · get directions by car, motorcycle, or walking</p>
        </div>
        <div class="flex items-center gap-4 text-sm">
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-primary-500 inline-block"></span> Seller</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-500 inline-block animate-pulse"></span> Live</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span> You</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Sidebar --}}
        <div class="lg:col-span-1 space-y-3 max-h-[600px] overflow-y-auto pr-1 scrollbar-hide">

            @php $liveCount = $sellers->where('is_live', true)->count(); @endphp
            @if($liveCount > 0)
                <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-2.5 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse inline-block"></span>
                    <span class="text-sm font-semibold text-red-700">{{ $liveCount }} seller{{ $liveCount > 1 ? 's' : '' }} live now!</span>
                </div>
            @endif

            @forelse($sellers as $seller)
                <div class="card p-4 border border-transparent hover:border-primary-300 transition-all cursor-pointer seller-card"
                     data-id="{{ $seller->id }}"
                     data-lat="{{ $seller->latitude }}"
                     data-lng="{{ $seller->longitude }}"
                     data-name="{{ $seller->name }}">
                    <div class="flex items-start gap-3">
                        <div class="relative shrink-0">
                            <img src="{{ $seller->avatar_url }}" class="w-12 h-12 rounded-xl object-cover" alt="">
                            @if($seller->is_live)
                                <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full border-2 border-white animate-pulse"></span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <p class="font-semibold text-gray-900 text-sm truncate">{{ $seller->name }}</p>
                                @if($seller->is_live)
                                    <span class="badge bg-red-100 text-red-600 text-[10px] px-1.5 py-0.5">● LIVE</span>
                                @endif
                            </div>
                            @if($seller->farm_name)
                                <p class="text-xs text-primary-600 font-medium truncate">{{ $seller->farm_name }}</p>
                            @endif
                            @if($seller->barangay)
                                <p class="text-xs text-gray-400 truncate">Brgy. {{ $seller->barangay }}</p>
                            @endif
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs text-gray-400">{{ $seller->products_count }} product{{ $seller->products_count != 1 ? 's' : '' }}</span>
                            </div>
                            {{-- Direction buttons --}}
                            <div class="flex gap-1.5 mt-2">
                                <button onclick="event.stopPropagation(); getDirections({{ $seller->latitude }}, {{ $seller->longitude }}, 'car', this)"
                                        class="transport-btn" title="Drive by car">
                                    <span style="font-size:15px;">🚗</span> Car
                                </button>
                                <button onclick="event.stopPropagation(); getDirections({{ $seller->latitude }}, {{ $seller->longitude }}, 'motorcycle', this)"
                                        class="transport-btn" title="Ride motorcycle">
                                    <span style="font-size:15px;">🏍️</span> Moto
                                </button>
                                <button onclick="event.stopPropagation(); getDirections({{ $seller->latitude }}, {{ $seller->longitude }}, 'walk', this)"
                                        class="transport-btn" title="Walk">
                                    <span style="font-size:15px;">🚶</span> Walk
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card p-8 text-center text-gray-400">
                    <p class="text-3xl mb-2">🌾</p>
                    <p class="text-sm">No sellers on the map yet.</p>
                </div>
            @endforelse
        </div>

        {{-- Map --}}
        <div class="lg:col-span-2 card overflow-hidden relative" style="height: 600px;">
            <div id="seller-map"></div>
            {{-- Route info bar (hidden by default) --}}
            <div id="route-info-bar" style="display:none;">
                <span id="route-mode-icon" style="font-size:22px;"></span>
                <div style="flex:1;">
                    <p style="font-size:13px;font-weight:700;color:#111827;margin:0;" id="route-dest-name"></p>
                    <p style="font-size:12px;color:#6b7280;margin:0;" id="route-summary"></p>
                </div>
                <button onclick="clearRoute()" style="background:#f3f4f6;border:none;border-radius:8px;padding:5px 10px;font-size:12px;cursor:pointer;font-weight:600;color:#374151;">✕ Clear</button>
            </div>
        </div>

    </div>

    {{-- Set My Location (farmers only) --}}
    @auth
        @if(auth()->user()->isFarmer() && auth()->user()->isApproved())
            <div class="card p-5 mt-6" x-data="{ open: false }">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-800">📍 Update Your Location on Map</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Click on the map below to set your exact farm location.</p>
                    </div>
                    <button @click="open = !open" class="btn-outline text-sm py-2 px-4">
                        <span x-text="open ? 'Close' : 'Set My Location'"></span>
                    </button>
                </div>
                <div x-show="open" x-transition class="mt-4">
                    <div id="location-picker-map" style="height: 350px; border-radius: 12px; overflow: hidden;"></div>
                    <p class="text-xs text-gray-400 mt-2">Click anywhere on the map to place your pin, then click Save.</p>
                    <div class="flex items-center gap-3 mt-3">
                        <input type="text" id="picked-coords" readonly placeholder="Click map to pick location"
                               class="input text-sm flex-1 bg-gray-50">
                        <button id="save-location-btn" class="btn-primary py-2 px-5 text-sm" disabled>Save Location</button>
                    </div>
                </div>
            </div>

            {{-- Live Toggle --}}
            <div class="card p-5 mt-4" x-data="{
                isLive: {{ auth()->user()->is_live ? 'true' : 'false' }},
                liveTitle: '{{ auth()->user()->live_title ?? '' }}',
                saving: false,
                async toggle() {
                    this.saving = true;
                    const res = await fetch('{{ route('farmer.live.toggle') }}', {
                        method: 'POST',
                        headers: {'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                        body: JSON.stringify({ is_live: !this.isLive, live_title: this.liveTitle })
                    });
                    const data = await res.json();
                    this.isLive = data.is_live;
                    this.saving = false;
                }
            }">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h3 class="font-semibold text-gray-800">📡 Go Live on Map</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Show buyers you're active — your pin will pulse red on the map.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="text" x-model="liveTitle" placeholder="e.g. Fresh harvest today!"
                               class="input text-sm w-56 py-2">
                        <button @click="toggle" :disabled="saving"
                                :class="isLive ? 'bg-red-600 hover:bg-red-700 text-white' : 'btn-primary'"
                                class="px-5 py-2 text-sm font-semibold rounded-xl transition-all">
                            <span x-text="saving ? '...' : (isLive ? '⏹ End Live' : '▶ Go Live')"></span>
                        </button>
                    </div>
                </div>
                <div x-show="isLive" x-transition class="mt-3 flex items-center gap-2 text-sm text-red-600 font-medium">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse inline-block"></span>
                    You are live on the map right now!
                </div>
            </div>
        @endif
    @endauth

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
// ── Map setup ────────────────────────────────────────────────
const sellers = @json($sellers);
const map = L.map('seller-map').setView([9.3139, 125.9897], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 19
}).addTo(map);

const markerMap = {};

// ── Seller markers ───────────────────────────────────────────
sellers.forEach(seller => {
    if (!seller.latitude || !seller.longitude) return;

    const isLive = seller.is_live;
    const color  = isLive ? '#ef4444' : '#16a34a';

    const icon = L.divIcon({
        className: '',
        html: `<div style="position:relative;width:42px;height:42px;">
                 <div class="seller-marker" style="background:${color};">
                   <div class="seller-marker-inner">🌾</div>
                 </div>
                 ${isLive ? '<div class="live-pulse"></div>' : ''}
               </div>`,
        iconSize: [42, 42], iconAnchor: [21, 42], popupAnchor: [0, -44],
    });

    const avatarUrl = seller.avatar
        ? `/storage/${seller.avatar}`
        : `https://ui-avatars.com/api/?name=${encodeURIComponent(seller.name)}&background=16a34a&color=fff&size=64`;

    const addrParts = [
        seller.street,
        seller.purok ? 'Purok ' + seller.purok : null,
        seller.barangay ? 'Brgy. ' + seller.barangay : null,
        'Cantilan, Surigao del Sur'
    ].filter(Boolean);
    const fullAddress = addrParts.join(', ');

    const productImgs = (seller.products || []).slice(0, 3).map(p =>
        `<img src="${p.image ? '/storage/'+p.image : 'https://placehold.co/60x60/e5e7eb/9ca3af?text=+'}"
              style="width:58px;height:58px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;" title="${p.name}">`
    ).join('');

    const productCount = seller.products_count ?? 0;
    const lat = seller.latitude, lng = seller.longitude;

    const popupHtml = `
        <div style="font-family:Inter,sans-serif;">
            <div style="background:${color};padding:12px 14px;display:flex;align-items:center;gap:10px;">
                <img src="${avatarUrl}" style="width:40px;height:40px;border-radius:10px;object-fit:cover;border:2px solid rgba(255,255,255,.4);">
                <div>
                    <p style="color:#fff;font-weight:700;font-size:14px;margin:0;">${seller.name}</p>
                    ${seller.farm_name ? `<p style="color:rgba(255,255,255,.8);font-size:11px;margin:2px 0 0;">${seller.farm_name}</p>` : ''}
                    ${isLive ? `<span style="background:rgba(255,255,255,.2);color:#fff;font-size:10px;padding:1px 6px;border-radius:20px;font-weight:600;">● LIVE</span>` : ''}
                </div>
            </div>
            <div style="padding:11px 14px;">
                ${isLive && seller.live_title ? `<p style="color:#ef4444;font-size:12px;font-weight:600;margin:0 0 5px;">"${seller.live_title}"</p>` : ''}
                <p style="color:#6b7280;font-size:12px;margin:0 0 4px;">📍 ${fullAddress}</p>
                <p style="color:#6b7280;font-size:12px;margin:0 0 8px;">🛒 ${productCount} product${productCount!=1?'s':''}</p>
                ${productImgs ? `<div style="display:flex;gap:4px;margin-bottom:10px;">${productImgs}</div>` : ''}

                <p style="font-size:11px;font-weight:700;color:#374151;margin:0 0 6px;text-transform:uppercase;letter-spacing:.05em;">Get Directions</p>
                <div style="display:flex;gap:6px;margin-bottom:10px;">
                    <button onclick="getDirections(${lat},${lng},'car')" class="transport-btn" style="font-size:11px;">🚗<br>Car</button>
                    <button onclick="getDirections(${lat},${lng},'motorcycle')" class="transport-btn" style="font-size:11px;">🏍️<br>Moto</button>
                    <button onclick="getDirections(${lat},${lng},'walk')" class="transport-btn" style="font-size:11px;">🚶<br>Walk</button>
                </div>

                <a href="/marketplace?seller=${seller.id}"
                   style="display:block;background:#16a34a;color:#fff;text-align:center;padding:7px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">
                   View Products →
                </a>
            </div>
        </div>`;

    const marker = L.marker([lat, lng], { icon })
        .addTo(map)
        .bindPopup(popupHtml, { maxWidth: 260 });

    markerMap[seller.id] = marker;
});

// Sidebar card → fly + open popup
document.querySelectorAll('.seller-card').forEach(card => {
    card.addEventListener('click', () => {
        const id  = card.dataset.id;
        const lat = parseFloat(card.dataset.lat);
        const lng = parseFloat(card.dataset.lng);
        if (!isNaN(lat) && !isNaN(lng)) {
            map.flyTo([lat, lng], 16, { duration: 1 });
            setTimeout(() => { if (markerMap[id]) markerMap[id].openPopup(); }, 900);
        }
    });
});

// ── Routing (OSRM) ───────────────────────────────────────────
const modes = {
    car:        { profile: 'driving', color: '#3b82f6', icon: '🚗',  label: 'Car',        speed: 40  },
    motorcycle: { profile: 'driving', color: '#f97316', icon: '🏍️', label: 'Motorcycle', speed: 50  },
    walk:       { profile: 'foot',    color: '#16a34a', icon: '🚶',  label: 'Walking',    speed: 5   },
};

let routeLayer   = null;
let userMarker   = null;
let userLat      = null;
let userLng      = null;
let activeBtn    = null;

function clearRoute() {
    if (routeLayer) { map.removeLayer(routeLayer); routeLayer = null; }
    document.getElementById('route-info-bar').style.display = 'none';
    document.querySelectorAll('.transport-btn.active').forEach(b => b.classList.remove('active'));
    activeBtn = null;
}

async function getUserLocation() {
    return new Promise((resolve, reject) => {
        if (!navigator.geolocation) { reject('Geolocation not supported'); return; }
        navigator.geolocation.getCurrentPosition(
            pos => { userLat = pos.coords.latitude; userLng = pos.coords.longitude; resolve(); },
            ()  => reject('Location permission denied')
        );
    });
}

function showUserMarker() {
    const icon = L.divIcon({ className: '', html: '<div class="user-dot"></div>', iconSize: [18,18], iconAnchor: [9,9] });
    if (userMarker) map.removeLayer(userMarker);
    userMarker = L.marker([userLat, userLng], { icon }).addTo(map).bindPopup('📍 You are here');
}

async function getDirections(destLat, destLng, mode, btn) {
    clearRoute();

    // Set active button state
    if (btn) { btn.classList.add('active'); activeBtn = btn; }

    const infoBar = document.getElementById('route-info-bar');
    infoBar.style.display = 'flex';
    document.getElementById('route-mode-icon').textContent = modes[mode].icon;
    document.getElementById('route-dest-name').textContent = 'Getting your location…';
    document.getElementById('route-summary').textContent   = 'Please allow location access';

    try {
        if (userLat === null) await getUserLocation();
        showUserMarker();

        const m = modes[mode];
        const url = `https://router.project-osrm.org/route/v1/${m.profile}/`
                  + `${userLng},${userLat};${destLng},${destLat}`
                  + `?overview=full&geometries=geojson`;

        const res  = await fetch(url);
        const data = await res.json();

        if (!data.routes || !data.routes.length) {
            document.getElementById('route-dest-name').textContent = 'No route found';
            document.getElementById('route-summary').textContent   = 'Try a different transport mode';
            return;
        }

        const route    = data.routes[0];
        const distKm   = (route.distance / 1000).toFixed(1);
        const mins     = Math.round(route.duration / 60);
        const timeText = mins >= 60
            ? `${Math.floor(mins/60)}h ${mins%60}m`
            : `${mins} min`;

        // Draw route polyline
        const coords = route.geometry.coordinates.map(c => [c[1], c[0]]);
        routeLayer = L.polyline(coords, {
            color: m.color, weight: 5, opacity: .85,
            dashArray: mode === 'walk' ? '8, 6' : null,
        }).addTo(map);

        // Fit map to show full route
        map.fitBounds(routeLayer.getBounds(), { padding: [40, 40] });

        // Update info bar
        document.getElementById('route-dest-name').textContent = `${m.label} · ${distKm} km`;
        document.getElementById('route-summary').textContent   = `Estimated travel time: ${timeText}`;

    } catch (err) {
        // OSRM failed — fall back to Google Maps
        const gmMode = mode === 'walk' ? 'walking' : 'driving';
        const gmUrl  = `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${destLat},${destLng}&travelmode=${gmMode}`;
        document.getElementById('route-dest-name').textContent = 'Routing unavailable';
        document.getElementById('route-summary').innerHTML     =
            `<a href="${gmUrl}" target="_blank" style="color:#3b82f6;font-weight:600;text-decoration:underline;">Open in Google Maps →</a>`;
    }
}

// Make accessible from popup onclick
window.getDirections = getDirections;
window.clearRoute    = clearRoute;

// ── Location picker map (farmers only) ───────────────────────
const pickerEl = document.getElementById('location-picker-map');
if (pickerEl) {
    const pickerMap = L.map('location-picker-map').setView([9.3139, 125.9897], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(pickerMap);

    let pickerMarker = null;
    const saveBtn    = document.getElementById('save-location-btn');
    const coordInput = document.getElementById('picked-coords');
    let pickedLat = null, pickedLng = null;

    pickerMap.on('click', e => {
        pickedLat = e.latlng.lat.toFixed(7);
        pickedLng = e.latlng.lng.toFixed(7);
        coordInput.value = `${pickedLat}, ${pickedLng}`;
        saveBtn.disabled = false;
        if (pickerMarker) pickerMarker.remove();
        pickerMarker = L.marker([pickedLat, pickedLng]).addTo(pickerMap);
    });

    saveBtn.addEventListener('click', async () => {
        saveBtn.textContent = 'Saving…';
        saveBtn.disabled = true;
        await fetch('{{ route('farmer.location.update') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ latitude: pickedLat, longitude: pickedLng })
        });
        saveBtn.textContent = '✓ Saved!';
        setTimeout(() => { saveBtn.textContent = 'Save Location'; }, 2000);
    });
}
</script>
@endpush
