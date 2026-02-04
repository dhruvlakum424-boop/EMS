<?php
// Database connection details
$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "employee_management_system"; 

// Connection create karein
$conn = new mysqli($servername, $username, $password, $dbname);

// Connection check karein
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Input data ko sanitize karein
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Database mein insert karne ki query
    $sql = "INSERT INTO contact_messages (full_name, email, subject, message) 
            VALUES ('$full_name', '$email', '$subject', '$message')";

    if ($conn->query($sql) === TRUE) {
        // Success message
        echo "<script>
            alert('Shukriya! Aapka message humein mil gaya hai.');
            window.location.href = 'index_contact.php';
        </script>";
        exit();
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | NexusCorp - Get In Touch</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }

        .input-focus:focus {
            border-color: #3b82f6;
            ring: 2px;
            ring-color: #bfdbfe;
        }
    </style>
</head>

<body class="bg-gray-50">

    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center">
                <div
                    class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3 text-white font-bold text-xl shadow-lg">
                    N</div>
                <span class="text-2xl font-bold text-gray-800">Nexus<span class="text-blue-600">Corp</span></span>
            </div>
            <div class="hidden md:flex space-x-8 items-center">
                <a href="index.php" class="text-gray-600 hover:text-blue-600 font-medium">Home</a>
                <a href="index_about.php" class="text-gray-600 hover:text-blue-600 font-medium">About</a>
                <a href="index_services.php" class="text-gray-600 hover:text-blue-600 font-medium">Services</a>
                <a href="index_contact.php" class="text-blue-600 font-bold border-b-2 border-blue-600">Contact</a>
            </div>
        </div>
    </nav>

    <section class="gradient-bg text-white py-20">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4">Let's Start a Conversation</h1>
            <p class="text-blue-100 max-w-2xl mx-auto text-lg">Aapke paas koi idea hai ya koi sawal? Humari team hamesha
                aapki madad ke liye taiyar hai.</p>
        </div>
    </section>

    <div class="container mx-auto px-4 -mt-12 mb-20">
        <div
            class="max-w-6xl mx-auto bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col lg:flex-row border border-gray-100">

            <div class="lg:w-1/3 bg-blue-600 p-12 text-white relative">
                <h2 class="text-3xl font-bold mb-8">Contact Information</h2>
                <p class="text-blue-100 mb-10">Humse judne ke liye niche diye gaye tareeqon ka istemal karein.</p>

                <div class="space-y-8">
                    <div class="flex items-start space-x-4">
                        <div class="bg-blue-500 p-3 rounded-lg"><i class="fas fa-phone-alt"></i></div>
                        <div>
                            <p class="text-sm text-blue-200">Phone Number</p>
                            <p class="text-lg font-semibold">+91 98765 43210</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="bg-blue-500 p-3 rounded-lg"><i class="fas fa-envelope"></i></div>
                        <div>
                            <p class="text-sm text-blue-200">Email Address</p>
                            <p class="text-lg font-semibold">support@nexuscorp.com</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="bg-blue-500 p-3 rounded-lg"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <p class="text-sm text-blue-200">Office Location</p>
                            <p class="text-lg font-semibold">Sector 62, Noida, UP, India</p>
                        </div>
                    </div>
                </div>

                <div class="mt-16 flex space-x-5">
                    <a href="#"
                        class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center hover:bg-white hover:text-blue-600 transition"><i
                            class="fab fa-facebook-f"></i></a>
                    <a href="#"
                        class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center hover:bg-white hover:text-blue-600 transition"><i
                            class="fab fa-linkedin-in"></i></a>
                    <a href="#"
                        class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center hover:bg-white hover:text-blue-600 transition"><i
                            class="fab fa-twitter"></i></a>
                    <a href="#"
                        class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center hover:bg-white hover:text-blue-600 transition"><i
                            class="fab fa-instagram"></i></a>
                </div>
            </div>

            <div class="lg:w-2/3 p-12">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Send us a Message</h2>
                <p class="text-gray-500 mb-8">Form bhariye, hum 24 ghante ke andar aapse rabta karenge.</p>

                <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-700">Full Name</label>
                        <input type="text" name="full_name" required placeholder="e.g. Rahul Sharma"
                            class="w-full p-4 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-blue-600 transition">
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-gray-700">Email Address</label>
                        <input type="email" name="email" required placeholder="rahul@example.com"
                            class="w-full p-4 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-blue-600 transition">
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="text-sm font-bold text-gray-700">Subject</label>
                        <select name="subject"
                            class="w-full p-4 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-blue-600 transition">
                            <option value="General Inquiry">General Inquiry</option>
                            <option value="Technical Support">Technical Support</option>
                            <option value="Business Partnership">Business Partnership</option>
                        </select>
                    </div>

                    <div class="md:col-span-2 space-y-2">
                        <label class="text-sm font-bold text-gray-700">Message</label>
                        <textarea name="message" required placeholder="Write your message here..." rows="5"
                            class="w-full p-4 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-blue-600 transition"></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <button type="submit"
                            class="bg-blue-600 text-white px-10 py-4 rounded-xl font-bold hover:bg-blue-700 transition w-full md:w-auto shadow-lg">
                            Send Message <i class="fas fa-paper-plane ml-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <section class="container mx-auto px-4 mb-20">
        <div
            class="rounded-3xl overflow-hidden shadow-xl border border-gray-200 h-96 grayscale hover:grayscale-0 transition duration-500">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3502.562064136281!2d77.37691407550004!3d28.61291197567311!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390ce560946d0a79%3A0x63d0859a7245b73d!2sSector%2062%2C%20Noida%2C%20Uttar%20Pradesh!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin"
                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </section>

    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4 text-center">
            <div class="flex items-center justify-center mb-6">
                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                    <span class="text-white font-bold">N</span>
                </div>
                <span class="text-xl font-bold tracking-wider">Nexus<span class="text-blue-400">Corp</span></span>
            </div>
            <p class="text-gray-500 mb-8 max-w-md mx-auto italic">"Connecting businesses with the technology of
                tomorrow."</p>
            <div class="border-t border-gray-800 pt-8">
                <p class="text-gray-500 text-sm">© 2026 NexusCorp. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>

</html>