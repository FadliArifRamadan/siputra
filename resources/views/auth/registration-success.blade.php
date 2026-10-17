<!DOCTYPE html>
<html>
<head>
    <title>Registrasi Berhasil</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full text-center">
        <div class="text-green-500 text-6xl mb-4">✅</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Registrasi Berhasil!</h1>
        <p class="text-gray-600 mb-6">
            Akun Anda telah berhasil didaftarkan. Silakan tunggu verifikasi dari RTK.
            Anda akan mendapatkan notifikasi setelah akun diaktifkan.
        </p>
        <a href="{{ route('login') }}" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition">
            Kembali ke Login
        </a>
    </div>
</body>
</html>