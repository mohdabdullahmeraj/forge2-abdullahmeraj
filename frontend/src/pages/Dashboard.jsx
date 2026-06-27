function Dashboard() {
  const user = JSON.parse(localStorage.getItem('user') || '{}');

  return (
    <div>
      <h1 className="text-3xl font-bold text-gray-800 mb-4">Welcome to PulseDesk</h1>
      <p className="text-gray-600">
        Hello, <span className="font-semibold">{user.name || 'User'}</span>!
      </p>
      <p className="text-gray-500 mt-2">
        Role: <span className="capitalize">{user.role || 'unknown'}</span>
      </p>
      <div className="mt-8 p-6 bg-white rounded-lg shadow">
        <p className="text-gray-700">Dashboard content coming soon...</p>
      </div>
    </div>
  );
}

export default Dashboard;
