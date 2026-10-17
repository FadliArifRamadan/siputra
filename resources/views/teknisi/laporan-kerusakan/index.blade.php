@extends('layouts.teknisi')

@section('title', 'Laporan Kerusakan Saya')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl shadow-sm border border-[#f5ede1]">
        <div class="flex items-center gap-3">
            <div class="p-3 rounded-xl bg-[#f2723b]/10 text-[#f2723b]">
                <i data-lucide="wrench" class="w-6 h-6"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold font-heading text-[#191333]">Laporan Kerusakan Saya</h1>
                <p class="text-xs text-[#7a7189] mt-0.5">Daftar seluruh laporan kerusakan yang ditugaskan kepada Anda</p>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if (session('success'))
        <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/80 text-emerald-800 text-sm flex items-center gap-3 shadow-sm">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0"></i>
            <div class="font-medium">{{ session('success') }}</div>
        </div>
    @endif

    <!-- Data Table / Empty State Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-[#f5ede1] overflow-hidden">
        @if ($laporan->isEmpty())
            <div class="text-center py-12 px-4">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-[#fbf7f0] flex items-center justify-center text-[#7a7189]">
                    <i data-lucide="clipboard-check" class="w-8 h-8"></i>
                </div>
                <h3 class="text-base font-bold text-[#191333]">Belum Ada Laporan</h3>
                <p class="text-xs text-[#7a7189] mt-1 max-w-sm mx-auto">Saat ini belum ada laporan kerusakan yang ditugaskan kepada Anda.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-[#f5ede1] bg-[#fbf7f0]/60 text-[11px] font-bold uppercase tracking-wider text-[#7a7189]">
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Pelapor</th>
                            <th class="px-6 py-4">Jenis Kerusakan</th>
                            <th class="px-6 py-4">Lokasi</th>
                            <th class="px-6 py-4">Urgensi</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#f5ede1] text-xs font-medium text-[#2a2338]">
                        @foreach ($laporan as $item)
                            <tr class="hover:bg-[#fbf7f0]/40 transition-colors">
                                <td class="px-6 py-4 font-semibold text-[#191333]">#{{ $item->id }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-[#191333]">{{ $item->pelapor->nama ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-medium text-[#2a2338]">{{ $item->jenis_kerusakan }}</span>
                                </td>
                                <td class="px-6 py-4 text-[#7a7189]">
                                    {{ $item->lokasi_detail }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $urgensiClasses = [
                                            'tinggi' => 'bg-rose-50 text-rose-700 border-rose-200/80',
                                            'sedang' => 'bg-amber-50 text-amber-700 border-amber-200/80',
                                            'rendah' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                                        ];
                                        $urgBadge = $urgensiClasses[$item->tingkat_urgensi] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border capitalize {{ $urgBadge }}">
                                        {{ ucfirst($item->tingkat_urgensi) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClasses = [
                                            'pending'         => 'bg-amber-50 text-amber-700 border-amber-200/80',
                                            'diterima'        => 'bg-sky-50 text-sky-700 border-sky-200/80',
                                            'dikerjakan'      => 'bg-purple-50 text-purple-700 border-purple-200/80',
                                            'menunggu_parts'  => 'bg-orange-50 text-orange-700 border-orange-200/80',
                                            'diproses'        => 'bg-sky-50 text-sky-700 border-sky-200/80',
                                            'selesai'         => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                                        ];
                                        $statusBadge = $statusClasses[$item->status_laporan] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border capitalize {{ $statusBadge }}">
                                        {{ ucfirst(str_replace('_', ' ', $item->status_laporan)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('teknisi.laporan-kerusakan.show', $item) }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-[#191333] bg-[#f5ede1]/60 hover:bg-[#f5ede1] transition-all duration-200">
                                        <span>Detail</span>
                                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($laporan->hasPages())
                <div class="px-6 py-4 border-t border-[#f5ede1] bg-[#fbf7f0]/30">
                    {{ $laporan->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection