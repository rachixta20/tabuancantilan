@extends('layouts.app')
@section('title', 'Create Account — TABUAN')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-2xl">
        <div class="card p-8">
            <div class="text-center mb-8">
                <div class="w-14 h-14 bg-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17 8C8 10 5.9 16.17 3.82 21c6.07-3.15 13.26-1.67 16.44-6C22 11 21 3 21 3c-1 2-4 4-4 5z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Join TABUAN</h1>
                <p class="text-gray-500 text-sm mt-1">Create your free account</p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm mb-6 space-y-1">
                    @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" class="space-y-5"
                  x-data="{ role: '{{ old('role', 'buyer') }}' }">
                @csrf

                {{-- Role Selection --}}
                <div>
                    <label class="label">I want to</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="buyer" x-model="role" class="sr-only">
                            <div :class="role === 'buyer' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 text-gray-500'"
                                 class="border-2 rounded-xl p-4 text-center transition-all hover:border-primary-300">
                                <div class="text-2xl mb-1">🛒</div>
                                <div class="text-sm font-semibold">Buy Products</div>
                                <div class="text-xs mt-0.5 text-gray-400">Instant access</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="role" value="farmer" x-model="role" class="sr-only">
                            <div :class="role === 'farmer' ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 text-gray-500'"
                                 class="border-2 rounded-xl p-4 text-center transition-all hover:border-primary-300">
                                <div class="text-2xl mb-1">🌾</div>
                                <div class="text-sm font-semibold">Sell Products</div>
                                <div class="text-xs mt-0.5 text-gray-400">Requires verification</div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Seller notice --}}
                <div x-show="role === 'farmer'" x-transition
                     class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-sm text-amber-800">
                    🔍 <strong>Seller Verification Required</strong> — You must submit a valid government ID, a selfie holding your ID, and farm documents. Our team will review your application within 1–2 business days.
                </div>

                {{-- Basic Info --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="label">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="input" placeholder="Juan dela Cruz">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="input" placeholder="you@example.com">
                    </div>
                    <div>
                        <label class="label">Phone Number <span class="text-red-500">*</span></label>
                        <input type="text" name="phone" value="{{ old('phone') }}" required
                               class="input" placeholder="+63 912 345 6789">
                    </div>
                    <div>
                        <label class="label">Location <span class="text-red-500">*</span></label>
                        <input type="text" name="location" value="{{ old('location', 'Cantilan, Surigao del Sur') }}" required
                               class="input" placeholder="Cantilan, Surigao del Sur">
                    </div>
                    <div>
                        <label class="label">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required class="input" placeholder="Min. 8 characters">
                    </div>
                    <div>
                        <label class="label">Confirm Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" required class="input" placeholder="Repeat password">
                    </div>
                </div>

                {{-- Farmer-only fields --}}
                <div x-show="role === 'farmer'" x-transition class="space-y-4 border-t border-gray-100 pt-5">
                    <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                        🌾 Seller Verification Documents
                        <span class="badge bg-red-100 text-red-600 text-xs">Required</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="label">Farm / Business Name <span class="text-red-500">*</span></label>
                            <input type="text" name="farm_name" value="{{ old('farm_name') }}"
                                   class="input" placeholder="e.g. Cruz Family Farm">
                        </div>
                        <div>
                            <label class="label">Type of Valid ID <span class="text-red-500">*</span></label>
                            <select name="id_type" class="input">
                                <option value="">Select ID type</option>
                                @foreach(["PhilSys / National ID", "Passport", "Driver's License", "SSS ID", "GSIS ID", "Voter's ID", "Postal ID", "Barangay ID", "PWD ID", "Senior Citizen ID"] as $idType)
                                    <option value="{{ $idType }}" {{ old('id_type') === $idType ? 'selected' : '' }}>{{ $idType }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Store Address --}}
                    <div class="border border-gray-100 rounded-xl p-4 space-y-3 bg-gray-50">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <p class="text-sm font-semibold text-gray-700">📍 Store / Farm Address</p>
                            <button type="button" id="reg-gps-btn" onclick="fillAddressFromGPS('reg')"
                                    class="flex items-center gap-1.5 text-xs font-semibold text-blue-700 bg-blue-50 border border-blue-200 px-3 py-1.5 rounded-lg hover:bg-blue-100 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span id="reg-gps-label">Use My GPS</span>
                            </button>
                        </div>
                        <p id="reg-gps-msg" class="text-xs hidden"></p>
                        <input type="hidden" name="latitude"  id="reg-latitude">
                        <input type="hidden" name="longitude" id="reg-longitude">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="label">Barangay <span class="text-red-500">*</span></label>
                                <select name="barangay" id="reg-barangay" class="input text-sm">
                                    <option value="">Select Barangay</option>
                                    @foreach(['Amoslog','Balibadon','Barcelona','Bitaugan','Bokinggan','Borbonan','Bucas Grande','Burgos','Calagdaan','Camam-onan','Casas','Danao','Doyos','Embarcadero','Flores','Gamut','Gigos','Hayanggabon','Hinapoyan','Kabungkasan','Kalubihan','Kinabigtasan','Kinagbaan','Kiyab','Lahi','Libuak','Libuton','Lipata','Lobo','Lobogon','Lucagam','Magsaysay','Malobago','Manambia','Mandus','Mangga','Motibot','Nato','Paco','Pangi','Pantukan','Parang','Patong','Payasan','Pempem','Picos','Pili','Poblacion','Poniente','Quezon','Rawis','Rizal','Romarate','San Isidro','Sangpit','Santo Niño','Tagongon','Tibgao','Tigo','Tuod','Union','Wakat'] as $brgy)
                                        <option value="{{ $brgy }}" {{ old('barangay') === $brgy ? 'selected' : '' }}>{{ $brgy }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Purok</label>
                                <input type="text" name="purok" id="reg-purok" value="{{ old('purok') }}"
                                       class="input text-sm" placeholder="e.g. 2, Sampaguita">
                            </div>
                            <div>
                                <label class="label">Street / Sitio</label>
                                <input type="text" name="street" id="reg-street" value="{{ old('street') }}"
                                       class="input text-sm" placeholder="e.g. 123 Rizal St.">
                            </div>
                        </div>
                    </div>

                    {{-- ID Document Upload --}}
                    <div x-data="{ preview: null }">
                        <label class="label">
                            Photo of Valid ID <span class="text-red-500">*</span>
                            <span class="text-gray-400 font-normal text-xs ml-1">(Front side, clearly visible)</span>
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-5 hover:border-primary-400 transition-colors">
                            <input type="file" name="id_document" accept="image/*" class="hidden" id="id-doc-upload"
                                   @change="preview = URL.createObjectURL($event.target.files[0])" :required="role === 'farmer'">
                            <template x-if="preview">
                                <img :src="preview" class="w-full max-h-40 object-contain rounded-lg mb-3 mx-auto">
                            </template>
                            <template x-if="!preview">
                                <div class="text-center">
                                    <div class="text-3xl mb-2">🪪</div>
                                    <p class="text-sm text-gray-500">Upload your valid government ID</p>
                                    <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 4MB</p>
                                </div>
                            </template>
                            <div class="text-center mt-3">
                                <label for="id-doc-upload" class="btn-secondary text-sm py-2 px-4 cursor-pointer">
                                    <span x-text="preview ? 'Change Photo' : 'Choose File'"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Selfie Upload --}}
                    <div x-data="{ preview: null }">
                        <label class="label">
                            Selfie Holding Your ID <span class="text-red-500">*</span>
                            <span class="text-gray-400 font-normal text-xs ml-1">(Face + ID must be clearly visible)</span>
                        </label>
                        <div class="border-2 border-dashed border-amber-300 bg-amber-50 rounded-xl p-5 hover:border-amber-400 transition-colors">
                            <input type="file" name="selfie_photo" accept="image/*" class="hidden" id="selfie-upload"
                                   @change="preview = URL.createObjectURL($event.target.files[0])" :required="role === 'farmer'">
                            <template x-if="preview">
                                <img :src="preview" class="w-full max-h-40 object-contain rounded-lg mb-3 mx-auto">
                            </template>
                            <template x-if="!preview">
                                <div class="text-center">
                                    <div class="text-3xl mb-2">🤳</div>
                                    <p class="text-sm text-amber-800 font-medium">Live selfie verification</p>
                                    <p class="text-xs text-amber-600 mt-1">Take a photo of yourself holding your ID — this proves you are the real owner of the ID.</p>
                                </div>
                            </template>
                            <div class="text-center mt-3">
                                <label for="selfie-upload" class="btn-secondary text-sm py-2 px-4 cursor-pointer">
                                    <span x-text="preview ? 'Change Photo' : 'Take / Upload Selfie'"></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Farm Document (optional) --}}
                    <div x-data="{ preview: null }">
                        <label class="label">
                            Farm Document
                            <span class="text-gray-400 font-normal text-xs ml-1">(Optional — land title, farm cert, barangay clearance, etc.)</span>
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-5 hover:border-primary-400 transition-colors">
                            <input type="file" name="farm_document" accept="image/*" class="hidden" id="farm-doc-upload"
                                   @change="preview = URL.createObjectURL($event.target.files[0])">
                            <template x-if="preview">
                                <img :src="preview" class="w-full max-h-40 object-contain rounded-lg mb-3 mx-auto">
                            </template>
                            <template x-if="!preview">
                                <div class="text-center">
                                    <div class="text-3xl mb-2">📄</div>
                                    <p class="text-sm text-gray-500">Optional farm/business document</p>
                                </div>
                            </template>
                            <div class="text-center mt-3">
                                <label for="farm-doc-upload" class="btn-secondary text-sm py-2 px-4 cursor-pointer">
                                    <span x-text="preview ? 'Change File' : 'Choose File'"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full py-3 text-base">
                    <span x-text="role === 'farmer' ? 'Submit Application' : 'Create Account'"></span>
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Already have an account?
                <a href="{{ route('login') }}" class="text-primary-600 font-semibold hover:text-primary-700">Sign in</a>
            </p>
        </div>
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
        // 1. Get GPS coordinates
        const pos = await new Promise((resolve, reject) =>
            navigator.geolocation.getCurrentPosition(resolve, reject, { timeout: 12000 })
        );
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;

        // Save to hidden inputs (register form)
        const latInput = document.getElementById(`${prefix}-latitude`);
        const lngInput = document.getElementById(`${prefix}-longitude`);
        if (latInput) latInput.value = lat.toFixed(7);
        if (lngInput) lngInput.value = lng.toFixed(7);

        if (msg) msg.textContent = '🔍 Looking up address…';

        // 2. Reverse geocode with Nominatim
        const res  = await fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&addressdetails=1`, {
            headers: { 'Accept-Language': 'en' }
        });
        const data = await res.json();
        const addr = data.address || {};

        // 3. Fill street
        const streetVal = [addr.house_number, addr.road || addr.pedestrian || addr.path].filter(Boolean).join(' ');
        const streetEl  = document.getElementById(`${prefix}-street`);
        if (streetEl && streetVal) streetEl.value = streetVal;

        // 4. Fill purok (neighbourhood / hamlet often maps to purok in PH)
        const purokVal = addr.neighbourhood || addr.hamlet || addr.quarter || '';
        const purokEl  = document.getElementById(`${prefix}-purok`);
        if (purokEl && purokVal && !/barangay|brgy/i.test(purokVal)) purokEl.value = purokVal;

        // 5. Try to match barangay from geocoded result
        const candidates = [
            addr.suburb, addr.village, addr.neighbourhood,
            addr.quarter, addr.city_district, addr.county
        ].filter(Boolean);

        const brgyEl = document.getElementById(`${prefix}-barangay`);
        if (brgyEl) {
            let matched = false;
            for (const candidate of candidates) {
                const clean = candidate.replace(/barangay|brgy\.?\s*/gi, '').trim();
                const found = CANTILAN_BARANGAYS.find(b =>
                    b.toLowerCase() === clean.toLowerCase() ||
                    clean.toLowerCase().includes(b.toLowerCase()) ||
                    b.toLowerCase().includes(clean.toLowerCase())
                );
                if (found) { brgyEl.value = found; matched = true; break; }
            }
            if (msg) {
                if (matched) {
                    msg.className = 'text-xs text-green-600 mt-1 font-medium';
                    msg.textContent = '✓ Location detected! Please verify the fields below.';
                } else {
                    msg.className = 'text-xs text-amber-600 mt-1';
                    msg.textContent = '⚠ GPS coordinates saved. Barangay not detected — please select manually.';
                }
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
