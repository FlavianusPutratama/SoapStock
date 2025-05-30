<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-3 sm:mb-0">
                {{ __('Laporan Revenue') }} - {{ $reportTitle ?? 'Data Tidak Tersedia' }}
            </h2>
            <a href="{{ route('penjual.revenue.export', request()->query()) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-md shadow-sm">
                Export ke Excel
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Form Filter --}}
            <div class="mb-6 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4">
                <form method="GET" action="{{ route('penjual.revenue.report') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label for="period" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Periode</label>
                        <select name="period" id="period" class="mt-1 block w-full rounded-md shadow-sm">
                            <option value="daily" {{ ($period ?? 'monthly') == 'daily' ? 'selected' : '' }}>Harian</option>
                            <option value="weekly" {{ ($period ?? 'monthly') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                            <option value="monthly" {{ ($period ?? 'monthly') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                            <option value="yearly" {{ ($period ?? 'monthly') == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                            <option value="custom_range" {{ ($period ?? 'monthly') == 'custom_range' ? 'selected' : '' }}>Rentang Kustom</option>
                        </select>
                    </div>
                    <div id="start_date_div" class="{{ ($period ?? 'monthly') == 'custom_range' || ($period ?? 'monthly') == 'daily' || ($period ?? 'monthly') == 'weekly' || ($period ?? 'monthly') == 'monthly' || ($period ?? 'monthly') == 'yearly' ? '' : 'hidden' }}">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300" id="start_date_label">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate ?? '' }}" class="mt-1 block w-full rounded-md shadow-sm">
                    </div>
                    <div id="end_date_div" class="{{ ($period ?? 'monthly') == 'custom_range' ? '' : 'hidden' }}">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Akhir</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate ?? '' }}" class="mt-1 block w-full rounded-md shadow-sm">
                    </div>
                    <div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-md shadow-sm">
                            Tampilkan Laporan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Ringkasan Total --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 p-4 shadow rounded-lg">
                    <h4 class="text-gray-500 dark:text-gray-400 font-medium">Total Penjualan (Omzet)</h4>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($totalAmountSold ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 shadow rounded-lg">
                    <h4 class="text-gray-500 dark:text-gray-400 font-medium">Total Modal (HPP)</h4>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($totalCostOfGoods ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 shadow rounded-lg">
                    <h4 class="text-gray-500 dark:text-gray-400 font-medium">Total Keuntungan (Revenue)</h4>
                    <p class="text-2xl font-semibold text-green-600 dark:text-green-400">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-4 shadow rounded-lg">
                    <h4 class="text-gray-500 dark:text-gray-400 font-medium">Jumlah Transaksi</h4>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $numberOfSales ?? 0 }}</p>
                </div>
            </div>

            {{-- Tempat untuk Grafik --}}
            <div class="mb-6 bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Grafik Revenue Harian</h3>
                <div style="height: 300px;"> {{-- Beri tinggi agar canvas terlihat --}}
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            {{-- Tabel Detail Penjualan --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-3">Detail Transaksi (Status: Sudah Dibayar)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium">ID Order</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium">Tanggal</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium">Pelanggan</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium">Omzet</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium">Modal</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium">Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($sales as $sale)
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">SO-{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">{{ Carbon\Carbon::parse($sale->sale_date)->isoFormat('D MMM YYYY, HH:mm') }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm">{{ $sale->customer_name ?? '-' }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-right">Rp {{ number_format($sale->total_amount_sold, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-right">Rp {{ number_format($sale->total_cost_of_goods, 0, ',', '.') }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-right font-semibold text-green-600 dark:text-green-400">Rp {{ number_format($sale->total_revenue, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-2 text-center text-sm">Tidak ada data penjualan lunas untuk periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- Jika ada paginasi untuk tabel ini --}}
                    {{-- <div class="mt-4">{{ $sales->appends(request()->query())->links() }}</div> --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk Chart.js dan filter tanggal --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Logika untuk menampilkan/menyembunyikan input tanggal berdasarkan periode
            const periodSelect = document.getElementById('period');
            const startDateDiv = document.getElementById('start_date_div');
            const endDateDiv = document.getElementById('end_date_div');
            const startDateLabel = document.getElementById('start_date_label');

            function toggleDateInputs() {
                const period = periodSelect.value;
                startDateDiv.classList.add('hidden');
                endDateDiv.classList.add('hidden');

                if (period === 'daily') {
                    startDateLabel.textContent = 'Pilih Tanggal';
                    startDateDiv.classList.remove('hidden');
                } else if (period === 'weekly') {
                    startDateLabel.textContent = 'Pilih Tanggal (Minggu akan dihitung dari tgl ini)';
                    startDateDiv.classList.remove('hidden');
                } else if (period === 'monthly') {
                    startDateLabel.textContent = 'Pilih Tanggal (Bulan akan dihitung dari tgl ini)';
                    startDateDiv.classList.remove('hidden');
                } else if (period === 'yearly') {
                    startDateLabel.textContent = 'Pilih Tanggal (Tahun akan dihitung dari tgl ini)';
                    startDateDiv.classList.remove('hidden');
                } else if (period === 'custom_range') {
                    startDateLabel.textContent = 'Tanggal Mulai';
                    startDateDiv.classList.remove('hidden');
                    endDateDiv.classList.remove('hidden');
                }
            }
            periodSelect.addEventListener('change', toggleDateInputs);
            toggleDateInputs(); // Panggil saat load untuk set state awal

            // Data untuk Chart.js (dikirim dari controller)
            const chartData = @json($chartData ?? []); // Pastikan $chartData adalah array asosiatif atau objek
            const labels = Object.keys(chartData);
            const dataValues = Object.values(chartData);

            const ctx = document.getElementById('revenueChart').getContext('2d');
            if (window.myRevenueChart instanceof Chart) {
                window.myRevenueChart.destroy(); // Hancurkan chart lama jika ada (untuk update filter)
            }
            window.myRevenueChart = new Chart(ctx, {
                type: 'line', // atau 'bar'
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Revenue Harian',
                        data: dataValues,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1,
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value, index, values) {
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>