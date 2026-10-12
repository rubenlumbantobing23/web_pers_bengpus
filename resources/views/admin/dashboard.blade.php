@extends('layouts.admin')

@section('page-title', 'Dashboard Monitoring Personalia')

@section('admin-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Welcome Banner -->
    <div class="glass-card" style="background: linear-gradient(135deg, rgba(217, 119, 6, 0.2) 0%, rgba(19, 27, 46, 0.9) 100%); border-color: rgba(245, 158, 11, 0.3); padding: 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
            <div>
                <span style="font-size: 0.85rem; color: var(--accent-gold); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Staf Personalia Bengpuskomlekad</span>
                <h1 style="font-size: 1.8rem; color: #fff; margin: 4px 0 4px 0;">Dashboard Monitoring Personalia</h1>
                <p style="color: var(--text-sub); font-size: 0.925rem; margin: 0;">Pantau aktivitas, pengajuan, personel, dan layanan administrasi personalia secara terpadu.</p>
            </div>
        </div>
    </div>

    <!-- 4 KPI Monitoring Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
        <!-- Card 1: Total Personel -->
        <div class="glass-card" style="padding: 20px; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -10px; right: -10px; font-size: 5rem; color: rgba(255, 255, 255, 0.03);"><i class="fa-solid fa-users"></i></div>
            <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">TOTAL PERSONEL</div>
            <div style="font-size: 2.2rem; font-weight: 800; color: #fff; margin-top: 6px;">{{ $totalPersonel }}</div>
        </div>

        <!-- Card 2: Total Pengajuan -->
        <div class="glass-card" style="padding: 20px; position: relative; overflow: hidden; border-color: rgba(59, 130, 246, 0.3);">
            <div style="position: absolute; top: -10px; right: -10px; font-size: 5rem; color: rgba(59, 130, 246, 0.05);"><i class="fa-solid fa-file-signature"></i></div>
            <div style="font-size: 0.8rem; font-weight: 700; color: #60a5fa; text-transform: uppercase;">TOTAL PENGAJUAN</div>
            <div style="font-size: 2.2rem; font-weight: 800; color: #93c5fd; margin-top: 6px;">{{ $totalPengajuan }}</div>
        </div>

        <!-- Card 3: Menunggu Tindakan -->
        <div class="glass-card" style="padding: 20px; position: relative; overflow: hidden; border-color: rgba(245, 158, 11, 0.4); background: rgba(245, 158, 11, 0.08);">
            <div style="position: absolute; top: -10px; right: -10px; font-size: 5rem; color: rgba(245, 158, 11, 0.05);"><i class="fa-solid fa-hourglass-half"></i></div>
            <div style="font-size: 0.8rem; font-weight: 700; color: #fbbf24; text-transform: uppercase;">SEDANG DIPROSES</div>
            <div style="font-size: 2.2rem; font-weight: 800; color: #fde047; margin-top: 6px;">{{ $totalPending }}</div>
        </div>

    </div>

    <!-- Monitoring Layanan -->
    <div>
        <h3 style="font-size: 1.15rem; color: #fff; display: flex; align-items: center; gap: 10px; margin-bottom: 16px;">
            <i class="fa-solid fa-layer-group" style="color: var(--primary);"></i> Monitoring Layanan
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px;">
            
            <!-- Pengajuan Cuti -->
            <div class="glass-card" style="padding: 20px; border-top: 3px solid var(--primary);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h4 style="color: #fff; font-size: 1.1rem; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-umbrella-beach" style="color: var(--primary);"></i> Pengajuan Cuti
                    </h4>
                    <a href="{{ route('admin.leave.index') }}" style="font-size: 0.8rem; color: var(--primary); font-weight: 600;">Lihat Pengajuan &rarr;</a>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div style="background: rgba(255,255,255,0.03); padding: 12px; border-radius: 8px;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Total</div>
                        <div style="font-size: 1.4rem; color: #fff; font-weight: 700;">{{ $leaveStats['total'] }}</div>
                    </div>
                    <div style="background: rgba(245,158,11,0.05); padding: 12px; border-radius: 8px; border-left: 2px solid #fbbf24;">
                        <div style="font-size: 0.75rem; color: #fbbf24; text-transform: uppercase;">Menunggu</div>
                        <div style="font-size: 1.4rem; color: #fde047; font-weight: 700;">{{ $leaveStats['pending'] }}</div>
                    </div>
                    <div style="background: rgba(16,185,129,0.05); padding: 12px; border-radius: 8px; border-left: 2px solid #34d399;">
                        <div style="font-size: 0.75rem; color: #34d399; text-transform: uppercase;">Disetujui</div>
                        <div style="font-size: 1.4rem; color: #6ee7b7; font-weight: 700;">{{ $leaveStats['approved'] }}</div>
                    </div>
                    <div style="background: rgba(239,68,68,0.05); padding: 12px; border-radius: 8px; border-left: 2px solid #f87171;">
                        <div style="font-size: 0.75rem; color: #f87171; text-transform: uppercase;">Ditolak</div>
                        <div style="font-size: 1.4rem; color: #fca5a5; font-weight: 700;">{{ $leaveStats['rejected'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Pengajuan Nikah -->
            <div class="glass-card" style="padding: 20px; border-top: 3px solid var(--accent-gold);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h4 style="color: #fff; font-size: 1.1rem; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="fa-solid fa-heart" style="color: var(--accent-gold);"></i> Pengajuan Nikah
                    </h4>
                    <a href="{{ route('admin.admin.pengajuan_nikah.index') }}" style="font-size: 0.8rem; color: var(--accent-gold); font-weight: 600;">Lihat Pengajuan &rarr;</a>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div style="background: rgba(255,255,255,0.03); padding: 12px; border-radius: 8px;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Total</div>
                        <div style="font-size: 1.4rem; color: #fff; font-weight: 700;">{{ $marriageStats['total'] }}</div>
                    </div>
                    <div style="background: rgba(245,158,11,0.05); padding: 12px; border-radius: 8px; border-left: 2px solid #fbbf24;">
                        <div style="font-size: 0.75rem; color: #fbbf24; text-transform: uppercase;">Menunggu</div>
                        <div style="font-size: 1.4rem; color: #fde047; font-weight: 700;">{{ $marriageStats['pending'] }}</div>
                    </div>
                    <div style="background: rgba(16,185,129,0.05); padding: 12px; border-radius: 8px; border-left: 2px solid #34d399;">
                        <div style="font-size: 0.75rem; color: #34d399; text-transform: uppercase;">Disetujui</div>
                        <div style="font-size: 1.4rem; color: #6ee7b7; font-weight: 700;">{{ $marriageStats['approved'] }}</div>
                    </div>
                    <div style="background: rgba(239,68,68,0.05); padding: 12px; border-radius: 8px; border-left: 2px solid #f87171;">
                        <div style="font-size: 0.75rem; color: #f87171; text-transform: uppercase;">Ditolak</div>
                        <div style="font-size: 1.4rem; color: #fca5a5; font-weight: 700;">{{ $marriageStats['rejected'] }}</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Charts Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px;">
        
        <div class="glass-card" style="padding: 20px;">
            <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-chart-pie" style="color: #60a5fa;"></i> Komposisi Personel
            </h3>
            <div style="height: 250px; position: relative; width: 100%;">
                <canvas id="personelChart"></canvas>
            </div>
        </div>

        <div class="glass-card" style="padding: 20px;">
            <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-chart-bar" style="color: #34d399;"></i> Statistik Layanan
            </h3>
            <div style="height: 250px; position: relative; width: 100%;">
                <canvas id="layananChart"></canvas>
            </div>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Chart.defaults.color = 'rgba(255, 255, 255, 0.7)';
        Chart.defaults.font.family = "'Inter', sans-serif";

        // Chart Personel (Doughnut)
        const ctxPersonel = document.getElementById('personelChart').getContext('2d');
        new Chart(ctxPersonel, {
            type: 'doughnut',
            data: {
                labels: ['Militer', 'PNS'],
                datasets: [{
                    data: [{{ $personelStats['militer'] }}, {{ $personelStats['pns'] }}],
                    backgroundColor: ['rgba(59, 130, 246, 0.8)', 'rgba(16, 185, 129, 0.8)'],
                    borderColor: ['rgba(59, 130, 246, 1)', 'rgba(16, 185, 129, 1)'],
                    borderWidth: 1,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Chart Layanan (Bar)
        const ctxLayanan = document.getElementById('layananChart').getContext('2d');
        new Chart(ctxLayanan, {
            type: 'bar',
            data: {
                labels: ['Cuti', 'Nikah'],
                datasets: [
                    {
                        label: 'Disetujui',
                        data: [{{ $leaveStats['approved'] }}, {{ $marriageStats['approved'] }}],
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    },
                    {
                        label: 'Menunggu',
                        data: [{{ $leaveStats['pending'] }}, {{ $marriageStats['pending'] }}],
                        backgroundColor: 'rgba(245, 158, 11, 0.8)',
                    },
                    {
                        label: 'Ditolak',
                        data: [{{ $leaveStats['rejected'] }}, {{ $marriageStats['rejected'] }}],
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true }
                },
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    });
</script>

</div>
@endsection
