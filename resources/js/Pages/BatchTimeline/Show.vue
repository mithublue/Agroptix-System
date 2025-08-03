<template>
  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Batch Timeline: {{ batch.name || 'Untitled Batch' }}
          </h2>
          <div class="text-sm text-gray-500">
            Trace Code: {{ batch.trace_code }}
          </div>
        </div>
        <div class="flex space-x-2">
          <div class="flex items-center space-x-2">
            <div class="relative group" v-tooltip="'Export Batch History'">
              <button 
                @click="showExportDropdown = !showExportDropdown"
                class="btn btn-secondary inline-flex items-center"
                :class="{ 'bg-gray-200': showExportDropdown }"
              >
                <Icon icon="mdi-download" class="mr-1.5 h-4 w-4" />
                <span>Export</span>
                <Icon icon="mdi-chevron-down" class="ml-1 h-4 w-4" />
              </button>
              
              <!-- Export Dropdown -->
              <div 
                v-show="showExportDropdown" 
                v-click-outside="() => showExportDropdown = false"
                class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10"
              >
                <div class="py-1" role="menu" aria-orientation="vertical">
                  <a 
                    :href="exportUrl('pdf')" 
                    class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                    role="menuitem"
                    @click="showExportDropdown = false"
                  >
                    <Icon icon="mdi-file-pdf" class="mr-3 h-5 w-5 text-red-500" />
                    <span>Export as PDF</span>
                  </a>
                  <a 
                    :href="exportUrl('csv')" 
                    class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                    role="menuitem"
                    @click="showExportDropdown = false"
                  >
                    <Icon icon="mdi-file-excel" class="mr-3 h-5 w-5 text-green-600" />
                    <span>Export as CSV</span>
                  </a>
                  <a 
                    :href="exportUrl('json')" 
                    class="group flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                    role="menuitem"
                    @click="showExportDropdown = false"
                  >
                    <Icon icon="mdi-code-json" class="mr-3 h-5 w-5 text-yellow-500" />
                    <span>Export as JSON</span>
                  </a>
                </div>
              </div>
            </div>
            
            <Link 
              :href="route('batches.edit', batch.id)" 
              class="btn btn-primary"
              v-if="can.update"
            >
              Edit Batch
            </Link>
            
            <div class="relative group" v-tooltip="'Share Batch'">
              <button 
                @click="showShareOptions = !showShareOptions"
                class="btn btn-secondary inline-flex items-center"
                :class="{ 'bg-gray-200': showShareOptions }"
              >
                <Icon icon="mdi-share-variant" class="mr-1.5 h-4 w-4" />
                <span>Share</span>
              </button>
              
              <!-- Share Options Dropdown -->
              <div 
                v-show="showShareOptions" 
                v-click-outside="() => showShareOptions = false"
                class="absolute right-0 mt-2 w-64 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10"
              >
                <div class="p-3 border-b border-gray-100">
                  <h4 class="text-sm font-medium text-gray-700">Share Batch</h4>
                </div>
                
                <div class="p-3 space-y-2">
                  <!-- Share via QR Code -->
                  <button
                    @click="shareViaQRCode"
                    class="w-full flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md"
                  >
                    <Icon icon="mdi-qrcode" class="mr-3 h-5 w-5 text-blue-500" />
                    <span>Share via QR Code</span>
                  </button>
                  
                  <!-- Copy Shareable Link -->
                  <div class="mt-2">
                    <label for="shareable-link" class="block text-xs font-medium text-gray-500 mb-1">Shareable Link</label>
                    <div class="flex rounded-md shadow-sm">
                      <input
                        type="text"
                        :value="shareableLink"
                        id="shareable-link"
                        readonly
                        class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        @click="$event.target.select()"
                      >
                      <button
                        @click="copyShareableLink"
                        class="inline-flex items-center px-3 py-2 border border-l-0 border-gray-300 bg-gray-50 text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 rounded-r-md"
                        :title="copyButtonText"
                      >
                        <Icon :icon="copyButtonIcon" class="h-4 w-4" />
                      </button>
                    </div>
                  </div>
                  
                  <!-- Social Sharing Buttons -->
                  <div class="pt-2 flex space-x-2">
                    <button
                      @click="shareOnSocial('email')"
                      class="p-2 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700"
                      title="Share via Email"
                    >
                      <Icon icon="mdi-email" class="h-5 w-5" />
                    </button>
                    <button
                      @click="shareOnSocial('whatsapp')"
                      class="p-2 rounded-full bg-green-100 hover:bg-green-200 text-green-600"
                      title="Share on WhatsApp"
                    >
                      <Icon icon="mdi-whatsapp" class="h-5 w-5" />
                    </button>
                    <button
                      @click="shareOnSocial('facebook')"
                      class="p-2 rounded-full bg-blue-100 hover:bg-blue-200 text-blue-600"
                      title="Share on Facebook"
                    >
                      <Icon icon="mdi-facebook" class="h-5 w-5" />
                    </button>
                    <button
                      @click="shareOnSocial('twitter')"
                      class="p-2 rounded-full bg-blue-50 hover:bg-blue-100 text-blue-400"
                      title="Share on Twitter"
                    >
                      <Icon icon="mdi-twitter" class="h-5 w-5" />
                    </button>
                    <button
                      @click="shareOnSocial('linkedin')"
                      class="p-2 rounded-full bg-blue-100 hover:bg-blue-200 text-blue-700"
                      title="Share on LinkedIn"
                    >
                      <Icon icon="mdi-linkedin" class="h-5 w-5" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <Link 
              :href="route('batches.index')" 
              class="btn btn-secondary"
            >
              Back to Batches
            </Link>
          </div>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Batch Summary Card -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <h3 class="text-lg font-medium text-gray-900">Batch Information</h3>
                <dl class="mt-2 space-y-2">
                  <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd>
                      <span 
                        :class="[
                          'px-2 py-1 text-xs font-medium rounded-full',
                          getStatusColor(batch.status)
                        ]"
                      >
                        {{ batch.status_label || batch.status }}
                      </span>
                    </dd>
                  </div>
                  <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                    <dd class="text-sm text-gray-900">
                      {{ formatDate(batch.created_at) }}
                    </dd>
                  </div>
                </dl>
              </div>
              
              <div v-if="batch.product">
                <h3 class="text-lg font-medium text-gray-900">Product</h3>
                <dl class="mt-2 space-y-2">
                  <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="text-sm text-gray-900">
                      {{ batch.product.name }}
                    </dd>
                  </div>
                  <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">SKU</dt>
                    <dd class="text-sm text-gray-900">
                      {{ batch.product.sku }}
                    </dd>
                  </div>
                </dl>
              </div>
              
              <div v-if="batch.farm">
                <h3 class="text-lg font-medium text-gray-900">Farm</h3>
                <dl class="mt-2 space-y-2">
                  <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="text-sm text-gray-900">
                      {{ batch.farm.name }}
                    </dd>
                  </div>
                  <div class="flex justify-between">
                    <dt class="text-sm font-medium text-gray-500">Location</dt>
                    <dd class="text-sm text-gray-900">
                      {{ batch.farm.location }}
                    </dd>
                  </div>
                </dl>
              </div>
            </div>
            
            <!-- Integrity Check Badge -->
            <div class="mt-4 pt-4 border-t border-gray-200">
              <div 
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                :class="{
                  'bg-green-100 text-green-800': integrity_check?.is_valid,
                  'bg-red-100 text-red-800': !integrity_check?.is_valid
                }"
              >
                <svg 
                  class="-ml-0.5 mr-1.5 h-2 w-2" 
                  :class="{
                    'text-green-400': integrity_check?.is_valid,
                    'text-red-400': !integrity_check?.is_valid
                  }" 
                  fill="currentColor" 
                  viewBox="0 0 8 8"
                >
                  <circle cx="4" cy="4" r="3" />
                </svg>
                {{ integrity_check?.is_valid ? 'Chain of Custody Verified' : 'Chain of Custody Compromised' }}
              </div>
              <span 
                v-if="!integrity_check?.is_valid"
                class="ml-2 text-xs text-red-600"
              >
                {{ integrity_check?.error_count || 0 }} issue(s) detected
              </span>
            </div>
          </div>
        </div>
        
        <!-- Timeline -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
          <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Batch Timeline
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
              Complete history of all events for this batch
            </p>
          </div>
          
          <div class="px-4 py-5 sm:p-6">
            <div class="flow-root">
              <ul class="-mb-8">
                <li v-for="(events, date) in timeline" :key="date">
                  <div class="relative pb-8">
                    <span 
                      class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" 
                      aria-hidden="true"
                    ></span>
                    <div class="relative flex space-x-3">
                      <div>
                        <span 
                          class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white"
                          :class="getEventColor(events[0])"
                        >
                          <Icon 
                            :icon="events[0].icon" 
                            class="h-5 w-5 text-white" 
                            aria-hidden="true" 
                          />
                        </span>
                      </div>
                      <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                        <div>
                          <p class="text-sm text-gray-800 font-medium">
                            {{ date }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Events for this date -->
                  <ul class="space-y-6">
                    <li v-for="event in events" :key="event.id" class="relative pl-12 pb-6">
                      <div class="relative pb-1">
                        <div class="flex items-center space-x-3">
                          <div class="flex-shrink-0">
                            <span 
                              class="h-8 w-8 rounded-full flex items-center justify-center"
                              :class="getEventColor(event)"
                            >
                              <Icon 
                                :icon="event.icon" 
                                class="h-5 w-5 text-white" 
                                aria-hidden="true" 
                              />
                            </span>
                          </div>
                          <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between">
                              <p class="text-sm font-medium text-gray-900">
                                {{ event.title }}
                              </p>
                              <time 
                                :datetime="event.timestamp" 
                                class="text-xs text-gray-500"
                              >
                                {{ formatTime(event.timestamp) }}
                              </time>
                            </div>
                            <p class="text-sm text-gray-600 mt-1">
                              {{ event.description }}
                            </p>
                            
                            <!-- Event metadata -->
                            <div v-if="event.actor || event.data" class="mt-2">
                              <div 
                                v-if="event.actor"
                                class="text-xs text-gray-500"
                              >
                                By {{ event.actor.name }}
                              </div>
                              
                              <!-- Show data if available -->
                              <div 
                                v-if="event.data && Object.keys(event.data).length > 0"
                                class="mt-1"
                              >
                                <button 
                                  @click="toggleEventDetails(event.id)"
                                  class="text-xs text-blue-600 hover:text-blue-800"
                                >
                                  {{ showEventDetails[event.id] ? 'Hide details' : 'Show details' }}
                                </button>
                                
                                <div 
                                  v-if="showEventDetails[event.id]"
                                  class="mt-1 p-2 bg-gray-50 rounded text-xs font-mono overflow-x-auto"
                                >
                                  <pre>{{ JSON.stringify(event.data, null, 2) }}</pre>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
          </div>
        </div>
        
        <!-- Workflow Status -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
          <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Workflow Status
            </h3>
            <p class="mt-1 max-w-2xl text-sm text-gray-500">
              Current status and next steps for this batch
            </p>
          </div>
          
          <div class="px-4 py-5 sm:p-6">
            <div class="overflow-hidden">
              <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                  <div class="flex-shrink-0">
                    <span 
                      class="h-12 w-12 rounded-full flex items-center justify-center"
                      :class="getStatusColor(batch.status)"
                    >
                      <Icon 
                        icon="mdi-information" 
                        class="h-6 w-6 text-white" 
                        aria-hidden="true" 
                      />
                    </span>
                  </div>
                  <div class="ml-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                      Current Status: {{ batch.status_label || batch.status }}
                    </h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500">
                      <p>
                        {{ getStatusDescription(batch.status) }}
                      </p>
                    </div>
                  </div>
                </div>
                
                <!-- Next Steps -->
                <div class="mt-8">
                  <h4 class="text-sm font-medium text-gray-500">
                    Next Steps
                  </h4>
                  <ul class="mt-3 space-y-3">
                    <li 
                      v-for="(status, index) in workflow.next_possible_statuses" 
                      :key="index"
                      class="flex items-start"
                    >
                      <div class="flex-shrink-0">
                        <span 
                          class="h-6 w-6 rounded-full flex items-center justify-center bg-green-100 text-green-800"
                        >
                          {{ index + 1 }}
                        </span>
                      </div>
                      <p class="ml-3 text-sm text-gray-700">
                        {{ getStatusDescription(status) }}
                      </p>
                    </li>
                    
                    <li 
                      v-if="workflow.next_possible_statuses.length === 0"
                      class="text-sm text-gray-500"
                    >
                      This batch has reached its final status.
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Icon from '@/Components/Icon.vue';
import { format, parseISO } from 'date-fns';
import { usePage } from '@inertiajs/vue3';

// Get the current page URL
const page = usePage();

const props = defineProps({
  batch: {
    type: Object,
    required: true,
  },
  timeline: {
    type: Object,
    required: true,
  },
  workflow: {
    type: Object,
    required: true,
  },
  integrity_check: {
    type: Object,
    default: () => ({}),
  },
  can: {
    type: Object,
    default: () => ({}),
  },
});

// UI State
const showEventDetails = ref({});
const showExportDropdown = ref(false);
const showShareOptions = ref(false);
const copyButtonText = ref('Copy Link');
const copyButtonIcon = ref('mdi-content-copy');
const shareableLink = ref('');

// Generate export URLs
const exportUrl = (format) => {
  return route('batches.export', {
    batch: props.batch.trace_code,
    format: format
  });
};

// Share via QR Code
const shareViaQRCode = () => {
  showShareOptions.value = false;
  router.visit(route('qrcode.show', props.batch.trace_code));
};

// Copy shareable link to clipboard
const copyShareableLink = async () => {
  try {
    await navigator.clipboard.writeText(shareableLink.value);
    copyButtonText.value = 'Copied!';
    copyButtonIcon.value = 'mdi-check';
    
    // Reset button after 2 seconds
    setTimeout(() => {
      copyButtonText.value = 'Copy Link';
      copyButtonIcon.value = 'mdi-content-copy';
    }, 2000);
  } catch (err) {
    console.error('Failed to copy link:', err);
    copyButtonText.value = 'Failed!';
    copyButtonIcon.value = 'mdi-alert';
    
    // Reset button after 2 seconds
    setTimeout(() => {
      copyButtonText.value = 'Copy Link';
      copyButtonIcon.value = 'mdi-content-copy';
    }, 2000);
  }
};

// Share on social media
const shareOnSocial = (platform) => {
  const url = encodeURIComponent(shareableLink.value);
  const title = encodeURIComponent(`Batch: ${props.batch.name || 'Untitled Batch'}`);
  const text = encodeURIComponent(`Check out this batch on ${page.props.app.name}`);
  
  let shareUrl = '';
  
  switch (platform) {
    case 'email':
      shareUrl = `mailto:?subject=${title}&body=${text}%0A%0A${url}`;
      break;
    case 'whatsapp':
      shareUrl = `https://wa.me/?text=${text}%20${url}`;
      break;
    case 'facebook':
      shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
      break;
    case 'twitter':
      shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${text}`;
      break;
    case 'linkedin':
      shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
      break;
  }
  
  if (shareUrl) {
    window.open(shareUrl, '_blank', 'noopener,noreferrer');
  }
  
  showShareOptions.value = false;
};

// Initialize shareable link
onMounted(() => {
  shareableLink.value = window.location.origin + route('batches.timeline', props.batch.trace_code);
});

// Toggle event details
const toggleEventDetails = (eventId) => {
  showEventDetails.value = {
    ...showEventDetails.value,
    [eventId]: !showEventDetails.value[eventId],
  };
};

// Format date for display
const formatDate = (dateString) => {
  if (!dateString) return 'N/A';
  return format(parseISO(dateString), 'MMM d, yyyy');
};

// Format time for display
const formatTime = (dateString) => {
  if (!dateString) return '';
  return format(parseISO(dateString), 'h:mm a');
};

// Get color class for status badge
const getStatusColor = (status) => {
  const statusColors = {
    'created': 'bg-blue-100 text-blue-800',
    'harvested': 'bg-green-100 text-green-800',
    'processing': 'bg-yellow-100 text-yellow-800',
    'qc_pending': 'bg-yellow-100 text-yellow-800',
    'qc_approved': 'bg-green-100 text-green-800',
    'qc_rejected': 'bg-red-100 text-red-800',
    'packaged': 'bg-blue-100 text-blue-800',
    'shipped': 'bg-purple-100 text-purple-800',
    'delivered': 'bg-green-100 text-green-800',
    'returned': 'bg-red-100 text-red-800',
    'destroyed': 'bg-gray-100 text-gray-800',
  };
  
  return statusColors[status] || 'bg-gray-100 text-gray-800';
};

// Get color class for event icon
const getEventColor = (event) => {
  if (typeof event === 'string') {
    // If it's just the event type string
    const eventType = event;
    if (eventType.includes('approval') || eventType === 'delivery') {
      return 'bg-green-500';
    }
    if (eventType.includes('rejection') || 
        ['recall', 'damage', 'theft', 'loss'].includes(eventType)) {
      return 'bg-red-500';
    }
    if (['harvest', 'processing', 'packaging', 'shipping'].includes(eventType)) {
      return 'bg-blue-500';
    }
    if (['qc_test', 'inspection', 'test_result'].includes(eventType)) {
      return 'bg-yellow-500';
    }
    return 'bg-gray-500';
  } else if (event.color) {
    // If it's an event object with a color property
    const colorMap = {
      'success': 'bg-green-500',
      'error': 'bg-red-500',
      'warning': 'bg-yellow-500',
      'info': 'bg-blue-500',
      'primary': 'bg-indigo-500',
    };
    return colorMap[event.color] || 'bg-gray-500';
  }
  
  return 'bg-gray-500';
};

// Get description for status
const getStatusDescription = (status) => {
  const descriptions = {
    'created': 'The batch has been created but no actions have been taken yet.',
    'harvested': 'The raw materials have been harvested from the farm.',
    'processing': 'The batch is currently being processed.',
    'qc_pending': 'The batch is awaiting quality control inspection.',
    'qc_approved': 'The batch has passed quality control and is ready for the next step.',
    'qc_rejected': 'The batch did not pass quality control and requires attention.',
    'packaged': 'The batch has been packaged and is ready for shipping.',
    'shipped': 'The batch has been shipped to the customer.',
    'delivered': 'The batch has been successfully delivered to the customer.',
    'returned': 'The batch has been returned by the customer.',
    'destroyed': 'The batch has been destroyed or disposed of.',
  };
  
  return descriptions[status] || `The batch is currently in the "${status}" status.`;
};
</script>

<style scoped>
/* Animation for copy button */
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Tooltip styles */
[v-tooltip] {
  position: relative;
  cursor: pointer;
}

[v-tooltip]:hover::after {
  content: attr(v-tooltip);
  position: absolute;
  bottom: 100%;
  left: 50%;
  transform: translateX(-50%);
  padding: 4px 8px;
  background-color: #333;
  color: white;
  border-radius: 4px;
  font-size: 12px;
  white-space: nowrap;
  z-index: 1000;
  margin-bottom: 5px;
  opacity: 0.9;
}
</style>
