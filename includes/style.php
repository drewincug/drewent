
  <style>
    body {
      background-color: #f4f4f8;
      height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .sidebar {
      background-color: #3f37c9;
      color: white;
      width: 240px;
      flex-shrink: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      position: fixed;
    }
    .sidebar .nav-link {
      color: #cdd2ff;
      border-radius: 8px;
      margin-bottom: 5px;
    }
    .sidebar .nav-link.active,
    .sidebar .nav-link:hover {
      background-color: #5a4df0;
      color: #fff;
    }
    .user-info {
      background-color: #4f46e5;
      border-radius: 8px;
      padding: 10px;
      margin-bottom: 10px;
    }
    .user-info h6 {
      margin: 0;
      font-size: 0.9rem;
    }
    .content-wrapper {
      margin-left: 240px;
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }
    .topbar {
      background-color: #4338ca;
      color: white;
      padding: 10px 20px;
      font-weight: 500;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .logout-btn {
      background-color: #f43f5e;
      border: none;
      color: white;
      border-radius: 6px;
      padding: 6px 12px;
      font-size: 0.9rem;
      transition: background-color 0.2s ease-in-out;
    }
    .logout-btn:hover {
      background-color: #e11d48;
    }
    footer {
      background-color: #212529;
      color: #ccc;
    }
  </style>