@extends('layouts.dashboard')
@section('title', 'Add Product')
@section('page-title', 'Add New Product')

@section('sidebar-nav')
    @include('farmer._sidebar')
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="card p-6">
        <form action="{{ route('farmer.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm space-y-1">
                    @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="label">Product Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="input" placeholder="e.g. Fresh Tomatoes">
                </div>

                <div>
                    <label class="label">Category <span class="text-red-500">*</span></label>
                    <select name="category_id" required class="input">
                        <option value="">Select category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="label">Location</label>
                    <input type="text" name="location" value="{{ old('location', auth()->user()->location) }}" class="input" placeholder="Farm location">
                </div>

                <div>
                    <label class="label">Price (₱) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">₱</span>
                        <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" required class="input pl-7" placeholder="0.00">
                    </div>
                </div>

                <div>
                    <label class="label">Original Price (₱) <span class="text-xs text-gray-400">(optional, for discount)</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">₱</span>
                        <input type="number" name="original_price" value="{{ old('original_price') }}" step="0.01" min="0" class="input pl-7" placeholder="0.00">
                    </div>
                </div>

                <div>
                    <label class="label">Unit <span class="text-red-500">*</span></label>
                    <select name="unit" class="input">
                        <option value="kg" {{ old('unit') == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                        <option value="g" {{ old('unit') == 'g' ? 'selected' : '' }}>Gram (g)</option>
                        <option value="pc" {{ old('unit') == 'pc' ? 'selected' : '' }}>Piece (pc)</option>
                        <option value="bundle" {{ old('unit') == 'bundle' ? 'selected' : '' }}>Bundle</option>
                        <option value="liter" {{ old('unit') == 'liter' ? 'selected' : '' }}>Liter</option>
                        <option value="dozen" {{ old('unit') == 'dozen' ? 'selected' : '' }}>Dozen</option>
                        <option value="sack" {{ old('unit') == 'sack' ? 'selected' : '' }}>Sack</option>
                    </select>
                </div>

                <div>
                    <label class="label">Stock Quantity <span class="text-red-500">*</span></label>
                    <input type="number" name="stock" value="{{ old('stock') }}" min="0" required class="input" placeholder="Available quantity">
                </div>

                <div class="sm:col-span-2">
                    <label class="label">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required class="input resize-none" placeholder="Describe your product — freshness, how it's grown, harvest date, etc.">{{ old('description') }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="label">Product Photo</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-primary-400 transition-colors" x-data="{ preview: null }">
                        <input type="file" name="image" accept="image/*" class="hidden" id="img-upload"
                               @change="preview = URL.createObjectURL($event.target.files[0])">
                        <template x-if="preview">
                            <img :src="preview" class="w-full max-h-48 object-contain mx-auto rounded-lg mb-3">
                        </template>
                        <template x-if="!preview">
                            <div>
                                <div class="text-3xl mb-2">📷</div>
                                <p class="text-sm text-gray-500">Click to upload product image</p>
                                <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 2MB</p>
                            </div>
                        </template>
                        <label for="img-upload" class="btn-secondary mt-3 cursor-pointer text-sm py-2 px-4 inline-block">
                            <span x-text="preview ? 'Change Photo' : 'Choose Photo'"></span>
                        </label>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label class="flex items-center gap-3 cursor-pointer p-4 bg-primary-50 rounded-xl hover:bg-primary-100 transition-colors">
                        <input type="checkbox" name="is_organic" value="1" {{ old('is_organic') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-primary-600 w-5 h-5">
                        <div>
                            <p class="font-medium text-gray-800">🌿 Certified Organic</p>
                            <p class="text-xs text-gray-500">Mark this if your product is organically grown without chemicals</p>
                        </div>
                    </label>
                </div>

                {{-- Harvest & Freshness --}}
                <div class="sm:col-span-2">
                    <div class="border border-gray-200 rounded-xl p-4 space-y-4">
                        <div class="flex items-center gap-2">
                            <span class="text-lg">🌾</span>
                            <div>
                                <p class="font-medium text-gray-800 text-sm">Harvest & Freshness</p>
                                <p class="text-xs text-gray-400">Help buyers know how fresh your product is</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="label">Date of Harvest</label>
                                <input type="date" name="harvest_date" value="{{ old('harvest_date') }}"
                                       max="{{ date('Y-m-d') }}"
                                       class="input @error('harvest_date') border-red-400 @enderror">
                                <p class="text-xs text-gray-400 mt-1">When was this batch harvested?</p>
                                @error('harvest_date')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="label">Shelf Life (days)</label>
                                <select name="shelf_life_days" class="input @error('shelf_life_days') border-red-400 @enderror">
                                    <option value="">— Select shelf life —</option>
                                    @foreach([1=>'1 day',2=>'2 days',3=>'3 days',5=>'5 days',7=>'1 week',14=>'2 weeks',21=>'3 weeks',30=>'1 month',60=>'2 months',90=>'3 months',180=>'6 months',365=>'1 year'] as $days => $label)
                                        <option value="{{ $days }}" {{ old('shelf_life_days') == $days ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-400 mt-1">How long does it stay fresh?</p>
                                @error('shelf_life_days')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1 py-3">List Product</button>
                <a href="{{ route('farmer.products') }}" class="btn-secondary px-6 py-3">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
