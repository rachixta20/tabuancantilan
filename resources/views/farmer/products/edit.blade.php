@extends('layouts.dashboard')
@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('sidebar-nav')
    @include('farmer._sidebar')
@endsection

@section('content')
<div class="max-w-2xl">
    <div class="card p-6">
        <form action="{{ route('farmer.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PUT')

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm space-y-1">
                    @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="sm:col-span-2">
                    <label class="label">Product Name</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="input">
                </div>
                <div>
                    <label class="label">Category</label>
                    <select name="category_id" required class="input">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Location</label>
                    <input type="text" name="location" value="{{ old('location', $product->location) }}" class="input">
                </div>
                <div>
                    <label class="label">Price (₱)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">₱</span>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required class="input pl-7">
                    </div>
                </div>
                <div>
                    <label class="label">Original Price (₱)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">₱</span>
                        <input type="number" name="original_price" value="{{ old('original_price', $product->original_price) }}" step="0.01" min="0" class="input pl-7">
                    </div>
                </div>
                <div>
                    <label class="label">Unit</label>
                    <select name="unit" class="input">
                        @foreach(['kg','g','pc','bundle','liter','dozen','sack'] as $u)
                            <option value="{{ $u }}" {{ old('unit', $product->unit) === $u ? 'selected' : '' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Stock</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required class="input">
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Description</label>
                    <textarea name="description" rows="4" required class="input resize-none">{{ old('description', $product->description) }}</textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="label">Product Photo</label>
                    @if($product->image)
                        <div class="mb-3">
                            <img src="{{ $product->image_url }}" class="w-32 h-32 object-cover rounded-xl border border-gray-200">
                            <p class="text-xs text-gray-400 mt-1">Current photo</p>
                        </div>
                    @endif
                    <input type="file" name="image" accept="image/*" class="input py-2">
                    <p class="text-xs text-gray-400 mt-1">Leave empty to keep current photo</p>
                </div>
                <div class="sm:col-span-2">
                    <label class="flex items-center gap-3 cursor-pointer p-4 bg-primary-50 rounded-xl">
                        <input type="checkbox" name="is_organic" value="1" {{ old('is_organic', $product->is_organic) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-primary-600 w-5 h-5">
                        <div>
                            <p class="font-medium text-gray-800">🌿 Certified Organic</p>
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
                                <input type="date" name="harvest_date"
                                       value="{{ old('harvest_date', $product->harvest_date?->format('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}"
                                       class="input @error('harvest_date') border-red-400 @enderror">
                                @error('harvest_date')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="label">Shelf Life (days)</label>
                                <select name="shelf_life_days" class="input @error('shelf_life_days') border-red-400 @enderror">
                                    <option value="">— Select shelf life —</option>
                                    @foreach([1=>'1 day',2=>'2 days',3=>'3 days',5=>'5 days',7=>'1 week',14=>'2 weeks',21=>'3 weeks',30=>'1 month',60=>'2 months',90=>'3 months',180=>'6 months',365=>'1 year'] as $days => $label)
                                        <option value="{{ $days }}" {{ old('shelf_life_days', $product->shelf_life_days) == $days ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('shelf_life_days')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        @if($product->freshness)
                            <div class="flex items-center gap-2 text-sm text-gray-600 bg-gray-50 rounded-lg px-3 py-2">
                                <span>{{ $product->freshness['icon'] }}</span>
                                <span>Currently showing as: <strong>{{ $product->freshness['label'] }}</strong></span>
                                @if($product->days_until_expiry !== null)
                                    <span class="text-gray-400">
                                        ({{ $product->days_until_expiry > 0 ? $product->days_until_expiry . ' days left' : 'expired' }})
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1 py-3">Update Product</button>
                <a href="{{ route('farmer.products') }}" class="btn-secondary px-6 py-3">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
