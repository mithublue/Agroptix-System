<template>
  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Batch QR Code: {{ batch.name || 'Untitled Batch' }}
          </h2>
          <div class="text-sm text-gray-500">
            Trace Code: {{ batch.trace_code }}
          </div>
        </div>
        <div class="flex space-x-2">
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
            :href="route('batches.timeline', batch.trace_code)" 
            class="btn btn-primary"
          >
            View Timeline
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
                <!-- Copy Shareable Link -->
                <div>
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
            :href="route('batches.show', batch.id)" 
            class="btn btn-secondary"
          >
            View Batch
          </Link>
        </div>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="text-center">
              <h3 class="text-lg font-medium text-gray-900 mb-2">
                {{ batch.name || 'Untitled Batch' }}
              </h3>
              <p class="text-sm text-gray-500 mb-6">
                Scan this QR code to view the batch timeline
              </p>
              
              <!-- QR Code Display -->
              <div class="flex justify-center mb-8">
                <div class="p-4 bg-white rounded-lg border border-gray-200 shadow-sm">
                  <div ref="qrCodeContainer" class="w-64 h-64 flex items-center justify-center">
                    <!-- QR code will be rendered here -->
                    <div v-if="isLoading" class="text-center">
                      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                      <p class="mt-2 text-sm text-gray-500">Generating QR code...</p>
                    </div>
                    <div v-else-if="error" class="text-center p-4 bg-red-50 rounded-lg">
                      <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <Icon 
                          icon="mdi-alert-circle" 
                          class="h-6 w-6 text-red-600" 
                          aria-hidden="true" 
                        />
                      </div>
                      <h3 class="mt-2 text-sm font-medium text-red-800">Error</h3>
                      <p class="mt-1 text-sm text-red-700">
                        {{ error }}
                      </p>
                      <button
                        @click="generateQRCode"
                        class="mt-3 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                      >
                        <Icon 
                          icon="mdi-refresh" 
                          class="-ml-0.5 mr-1.5 h-4 w-4" 
                          aria-hidden="true" 
                        />
                        Try Again
                      </button>
                    </div>
                    <div v-else class="text-center">
                      <img 
                        :src="qrCodeDataUrl" 
                        :alt="`QR Code for ${batch.trace_code}`"
                        class="w-full h-auto"
                        @load="onQRCodeLoad"
                      />
                      <div class="mt-2 text-xs text-gray-500">
                        Trace Code: {{ batch.trace_code }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Batch Information -->
              <div class="mt-8 border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                  Batch Information
                </h3>
                
                <div class="bg-gray-50 rounded-lg p-4">
                  <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                      <dt class="text-sm font-medium text-gray-500">Batch Name</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        {{ batch.name || 'N/A' }}
                      </dd>
                    </div>
                    
                    <div class="sm:col-span-1">
                      <dt class="text-sm font-medium text-gray-500">Status</dt>
                      <dd class="mt-1">
                        <span 
                          :class="[
                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                            getStatusColor(batch.status)
                          ]"
                        >
                          {{ batch.status || 'N/A' }}
                        </span>
                      </dd>
                    </div>
                    
                    <div class="sm:col-span-1">
                      <dt class="text-sm font-medium text-gray-500">Product</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        {{ batch.product?.name || 'N/A' }}
                      </dd>
                    </div>
                    
                    <div class="sm:col-span-1">
                      <dt class="text-sm font-medium text-gray-500">Created</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        {{ formatDate(batch.created_at) }}
                      </dd>
                    </div>
                    
                    <div class="sm:col-span-2">
                      <dt class="text-sm font-medium text-gray-500">QR Code URL</dt>
                      <dd class="mt-1">
                        <div class="flex rounded-md shadow-sm">
                          <div class="relative flex-grow focus-within:z-10">
                            <input
                              type="text"
                              :value="shortUrl"
                              readonly
                              class="focus:ring-blue-500 focus:border-blue-500 block w-full rounded-none rounded-l-md sm:text-sm border-gray-300"
                              ref="urlInput"
                            />
                          </div>
                          <button
                            @click="copyToClipboard"
                            type="button"
                            class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                            :title="copyButtonText"
                          >
                            <Icon 
                              :icon="copyButtonIcon" 
                              class="h-5 w-5 text-gray-400" 
                              aria-hidden="true" 
                            />
                            <span>{{ copyButtonText }}</span>
                          </button>
                        </div>
                      </dd>
                    </div>
                  </dl>
                </div>
              </div>
              
              <!-- Actions -->
              <div class="mt-8 pt-6 border-t border-gray-200 flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                <a
                  :href="downloadUrl"
                  download
                  class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <Icon 
                    icon="mdi-download" 
                    class="-ml-1 mr-2 h-5 w-5 text-gray-500" 
                    aria-hidden="true" 
                  />
                  Download QR Code
                </a>
                
                <a
                  :href="printUrl"
                  target="_blank"
                  class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <Icon 
                    icon="mdi-printer" 
                    class="-ml-1 mr-2 h-5 w-5 text-gray-500" 
                    aria-hidden="true" 
                  />
                  Print Label
                </a>
                
                <button
                  @click="regenerateQRCode"
                  type="button"
                  class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  :disabled="isLoading"
                >
                  <Icon 
                    icon="mdi-refresh" 
                    class="-ml-1 mr-2 h-5 w-5" 
                    :class="{ 'animate-spin': isLoading }"
                    aria-hidden="true" 
                  />
                  {{ isLoading ? 'Regenerating...' : 'Regenerate QR Code' }}
                </button>
              </div>
              
              <!-- Print-only content -->
              <div class="hidden print:block mt-12">
                <div class="text-center">
                  <h3 class="text-lg font-bold">{{ batch.name || 'Untitled Batch' }}</h3>
                  <p class="text-sm text-gray-600">Trace Code: {{ batch.trace_code }}</p>
                  <p class="text-sm text-gray-600">{{ formatDate(batch.created_at, 'long') }}</p>
                  
                  <div class="mt-4 flex justify-center">
                    <img 
                      :src="qrCodeDataUrl" 
                      :alt="`QR Code for ${batch.trace_code}`"
                      class="w-48 h-48"
                    />
                  </div>
                  
                  <div class="mt-4 text-xs text-gray-500">
                    <p>Scan this code or visit: {{ shortUrl }}</p>
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

<script setup>
import { ref, onMounted, computed } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Icon from '@/Components/Icon.vue';
import { format } from 'date-fns';

// Get the current page URL
const page = usePage();

const props = defineProps({
  batch: {
    type: Object,
    required: true,
  },
  qr_code_url: {
    type: String,
    required: true,
  },
  short_url: {
    type: String,
    required: true,
  },
  download_url: {
    type: String,
    required: true,
  },
});

// Refs
const qrCodeDataUrl = ref('');
const isLoading = ref(true);
const error = ref(null);
const copyButtonText = ref('Copy URL');
const copyButtonIcon = ref('mdi-content-copy');
const urlInput = ref(null);
const showExportDropdown = ref(false);
const showShareOptions = ref(false);
const shareableLink = ref('');

// Computed properties
const printUrl = computed(() => {
  return route('qrcode.print', props.batch.trace_code);
});

const downloadUrl = computed(() => {
  return route('qrcode.download', props.batch.trace_code);
});

// Generate export URLs
const exportUrl = (format) => {
  return route('batches.export', {
    batch: props.batch.trace_code,
    format: format
  });
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

// Methods
const formatDate = (dateString, formatType = 'short') => {
  if (!dateString) return 'N/A';
  
  const date = new Date(dateString);
  
  if (formatType === 'long') {
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  }
  
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

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

const copyToClipboard = async () => {
  try {
    await navigator.clipboard.writeText(props.short_url);
    copyButtonText.value = 'Copied!';
    copyButtonIcon.value = 'mdi-check';
    
    // Reset button after 2 seconds
    setTimeout(() => {
      copyButtonText.value = 'Copy URL';
      copyButtonIcon.value = 'mdi-content-copy';
    }, 2000);
  } catch (err) {
    console.error('Failed to copy URL:', err);
    
    // Fallback for browsers that don't support clipboard API
    if (urlInput.value) {
      urlInput.value.select();
      document.execCommand('copy');
      
      copyButtonText.value = 'Copied!';
      copyButtonIcon.value = 'mdi-check';
      
      // Reset button after 2 seconds
      setTimeout(() => {
        copyButtonText.value = 'Copy URL';
        copyButtonIcon.value = 'mdi-content-copy';
      }, 2000);
    }
  }
};

const generateQRCode = async () => {
  isLoading.value = true;
  error.value = null;
  
  try {
    // In a real app, you might want to regenerate the QR code on the server
    // For now, we'll just reload the current data
    router.reload({
      only: ['qr_code_url'],
      preserveScroll: true,
      onSuccess: () => {
        qrCodeDataUrl.value = props.qr_code_url;
      },
      onError: (errors) => {
        error.value = 'Failed to regenerate QR code. Please try again.';
        console.error('Error regenerating QR code:', errors);
      },
      onFinish: () => {
        isLoading.value = false;
      },
    });
  } catch (err) {
    error.value = 'An unexpected error occurred. Please try again.';
    console.error('Error generating QR code:', err);
    isLoading.value = false;
  }
};

const regenerateQRCode = async () => {
  await generateQRCode();
};

const onQRCodeLoad = () => {
  isLoading.value = false;
};

// Lifecycle hooks
onMounted(() => {
  // Set the QR code data URL from props
  qrCodeDataUrl.value = props.qr_code_url;
  
  // If we have a QR code URL, we're not loading
  if (props.qr_code_url) {
    isLoading.value = false;
  } else {
    // Otherwise, try to generate one
    generateQRCode();
  }
  
  // Set the shareable link
  shareableLink.value = window.location.origin + route('batches.timeline', props.batch.trace_code);
});
</script>

<style scoped>
/* Print styles */
@media print {
  .no-print {
    display: none !important;
  }
  
  body, html {
    background: white !important;
    color: black !important;
    font-size: 10pt;
  }
  
  @page {
    margin: 0.5cm;
    size: auto;
  }
}

/* Animation for copy button */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
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

/* Ensure buttons have consistent height */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 2.25rem;
}

/* Export and share dropdowns */
.relative.group {
  position: relative;
}

/* Smooth transitions for dropdowns */
.transition-all {
  transition: all 0.2s ease-in-out;
}

/* Improve button hover states */
.btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.btn:active {
  transform: translateY(0);
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

/* Social sharing buttons */
.social-btn {
  transition: all 0.2s ease-in-out;
}

.social-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.social-btn:active {
  transform: translateY(0);
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .flex.space-x-2 {
    flex-wrap: wrap;
    gap: 0.5rem;
  }
  
  .btn {
    flex: 1 1 auto;
    min-width: 120px;
  }
  
  .relative.group {
    width: 100%;
  }
  
  .w-64 {
    width: 90vw;
    max-width: 20rem;
  }
}
</style>
