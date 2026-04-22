@extends('layouts.dashboard')
@section('title', 'Categories — Admin')
@section('page-title', 'Manage Categories')

@section('sidebar-nav')
    @include('admin._sidebar')
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Add Category --}}
    <div class="card p-5">
        <h3 class="font-semibold text-gray-800 mb-4">Add Category</h3>
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="label">Name</label>
                <input type="text" name="name" required class="input" placeholder="e.g. Vegetables">
            </div>
            <div>
                <label class="label">Icon (emoji)</label>
                <input type="text" name="icon" class="input" placeholder="🥬">
            </div>
            <div>
                <label class="label">Description</label>
                <textarea name="description" rows="2" class="input resize-none" placeholder="Optional description"></textarea>
            </div>
            <button type="submit" class="btn-primary w-full py-2.5">Add Category</button>
        </form>
    </div>

    {{-- Categories List --}}
    <div class="lg:col-span-2 card overflow-hidden">
        <div class="p-5 border-b border-gray-50">
            <h3 class="font-semibold text-gray-800">All Categories</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($categories as $cat)
                <div class="px-5 py-4 flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center text-xl">
                        {{ $cat->icon ?? '📦' }}
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">{{ $cat->name }}</p>
                        <p class="text-xs text-gray-400">{{ $cat->products_count }} products</p>
                    </div>
                    <span class="text-xs text-gray-400 font-mono">{{ $cat->slug }}</span>
                </div>
            @empty
                <div class="p-12 text-center text-gray-400 text-sm">No categories yet</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
