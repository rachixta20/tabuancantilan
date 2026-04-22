@extends('layouts.app')
@section('title', 'Submit a Report — TABUAN')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-8">
    <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary-600 mb-6">
        ← Go Back
    </a>

    <div class="card p-6 sm:p-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center text-red-600 text-lg">!</div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Submit a Report</h1>
                <p class="text-sm text-gray-500">Help keep TABUAN safe for everyone</p>
            </div>
        </div>

        @if($reportedUser || $reportedProduct || $order)
            <div class="bg-gray-50 rounded-xl p-4 mb-6 text-sm space-y-1">
                @if($reportedUser)
                    <p class="text-gray-600">Reporting user: <span class="font-semibold text-gray-800">{{ $reportedUser->name }}</span></p>
                @endif
                @if($reportedProduct)
                    <p class="text-gray-600">Regarding product: <span class="font-semibold text-gray-800">{{ $reportedProduct->name }}</span></p>
                @endif
                @if($order)
                    <p class="text-gray-600">Regarding order: <span class="font-semibold font-mono text-gray-800">{{ $order->order_number }}</span></p>
                @endif
            </div>
        @endif

        @if(session('success'))
            <div class="mb-5 bg-primary-50 border border-primary-200 text-primary-800 px-4 py-3 rounded-xl text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-5 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('reports.store') }}" method="POST" class="space-y-5">
            @csrf

            @if($reportedUser)
                <input type="hidden" name="reported_user_id" value="{{ $reportedUser->id }}">
            @endif
            @if($reportedProduct)
                <input type="hidden" name="reported_product_id" value="{{ $reportedProduct->id }}">
            @endif
            @if($order)
                <input type="hidden" name="order_id" value="{{ $order->id }}">
            @endif

            <div>
                <label class="label">What are you reporting?</label>
                <select name="type" class="input" required>
                    <option value="">Select a reason...</option>
                    @foreach(\App\Enums\ReportType::cases() as $type)
                        <option value="{{ $type->value }}" {{ old('type') === $type->value ? 'selected' : '' }}>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="label">Description <span class="text-gray-400 font-normal">(minimum 10 characters)</span></label>
                <textarea name="description" rows="5" required minlength="10" maxlength="2000"
                          class="input resize-none"
                          placeholder="Please describe the issue in detail. Include dates, order numbers, or screenshots if relevant.">{{ old('description') }}</textarea>
                <p class="text-xs text-gray-400 mt-1">Your report is confidential and will be reviewed by our admin team.</p>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-700">
                <p class="font-semibold mb-1">Important</p>
                <p>False or malicious reports may result in action against your account. Only report genuine violations of TABUAN's community guidelines.</p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary flex-1 py-2.5">Submit Report</button>
                <a href="{{ url()->previous() }}" class="btn-secondary py-2.5 px-5">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
