<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh; 
            margin: 0;
            display: flex;
            flex-direction: column; /* Ensure layout works correctly */
            background-color: #f0f4f8; 
        }
        /* Ensure the main area fills available space */
        main {
            flex-grow: 1;
        }
    </style>
</head>
<body class="w-full min-h-screen flex flex-col">
    
    <!-- TOP BAR (Header) -->
    <header class="bg-indigo-700 text-white p-4 flex justify-between items-center shadow-xl flex-shrink-0">
        <h1 class="text-xl font-bold">Property System Dashboard</h1>
        <!-- In a real PHP app, this would be a link to a logout.php script -->
        <a href="index.php" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
            Simulate Log Out
        </a>
    </header>

    <!-- MAIN APP AREA (Sidebar + Content - takes remaining height) -->
    <main class="flex flex-grow overflow-hidden">
        
        <!-- SIDE BAR - Links added -->
        <aside class="w-64 bg-gray-800 text-white flex-shrink-0 p-4 hidden md:block overflow-y-auto">
            <nav class="space-y-2">
                <a href="dashboard.php" class="block p-2 bg-indigo-600 rounded-lg font-semibold hover:bg-indigo-500 transition duration-150">Dashboard</a>
                <a href="propertyandunits/properties.php" class="block p-2 hover:bg-gray-700 rounded-lg transition duration-150">Properties & Units</a>
                <a href="clients.php" class="block p-2 hover:bg-gray-700 rounded-lg transition duration-150">Clients</a>
                <a href="tenants.php" class="block p-2 hover:bg-gray-700 rounded-lg font-bold text-yellow-300 transition duration-150">Tents </a>
                <a href="tenants.php" class="block p-2 hover:bg-gray-700 rounded-lg font-bold text-yellow-300 transition duration-150">Tenants</a>
                <a href="leases.php" class="block p-2 hover:bg-gray-700 rounded-lg transition duration-150">Leases & Payments</a>
                <a href="Reports.php" class="block p-2 hover:bg-gray-700 rounded-lg transition duration-150">Audit Log</a>
                <a href="audit.php" class="block p-2 hover:bg-gray-700 rounded-lg transition duration-150">Audit Log</a>
                <hr class="border-gray-700 my-4">
                <!-- PHP would dynamically display user info here -->
                <p class="text-sm text-gray-400 p-2">Logged in as: Admin</p>
            </nav>
        </aside>

        <!-- MAIN CONTENT SECTION (Scrollable) -->
        <section class="flex-grow p-8 bg-gray-50 overflow-y-auto">
            <h2 class="text-3xl font-bold text-indigo-700 mb-4">System Overview</h2>
            <p class="text-xl text-gray-700 mb-8 font-semibold">Welcome back to the system!</p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Stat Cards -->
                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500">
                    <p class="text-sm font-medium text-gray-500">Total Properties</p>
                    <p class="text-3xl font-extrabold text-gray-900 mt-1">12</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-yellow-500">
                    <p class="text-sm font-medium text-gray-500">Active Leases</p>
                    <p class="text-3xl font-extrabold text-gray-900 mt-1">45</p>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-red-500">
                    <p class="text-sm font-medium text-gray-500">Vacant Units</p>
                    <p class="text-3xl font-extrabold text-gray-900 mt-1">8</p>
                </div>
            </div>

            <div class="mt-10 bg-indigo-100 p-6 rounded-xl border border-indigo-300">
                <h3 class="text-xl font-bold text-indigo-800 mb-3">Login Simulation Details</h3>
                <p class="text-md text-gray-800 mb-1">Username: admin</p>
                <p class="text-md text-gray-800">Assumed Role: Admin</p>
            </div>

        </section>
    </main>

    <!-- FOOTER -->
    <footer class="bg-gray-700 text-gray-400 p-3 text-center text-xs shadow-inner flex-shrink-0">
        &copy; 2025 Property Management System | All Rights Reserved.
    </footer>
</body>
</php>
