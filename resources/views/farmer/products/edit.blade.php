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
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary flex-1 py-3">Update Product</button>
                <a href="{{ route('farmer.products') }}" class="btn-secondary px-6 py-3">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
