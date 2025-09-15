@extends('layouts.app')

@section('title', 'Detail Log Aktivitas')
@section('subtitle', 'Informasi lengkap aktivitas pengguna')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Log Aktivitas</h1>
                <p class="text-gray-600 mt-1">Informasi lengkap aktivitas #{{ $activityLog->id }}</p>
            </div>
            <a href="{{ route('admin.activity-logs') }}" 
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Activity Details -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Informasi Aktivitas</h3>
        </div>

        <div class="p-6 space-y-6">
            <!-- User Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Informasi User</h4>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-sm font-semibold text-white">
                                    {{ substr($activityLog->user->name ?? 'N/A', 0, 1) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $activityLog->user->name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">{{ $activityLog->user->role->display_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Email</p>
                            <p class="text-sm text-gray-900">{{ $activityLog->user->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Detail Aktivitas</h4>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-500">Aksi</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $activityLog->action_badge_color }}">
                                {{ $activityLog->action_display_name }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Deskripsi</p>
                            @if($activityLog->action === 'failed_login' && $activityLog->metadata)
                                @if(isset($activityLog->metadata['user_exists']) && !$activityLog->metadata['user_exists'])
                                    <div class="text-sm text-gray-900">
                                        <span class="text-red-600 font-medium">User tidak dikenal</span>
                                        <p class="text-gray-500 mt-1">Email: {{ $activityLog->metadata['email'] ?? 'N/A' }}</p>
                                    </div>
                                @elseif(isset($activityLog->metadata['user_name']))
                                    <div class="text-sm text-gray-900">
                                        <span class="text-orange-600 font-medium">Password salah</span>
                                        <p class="text-gray-500 mt-1">User: {{ $activityLog->metadata['user_name'] }} ({{ $activityLog->metadata['user_role'] ?? 'Unknown' }})</p>
                                    </div>
                                @else
                                    <p class="text-sm text-gray-900">{{ $activityLog->description ?? '-' }}</p>
                                @endif
                            @else
                                <p class="text-sm text-gray-900">{{ $activityLog->description ?? '-' }}</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Waktu</p>
                            <p class="text-sm text-gray-900">{{ $activityLog->formatted_time }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technical Information -->
            <div class="border-t border-gray-200 pt-6">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Informasi Teknis</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs text-gray-500">IP Address</p>
                        <p class="text-sm text-gray-900 font-mono">{{ $activityLog->ip_address ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">User Agent</p>
                        <p class="text-sm text-gray-900 break-all">{{ $activityLog->user_agent ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Metadata -->
            @if($activityLog->metadata)
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Metadata Tambahan</h4>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <pre class="text-sm text-gray-900 whitespace-pre-wrap">{{ json_encode($activityLog->metadata, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Related Activities -->
    @if($activityLog->user)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Aktivitas Terkait</h3>
                <p class="text-sm text-gray-600">Aktivitas terbaru dari user yang sama</p>
            </div>

            <div class="p-6">
                @php
                    $relatedLogs = \App\Models\ActivityLog::where('user_id', $activityLog->user_id)
                        ->where('id', '!=', $activityLog->id)
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp

                @if($relatedLogs->count() > 0)
                    <div class="space-y-3">
                        @foreach($relatedLogs as $relatedLog)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $relatedLog->action_badge_color }}">
                                        {{ $relatedLog->action_display_name }}
                                    </span>
                                    <span class="text-sm text-gray-900">{{ $relatedLog->description ?? '-' }}</span>
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $relatedLog->created_at->diffForHumans() }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-4">Tidak ada aktivitas terkait</p>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

