<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>
<style>
    p {
    font-style: italic;
    font-size: 13px;
}
</style>
<body class="bg-gray-900 flex items-center justify-center min-h-screen">

    <div class="bg-[#2c3e50] p-6 rounded-lg shadow-lg w-96">
        <div class="text-center mb-4">
            <div class="w-28 h-28 mx-auto flex items-center justify-center">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" style="width: 100px; height: 100px;">
            </div>
            <h2 class="text-lg font-semibold text-white">Selamat Datang!</h2>
            <p class="text-gray-300 text-xs">Masuk sekarang untuk melanjutkan!</p>
        </div>      
        
        @if(session('logout'))
        <script>
            Toastify({
                text: "<i class='fa-solid fa-check-circle' style='color: lightgreen;'></i> {{ session('logout') }}",
                duration: 3000,
                gravity: "top",
                position: "right",
                stopOnFocus: true,
                escapeMarkup: false, // Agar ikon HTML bisa dirender
                style: {
                    background: "none",
                    color: "white",
                    fontSize: "14px",
                    boxShadow: "none",
                    padding: "5px"
                }
            }).showToast();
            </script>
        @endif

        @if ($errors->any())
            <div class="mb-3 p-2 bg-red-500 text-white rounded text-xs">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="email" class="block text-gray-300 text-xs" style="margin-bottom: 10px;">Email Address :</label>
                <div class="flex items-center border rounded-lg px-2 py-1.5 bg-gray-700">
                    <span class="text-gray-400">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" name="email" required placeholder="Masukan email" class="w-full bg-transparent outline-none px-2 text-white text-xs">
                </div>
            </div>

            <div class="mb-3">
                <label for="password" class="block text-gray-300 text-xs" style="margin-bottom: 10px;">Password :</label>
                <div class="flex items-center border rounded-lg px-2 py-1.5 bg-gray-700">
                    <span class="text-gray-400">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" required placeholder="Masukan password" class="w-full bg-transparent outline-none px-2 text-white text-xs">
                </div>
            </div>

            <div class="flex justify-between text-xs mb-3">
                <a href="#" class="text-blue-400 hover:underline">Forget password?</a>
            </div>

            <button type="submit" class="w-full bg-white text-[#2c3e50] py-1.5 rounded-lg text-sm font-semibold hover:bg-gray-300 transition">Login</button>

            <p class="text-center text-xs text-gray-300 mt-3">
                Don't have an account? <a href="#" class="text-blue-400 font-semibold hover:underline">Sign Up</a>
            </p>
        </form>
    </div>
</body>
</html>
