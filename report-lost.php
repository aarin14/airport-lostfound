<?php
session_start();
if (!isset($_SESSION['username'])) {
  header('Location: login.html');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Report Lost Item - Airport Lost & Found</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
  <!-- Header -->
  <header class="bg-gradient-to-r from-blue-900 to-indigo-900 text-white shadow-lg">
    <div class="container mx-auto px-6 py-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <a href="index.php" class="text-blue-200 hover:text-white transition duration-200">
            <i class="fas fa-arrow-left text-xl"></i>
          </a>
          <div class="bg-white bg-opacity-20 p-3 rounded-full">
            <i class="fas fa-plus text-2xl"></i>
          </div>
          <div>
            <h1 class="text-2xl font-bold">Report Lost Item</h1>
            <p class="text-blue-200 text-sm">Add new lost item to the system</p>
          </div>
        </div>
        <div class="flex items-center space-x-4">
          <span class="text-blue-200">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
          <a href="backend/logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200">
            <i class="fas fa-sign-out-alt mr-2"></i>Logout
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="container mx-auto px-6 py-8">
    <div class="max-w-4xl mx-auto">
      <!-- Form Card -->
      <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white">
          <h2 class="text-2xl font-bold">Lost Item Report Form</h2>
          <p class="text-blue-100 mt-2">Please fill in all the details about the lost item and passenger information</p>
        </div>
        
        <form class="p-8" action="backend/save_lost.php" method="POST" enctype="multipart/form-data">
          <!-- Passenger Information Section -->
          <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
              <i class="fas fa-user mr-2 text-blue-600"></i>
              Passenger Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Passenger Name *</label>
                <input type="text" name="passenger_name" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                       placeholder="Full name of the passenger">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                <input type="tel" name="passenger_phone" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                       placeholder="+1 (555) 123-4567">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" name="passenger_email" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                       placeholder="passenger@email.com">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Flight Number</label>
                <input type="text" name="flight_number" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                       placeholder="e.g., AA123">
              </div>
            </div>
          </div>

          <!-- Item Information Section -->
          <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
              <i class="fas fa-box mr-2 text-blue-600"></i>
              Item Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Item Description *</label>
                <textarea name="description" required rows="3" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                          placeholder="Detailed description of the lost item (color, brand, size, distinctive features)"></textarea>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select name="category" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                  <option value="">Select category</option>
                  <option value="Electronics">Electronics</option>
                  <option value="Jewelry">Jewelry</option>
                  <option value="Clothing">Clothing</option>
                  <option value="Luggage">Luggage</option>
                  <option value="Documents">Documents</option>
                  <option value="Wallets">Wallets & Bags</option>
                  <option value="Toys">Toys</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Value</label>
                <input type="text" name="estimated_value" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                       placeholder="e.g., $500">
              </div>
            </div>
          </div>

          <!-- Location & Date Section -->
          <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
              <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
              Location & Date Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Date Lost *</label>
                <input type="date" name="date_of_loss" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Time Lost (Approximate)</label>
                <input type="time" name="time_of_loss" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Location Lost *</label>
                <input type="text" name="location" required 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                       placeholder="e.g., Terminal 1, Gate A5, Security Checkpoint, Restroom, etc.">
              </div>
            </div>
          </div>

          <!-- Image Upload Section -->
          <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
              <i class="fas fa-camera mr-2 text-blue-600"></i>
              Item Image (Optional)
            </h3>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition duration-200">
              <input type="file" name="image" accept="image/*" 
                     class="hidden" id="image-upload">
              <label for="image-upload" class="cursor-pointer">
                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">Click to upload an image of the item</p>
                <p class="text-sm text-gray-500 mt-2">Supports: JPG, PNG, GIF (Max 5MB)</p>
              </label>
            </div>
          </div>

          <!-- Additional Notes Section -->
          <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
              <i class="fas fa-sticky-note mr-2 text-blue-600"></i>
              Additional Notes
            </h3>
            <textarea name="notes" rows="3" 
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                      placeholder="Any additional information, special instructions, or notes about the item or passenger"></textarea>
          </div>

          <!-- Submit Buttons -->
          <div class="flex flex-col sm:flex-row gap-4">
            <button type="submit" 
                    class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-4 px-6 rounded-lg font-semibold transition duration-200 flex items-center justify-center space-x-2">
              <i class="fas fa-save"></i>
              <span>Submit Lost Item Report</span>
            </button>
            <a href="index.php" 
               class="flex-1 bg-gray-500 hover:bg-gray-600 text-white py-4 px-6 rounded-lg font-semibold transition duration-200 flex items-center justify-center space-x-2">
              <i class="fas fa-times"></i>
              <span>Cancel</span>
            </a>
          </div>
        </form>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-gray-800 text-gray-300 py-6 mt-12">
    <div class="container mx-auto px-6 text-center">
      <p>&copy; 2024 Airport Lost & Found System. All rights reserved.</p>
    </div>
  </footer>
</body>
</html> 