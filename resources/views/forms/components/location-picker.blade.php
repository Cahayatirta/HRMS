<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="locationPicker({
        statePath: '{{ $getStatePath() }}'
    })" class="space-y-4">
        
        {{-- Hidden inputs untuk menyimpan data --}}
        <input type="hidden" x-model="latitude" />
        <input type="hidden" x-model="longitude" />
        
        {{-- Display current location --}}
        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-2">
                <span class="font-medium text-gray-700 dark:text-gray-300">Lokasi Saat Ini:</span>
                <button 
                    type="button"
                    @click="getCurrentLocation()"
                    :disabled="loading"
                    class="px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span x-show="!loading">Dapatkan Lokasi</span>
                    <span x-show="loading">Memuat...</span>
                </button>
            </div>
            
            <div x-show="address" class="text-sm text-gray-600 dark:text-gray-400 mb-2" x-text="address"></div>
            
            <div x-show="latitude && longitude" class="text-xs text-gray-500 dark:text-gray-400">
                Lat: <span x-text="latitude"></span>, Lng: <span x-text="longitude"></span>
            </div>
            
            <div x-show="error" class="text-sm text-red-600 dark:text-red-400 mt-2" x-text="error"></div>
        </div>
        
        {{-- Manual input fields --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Latitude</label>
                <input 
                    type="number" 
                    step="any"
                    x-model="latitude"
                    @input="updateLocation()"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2 text-sm"
                    placeholder="Contoh: -8.409518"
                />
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Longitude</label>
                <input 
                    type="number" 
                    step="any"
                    x-model="longitude"
                    @input="updateLocation()"
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md px-3 py-2 text-sm"
                    placeholder="Contoh: 115.188916"
                />
            </div>
        </div>
    </div>
</x-dynamic-component>

@push('scripts')
<script>
function locationPicker({ statePath }) {
    return {
        latitude: null,
        longitude: null,
        address: '',
        loading: false,
        error: '',
        
        init() {
            // Load existing data if available
            const existingData = this.$wire.get(statePath);
            if (existingData) {
                this.latitude = existingData.latitude;
                this.longitude = existingData.longitude;
                this.address = existingData.address || '';
            }
        },
        
        getCurrentLocation() {
            if (!navigator.geolocation) {
                this.error = 'Geolocation tidak didukung oleh browser ini.';
                return;
            }
            
            this.loading = true;
            this.error = '';
            
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.latitude = position.coords.latitude.toFixed(8);
                    this.longitude = position.coords.longitude.toFixed(8);
                    this.loading = false;
                    
                    // Get address from coordinates
                    this.reverseGeocode();
                    
                    // Update Filament state
                    this.updateLocation();
                },
                (error) => {
                    this.loading = false;
                    
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            this.error = 'Akses lokasi ditolak. Mohon izinkan akses lokasi di browser Anda.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            this.error = 'Informasi lokasi tidak tersedia.';
                            break;
                        case error.TIMEOUT:
                            this.error = 'Timeout dalam mendapatkan lokasi.';
                            break;
                        default:
                            this.error = 'Terjadi error yang tidak diketahui.';
                            break;
                    }
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0 // Don't use cached position
                }
            );
        },
        
        reverseGeocode() {
            // Using OpenStreetMap Nominatim API (free)
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${this.latitude}&lon=${this.longitude}&addressdetails=1`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        this.address = data.display_name;
                        this.updateLocation();
                    }
                })
                .catch(error => {
                    console.error('Error getting address:', error);
                });
        },
        
        updateLocation() {
            if (this.latitude && this.longitude) {
                this.$wire.set(statePath, {
                    latitude: parseFloat(this.latitude),
                    longitude: parseFloat(this.longitude),
                    address: this.address
                });
            }
        }
    }
}
</script>
@endpush