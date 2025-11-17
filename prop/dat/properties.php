<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Drew Enterprises – Property & Unit Management</title>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

  <!-- Header -->
  <header class="bg-indigo-700 text-white shadow-lg">
    <div class="flex items-center justify-between px-6 py-4">
      <h1 class="text-2xl font-bold">Drew Enterprises</h1>
      <button id="menu-toggle" class="md:hidden">
        <i data-lucide="menu"></i>
      </button>
    </div>
  </header>

  <div class="flex min-h-screen">
    <!-- Sidebar -->
    <aside id="sidebar" class="hidden md:block bg-indigo-800 text-white w-64 p-6">
      <h2 class="text-lg font-semibold mb-4">Navigation</h2>
      <nav class="space-y-3">
        <button class="sidebar-link w-full flex items-center px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 active text-left">
          <i data-lucide="home" class="w-5 h-5 mr-2"></i> Dashboard
        </button>
        <button class="sidebar-link w-full flex items-center px-4 py-2 rounded-lg hover:bg-indigo-500 text-left">
          <i data-lucide="building" class="w-5 h-5 mr-2"></i> Properties
        </button>
        <button class="sidebar-link w-full flex items-center px-4 py-2 rounded-lg hover:bg-indigo-500 text-left">
          <i data-lucide="layers" class="w-5 h-5 mr-2"></i> Units
        </button>
        <button class="sidebar-link w-full flex items-center px-4 py-2 rounded-lg hover:bg-indigo-500 text-left">
          <i data-lucide="users" class="w-5 h-5 mr-2"></i> Tenants
        </button>
        <button class="sidebar-link w-full flex items-center px-4 py-2 rounded-lg hover:bg-indigo-500 text-left">
          <i data-lucide="users" class="w-5 h-5 mr-2"></i> Payments  
        </button>
        <button class="sidebar-link w-full flex items-center px-4 py-2 rounded-lg hover:bg-indigo-500 text-left">
          <i data-lucide="file" class="w-5 h-5 mr-2"></i> Reports 
        </button>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8">
      <!-- Top Nav Tabs (All Combined) -->
      <div class="flex border-b border-gray-300 mb-6">
        <button id="tab-add-property" class="top-tab border-b-2 border-indigo-600 text-indigo-600 font-semibold px-4 py-2">Add Property</button>
        <button id="tab-view-property" class="top-tab border-b-2 border-transparent text-gray-600 hover:text-indigo-600 px-4 py-2">View Property</button>
        <button id="tab-add-unit" class="top-tab border-b-2 border-transparent text-gray-600 hover:text-indigo-600 px-4 py-2">Add Unit</button>
        <button id="tab-view-unit" class="top-tab border-b-2 border-transparent text-gray-600 hover:text-indigo-600 px-4 py-2">View Unit</button>
      </div>

      <!-- Message -->
      <div id="message-container" aria-live="polite" class="mb-4"></div>

      <!-- TAB CONTENT SECTIONS -->

      <!-- Add Property -->
      <div id="content-add-property" class="tab-content">
        <h2 class="text-xl font-semibold mb-4">Add Property</h2>
        <form id="property-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 bg-white p-6 rounded-lg shadow">
          <div>
            <label class="block text-gray-700 font-medium">Property Code</label>
            <input type="text" id="property_code" class="mt-1 w-full border border-gray-300 rounded-md p-2" required>
          </div>
          <div>
            <label class="block text-gray-700 font-medium">Property Name</label>
            <input type="text" id="property_name" class="mt-1 w-full border border-gray-300 rounded-md p-2" required>
          </div>
          <div>
            <label class="block text-gray-700 font-medium">Property Type</label>
            <input type="text" id="property_type" class="mt-1 w-full border border-gray-300 rounded-md p-2" required>
          </div>
          <div>
            <label class="block text-gray-700 font-medium">Location</label>
            <input type="text" id="location" class="mt-1 w-full border border-gray-300 rounded-md p-2" required>
          </div>
          <div>
            <label class="block text-gray-700 font-medium">Size (sqft)</label>
            <input type="number" id="size" class="mt-1 w-full border border-gray-300 rounded-md p-2" required>
          </div>
          <div>
            <label class="block text-gray-700 font-medium">Value (UGX)</label>
            <input type="number" id="value" class="mt-1 w-full border border-gray-300 rounded-md p-2" required>
          </div>
          <div class="col-span-full">
            <label class="block text-gray-700 font-medium">Description</label>
            <textarea id="description" rows="3" class="mt-1 w-full border border-gray-300 rounded-md p-2"></textarea>
          </div>
          <div class="col-span-full">
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-md">Save Property</button>
          </div>
        </form>
      </div>

      <!-- View Property -->
      <div id="content-view-property" class="tab-content hidden">
        <h2 class="text-xl font-semibold mb-4">All Properties</h2>
        <div class="bg-white rounded-lg shadow p-6">
          <table class="w-full border-collapse">
            <thead class="bg-gray-100 text-gray-700">
              <tr>
                <th class="p-2 text-left">Code</th>
                <th class="p-2 text-left">Name</th>
                <th class="p-2 text-left">Type</th>
                <th class="p-2 text-left">Location</th>
                <th class="p-2 text-left">Value</th>
              </tr>
            </thead>
            <tbody id="property-table-body">
              <tr><td colspan="5" class="text-center text-gray-500 p-4">No properties available</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Add Unit -->
      <div id="content-add-unit" class="tab-content hidden">
        <h2 class="text-xl font-semibold mb-4">Add Unit</h2>
        <form id="unit-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 bg-white p-6 rounded-lg shadow">
          <div>
            <label class="block text-gray-700 font-medium">Unit Code</label>
            <input type="text" id="unit_code" class="mt-1 w-full border border-gray-300 rounded-md p-2" required>
          </div>
          <div>
            <label class="block text-gray-700 font-medium">Property</label>
            <select id="property_id" class="mt-1 w-full border border-gray-300 rounded-md p-2" required>
              <option value="">Select Property</option>
              <option value="1">Property A</option>
              <option value="2">Property B</option>
            </select>
          </div>
          <div>
            <label class="block text-gray-700 font-medium">Unit Type</label>
            <input type="text" id="unit_type" class="mt-1 w-full border border-gray-300 rounded-md p-2" required>
          </div>
          <div>
            <label class="block text-gray-700 font-medium">Rent Amount (UGX)</label>
            <input type="number" id="rent_amount" class="mt-1 w-full border border-gray-300 rounded-md p-2" required step="0.01">
          </div>
          <div>
            <label class="block text-gray-700 font-medium">Status</label>
            <select id="status" class="mt-1 w-full border border-gray-300 rounded-md p-2" required>
              <option value="Available">Available</option>
              <option value="Occupied">Occupied</option>
            </select>
          </div>
          <div class="col-span-full">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-md">Save Unit</button>
          </div>
        </form>
      </div>

      <!-- View Unit -->
      <div id="content-view-unit" class="tab-content hidden">
        <h2 class="text-xl font-semibold mb-4">All Units</h2>
        <div class="bg-white rounded-lg shadow p-6">
          <table class="w-full border-collapse">
            <thead class="bg-gray-100 text-gray-700">
              <tr>
                <th class="p-2 text-left">Code</th>
                <th class="p-2 text-left">Property</th>
                <th class="p-2 text-left">Type</th>
                <th class="p-2 text-left">Rent</th>
                <th class="p-2 text-left">Status</th>
              </tr>
            </thead>
            <tbody id="unit-table-body">
              <tr><td colspan="5" class="text-center text-gray-500 p-4">No units available</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <script>
    lucide.createIcons();

    // Mobile Sidebar Toggle
    document.getElementById("menu-toggle").addEventListener("click", () => {
      document.getElementById("sidebar").classList.toggle("hidden");
    });

    // Tab Switching Logic
    const tabs = document.querySelectorAll(".top-tab");
    const contents = document.querySelectorAll(".tab-content");

    tabs.forEach(tab => {
      tab.addEventListener("click", () => {
        tabs.forEach(btn => btn.classList.remove("border-indigo-600", "text-indigo-600", "font-semibold"));
        contents.forEach(c => c.classList.add("hidden"));

        tab.classList.add("border-indigo-600", "text-indigo-600", "font-semibold");
        document.getElementById(`content-${tab.id.split('-')[1]}-${tab.id.split('-')[2]}`).classList.remove("hidden");
      });
    });

    // Message display helper
    function displayMessage(type, text) {
      const msg = document.getElementById("message-container");
      msg.innerHTML = `<div class="p-3 rounded-lg text-white ${type === 'success' ? 'bg-green-600' : 'bg-red-600'} shadow">${text}</div>`;
      setTimeout(() => msg.innerHTML = "", 3000);
    }

    // Mock form submissions
    document.getElementById("property-form").addEventListener("submit", e => {
      e.preventDefault();
      displayMessage("success", "Property saved successfully!");
      e.target.reset();
    });

    document.getElementById("unit-form").addEventListener("submit", e => {
      e.preventDefault();
      displayMessage("success", "Unit saved successfully!");
      e.target.reset();
    });
  </script>
</body>
</html>
