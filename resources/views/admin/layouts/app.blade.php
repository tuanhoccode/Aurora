<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') Admin Panel</title>

    <!-- Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome-pro.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/flaticon_shofy.css') }}">
    
    <!-- Plugin CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- jQuery + Toastr JS (nên để trước </body>, nhưng nếu cần ở head thì giữ nguyên) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    @stack('styles')
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fc;
            overflow-x: hidden;
        }
        #content-wrapper {
            margin-left: 270px;
            transition: margin 0.3s ease;
        }
        @media (max-width: 991px) {
            #content-wrapper {
                margin-left: 0;
            }
        }

        /* Sidebar Styles */
        .sidebar {
            min-height: 100vh;
            width: 270px;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 100;
            background: linear-gradient(180deg, #2B1B5C 0%, #B9275E 50%, #FF8C42 100%);
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .sidebar .nav {
            padding-bottom: 2rem;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.7rem 1.2rem;
            margin: 0.2rem 0.8rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .sidebar .nav-icon {
            font-size: 1.1rem;
            width: 1.5rem;
            text-align: center;
        }

        .sidebar-heading {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
            font-weight: 600;
            padding: 1rem 1.5rem 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .sidebar hr {
            margin: 0.5rem 1rem;
            border-color: rgba(255, 255, 255, 0.1);
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
        }

        /* Form & Editor Styles */
        .form-label {
            font-weight: 500;
            color: #566a7f;
        }
        .form-control:focus,
        .form-select:focus {
            border-color: #e4e4e4;
            box-shadow: 0 0 0 0.2rem rgba(67, 94, 190, 0.15);
        }
        .ck-editor__editable {
            min-height: 200px;
            max-height: 400px;
        }
        .ck.ck-editor__main>.ck-editor__editable {
            background: #fff !important;
            border-radius: 0 0 0.375rem 0.375rem !important;
            border-color: #dee2e6 !important;
            padding: 0 1rem !important;
        }
        .ck.ck-toolbar {
            border-radius: 0.375rem 0.375rem 0 0 !important;
            border-color: #dee2e6 !important;
        }

        /* Card Styles */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 2px 6px 0 rgba(67, 89, 113, 0.12);
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid #e9ecef;
            padding: 1.25rem;
        }
        .card-title {
            color: #566a7f;
            font-weight: 600;
            margin: 0;
        }

        /* Animation Styles */
        #variantSection {
            opacity: 1;
            transform: translateY(0);
            transition: all 0.3s ease-in-out;
        }
        #variantSection[style*="display: none"] {
            opacity: 0;
            transform: translateY(-10px);
        }
        .variant-values {
            opacity: 1;
            transform: translateY(0);
            transition: all 0.3s ease-in-out;
        }
        .variant-values[style*="display: none"] {
            opacity: 0;
            transform: translateY(-10px);
        }

        /* Select2 Styles */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
            border-color: #dee2e6;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple {
            padding: 0.375rem 0.75rem;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: #e4e4e4;
            color: #fff;
            border: none;
            padding: 2px 8px;
            margin: 2px;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff;
            margin-right: 5px;
        }
        .select2-container--bootstrap-5 .select2-dropdown {
            border-color: #dee2e6;
        }
        .select2-container--bootstrap-5 .select2-search__field:focus {
            border-color: #e4e4e4;
            box-shadow: 0 0 0 0.2rem rgba(67, 94, 190, 0.15);
        }
    </style>
</head>
<body>
    <div id="wrapper" class="d-flex">
        <!-- Sidebar -->
        @include('admin.layouts.sidebar')

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="flex-grow-1 d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 shadow-sm">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle me-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="me-2 d-none d-lg-inline text-gray-600 small">Admin</span>
                                <img class="img-profile rounded-circle" style="width: 40px; height: 40px;" 
                                     src="https://ui-avatars.com/api/?name=Admin&background=4e73df&color=ffffff&size=128">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="{{ route('home') }}" target="_blank">
                                    <i class="fa fa-globe fa-sm fa-fw me-2 text-gray-400"></i>
                                    Xem trang khách
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fa fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">
                                    <i class="fa fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <!-- Page Content -->
                <main class="flex-grow-1 p-4">
                    @yield('content')
                </main>
            </div>
            <!-- Footer -->
            <footer class="bg-white py-3 mt-auto shadow-sm">
                <div class="container-fluid text-center">
                    <span class="text-muted">&copy; Your Website {{ date('Y') }}</span>
                </div>
            </footer>
        </div>
    </div>

    <!-- Core Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <!-- Plugin Scripts -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Custom Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle
            const sidebarToggle = document.getElementById('sidebarToggleTop');
            const contentWrapper = document.getElementById('content-wrapper');
            const sidebar = document.querySelector('.sidebar');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    contentWrapper.style.marginLeft = sidebar.classList.contains('show') ? '270px' : '0';
                });
            }

            // Initialize plugins
            if ($.fn.DataTable) {
                $('.datatable').DataTable({
                    responsive: true,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/vi.json'
                    }
                });
            }
        });
    </script>
    @if (session('success'))
   <script>
      toastr.options = {
         "positionClass": "toast-top-right",
         "timeOut": 2000
      };
      toastr.success(@json(session('success')));
   </script>
   @endif

   @if (session('error'))
   <script>
      toastr.options = {
         "positionClass": "toast-top-right",
         "timeOut": 2000
      };
      toastr.error(@json(session('error')));
   </script>
   @endif

    @stack('scripts')
</body>
</html> 
