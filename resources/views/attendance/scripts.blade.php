{{-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        console.log("📍 Script geolocation dimuat");

        if ('geolocation' in navigator) {
            console.log("🧭 Geolocation tersedia di browser");

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    console.log("✅ Lokasi diperoleh:");
                    console.log("Latitude:", position.coords.latitude);
                    console.log("Longitude:", position.coords.longitude);

                    window.livewire.emit('fillLocationFromBrowser', {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude,
                        isInOfficeRadius: true
                    });
                },
                function (error) {
                    console.error("❌ Gagal mendapatkan lokasi:", error.message);
                    alert('Gagal mengakses lokasi: ' + error.message);
                }
            );
        } else {
            console.warn("⚠️ Geolocation tidak didukung browser");
            alert('Browser tidak mendukung geolocation.');
        }
    });
</script> --}}

{{-- <script>
navigator.geolocation.getCurrentPosition(
    function (position) {
        console.log("✅ Lokasi diperoleh (akurasi tinggi):");
        console.log("Latitude:", position.coords.latitude);
        console.log("Longitude:", position.coords.longitude);
        console.log("Accuracy (meter):", position.coords.accuracy);

        window.livewire.emit('fillLocationFromBrowser', {
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
            isInOfficeRadius: true
        });
    },
    function (error) {
        console.error("❌ Gagal mendapatkan lokasi:", error.message);
        alert('Gagal mengakses lokasi: ' + error.message);
    },
    {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
    }
);
</script> --}}

{{-- <script>
    document.addEventListener("DOMContentLoaded", () => {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                console.log("✅ Lokasi diperoleh:", position.coords);

                // Tunggu wire ready
                setTimeout(() => {
                    const livewireComponent = document.querySelector('[wire\\:id]');
                    if (livewireComponent && livewireComponent.__livewire) {
                        livewireComponent.__livewire.$call('fillLocationFromBrowser', {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                            accuracy: position.coords.accuracy,
                        });
                    } else {
                        console.error('⚠️ Tidak menemukan instance Livewire.');
                    }
                }, 500);
            },
            function (error) {
                console.error("❌ Gagal mendapatkan lokasi:", error.message);
                alert('Gagal mengakses lokasi: ' + error.message);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });
</script> --}}

{{-- <div x-data @load.window="
    navigator.geolocation.getCurrentPosition(
        function (position) {
            console.log('✅ Lokasi didapat:', position.coords);

            $wire.fillLocationFromBrowser({
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                accuracy: position.coords.accuracy,
            });
        },
        function (error) {
            console.error('❌ Gagal mendapatkan lokasi:', error.message);
            alert('Gagal mengakses lokasi: ' + error.message);
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
">
</div> --}}

{{-- <div x-data x-init="
    navigator.geolocation.getCurrentPosition(
        function (position) {
            console.log('✅ Lokasi diperoleh:', position.coords);
            $wire.fillLocationFromBrowser({
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                accuracy: position.coords.accuracy
            });
        },
        function (error) {
            console.error('❌ Gagal mendapatkan lokasi:', error.message);
            alert('Gagal mengakses lokasi: ' + error.message);
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
">
    <p class="text-xs text-gray-500">📍 Mendeteksi lokasi Anda...</p>
</div> --}}

<div x-data="{
    ambilLokasi() {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                console.log('📍 Lokasi diambil manual:', position.coords);
                $wire.fillLocationFromBrowser({
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                });
            },
            function (error) {
                alert('❌ Gagal mengambil lokasi: ' + error.message);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }
}" class="p-2">
    <p class="text-xs text-gray-500">Script Geolocation siap ✅</p>
</div>
