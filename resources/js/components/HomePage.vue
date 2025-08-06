<template>
  <div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Money Organiser Dashboard</h1>

    <!-- Time Period Selector -->
    <div class="mb-8 bg-white p-4 rounded shadow">
      <h2 class="text-xl font-semibold mb-4">Select Time Period</h2>
      <div class="flex flex-wrap gap-4">
        <button
          v-for="period in periods"
          :key="period.value"
          @click="changePeriod(period.value)"
          :class="[
            'px-4 py-2 rounded transition-colors',
            selectedPeriod === period.value
              ? 'bg-blue-600 text-white'
              : 'bg-gray-200 hover:bg-gray-300'
          ]"
        >
          {{ period.label }}
        </button>

        <!-- Custom Date Range -->
        <div v-if="selectedPeriod === 'custom'" class="w-full mt-4 flex gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Start Date</label>
            <input
              type="date"
              v-model="customStartDate"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">End Date</label>
            <input
              type="date"
              v-model="customEndDate"
              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
            >
          </div>
          <button
            @click="fetchStatistics"
            class="self-end px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
          >
            Apply
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
    </div>

    <!-- Error State -->
    <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
      {{ error }}
    </div>

    <!-- Statistics Dashboard -->
    <div v-if="!loading && statistics" class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Summary Card -->
      <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Summary</h2>
        <div class="grid grid-cols-3 gap-4">
          <div class="text-center">
            <p class="text-sm text-gray-500">Total Expenses</p>
            <p class="text-2xl font-bold text-red-600">{{ formatCurrency(statistics.summary.total_expenses) }}</p>
          </div>
          <div class="text-center">
            <p class="text-sm text-gray-500">Total Income</p>
            <p class="text-2xl font-bold text-green-600">{{ formatCurrency(statistics.summary.total_deposits) }}</p>
          </div>
          <div class="text-center">
            <p class="text-sm text-gray-500">Balance</p>
            <p class="text-2xl font-bold" :class="statistics.summary.balance >= 0 ? 'text-green-600' : 'text-red-600'">
              {{ formatCurrency(statistics.summary.balance) }}
            </p>
          </div>
        </div>
      </div>

      <!-- Period Info -->
      <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Period Information</h2>
        <p><span class="font-medium">From:</span> {{ formatDate(statistics.period.start) }}</p>
        <p><span class="font-medium">To:</span> {{ formatDate(statistics.period.end) }}</p>
        <p class="mt-2"><span class="font-medium">Period Type:</span> {{ getPeriodLabel(statistics.period.type) }}</p>
      </div>

      <!-- Expenses by Category -->
      <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Expenses by Category</h2>
        <div v-if="statistics.expenses_by_category.length === 0" class="text-gray-500">
          No expense data available for this period
        </div>
        <div v-else class="h-64">
          <pie-chart :chart-data="expensesByCategoryChartData" />
        </div>
      </div>

      <!-- Income by Category -->
      <div class="bg-white p-6 rounded shadow">
        <h2 class="text-xl font-semibold mb-4">Income by Category</h2>
        <div v-if="statistics.deposits_by_category.length === 0" class="text-gray-500">
          No income data available for this period
        </div>
        <div v-else class="h-64">
          <pie-chart :chart-data="depositsByCategoryChartData" />
        </div>
      </div>

      <!-- Expenses Over Time -->
      <div class="bg-white p-6 rounded shadow col-span-1 md:col-span-2">
        <h2 class="text-xl font-semibold mb-4">Expenses & Income Over Time</h2>
        <div v-if="statistics.expenses_by_day.length === 0 && statistics.deposits_by_day.length === 0" class="text-gray-500">
          No data available for this period
        </div>
        <div v-else class="h-80">
          <line-chart :chart-data="timeSeriesChartData" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';
import { Pie as PieChart, Line as LineChart } from 'vue-chartjs';
import { Chart as ChartJS, ArcElement, Tooltip, Legend, CategoryScale, LinearScale, PointElement, LineElement, Title } from 'chart.js';

// Register Chart.js components
ChartJS.register(ArcElement, Tooltip, Legend, CategoryScale, LinearScale, PointElement, LineElement, Title);

export default {
  components: {
    PieChart,
    LineChart
  },
  setup() {
    // State
    const loading = ref(false);
    const error = ref(null);
    const statistics = ref(null);
    const selectedPeriod = ref('month');
    const customStartDate = ref('');
    const customEndDate = ref('');

    // Available time periods
    const periods = [
      { label: 'This Week', value: 'week' },
      { label: 'This Month', value: 'month' },
      { label: 'This Quarter', value: 'quarter' },
      { label: 'This Year', value: 'year' },
      { label: 'Custom Range', value: 'custom' }
    ];

    // Chart data computed properties
    const expensesByCategoryChartData = computed(() => {
      if (!statistics.value) return { labels: [], datasets: [] };

      const data = statistics.value.expenses_by_category;
      return {
        labels: data.map(item => item.category),
        datasets: [{
          data: data.map(item => item.total),
          backgroundColor: generateColors(data.length)
        }]
      };
    });

    const depositsByCategoryChartData = computed(() => {
      if (!statistics.value) return { labels: [], datasets: [] };

      const data = statistics.value.deposits_by_category;
      return {
        labels: data.map(item => item.category),
        datasets: [{
          data: data.map(item => item.total),
          backgroundColor: generateColors(data.length, 120)
        }]
      };
    });

    const timeSeriesChartData = computed(() => {
      if (!statistics.value) return { labels: [], datasets: [] };

      // Combine and sort all dates
      const allDates = [...new Set([
        ...statistics.value.expenses_by_day.map(item => item.day),
        ...statistics.value.deposits_by_day.map(item => item.day)
      ])].sort();

      // Create lookup maps for quick access
      const expensesMap = Object.fromEntries(
        statistics.value.expenses_by_day.map(item => [item.day, item.total])
      );

      const depositsMap = Object.fromEntries(
        statistics.value.deposits_by_day.map(item => [item.day, item.total])
      );

      return {
        labels: allDates,
        datasets: [
          {
            label: 'Expenses',
            data: allDates.map(date => expensesMap[date] || 0),
            borderColor: 'rgb(239, 68, 68)',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.2
          },
          {
            label: 'Income',
            data: allDates.map(date => depositsMap[date] || 0),
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.2
          }
        ]
      };
    });

    // Methods
    const fetchStatistics = async () => {
      loading.value = true;
      error.value = null;

      try {
        let url = `/api/statistics?period=${selectedPeriod.value}`;

        if (selectedPeriod.value === 'custom' && customStartDate.value && customEndDate.value) {
          url += `&start_date=${customStartDate.value}&end_date=${customEndDate.value}`;
        }

        const response = await window.axios.get(url);
        statistics.value = response.data;
      } catch (err) {
        console.error('Error fetching statistics:', err);
        error.value = 'Failed to load statistics. Please try again.';
      } finally {
        loading.value = false;
      }
    };

    const changePeriod = (period) => {
      selectedPeriod.value = period;
      if (period !== 'custom') {
        fetchStatistics();
      }
    };

    const formatCurrency = (value) => {
      return new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: 'PLN'
      }).format(value);
    };

    const formatDate = (dateString) => {
      return new Date(dateString).toLocaleDateString('pl-PL');
    };

    const getPeriodLabel = (periodType) => {
      const period = periods.find(p => p.value === periodType);
      return period ? period.label : periodType;
    };

    const generateColors = (count, hueOffset = 0) => {
      return Array.from({ length: count }, (_, i) => {
        const hue = (i * 137.5 + hueOffset) % 360;
        return `hsl(${hue}, 70%, 60%)`;
      });
    };

    // Lifecycle hooks
    onMounted(() => {
      fetchStatistics();
    });

    return {
      loading,
      error,
      statistics,
      selectedPeriod,
      customStartDate,
      customEndDate,
      periods,
      expensesByCategoryChartData,
      depositsByCategoryChartData,
      timeSeriesChartData,
      changePeriod,
      fetchStatistics,
      formatCurrency,
      formatDate,
      getPeriodLabel
    };
  }
};
</script>
