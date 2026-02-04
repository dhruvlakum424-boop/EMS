<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexusCorp | Professional Solutions for Global Business</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }

        .card-hover:hover {
            transform: translateY(-10px);
            transition: all 0.3s ease;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        /* Login Card Specific */
        .login-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .login-card:hover {
            border-color: #3b82f6;
            background: white;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                        <span class="text-white font-bold text-xl">N</span>
                    </div>
                    <span class="text-2xl font-bold text-gray-900 tracking-tight">Nexus<span
                            class="text-blue-600">Corp</span></span>
                </div>
                <div class="hidden md:flex space-x-10 items-center">
                    <a href="index.php" class="text-gray-600 hover:text-blue-600 font-semibold">Home</a>
                    <a href="index_about.php" class="text-gray-600 hover:text-blue-600 font-semibold">About</a>
                    <a href="index_services.php" class="text-gray-600 hover:text-blue-600 font-semibold">Services</a>
                    <a href="index_contact.php" class="text-gray-600 hover:text-blue-600 font-semibold">Contact</a>
                    <a href="#login-portal"
                        class="text-blue-600 border-2 border-blue-600 px-6 py-2 rounded-full font-bold hover:bg-blue-600 hover:text-white transition">Portals</a>
                </div>
                <button id="mobile-menu-button" class="md:hidden text-gray-700"><i
                        class="fas fa-bars text-2xl"></i></button>
            </div>
        </div>
    </nav>

    <section class="gradient-bg text-white py-20 md:py-32 overflow-hidden relative">
        <div class="container mx-auto px-4 flex flex-col items-center relative z-10 text-center">
            <span class="bg-blue-400 bg-opacity-30 text-white px-4 py-1 rounded-full text-sm font-bold mb-6">Trusted by
                500+ Companies</span>
            <h1 class="text-5xl md:text-7xl font-extrabold mb-8 leading-tight">Elevate Your Business <br><span
                    class="text-blue-200">With Next-Gen Tech</span></h1>
            <p class="text-xl md:text-2xl text-blue-100 mb-12 max-w-3xl leading-relaxed">
                We provide end-to-end digital solutions, from cloud infrastructure to advanced business analytics. Let's
                transform your vision into a scalable reality.
            </p>
            <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-6">
                <a href="index_contact.php"
                    class="bg-white text-blue-700 px-10 py-4 rounded-full font-bold text-lg hover:bg-gray-100 transition shadow-2xl">Start
                    a Project</a>
                <a href="#login-portal"
                    class="border-2 border-white text-white px-10 py-4 rounded-full font-bold text-lg hover:bg-white hover:text-blue-700 transition">Access
                    Portal</a>
            </div>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container mx-auto px-4 flex flex-col md:flex-row items-center gap-16">
            <div class="md:w-1/2 relative">
                <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=800&q=80"
                    alt="About NexusCorp" class="rounded-2xl shadow-2xl">
                <div class="absolute -bottom-6 -right-6 bg-blue-600 p-8 rounded-2xl hidden md:block text-white">
                    <p class="text-4xl font-bold">12+</p>
                    <p class="text-sm">Years of Innovation</p>
                </div>
            </div>
            <div class="md:w-1/2">
                <h2 class="text-blue-600 font-bold uppercase tracking-widest mb-4">Who We Are</h2>
                <h3 class="text-4xl font-bold text-gray-900 mb-6">We are more than just a technology provider.</h3>
                <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                    NexusCorp was founded on the principle of making high-end technology accessible to everyone. We
                    specialize in helping businesses migrate to the digital age without the technical headache.
                </p>
                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div class="flex items-center space-x-3"><i class="fas fa-check-circle text-blue-600"></i> <span
                            class="font-medium">Global Standards</span></div>
                    <div class="flex items-center space-x-3"><i class="fas fa-check-circle text-blue-600"></i> <span
                            class="font-medium">Expert Consultants</span></div>
                    <div class="flex items-center space-x-3"><i class="fas fa-check-circle text-blue-600"></i> <span
                            class="font-medium">24/7 Security</span></div>
                    <div class="flex items-center space-x-3"><i class="fas fa-check-circle text-blue-600"></i> <span
                            class="font-medium">Scalable Design</span></div>
                </div>
                <a href="index_about.php"
                    class="text-blue-600 font-bold border-b-2 border-blue-600 pb-1 hover:text-blue-800 transition">Discover
                    our full story</a>
            </div>
        </div>
    </section>

    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 text-center mb-16">
            <h2 class="text-4xl font-bold mb-4">What We Offer</h2>
            <div class="w-20 h-1.5 bg-blue-600 mx-auto"></div>
        </div>
        <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="card-hover bg-white p-10 rounded-3xl border border-gray-100 text-left">
                <div
                    class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 text-blue-600 text-3xl">
                    <i class="fas fa-chart-pie"></i></div>
                <h3 class="text-2xl font-bold mb-4">Strategic Analytics</h3>
                <p class="text-gray-600 mb-6">Gain deep insights into your market and customer behavior using our
                    AI-driven analytics tools.</p>
                <a href="#" class="text-blue-600 font-bold hover:underline">Learn more <i
                        class="fas fa-arrow-right ml-2"></i></a>
            </div>
            <div class="card-hover bg-white p-10 rounded-3xl border border-gray-100 text-left">
                <div
                    class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 text-blue-600 text-3xl">
                    <i class="fas fa-server"></i></div>
                <h3 class="text-2xl font-bold mb-4">Cloud Infrastructure</h3>
                <p class="text-gray-600 mb-6">Seamlessly migrate your data and workflows to high-performance, secure
                    cloud environments.</p>
                <a href="#" class="text-blue-600 font-bold hover:underline">Learn more <i
                        class="fas fa-arrow-right ml-2"></i></a>
            </div>
            <div class="card-hover bg-white p-10 rounded-3xl border border-gray-100 text-left">
                <div
                    class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 text-blue-600 text-3xl">
                    <i class="fas fa-shield-virus"></i></div>
                <h3 class="text-2xl font-bold mb-4">Cyber Defense</h3>
                <p class="text-gray-600 mb-6">Protect your business from evolving threats with our world-class
                    cybersecurity protocols.</p>
                <a href="#" class="text-blue-600 font-bold hover:underline">Learn more <i
                        class="fas fa-arrow-right ml-2"></i></a>
            </div>
        </div>
    </section>

    <section id="login-portal" class="py-24 bg-white border-t border-gray-100">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-blue-600 font-bold uppercase tracking-widest mb-4">Secure Access</h2>
                <h3 class="text-4xl font-bold text-gray-900">NexusCorp Portals</h3>
                <p class="text-gray-500 mt-4">Apne role ke hisaab se portal choose karein.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <div class="login-card bg-gray-50 p-10 rounded-3xl text-center">
                    <div
                        class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 text-white text-3xl shadow-lg">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h4 class="text-2xl font-bold mb-2">Admin Portal</h4>
                    <p class="text-gray-600 mb-8">Management, employees aur system configurations ko control karein.</p>
                    <a href="admin/admin_login.php"
                        class="block w-full bg-blue-600 text-white py-4 rounded-xl font-bold hover:bg-blue-700 transition shadow-md">Admin
                        Login</a>
                </div>

                <div class="login-card bg-gray-50 p-10 rounded-3xl text-center">
                    <div
                        class="w-20 h-20 bg-white border-2 border-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 text-blue-600 text-3xl shadow-lg">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h4 class="text-2xl font-bold mb-2">Employee Portal</h4>
                    <p class="text-gray-600 mb-8">Attendance, leave requests aur personal dashboard access karein.</p>
                    <a href="employee/employee_login.php"
                        class="block w-full border-2 border-blue-600 text-blue-600 py-4 rounded-xl font-bold hover:bg-blue-600 hover:text-white transition">Employee
                        Login</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-white py-20">
        <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-4 gap-12">
            <div>
                <span class="text-3xl font-bold text-white mb-6 block">Nexus<span
                        class="text-blue-500">Corp</span></span>
                <p class="text-gray-400 leading-relaxed mb-6">Leading global provider of digital business solutions and
                    strategic technology innovations.</p>
                <div class="flex space-x-4">
                    <a href="#"
                        class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-blue-600 transition"><i
                            class="fab fa-facebook-f"></i></a>
                    <a href="#"
                        class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-blue-600 transition"><i
                            class="fab fa-linkedin-in"></i></a>
                    <a href="#"
                        class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-blue-600 transition"><i
                            class="fab fa-twitter"></i></a>
                </div>
            </div>
            <div>
                <h4 class="text-xl font-bold mb-6">Quick Links</h4>
                <ul class="space-y-4 text-gray-400">
                    <li><a href="index.php" class="hover:text-blue-500">Home</a></li>
                    <li><a href="index_about.php" class="hover:text-blue-500">Our Story</a></li>
                    <li><a href="index_services.php" class="hover:text-blue-500">Services</a></li>
                    <li><a href="index_contact.php" class="hover:text-blue-500">Contact Us</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-xl font-bold mb-6">Services</h4>
                <ul class="space-y-4 text-gray-400">
                    <li><a href="#" class="hover:text-blue-500">Cloud Hosting</a></li>
                    <li><a href="#" class="hover:text-blue-500">AI Analytics</a></li>
                    <li><a href="#" class="hover:text-blue-500">Security Audit</a></li>
                    <li><a href="#" class="hover:text-blue-500">App Development</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-xl font-bold mb-6">Newsletter</h4>
                <p class="text-gray-400 mb-6">Get latest industry updates in your inbox.</p>
                <div class="flex flex-col space-y-3">
                    <input type="email" placeholder="Email Address"
                        class="bg-gray-800 border-0 rounded-lg p-3 text-white focus:ring-2 focus:ring-blue-600 outline-none">
                    <button
                        class="bg-blue-600 hover:bg-blue-700 py-3 rounded-lg font-bold transition">Subscribe</button>
                </div>
            </div>
        </div>
        <div class="container mx-auto px-4 border-t border-gray-800 mt-16 pt-8 text-center text-gray-500">
            <p>&copy; ☯ 2026 NexusCorp. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Mobile Menu Script
        const btn = document.getElementById('mobile-menu-button');
        btn.addEventListener('click', () => {
            // Logic for mobile menu toggle
            alert('Menu functionality can be added here.');
        });
    </script>
</body>

</html>