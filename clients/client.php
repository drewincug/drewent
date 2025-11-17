<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Base styles */
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh; 
            margin: 0;
            display: flex;
            flex-direction: column;
            background-color: #f0f4f8; 
        }
        main {
            flex-grow: 1;
        }
        /* Custom styles for the form elements */
        .form-input-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-input-group input[type="text"], 
        .form-input-group input[type="email"], 
        .form-input-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db; /* gray-300 */
            border-radius: 0.375rem; /* rounded-md */
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }
        .form-input-group input:focus, .form-input-group select:focus {
            border-color: #4f46e5; /* indigo-600 */
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.5);
            outline: none;
        }
    </style>
    <script>
        // Script to display URL messages (success/error)
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            const success = urlParams.get('success');
            const messageContainer = document.getElementById('message-container');

            if (error) {
                messageContainer.innerHTML = `<div class="p-4 mb-4 text-sm text-red-800 bg-red-100 rounded-lg" role="alert">Error: ${decodeURIComponent(error)}</div>`;
            } else if (success) {
                messageContainer.innerHTML = `<div class="p-4 mb-4 text-sm text-green-800 bg-green-100 rounded-lg" role="alert">Success: ${decodeURIComponent(success)}</div>`;
            }
            
            // Remove messages from URL after display for clean history
            if (error || success) {
                setTimeout(() => {
                     window.history.replaceState(null, '', window.location.pathname);
                }, 100);
            }
        });
    </script>
</head>
<body class="w-full min-h-screen flex flex-col">
    
    <header class="bg-indigo-700 text-white p-4 flex justify-between items-center shadow-xl flex-shrink-0">
        <h1 class="text-xl font-bold">Client Management</h1>
        <a href="index.html" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
            Simulate Log Out
        </a>
    </header>

    <main class="flex flex-grow overflow-hidden">
        
        <aside class="w-64 bg-gray-800 text-white flex-shrink-0 p-4 hidden md:block overflow-y-auto">
            <nav class="space-y-2">
                <a href="dashboard.html" class="block p-2 hover:bg-gray-700 rounded-lg transition duration-150">Dashboard</a>
                <a href="properties.html" class="block p-2 hover:bg-gray-700 rounded-lg transition duration-150">Properties & Units</a>
                <a href="clients.html" class="block p-2 bg-indigo-600 rounded-lg font-semibold hover:bg-indigo-500 transition duration-150">Clients</a>
                <a href="tenants.html" class="block p-2 hover:bg-gray-700 rounded-lg transition duration-150">Tenants</a>
                <a href="leases.html" class="block p-2 hover:bg-gray-700 rounded-lg transition duration-150">Leases & Payments</a>
                <a href="audit.html" class="block p-2 hover:bg-gray-700 rounded-lg transition duration-150">Audit Log</a>
                <hr class="border-gray-700 my-4">
                <p class="text-sm text-gray-400 p-2">Logged in as: Admin</p>
            </nav>
        </aside>

        <section class="flex-grow p-8 bg-gray-50 overflow-y-auto">
            <div class="max-w-7xl mx-auto">
                
                <h2 class="text-3xl font-extrabold text-gray-900 mb-6">Register New Client</h2>
                
                <div id="message-container" class="mb-6">
                    </div>

                <div class="bg-white shadow-xl rounded-lg p-6 md:p-8">
                    <form action="process_submission.php" method="POST" class="space-y-4">
                        
                        <input type="hidden" name="form_type" value="add_client">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div class="form-input-group">
                                <label for="client_name">Client Name <span class="text-red-500">*</span></label>
                                <input type="text" id="client_name" name="client_name" placeholder="E.g., John Doe" required>
                            </div>

                            <div class="form-input-group">
                                <label for="contact_number">Contact Number <span class="text-red-500">*</span></label>
                                <input type="text" id="contact_number" name="contact_number" placeholder="E.g., +1234567890" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="form-input-group">
                                <label for="email">Email <span class="text-red-500">*</span></label>
                                <input type="email" id="email" name="email" placeholder="E.g., client@example.com" required>
                            </div>

                            <div class="form-input-group">
                                <label for="client_type">Client Type <span class="text-red-500">*</span></label>
                                <select id="client_type" name="client_type" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="Individual">Individual</option>
                                    <option value="Corporate">Corporate</option>
                                    <option value="Government">Government</option>
                                    <option value="Partnership">Partnership</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-input-group">
                            <label for="organization">Organization/Company <span class="text-red-500">*</span></label>
                            <input type="text" id="organization" name="organization" placeholder="E.g., Acme Corporation" required>
                        </div>
                        
                        <div class="form-input-group">
                            <label for="address">Address <span class="text-red-500">*</span></label>
                            <input type="text" id="address" name="address" placeholder="E.g., 123 Main St, Anytown, USA" required>
                        </div>

                        <div>
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 shadow-md focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                </svg>
                                Add New Client
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            </section>
    </main>

    <footer class="bg-gray-700 text-gray-400 p-3 text-center text-xs shadow-inner flex-shrink-0">
        &copy; 2025 Property Management System | All Rights Reserved.
    </footer>
</body>
</html>