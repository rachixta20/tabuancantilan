@extends('layouts.dashboard')
@section('title', 'Farmer Dashboard')
@section('page-title', 'Dashboard')

@section('sidebar-nav')
    <a href="{{ route('farmer.dashboard') }}" class="sidebar-link {{ request()->routeIs('farmer.dashboard') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Dashboard
    </a>
    <a href="{{ route('farmer.products') }}" class="sidebar-link {{ request()->routeIs('farmer.products*') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
        My Products
    </a>
    <a href="{{ route('farmer.orders') }}" class="sidebar-link {{ request()->routeIs('farmer.orders') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        Orders
        @php $pendingCount = auth()->user()->ordersAsSeller()->where('status', 'pending')->count(); @endphp
        @if($pendingCount > 0)
            <span class="ml-auto badge bg-amber-100 text-amber-700">{{ $pendingCount }}</span>
        @endif
    </a>
    <a href="{{ route('messages.index') }}" class="sidebar-link {{ request()->routeIs('messages*') ? 'active' : '' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
        Messages
    </a>
@endsection

@section('content')
    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
        @php
            $statItems = [
                ['label'=>'Total Products', 'value'=>$stats['products'],                                   'icon'=>'📦','color'=>'bg-blue-50 text-blue-600'],
                ['label'=>'Total Orders',   'value'=>$stats['orders'],                                     'icon'=>'🧾','color'=>'bg-purple-50 text-purple-600'],
                ['label'=>'Your Earnings',  'value'=>'₱'.number_format($stats['earnings'],2),              'icon'=>'💵','color'=>'bg-primary-50 text-primary-600'],
                ['label'=>'Pending Orders', 'value'=>$stats['pending'],                                    'icon'=>'⏳','color'=>'bg-amber-50 text-amber-600'],
            ];
        @endphp
        @foreach($statItems as $s)
            <div class="stat-card">
                <div class="w-12 h-12 {{ $s['color'] }} rounded-xl flex items-center justify-center text-xl flex-shrink-0">{{ $s['icon'] }}</div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $s['value'] }}</p>
                    <p class="text-sm text-gray-500">{{ $s['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    @if($stats['commission_paid'] > 0)
    <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 mb-6 text-sm text-amber-800">
        <span class="text-lg">ℹ️</span>
        <span>Platform commission paid: <strong>₱{{ number_format($stats['commission_paid'], 2) }}</strong> ({{ config('marketplace.commission_rate') }}% of your subtotal on delivered orders). Your earnings shown above are already after commission.</span>
    </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Recent Orders --}}
        <div class="xl:col-span-2 card">
            <div class="flex items-center justify-between p-5 border-b border-gray-50">
                <h3 class="font-semibold text-gray-800">Recent Orders</h3>
                <a href="{{ route('farmer.orders') }}" class="text-sm text-primary-600 hover:underline">View all</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentOrders as $order)
                    <div class="px-5 py-4 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $order->buyer->avatar_url }}" class="w-9 h-9 rounded-lg object-cover flex-shrink-0" alt="">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $order->order_number }}</p>
                                <p class="text-xs text-gray-400">{{ $order->buyer->name }} · {{ $order->items->count() }} item(s)</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-gray-900">₱{{ number_format($order->total, 2) }}</p>
                            <span class="badge bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-700 capitalize">{{ $order->status }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-gray-400 text-sm">No orders yet</div>
                @endforelse
            </div>
        </div>

        {{-- Top Products --}}
        <div class="card">
            <div class="flex items-center justify-between p-5 border-b border-gray-50">
                <h3 class="font-semibold text-gray-800">Top Products</h3>
                <a href="{{ route('farmer.products') }}" class="text-sm text-primary-600 hover:underline">Manage</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($topProducts as $product)
                    <div class="px-5 py-3.5 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                            <img src="{{ $product->image_url }}" class="w-full h-full object-cover"
                                 onerror="this.src='https://placehold.co/40x40/f0fdf4/16a34a?text={{ substr($product->name,0,1) }}'">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $product->name }}</p>
                            <p class="text-xs text-gray-400">{{ $product->total_sold }} sold · ₱{{ number_format($product->price,2) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-gray-400 text-sm">No products yet</div>
                @endforelse
            </div>
            <div class="p-4 border-t border-gray-50">
                <a href="{{ route('farmer.products.create') }}" class="btn-primary w-full text-sm py-2.5">+ Add Product</a>
            </div>
        </div>
    </div>

    {{-- Store Profile --}}
    <div class="card p-5 mt-6" x-data="{ open: false }">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-gray-800">🏪 Store Profile</h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    @php $u = auth()->user(); @endphp
                    @if($u->barangay)
                        {{ collect([$u->street, $u->purok ? 'Purok '.$u->purok : null, 'Brgy. '.$u->barangay, 'Cantilan, Surigao del Sur'])->filter()->implode(', ') }}
                    @else
                        <span class="text-amber-600">No store address set yet</span>
                    @endif
                </p>
            </div>
            <button @click="open = !open" class="btn-outline text-sm py-2 px-4">
                <span x-text="open ? 'Close' : 'Edit Profile'"></span>
            </button>
        </div>

        <div x-show="open" x-transition class="mt-5 border-t border-gray-100 pt-5">
            <form action="{{ route('farmer.profile.update') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="label">Farm / Business Name</label>
                        <input type="text" name="farm_name" value="{{ $u->farm_name }}" class="input text-sm" placeholder="e.g. Cruz Family Farm">
                    </div>
                    <div>
                        <label class="label">Phone Number</label>
                        <input type="text" name="phone" value="{{ $u->phone }}" class="input text-sm" placeholder="+63 912 345 6789">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Bio / Store Description</label>
                        <textarea name="bio" rows="2" class="input text-sm resize-none" placeholder="Tell buyers about your farm...">{{ $u->bio }}</textarea>
                    </div>
                </div>

                <div class="border border-gray-100 rounded-xl p-4 bg-gray-50 space-y-3">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <p class="text-sm font-semibold text-gray-700">📍 Store Address</p>
                        <button type="button" id="dash-gps-btn" onclick="fillAddressFromGPS('dash')"
                                class="flex items-center gap-1.5 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-lg hover:bg-blue-100 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span id="dash-gps-label">Use My GPS</span>
                        </button>
                    </div>
                    <p id="dash-gps-msg" class="text-xs hidden"></p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="label text-xs">Barangay</label>
                            <select name="barangay" id="dash-barangay" class="input text-sm">
                                <option value="">Select Barangay</option>
                                @foreach(['Amoslog','Balibadon','Barcelona','Bitaugan','Bokinggan','Borbonan','Bucas Grande','Burgos','Calagdaan','Camam-onan','Casas','Danao','Doyos','Embarcadero','Flores','Gamut','Gigos','Hayanggabon','Hinapoyan','Kabungkasan','Kalubihan','Kinabigtasan','Kinagbaan','Kiyab','Lahi','Libuak','Libuton','Lipata','Lobo','Lobogon','Lucagam','Magsaysay','Malobago','Manambia','Mandus','Mangga','Motibot','Nato','Paco','Pangi','Pantukan','Parang','Patong','Payasan','Pempem','Picos','Pili','Poblacion','Poniente','Quezon','Rawis','Rizal','Romarate','San Isidro','Sangpit','Santo Niño','Tagongon','Tibgao','Tigo','Tuod','Union','Wakat'] as $brgy)
                                    <option value="{{ $brgy }}" {{ $u->barangay === $brgy ? 'selected' : '' }}>{{ $brgy }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="label text-xs">Purok</label>
                            <input type="text" name="purok" id="dash-purok" value="{{ $u->purok }}" class="input text-sm" placeholder="e.g. 2 or Sampaguita">
                        </div>
                        <div>
                            <label class="label text-xs">Street / Sitio</label>
                            <input type="text" name="street" id="dash-street" value="{{ $u->street }}" class="input text-sm" placeholder="e.g. 123 Rizal St.">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn-primary py-2 px-6 text-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
const CANTILAN_BARANGAYS = ['Amoslog','Balibadon','Barcelona','Bitaugan','Bokinggan','Borbonan','Bucas Grande','Burgos','Calagdaan','Camam-onan','Casas','Danao','Doyos','Embarcadero','Flores','Gamut','Gigos','Hayanggabon','Hinapoyan','Kabungkasan','Kalubihan','Kinabigtasan','Kinagbaan','Kiyab','Lahi','Libuak','Libuton','Lipata','Lobo','Lobogon','Lucagam','Magsaysay','Malobago','Manambia','Mandus','Mangga','Motibot','Nato','Paco','Pangi','Pantukan','Parang','Patong','Payasan','Pempem','Picos','Pili','Poblacion','Poniente','Quezon','Rawis','Rizal','Romarate','San Isidro','Sangpit','Santo Niño','Tagongon','Tibgao','Tigo','Tuod','Union','Wakat'];

async function fillAddressFromGPS(prefix) {
    const btn   = document.getElementById(`${prefix}-gps-btn`);
    const label = document.getElementById(`${prefix}-gps-label`);
    const msg   = document.getElementById(`${prefix}-gps-msg`);

    label.textContent = 'Detecting…';
    btn.disabled = true;
    if (msg) { msg.className = 'text-xs text-blue-600 mt-1'; msg.textContent = '📡 Getting your GPS location…'; }

    try {
        const pos = await new Promise((resolve, reject) =>
            navigator.geolocation.getCurrentPosition(resolve, reject, { timeout: 12000 })
        );
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;

        // Auto-save coordinates and update map pin silently
        fetch('{{ route('farmer.location.update') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ latitude: lat.toFixed(7), longitude: lng.toFixed(7) })
        });

        if (msg) msg.textContent = '🔍 Looking up address…';

        const res  = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&addressdetails=1`, {
            headers: { 'Accept-Language': 'en' }
        });
        const data = await res.json();
        const addr = data.address || {};

        // Street
        const streetVal = [addr.house_number, addr.road || addr.pedestrian || addr.path].filter(Boolean).join(' ');
        const streetEl  = document.getElementById(`${prefix}-street`);
        if (streetEl && streetVal) streetEl.value = streetVal;

        // Purok
        const purokVal = addr.neighbourhood || addr.hamlet || addr.quarter || '';
        const purokEl  = document.getElementById(`${prefix}-purok`);
        if (purokEl && purokVal && !/barangay|brgy/i.test(purokVal)) purokEl.value = purokVal;

        // Barangay matching
        const candidates = [
            addr.suburb, addr.village, addr.neighbourhood,
            addr.quarter, addr.city_district, addr.county
        ].filter(Boolean);

        const brgyEl = document.getElementById(`${prefix}-barangay`);
        let matched = false;
        if (brgyEl) {
            for (const candidate of candidates) {
                const clean = candidate.replace(/barangay|brgy\.?\s*/gi, '').trim();
                const found = CANTILAN_BARANGAYS.find(b =>
                    b.toLowerCase() === clean.toLowerCase() ||
                    clean.toLowerCase().includes(b.toLowerCase()) ||
                    b.toLowerCase().includes(clean.toLowerCase())
                );
                if (found) { brgyEl.value = found; matched = true; break; }
            }
        }

        if (msg) {
            if (matched) {
                msg.className = 'text-xs text-green-600 mt-1 font-medium';
                msg.textContent = '✓ Address detected! Map pin also updated. Please verify fields below.';
            } else {
                msg.className = 'text-xs text-amber-600 mt-1';
                msg.textContent = '⚠ Map pin updated with your GPS. Barangay not auto-detected — please select manually.';
            }
        }

    } catch (err) {
        if (msg) {
            msg.className = 'text-xs text-red-600 mt-1';
            msg.textContent = err.code === 1
                ? '⛔ Location access denied. Please allow GPS in your browser settings.'
                : '⚠ Could not detect location. Please fill in manually.';
        }
    }

    label.textContent = 'Use My GPS';
    btn.disabled = false;
}
</script>
@endpush
