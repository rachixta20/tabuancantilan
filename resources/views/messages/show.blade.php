@extends('layouts.app')
@section('title', 'Chat — TABUAN')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 py-4 sm:py-8">
    {{-- Mobile back button --}}
    <a href="{{ route('messages.index') }}" class="md:hidden inline-flex items-center gap-1 text-sm text-gray-500 hover:text-primary-600 mb-3">
        ← All Messages
    </a>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 h-[calc(100vh-160px)] md:h-[calc(100vh-160px)]" style="min-height: 400px;">

        {{-- Conversation List --}}
        <div class="hidden md:flex flex-col card overflow-hidden">
            <div class="p-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">Messages</h3>
            </div>
            <div class="flex-1 overflow-y-auto divide-y divide-gray-50">
                @foreach($conversations as $conv)
                    @php $other = $conv->otherUser($user); @endphp
                    <a href="{{ route('messages.show', $conv) }}"
                       class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition-colors {{ $conv->id === $conversation->id ? 'bg-primary-50' : '' }}">
                        <img src="{{ $other->avatar_url }}" class="w-9 h-9 rounded-lg object-cover flex-shrink-0" alt="">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">{{ $other->name }}</p>
                            @if($conv->lastMessage)
                                <p class="text-xs text-gray-400 truncate">{{ Str::limit($conv->lastMessage->body, 30) }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Chat Window --}}
        <div class="md:col-span-2 flex flex-col card overflow-hidden">
            {{-- Chat Header --}}
            @php $other = $conversation->otherUser($user); @endphp
            <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
                <img src="{{ $other->avatar_url }}" class="w-10 h-10 rounded-xl object-cover" alt="">
                <div>
                    <p class="font-semibold text-gray-800">{{ $other->name }}</p>
                    @if($conversation->product)
                        <a href="{{ route('marketplace.show', $conversation->product->slug) }}"
                           class="text-xs text-primary-600 hover:underline">
                            📦 {{ $conversation->product->name }}
                        </a>
                    @endif
                </div>
            </div>

            {{-- Messages --}}
            <div class="flex-1 overflow-y-auto p-5 space-y-4" id="messages-container">
                @forelse($conversation->messages as $message)
                    @php $isMine = $message->sender_id === $user->id; @endphp
                    <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }} gap-2">
                        @if(!$isMine)
                            <img src="{{ $message->sender->avatar_url }}" class="w-7 h-7 rounded-lg flex-shrink-0 mt-1" alt="">
                        @endif
                        <div class="{{ $isMine ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-800' }} max-w-xs lg:max-w-md px-4 py-2.5 rounded-2xl {{ $isMine ? 'rounded-tr-sm' : 'rounded-tl-sm' }}">
                            <p class="text-sm leading-relaxed">{{ $message->body }}</p>
                            <p class="{{ $isMine ? 'text-primary-200' : 'text-gray-400' }} text-[11px] mt-1">{{ $message->created_at->format('h:i A') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-400 text-sm py-8">No messages yet. Say hello!</div>
                @endforelse
            </div>

            {{-- Input --}}
            <div class="px-4 py-3 border-t border-gray-100">
                <form action="{{ route('messages.send', $conversation) }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <input type="text" name="body" placeholder="Type a message..." required
                           class="input flex-1 py-2.5" autocomplete="off">
                    <button type="submit"
                            class="w-10 h-10 bg-primary-600 text-white rounded-xl flex items-center justify-center hover:bg-primary-700 transition-colors flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const container = document.getElementById('messages-container');
    if (container) container.scrollTop = container.scrollHeight;
</script>
@endpush
@endsection
