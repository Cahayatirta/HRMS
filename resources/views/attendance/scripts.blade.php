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

<script>
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
</script>