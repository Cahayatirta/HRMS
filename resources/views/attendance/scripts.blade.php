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
                // Save coords to session storage
                sessionStorage.setItem('coords', JSON.stringify(position.coords));

                // Tunggu wire ready
                setTimeout(() => {
                    const livewireComponent = document.querySelector('[wire\\:id]');
                    if (livewireComponent && livewireComponent.__livewire) {
                        livewireComponent.__livewire.$call('fillLocationFromBrowser', {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                            accuracy: position.coords.accuracy,
                        });
                        console.log("✅ Lokasi Berhasil Dikirim :", position.coords);
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

{{-- <div x-data="{
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
</div> --}}
{{-- 
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function (position) {
            const latInput = document.querySelector('input[name="latitude"]');
            const longInput = document.querySelector('input[name="longitude"]');
            if (latInput && longInput) {
                latInput.value = position.coords.latitude;
                longInput.value = position.coords.longitude;
            }
        });
    }
});
</script> --}}
{{-- <script>
document.addEventListener("DOMContentLoaded", () => {
    navigator.geolocation.getCurrentPosition(
        function (position) {
            console.log("✅ Lokasi diperoleh:", position.coords);
            sessionStorage.setItem('coords', JSON.stringify(position.coords));

            // Tunggu Livewire siap
            setTimeout(() => {
                // Temukan komponen Livewire
                const livewireElement = document.querySelector('[wire\\:id]');
                if (!livewireElement) {
                    return console.error('❌ Tidak menemukan elemen Livewire.');
                }

                const livewireInstance = window.Livewire.find(livewireElement.getAttribute('wire:id'));

                if (!livewireInstance) {
                    return console.error('❌ Tidak menemukan instance Livewire.');
                }

                // Kirim data ke method Livewire
                livewireInstance.$dispatch('fillLocationFromBrowser', {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                });

                console.log('📤 Lokasi dikirim ke Livewire.');
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


<script>
document.addEventListener("DOMContentLoaded", () => {
    navigator.geolocation.getCurrentPosition(
        function (position) {
            console.log("✅ Lokasi diperoleh:", position.coords);
            
            const locationData = {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                accuracy: position.coords.accuracy,
            };

            // Simpan ke sessionStorage (untuk client-side)
            sessionStorage.setItem('coords', JSON.stringify(locationData));

            // Tunggu Livewire siap
            setTimeout(() => {
                // CARA 1: Kirim ke Livewire (RECOMMENDED)
                const livewireElement = document.querySelector('[wire\\:id]');
                if (livewireElement) {
                    const livewireInstance = window.Livewire.find(livewireElement.getAttribute('wire:id'));
                    if (livewireInstance) {
                        livewireInstance.$dispatch('fillLocationFromBrowser', locationData);
                        console.log('📤 Lokasi dikirim ke Livewire.');
                    }
                }

                // CARA 2: Kirim ke endpoint Laravel via AJAX (ALTERNATIVE)
                fetch('/store-location', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(locationData)
                })
                .then(response => response.json())
                .then(data => {
                    console.log('📤 Lokasi disimpan ke session:', data);
                })
                .catch(error => {
                    console.error('❌ Error menyimpan lokasi:', error);
                });
                

            }, 500);
        },
        function (error) {
            console.error("❌ Gagal mendapatkan lokasi:", error.message);
            
            // Kirim error ke Livewire
            setTimeout(() => {
                const livewireElement = document.querySelector('[wire\\:id]');
                if (livewireElement) {
                    const livewireInstance = window.Livewire.find(livewireElement.getAttribute('wire:id'));
                    if (livewireInstance) {
                        livewireInstance.$dispatch('locationError', {
                            error: error.message,
                            code: error.code
                        });
                    }
                }
            }, 500);
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
});
</script>   