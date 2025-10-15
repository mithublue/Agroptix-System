<template>
  <AppLayout title="Live Monitoring">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Live Monitoring
      </h2>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <!-- Real-time Metrics Cards -->
              <div class="bg-blue-50 p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Active Batches</h3>
                <div class="text-3xl font-bold text-blue-600">24</div>
                <p class="text-sm text-gray-500 mt-2">Currently in production</p>
              </div>

              <div class="bg-green-50 p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Quality Tests Today</h3>
                <div class="text-3xl font-bold text-green-600">156</div>
                <p class="text-sm text-gray-500 mt-2">+12% from yesterday</p>
              </div>

              <div class="bg-yellow-50 p-6 rounded-lg shadow">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Issues Detected</h3>
                <div class="text-3xl font-bold text-yellow-600">3</div>
                <p class="text-sm text-gray-500 mt-2">Requires attention</p>
              </div>
            </div>

            <!-- Real-time Activity Feed -->
            <div class="mt-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
              <div class="bg-gray-50 p-4 rounded-lg">
                <div class="space-y-4">
                  <div v-for="(activity, index) in activities" :key="index" class="flex items-start">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                      <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                      </svg>
                    </div>
                    <div class="ml-4">
                      <p class="text-sm font-medium text-gray-900">{{ activity.title }}</p>
                      <p class="text-sm text-gray-500">{{ activity.description }}</p>
                      <p class="text-xs text-gray-400 mt-1">{{ activity.time }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue';

export default {
  components: {
    AppLayout,
  },

  data() {
    return {
      activities: [
        {
          title: 'Batch #B-2023-00124',
          description: 'Moved to Packaging stage',
          time: '2 minutes ago'
        },
        {
          title: 'Quality Test Completed',
          description: 'Batch #B-2023-00123 passed all quality checks',
          time: '15 minutes ago'
        },
        {
          title: 'New Batch Created',
          description: 'New batch #B-2023-00125 created by John Doe',
          time: '1 hour ago'
        },
        {
          title: 'System Alert',
          description: 'Irregular temperature detected in Storage Unit #3',
          time: '2 hours ago'
        },
      ]
    };
  },

  mounted() {
    // In a real application, you would set up WebSocket or polling here
    // to get real-time updates
  },
};
</script>
