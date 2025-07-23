<template>
  <AppLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ title }}
        </h2>
        <Link 
          :href="route('batches.index')" 
          class="btn btn-secondary"
        >
          Back to Batches
        </Link>
      </div>
    </template>

    <div class="py-6">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="text-center">
              <p class="text-gray-600 mb-6">
                {{ description }}
              </p>
              
              <!-- Error message -->
              <div 
                v-if="error"
                class="mb-6 p-4 bg-red-50 border-l-4 border-red-500"
              >
                <div class="flex">
                  <div class="flex-shrink-0">
                    <Icon 
                      icon="mdi-alert-circle" 
                      class="h-5 w-5 text-red-500" 
                      aria-hidden="true" 
                    />
                  </div>
                  <div class="ml-3">
                    <p class="text-sm text-red-700">
                      {{ error }}
                    </p>
                    <p 
                      v-if="scannedCode"
                      class="mt-1 text-sm text-red-600"
                    >
                      Scanned code: <code class="bg-gray-100 px-2 py-1 rounded">{{ scannedCode }}</code>
                    </p>
                  </div>
                </div>
              </div>
              
              <!-- Scanner container -->
              <div class="relative mx-auto w-full max-w-md">
                <!-- Video element for the camera -->
                <video 
                  ref="video" 
                  class="w-full h-auto rounded-lg border-2 border-gray-300"
                  :class="{ 'border-red-500': error }"
                  autoplay
                  playsinline
                ></video>
                
                <!-- Scanner overlay -->
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="relative w-64 h-64 border-4 border-blue-400 rounded-lg">
                    <!-- Corner markers -->
                    <div class="absolute -top-1 -left-1 w-12 h-12 border-t-4 border-l-4 border-blue-500 rounded-tl-lg"></div>
                    <div class="absolute -top-1 -right-1 w-12 h-12 border-t-4 border-r-4 border-blue-500 rounded-tr-lg"></div>
                    <div class="absolute -bottom-1 -left-1 w-12 h-12 border-b-4 border-l-4 border-blue-500 rounded-bl-lg"></div>
                    <div class="absolute -bottom-1 -right-1 w-12 h-12 border-b-4 border-r-4 border-blue-500 rounded-br-lg"></div>
                    
                    <!-- Scan line animation -->
                    <div 
                      class="absolute top-0 left-0 right-0 h-1 bg-blue-400 opacity-75"
                      :style="{ top: scanLinePosition + 'px' }"
                    ></div>
                  </div>
                </div>
                
                <!-- Scan result overlay -->
                <div 
                  v-if="scanResult"
                  class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center rounded-lg"
                >
                  <div class="bg-white p-6 rounded-lg shadow-xl text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                      <Icon 
                        icon="mdi-check" 
                        class="h-6 w-6 text-green-600" 
                        aria-hidden="true" 
                      />
                    </div>
                    <h3 class="mt-3 text-lg font-medium text-gray-900">Batch Found!</h3>
                    <div class="mt-2">
                      <p class="text-sm text-gray-500">
                        Redirecting to batch details...
                      </p>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Manual entry -->
              <div class="mt-8 max-w-md mx-auto">
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <Icon 
                      icon="mdi-barcode-scan" 
                      class="h-5 w-5 text-gray-400" 
                      aria-hidden="true" 
                    />
                  </div>
                  <input
                    type="text"
                    v-model="manualCode"
                    @keyup.enter="submitManualCode"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="Or enter batch code manually"
                  />
                  <button
                    @click="submitManualCode"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                    :disabled="!manualCode.trim()"
                  >
                    <Icon 
                      icon="mdi-arrow-right" 
                      class="h-5 w-5 text-blue-600 hover:text-blue-800" 
                      :class="{ 'opacity-50': !manualCode.trim() }"
                      aria-hidden="true" 
                    />
                  </button>
                </div>
                <p class="mt-2 text-sm text-gray-500">
                  Enter the batch code or scan the QR code above
                </p>
              </div>
              
              <!-- Camera controls -->
              <div class="mt-8 flex justify-center space-x-4">
                <button
                  v-if="hasCamera"
                  @click="switchCamera"
                  type="button"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <Icon 
                    icon="mdi-camcorder" 
                    class="-ml-1 mr-2 h-5 w-5 text-gray-500" 
                    aria-hidden="true" 
                  />
                  Switch Camera
                </button>
                
                <button
                  @click="requestCamera"
                  v-if="!hasCamera"
                  type="button"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                  <Icon 
                    icon="mdi-camera" 
                    class="-ml-1 mr-2 h-5 w-5" 
                    aria-hidden="true" 
                  />
                  Enable Camera
                </button>
                
                <button
                  @click="stopCamera"
                  v-if="hasCamera"
                  type="button"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                  <Icon 
                    icon="mdi-camera-off" 
                    class="-ml-1 mr-2 h-5 w-5" 
                    aria-hidden="true" 
                  />
                  Stop Camera
                </button>
              </div>
              
              <!-- Debug info (hidden in production) -->
              <div 
                v-if="false"
                class="mt-8 p-4 bg-gray-100 rounded-lg text-xs"
              >
                <h4 class="font-medium mb-2">Debug Info</h4>
                <div>Has camera: {{ hasCamera }}</div>
                <div>Camera active: {{ cameraActive }}</div>
                <div>Scanning: {{ isScanning }}</div>
                <div>Scan result: {{ scanResult }}</div>
                <div>Error: {{ error }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Icon from '@/Components/Icon.vue';
import { usePermission } from '@/Composables/usePermission';

const props = defineProps({
  title: {
    type: String,
    default: 'Scan QR Code',
  },
  description: {
    type: String,
    default: 'Scan a QR code to view batch details',
  },
  error: {
    type: String,
    default: '',
  },
  scannedCode: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['scan']);

// Refs
const video = ref(null);
const manualCode = ref('');
const hasCamera = ref(false);
const cameraActive = ref(false);
const isScanning = ref(false);
const scanResult = ref('');
const scanLinePosition = ref(0);
const currentStream = ref(null);
const facingMode = ref('environment'); // 'user' for front camera, 'environment' for back

// Check for camera permissions
const { hasPermission, requestPermission } = usePermission();

// Initialize the scanner
const initScanner = async () => {
  try {
    // Check if we already have a stream
    if (currentStream.value) {
      stopCamera();
    }
    
    // Request camera permissions
    const permissionGranted = await requestPermission('camera');
    if (!permissionGranted) {
      throw new Error('Camera permission denied');
    }
    
    // Get available devices
    const devices = await navigator.mediaDevices.enumerateDevices();
    const videoDevices = devices.filter(device => device.kind === 'videoinput');
    
    if (videoDevices.length === 0) {
      throw new Error('No video input devices found');
    }
    
    // Set up video stream
    const constraints = {
      video: { 
        facingMode: facingMode.value,
        width: { ideal: 1280 },
        height: { ideal: 720 }
      },
      audio: false
    };
    
    const stream = await navigator.mediaDevices.getUserMedia(constraints);
    
    // Update state
    currentStream.value = stream;
    hasCamera.value = true;
    cameraActive.value = true;
    
    // Set the video source
    if (video.value) {
      video.value.srcObject = stream;
      await video.value.play();
    }
    
    // Start the scanner
    startScanner();
    
  } catch (err) {
    console.error('Error initializing scanner:', err);
    emit('error', err.message || 'Failed to access camera');
  }
};

// Start the QR code scanner
const startScanner = () => {
  if (isScanning.value) return;
  
  isScanning.value = true;
  scanResult.value = '';
  
  // Start the scan line animation
  const animateScanLine = () => {
    if (!isScanning.value) return;
    
    scanLinePosition.value = (scanLinePosition.value + 2) % 256;
    requestAnimationFrame(animateScanLine);
  };
  
  animateScanLine();
  
  // In a real app, you would use a QR code scanning library here
  // For example: const codeReader = new ZXing.BrowserQRCodeReader();
  // Then set up the scanner to decode from the video element
};

// Stop the scanner
const stopScanner = () => {
  isScanning.value = false;
  // Clean up any scanner instances here
};

// Stop the camera
const stopCamera = () => {
  if (currentStream.value) {
    const tracks = currentStream.value.getTracks();
    tracks.forEach(track => track.stop());
    currentStream.value = null;
  }
  
  cameraActive.value = false;
  stopScanner();
  
  if (video.value) {
    video.value.srcObject = null;
  }
};

// Switch between front and back camera
const switchCamera = () => {
  facingMode.value = facingMode.value === 'user' ? 'environment' : 'user';
  initScanner();
};

// Request camera access
const requestCamera = () => {
  initScanner();
};

// Submit a manually entered code
const submitManualCode = () => {
  const code = manualCode.value.trim();
  if (!code) return;
  
  // Process the code as if it was scanned
  processScanResult(code);
};

// Process a scan result
const processScanResult = (result) => {
  scanResult.value = result;
  stopScanner();
  
  // Emit the scan event
  emit('scan', result);
  
  // In a real app, you would navigate to the batch details page
  // For now, we'll just log it
  console.log('Scanned code:', result);
  
  // Redirect to the batch timeline
  router.visit(route('qrcode.process'), {
    method: 'post',
    data: { code: result },
    preserveScroll: true,
    onError: (errors) => {
      console.error('Error processing QR code:', errors);
    },
  });
};

// Clean up on unmount
onUnmounted(() => {
  stopCamera();
  stopScanner();
});

// Initialize on mount
onMounted(() => {
  // Check if the browser supports the required APIs
  if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
    emit('error', 'Your browser does not support camera access');
    return;
  }
  
  // Check for camera permissions
  hasPermission('camera').then((granted) => {
    hasCamera.value = granted;
    if (granted) {
      initScanner();
    }
  });
  
  // In a real app, you would initialize the QR code scanner here
  // For example: initQrCodeScanner();
});
</script>

<style scoped>
/* Add any custom styles here */
</style>
