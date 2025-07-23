import { ref } from 'vue';

/**
 * Composable for handling browser permissions
 */
export function usePermission() {
  const permissionState = ref(null);
  const error = ref(null);
  const isSupported = 'permissions' in navigator;

  /**
   * Check if a permission is granted
   * @param {string} permission - The permission name (e.g., 'camera', 'geolocation')
   * @returns {Promise<boolean>} - Whether the permission is granted
   */
  const hasPermission = async (permission) => {
    if (!isSupported) {
      console.warn('Permissions API not supported in this browser');
      return false;
    }

    try {
      const permissionMap = {
        'camera': { name: 'camera' },
        'microphone': { name: 'microphone' },
        'geolocation': { name: 'geolocation' },
        'notifications': { name: 'notifications' },
        'persistent-storage': { name: 'persistent-storage' },
        'push': { name: 'push', userVisibleOnly: true },
        'midi': { name: 'midi', sysex: true },
        'clipboard-read': { name: 'clipboard-read' },
        'clipboard-write': { name: 'clipboard-write' },
      };

      const permissionName = permissionMap[permission]?.name || permission;
      const permissionParams = permissionMap[permission] || { name: permission };

      const status = await navigator.permissions.query(permissionParams);
      permissionState.value = status.state;

      return status.state === 'granted';
    } catch (err) {
      console.error(`Error checking ${permission} permission:`, err);
      error.value = err;
      return false;
    }
  };

  /**
   * Request a permission from the user
   * @param {string} permission - The permission to request (e.g., 'camera')
   * @param {Object} [options] - Additional options for the permission request
   * @returns {Promise<boolean>} - Whether the permission was granted
   */
  const requestPermission = async (permission, options = {}) => {
    try {
      // Handle camera permission specially as it's requested via getUserMedia
      if (permission === 'camera') {
        const stream = await navigator.mediaDevices.getUserMedia({
          video: true,
          ...options,
        });
        
        // Stop all tracks in the stream
        stream.getTracks().forEach(track => track.stop());
        
        return true;
      }
      
      // Handle other permissions
      const result = await navigator.permissions.query({ name: permission });
      
      if (result.state === 'granted') {
        return true;
      }
      
      if (result.state === 'prompt') {
        // For some permissions, we need to request them differently
        if (permission === 'notifications') {
          const permissionResult = await Notification.requestPermission();
          return permissionResult === 'granted';
        }
        
        // For other permissions, we might need to trigger the permission prompt
        // by calling the relevant API
        if (permission === 'geolocation') {
          return new Promise((resolve) => {
            navigator.geolocation.getCurrentPosition(
              () => resolve(true),
              () => resolve(false),
              { enableHighAccuracy: false, maximumAge: 10000, timeout: 5000 }
            );
          });
        }
      }
      
      return result.state === 'granted';
    } catch (err) {
      console.error(`Error requesting ${permission} permission:`, err);
      error.value = err;
      return false;
    }
  };

  /**
   * Check if the browser supports a specific permission
   * @param {string} permission - The permission to check support for
   * @returns {boolean} - Whether the permission is supported
   */
  const isPermissionSupported = (permission) => {
    if (!isSupported) return false;
    
    const supportedPermissions = [
      'camera',
      'microphone',
      'geolocation',
      'notifications',
      'persistent-storage',
      'push',
      'midi',
      'clipboard-read',
      'clipboard-write',
    ];
    
    return supportedPermissions.includes(permission);
  };

  return {
    hasPermission,
    requestPermission,
    isPermissionSupported,
    permissionState,
    error,
    isSupported,
  };
}

export default usePermission;
