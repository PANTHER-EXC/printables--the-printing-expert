window.onload = function () {
  // Fetch data from the server using AJAX and update the dashboard numbers
  fetchDashboardData();
};

function fetchDashboardData() {
  fetch('fetch_dashboard_data.php')
      .then(response => response.json())
      .then(data => {
          document.getElementById('total-shops').innerText = data.totalShops;
          document.getElementById('total-users').innerText = data.totalUsers;
          document.getElementById('total-complaints').innerText = data.totalComplaints;
          document.getElementById('total-orders').innerText = data.totalOrders;
      })
      .catch(error => console.error('Error fetching dashboard data:', error));
}
