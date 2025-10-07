<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - St. Lawrence University Uganda</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .slu-blue { background-color: #003366; }
        .slu-gold { background-color: #FFD700; }
        .slu-light-blue { background-color: #0056b3; }
        .text-slu-blue { color: #003366; }
        .text-slu-gold { color: #FFD700; }
        .border-slu-gold { border-color: #FFD700; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="slu-blue text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <!-- University Logo -->
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center">
                        <span class="text-slu-blue font-bold text-lg">SLU</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">St. Lawrence University</h1>
                        <p class="text-slu-gold font-semibold">Uganda - Attendance Management System</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-300">Excellence in Education</p>
                    <p class="text-slu-gold font-semibold">Since 2007</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <div class="relative bg-gradient-to-r from-slu-blue to-slu-light-blue text-white">
        <div class="absolute inset-0 bg-black opacity-10"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <h1 class="text-5xl font-bold mb-6">Welcome to SLU Attendance System</h1>
                <p class="text-xl mb-8 max-w-3xl mx-auto leading-relaxed">
                    Streamlining class attendance management for St. Lawrence University Uganda. 
                    Efficient, reliable, and designed for academic excellence.
                </p>
                <div class="flex justify-center space-x-6">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-slu-gold">16+</div>
                        <div class="text-sm">Years of Excellence</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-slu-gold">5000+</div>
                        <div class="text-sm">Students Served</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-slu-gold">100+</div>
                        <div class="text-sm">Academic Programs</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Login Section -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="md:flex">
                <!-- Welcome Message -->
                <div class="md:w-1/2 slu-blue text-white p-12">
                    <h2 class="text-3xl font-bold mb-6">Get Started</h2>
                    <p class="text-blue-100 mb-8 leading-relaxed">
                        Access the St. Lawrence University Attendance Management System to:
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-slu-gold mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Track student attendance efficiently
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-slu-gold mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Generate comprehensive reports
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-slu-gold mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Monitor academic progress
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 text-slu-gold mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Support student success
                        </li>
                    </ul>
                </div>

                <!-- Login Form -->
                <div class="md:w-1/2 p-12">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold text-slu-blue">Sign In</h3>
                        <p class="text-gray-600 mt-2">Access your account</p>
                    </div>

                    {{-- <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address
                                </label>
                                <input type="email" id="email" name="email" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slu-blue focus:border-slu-blue transition"
                                       placeholder="Enter your email">
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Password
                                </label>
                                <input type="password" id="password" name="password" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-slu-blue focus:border-slu-blue transition"
                                       placeholder="Enter your password">
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input type="checkbox" id="remember" name="remember"
                                           class="w-4 h-4 text-slu-blue border-gray-300 rounded focus:ring-slu-blue">
                                    <label for="remember" class="ml-2 text-sm text-gray-600">
                                        Remember me
                                    </label>
                                </div>
                                <a href="#" class="text-sm text-slu-blue hover:text-slu-light-blue">
                                    Forgot password?
                                </a>
                            </div>

                            
                        </div>
                    </form> --}}

                    <div class="">
                        <a href="/login" class="w-full slu-blue text-white py-3 px-4 rounded-lg hover:bg-slu-light-blue transition font-semibold text-lg">
                            Sign In
                        </a>
                    </div>

                    <div class="mt-8 text-center">
                        <p class="text-gray-600">
                            Need help? Contact 
                            <a href="mailto:ict@slu.ac.ug" class="text-slu-blue hover:text-slu-light-blue font-semibold">
                                ICT Department
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-slu-blue">System Features</h2>
                <p class="text-gray-600 mt-4 max-w-2xl mx-auto">
                    Comprehensive attendance management solutions designed for academic institutions
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6">
                    <div class="w-16 h-16 slu-blue rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-slu-blue mb-3">Real-time Tracking</h3>
                    <p class="text-gray-600">Monitor attendance as it happens with live updates and instant reporting.</p>
                </div>

                <div class="text-center p-6">
                    <div class="w-16 h-16 slu-blue rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-slu-blue mb-3">Comprehensive Reports</h3>
                    <p class="text-gray-600">Generate detailed attendance reports for students, classes, and departments.</p>
                </div>

                <div class="text-center p-6">
                    <div class="w-16 h-16 slu-blue rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-slu-blue mb-3">Secure Access</h3>
                    <p class="text-gray-600">Role-based access control ensures data security and privacy protection.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="slu-blue text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} St. Lawrence University Uganda. All rights reserved.</p>
                <p class="text-slu-gold mt-2">"Education for Liberation and Transformation"</p>
                <div class="mt-4 text-sm text-gray-300">
                    <p>Lubaga Campus, Kampala | +256 414 123 456 | info@slu.ac.ug</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>