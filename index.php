<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh; 
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f4f8; 
        }
        .card {
            background-color: white;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 0 15px 0 rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
            border-radius: 0.75rem;
        }
        .btn {
            background-color: #4c51bf;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #434190;
        }
    </style>
</head>
<body>

    <!-- LOGIN CARD -->
    <div id="login-card" class="card p-8 w-full max-w-sm">
        <h2 class="text-3xl font-bold text-gray-800 mb-6 text-center">System Login</h2>
        
        <!-- Display error messages if present -->
        <?php if (isset($_GET['error'])): ?>
            <div id="status-message" class="mb-4 p-3 rounded-lg text-center font-medium bg-red-100 text-red-700" role="alert">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <!-- Form submits to login_process.php -->
        <form action="login.php" method="POST" class="space-y-4 w-full">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" id="username" name="username" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
            </div>
            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Required Role</label>
                <select id="role" name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500" required>
                    <option value="Admin">Admin</option>
                    <option value="Manager">Manager</option>
                    <option value="Staff">Staff</option>
                    <option value="Client">Client</option>
                </select>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center space-x-2">
                <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="remember" class="text-sm text-gray-700">Remember Me</label>
            </div>

            <button type="submit" class="btn w-full text-white font-semibold py-2 rounded-lg shadow-lg hover:shadow-xl">
                Log In
            </button>
        </form>
    </div>

</body>
</html>
