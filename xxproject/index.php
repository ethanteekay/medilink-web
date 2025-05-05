<?php
require_once 'auth.php';
require_once 'appointments.php';
require_once 'notifications.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $user_type = $_POST['user_type'];
        if (login($email, $password, $user_type)) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid credentials!";
        }
    } elseif ($_POST['action'] === 'signup') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $user_type = $_POST['user_type'];
        if (signup($name, $email, $password, $user_type)) {
            login($email, $password, $user_type);
            header("Location: index.php");
            exit();
        } else {
            $error = "Signup failed!";
        }
    } elseif ($_POST['action'] === 'book_appointment') {
        $doctor_id = $_POST['doctor'];
        $date = $_POST['appointment_date'];
        $time = $_POST['time_slot'];
        $reason = $_POST['reason'];
        $patient_id = getCurrentUser()['id'];
        if (bookAppointment($patient_id, $doctor_id, $date, $time, $reason)) {
            $success = "Appointment booked successfully!";
        } else {
            $error = "Failed to book appointment!";
        }
    } elseif ($_POST['action'] === 'confirm_appointment') {
        $appointment_id = $_POST['appointment_id'];
        if (confirmAppointment($appointment_id)) {
            $success = "Appointment confirmed!";
        } else {
            $error = "Failed to confirm appointment!";
        }
    }
}

$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MediLink - Online Healthcare Appointment System</title>
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: linear-gradient(135deg, #f7f9fc 0%, #e3efff 100%);
      min-height: 100vh;
    }
    header {
      background: linear-gradient(to right, #2f80ed, #1c60b3);
      color: white;
      padding: 20px;
      text-align: center;
      position: relative;
    }
    .logo {
      width: 80px;
      height: 80px;
      position: absolute;
      left: 20px;
      top: 10px;
    }
    nav {
      background-color: rgba(28, 96, 179, 0.9);
      padding: 15px;
      display: flex;
      justify-content: center;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    nav a {
      color: white;
      text-decoration: none;
      margin: 0 15px;
      font-weight: bold;
      padding: 8px 15px;
      border-radius: 5px;
      transition: background-color 0.3s;
    }
    nav a:hover {
      background-color: rgba(255, 255, 255, 0.2);
    }
    .banner {
      background: url('https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center center;
      background-size: cover;
      padding: 100px 40px;
      text-align: center;
      color: white;
      position: relative;
    }
    .banner::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
    }
    .banner-content {
      position: relative;
      z-index: 1;
    }
    .banner h1 {
      margin-bottom: 20px;
      font-size: 2.5em;
    }
    .cta-button {
      padding: 12px 30px;
      background-color: #2f80ed;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
      transition: background-color 0.3s;
    }
    .cta-button:hover {
      background-color: #1c60b3;
    }
    .content {
      padding: 40px;
      text-align: center;
    }
    .home-section {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 30px;
      padding: 40px;
    }
    .feature-card {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      transition: transform 0.3s;
    }
    .feature-card:hover {
      transform: translateY(-5px);
    }
    .feature-icon {
      width: 60px;
      height: 60px;
      margin-bottom: 20px;
    }
    .contact-section {
      background: white;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      max-width: 800px;
      margin: 0 auto;
    }
    .contact-info {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-top: 30px;
    }
    .contact-item {
      text-align: center;
      padding: 20px;
      background: #f7f9fc;
      border-radius: 8px;
    }
    .contact-icon {
      width: 40px;
      height: 40px;
      margin-bottom: 10px;
    }
    footer {
      background-color:rgb(47, 151, 237);
      color: white;
      text-align: center;
      padding: 15px;
      position: fixed;
      bottom: 0;
      width: 100%;
    }
    .form-section, .dashboard {
      max-width: 500px;
      margin: auto;
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      margin-bottom: 40px;
    }
    .form-section h2, .dashboard h2 {
      margin-bottom: 20px;
    }
    .form-section input, .form-section select, .form-section textarea {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    .form-section button, .dashboard button {
      width: 100%;
      padding: 10px;
      background-color: #2f80ed;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .appointment-entry {
      text-align: left;
      background: #f7f9fc;
      padding: 15px;
      margin-bottom: 15px;
      border-radius: 8px;
      border-left: 4px solid #2f80ed;
    }
    .appointment-entry strong {
      display: block;
      margin-bottom: 5px;
    }
    .dashboard {
      display: none;
      max-width: 800px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .appointment-list {
      margin-top: 20px;
    }
    .book-appointment-btn {
      display: inline-block;
      padding: 10px 20px;
      background-color: #2f80ed;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      margin-top: 20px;
      transition: background-color 0.3s;
    }
    .book-appointment-btn:hover {
      background-color: #1c60b3;
    }
    .error, .success {
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 4px;
    }
    .error {
      background-color: #ffe6e6;
      color: #cc0000;
    }
    .success {
      background-color: #e6ffe6;
      color: #006600;
    }
  </style>
</head>
<body>
  <header>
    <img src="https://cdn-icons-png.flaticon.com/512/2965/2965879.png" alt="MediLink Logo" class="logo">
    <h1>MediLink</h1>
    <p>Online Healthcare Appointment Booking System</p>
  </header>

  <nav>
    <a href="#home">Home</a>
    <a href="#book">Book Appointment</a>
    <?php if ($user): ?>
      <a href="notifications.php">Notifications</a>
      <a href="?action=logout">Logout</a>
    <?php else: ?>
      <a href="#login">Login</a>
      <a href="#signup">Signup</a>
    <?php endif; ?>
    <a href="#contact">Contact</a>
  </nav>

  <?php if (isset($error)): ?>
    <div class="error"><?php echo $error; ?></div>
  <?php endif; ?>
  <?php if (isset($success)): ?>
    <div class="success"><?php echo $success; ?></div>
  <?php endif; ?>

  <div class="banner">
    <div class="banner-content">
      <h1>Book Your Appointment Online</h1>
      <p>Anytime, Anywhere</p>
      <a class="cta-button" href="#book">Book Now</a>
    </div>
  </div>

  <div class="content" id="home">
    <div class="home-section">
      <div class="feature-card">
        <img src="https://cdn-icons-png.flaticon.com/512/2965/2965879.png" alt="Easy Booking" class="feature-icon">
        <h3>Easy Booking</h3>
        <p>Book appointments with just a few clicks from anywhere</p>
      </div>
      <div class="feature-card">
        <img src="https://cdn-icons-png.flaticon.com/512/2965/2965879.png" alt="24/7 Access" class="feature-icon">
        <h3>24/7 Access</h3>
        <p>Access your medical records and appointments anytime</p>
      </div>
      <div class="feature-card">
        <img src="https://cdn-icons-png.flaticon.com/512/2965/2965879.png" alt="Expert Doctors" class="feature-icon">
        <h3>Expert Doctors</h3>
        <p>Connect with experienced healthcare professionals</p>
      </div>
    </div>
  </div>

  <!-- Login Form -->
  <?php if (!$user): ?>
    <div class="form-section" id="login">
      <h2>Login</h2>
      <form method="POST">
        <input type="hidden" name="action" value="login">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="user_type" required>
          <option value="patient">Patient</option>
          <option value="doctor">Doctor</option>
        </select>
        <button type="submit">Login</button>
      </form>
    </div>

    <!-- Signup Form -->
    <div class="form-section" id="signup">
      <h2>Signup</h2>
      <form method="POST">
        <input type="hidden" name="action" value="signup">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="user_type" required>
          <option value="patient">Patient</option>
          <option value="doctor">Doctor</option>
        </select>
        <button type="submit">Signup</button>
      </form>
    </div>
  <?php endif; ?>

  <!-- Book Appointment Form -->
  <div class="form-section" id="book">
    <h2>Book Appointment</h2>
    <?php if ($user && $user['user_type'] === 'patient'): ?>
      <form method="POST">
        <input type="hidden" name="action" value="book_appointment">
        <select name="doctor" id="doctorSelect" required>
          <option value="">Select Doctor</option>
          <?php
          $stmt = $pdo->query("SELECT id, name FROM users WHERE user_type = 'doctor'");
          while ($doctor = $stmt->fetch(PDO::FETCH_ASSOC)) {
              echo "<option value='{$doctor['id']}'>Dr. {$doctor['name']}</option>";
          }
          ?>
        </select>
        <input type="date" name="appointment_date" id="appointmentDate" required>
        <select name="time_slot" id="timeSlot" required>
          <option value="">Select Time</option>
          <option value="09:00">09:00 AM</option>
          <option value="10:00">10:00 AM</option>
          <option value="11:00">11:00 AM</option>
          <option value="14:00">02:00 PM</option>
          <option value="15:00">03:00 PM</option>
        </select>
        <input type="text" name="reason" placeholder="Reason for Appointment" required>
        <button type="submit">Book Appointment</button>
      </form>
    <?php else: ?>
      <p>Please <a href="#login">login</a> as a patient to book an appointment.</p>
    <?php endif; ?>
  </div>

  <!-- Patient Dashboard -->
  <?php if ($user && $user['user_type'] === 'patient'): ?>
    <div class="dashboard" id="patientDashboard" style="display: block;">
      <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
      <div class="appointment-list">
        <h3>Your Appointments</h3>
        <div id="patientAppointments">
          <?php
          $appointments = getAppointments($user['id'], 'patient');
          foreach ($appointments as $appt): ?>
            <div class="appointment-entry">
              <strong>Doctor:</strong> <?php echo htmlspecialchars($appt['doctor_name']); ?><br>
              <strong>Date:</strong> <?php echo $appt['appointment_date']; ?><br>
              <strong>Time:</strong> <?php echo $appt['time_slot']; ?><br>
              <strong>Reason:</strong> <?php echo htmlspecialchars($appt['reason']); ?><br>
              <strong>Status:</strong> <?php echo ucfirst($appt['status']); ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <a href="#book" class="book-appointment-btn">Book New Appointment</a>
    </div>
  <?php endif; ?>

  <!-- Doctor Dashboard -->
  <?php if ($user && $user['user_type'] === 'doctor'): ?>
    <div class="dashboard" id="doctorDashboard" style="display: block;">
      <h2>Welcome, Dr. <?php echo htmlspecialchars($user['name']); ?>!</h2>
      <div class="appointment-list">
        <h3>Today's Appointments</h3>
        <div id="doctorAppointments">
          <?php
          $appointments = getAppointments($user['id'], 'doctor');
          foreach ($appointments as $appt): ?>
            <div class="appointment-entry">
              <strong>Patient:</strong> <?php echo htmlspecialchars($appt['patient_name']); ?><br>
              <strong>Date:</strong> <?php echo $appt['appointment_date']; ?><br>
              <strong>Time:</strong> <?php echo $appt['time_slot']; ?><br>
              <strong>Reason:</strong> <?php echo htmlspecialchars($appt['reason']); ?><br>
              <strong>Status:</strong> <?php echo ucfirst($appt['status']); ?><br>
              <?php if ($appt['status'] === 'pending'): ?>
                <form method="POST" style="margin-top: 10px;">
                  <input type="hidden" name="action" value="confirm_appointment">
                  <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">
                  <button type="submit">Confirm Appointment</button>
                </form>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <div class="content" id="contact">
    <div class="contact-section">
      <h2>Contact Us</h2>
      <div class="contact-info">
        <div class="contact-item">
          <img src="https://cdn-icons-png.flaticon.com/512/2965/2965879.png" alt="Location" class="contact-icon">
          <h3>Location</h3>
          <p>123 Medical Center Drive<br>Kampala, Uganda</p>
        </div>
        <div class="contact-item">
          <img src="https://cdn-icons-png.flaticon.com/512/2965/2965879.png" alt="Phone" class="contact-icon">
          <h3>Phone</h3>
          <p>+256-123-456-789</p>
        </div>
        <div class="contact-item">
          <img src="https://cdn-icons-png.flaticon.com/512/2965/2965879.png" alt="Email" class="contact-icon">
          <h3>Email</h3>
          <p><a href="mailto:support@medilink.com">support@medilink.com</a></p>
        </div>
      </div>
      <form class="contact-form" style="margin-top: 30px;">
        <input type="text" placeholder="Your Name" style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">
        <input type="email" placeholder="Your Email" style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px;">
        <textarea placeholder="Your Message" style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; height: 150px;"></textarea>
        <button type="submit" style="width: 100%; padding: 10px; background-color: #2f80ed; color: white; border: none; border-radius: 4px; cursor: pointer;">Send Message</button>
      </form>
    </div>
  </div>

  <footer>
    <p>&copy; 2025 MediLink | <a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
    <p>Email: <a href="mailto:support@medilink.com">support@medilink.com</a> | Phone: +256-723-456-789</p>
  </footer>

  <?php if (isset($_GET['action']) && $_GET['action'] === 'logout'): logout(); endif; ?>
</body>
</html>