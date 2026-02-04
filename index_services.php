<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services | NexusCorp - Advanced Tech Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }

        .service-card:hover {
            transform: translateY(-10px);
            transition: 0.3s;
            box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.1);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        }

        .step-number {
            font-size: 4rem;
            opacity: 0.1;
            position: absolute;
            right: 10px;
            top: -10px;
            font-weight: 800;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800">

    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex justify-between items-center">
            <div class="flex items-center">
                <div
                    class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mr-3 text-white font-bold">
                    N</div>
                <span class="text-2xl font-bold text-gray-800">Nexus<span class="text-blue-600">Corp</span></span>
            </div>
            <div class="hidden md:flex space-x-8">
                <a href="index.php" class="text-gray-700 hover:text-blue-600 font-medium">Home</a>
                <a href="index_about.php" class="text-gray-700 hover:text-blue-600 font-medium">About</a>
                <a href="index_services.php" class="text-blue-600 font-bold border-b-2 border-blue-600">Services</a>
                <a href="index_contact.php" class="text-gray-700 hover:text-blue-600 font-medium">Contact</a>
            </div>

        </div>
    </nav>

    <section class="gradient-bg text-white py-20 text-center">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl md:text-6xl font-extrabold mb-4">Our Premium Expertise</h1>
            <p class="mt-4 text-blue-100 max-w-2xl mx-auto text-lg">We don't just provide services; we deliver
                measurable results that push your business towards digital excellence.</p>
        </div>
    </section>

    <section class="py-20 container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold mb-4">Core Competencies</h2>
            <div class="w-24 h-1 bg-blue-600 mx-auto"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            <div class="service-card bg-white p-10 rounded-2xl shadow-sm border border-gray-100 transition">
                <i class="fas fa-laptop-code text-5xl text-blue-600 mb-6"></i>
                <h3 class="text-2xl font-bold mb-3">Web Development</h3>
                <p class="text-gray-600">Custom web applications, e-commerce solutions, and corporate portals built with
                    the latest frameworks like React and Node.js.</p>
            </div>
            <div class="service-card bg-white p-10 rounded-2xl shadow-sm border border-gray-100 transition">
                <i class="fas fa-mobile-alt text-5xl text-blue-600 mb-6"></i>
                <h3 class="text-2xl font-bold mb-3">App Development</h3>
                <p class="text-gray-600">Native and Cross-platform (Flutter/React Native) mobile apps that provide a
                    seamless user experience on all devices.</p>
            </div>
            <div class="service-card bg-white p-10 rounded-2xl shadow-sm border border-gray-100 transition">
                <i class="fas fa-bullhorn text-5xl text-blue-600 mb-6"></i>
                <h3 class="text-2xl font-bold mb-3">Digital Marketing</h3>
                <p class="text-gray-600">Data-driven SEO, SEM, and Social Media strategies designed to increase your
                    brand's visibility and conversion rates.</p>
            </div>
            <div class="service-card bg-white p-10 rounded-2xl shadow-sm border border-gray-100 transition">
                <i class="fas fa-cloud-upload-alt text-5xl text-blue-600 mb-6"></i>
                <h3 class="text-2xl font-bold mb-3">Cloud Solutions</h3>
                <p class="text-gray-600">AWS and Azure migration, cloud security, and serverless architecture to make
                    your business truly scalable.</p>
            </div>
            <div class="service-card bg-white p-10 rounded-2xl shadow-sm border border-gray-100 transition">
                <i class="fas fa-paint-brush text-5xl text-blue-600 mb-6"></i>
                <h3 class="text-2xl font-bold mb-3">UI/UX Design</h3>
                <p class="text-gray-600">User-centric designs that are not only beautiful but also intuitive, ensuring
                    higher customer retention.</p>
            </div>
            <div class="service-card bg-white p-10 rounded-2xl shadow-sm border border-gray-100 transition">
                <i class="fas fa-headset text-5xl text-blue-600 mb-6"></i>
                <h3 class="text-2xl font-bold mb-3">24/7 Support</h3>
                <p class="text-gray-600">Dedicated maintenance team to ensure your digital assets are always up and
                    running without glitches.</p>
            </div>
        </div>
    </section>

    <section class="py-20 bg-blue-900 text-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-16">How We Work</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="relative bg-blue-800 p-8 rounded-xl overflow-hidden">
                    <span class="step-number text-white">01</span>
                    <h4 class="text-xl font-bold mb-4">Discovery</h4>
                    <p class="text-blue-100">Hum aapke requirements ko samajhte hain aur ek road-map taiyar karte hain.
                    </p>
                </div>
                <div class="relative bg-blue-800 p-8 rounded-xl overflow-hidden">
                    <span class="step-number text-white">02</span>
                    <h4 class="text-xl font-bold mb-4">Strategy</h4>
                    <p class="text-blue-100">Sahi technology aur design approach select ki jaati hai.</p>
                </div>
                <div class="relative bg-blue-800 p-8 rounded-xl overflow-hidden">
                    <span class="step-number text-white">03</span>
                    <h4 class="text-xl font-bold mb-4">Execution</h4>
                    <p class="text-blue-100">Humari expert team coding aur development start karti hai.</p>
                </div>
                <div class="relative bg-blue-800 p-8 rounded-xl overflow-hidden">
                    <span class="step-number text-white">04</span>
                    <h4 class="text-xl font-bold mb-4">Launch</h4>
                    <p class="text-blue-100">Testing ke baad project live hota hai aur hum support dete hain.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 container mx-auto px-4 max-w-4xl">
        <h2 class="text-3xl font-bold text-center mb-12">Frequently Asked Questions</h2>
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <h4 class="font-bold text-lg mb-2 text-blue-600">Project delivery mein kitna time lagta hai?</h4>
                <p class="text-gray-600">Ek standard web project mein usually 4 se 8 hafte lagte hain, depend karta hai
                    project ki complexity par.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <h4 class="font-bold text-lg mb-2 text-blue-600">Kya aap custom packages offer karte hain?</h4>
                <p class="text-gray-600">Ji haan, hum har business ki unique needs ke hisaab se customized plans banate
                    hain.</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-sm border">
                <h4 class="font-bold text-lg mb-2 text-blue-600">Service ke baad support milega?</h4>
                <p class="text-gray-600">Bilkul, hum har project ke saath maintenance aur technical support provide
                    karte hain.</p>
            </div>
        </div>
    </section>

    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4 text-center">
            <div class="flex items-center justify-center mb-6">
                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                    <span class="text-white font-bold">N</span>
                </div>
                <span class="text-xl font-bold">Nexus<span class="text-blue-400">Corp</span></span>
            </div>
            <p class="text-gray-400 mb-6">Innovative solutions for a better digital world.</p>
            <div class="flex justify-center space-x-6 mb-8">
                <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-instagram"></i></a>
            </div>
            <p class="text-gray-500 text-sm">&copy; 2026 NexusCorp. All rights reserved.</p>
        </div>
    </footer>

</body>

</html>