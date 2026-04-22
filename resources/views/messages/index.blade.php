@extends('layouts.app')
@section('title', 'Messages — TABUAN')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
    <h1 class="section-title mb-6">Messages</h1>

    @if($conversations->isEmpty())
        <div class="card p-16 text-center">
            <div class="text-5xl mb-4">💬</div>
            <h3 class="text-lg font-semibold text-gray-700 mb-2">No conversations yet</h3>
            <p class="text-gray-400 text-sm mb-4">Start a conversation by messaging a farmer on their product page</p>
            <a href="{{ route('marketplace') }}" class="btn-primary">Browse Products</a>
        </div>
    @else
        <div class="card divide-y divide-gray-50">
            @foreach($conversations as $conv)
                @php $other = $conv->otherUser($user); $unread = $conv->unreadCount($user); @endphp
                <a href="{{ route('messages.show', $conv) }}"
                   class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition-colors {{ $unread > 0 ? 'bg-primary-50/30' : '' }}">
                    <div class="relative">
                        <img src="{{ $other->avatar_url }}" class="w-12 h-12 rounded-xl object-cover" alt="">
                        @if($unread > 0)
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-primary-600 text-white text-[10px] rounded-full flex items-center justify-center font-bold">{{ $unread }}</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-semibold text-gray-800 {{ $unread > 0 ? 'text-primary-700' : '' }}">{{ $other->name }}</p>
                            @if($conv->lastMessage)
                                <span class="text-xs text-gray-400 flex-shrink-0">{{ $conv->lastMessage->created_at->diffForHumans(null, true) }}</span>
                            @endif
                        </div>
                        @if($conv->product)
                            <p class="text-xs text-primary-600 mb-0.5">re: {{ $conv->product->name }}</p>
                        @endif
                        @if($conv->lastMessage)
                            <p class="text-sm text-gray-500 truncate {{ $unread > 0 ? 'font-medium text-gray-700' : '' }}">{{ $conv->lastMessage->body }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
