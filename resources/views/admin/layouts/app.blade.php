<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') Admin Panel</title>

    <!-- Vendor CSS from public/assets -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome-pro.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/flaticon_shofy.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/spacing.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/swiper-bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Custom admin CSS -->
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

        .navbar {
            padding: 1rem 1.5rem;
        }

        .img-profile {
            width: 40px;
            height: 40px;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .dropdown-item {
            padding: 0.5rem 1.5rem;
        }

        .dropdown-item i {
            width: 1.25rem;
        }

        /* Dashboard & General styles */
        h1,
        h3,
        h6 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: #5a5c69;
            /* A slightly softer dark color */
            opacity: 1;
            /* Make headings always visible */
            animation: none;
            /* Remove fade-in animation for headings */
        }

        h1 {
            font-size: 1.75rem;
            /* Adjusted main heading size */
            margin-bottom: 1.5rem;
            /* Adjusted margin below main heading */
        }

        h3 {
            font-size: 1.5rem;
            /* Adjusted subheading size */
            margin-bottom: 1rem;
            /* Adjusted margin below subheading */
        }

        h6 {
            font-size: 1rem;
            /* Adjusted small heading size */
            margin-bottom: 0.75rem;
            /* Adjusted margin below small heading */
        }

        .card {
            border: none;
            /* Remove default border */
            border-radius: 0.5rem;
            /* Slightly less rounded corners for a cleaner look */
            transition: all 0.3s ease-in-out;
            /* Smooth transition for hover effects */
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            /* Softer and more subtle shadow */
            padding: 1.25rem;
            /* Add padding inside the card */
        }

        .card:hover {
            transform: translateY(-3px);
            /* Subtle lift effect on hover */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            /* Slightly larger shadow on hover */
        }

        /* Add entrance animation for cards */
        .card {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s ease-out forwards;
            /* Adjust animation-delay based on card order if needed */
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Style for table headers */
        .table thead th {
            border-bottom: 2px solid #e3e6f0;
            font-weight: 700;
            color: #858796;
            text-transform: uppercase;
            font-size: 0.85rem;
            padding: 0.75rem;
            /* Adjust padding in headers */
        }

        /* Style for table rows on hover */
        .table tbody tr:hover {
            background-color: #f1f1f1;
            /* Lighter highlight on hover */
            transition: background-color 0.2s ease-in-out;
        }

        /* Style for table cells */
        .table td,
        .table th {
            padding: 0.75rem;
            /* Adjust padding in cells */
            vertical-align: middle;
            /* Align content vertically */
        }

        /* Add subtle border to table cells */
        .table td {
            border-top: 1px solid #e9ecef;
            /* Subtle top border for cells */
        }

        /* Remove last row border */
        .table tbody tr:last-child td {
            border-bottom: none;
            /* Remove bottom border from last row */
        }

        /* Ensure table container does not add extra padding/margin */
        .table-responsive>.table {
            margin-bottom: 0;
            /* Remove default bottom margin */
        }

        /* Ensure consistent spacing for elements within table cells */
        .table td .btn-group,
        .table td form {
            margin-bottom: 0;
            display: flex;
            /* Use flexbox for alignment */
            align-items: center;
            /* Center items vertically */
            gap: 5px;
            /* Space between buttons/elements */
        }

        /* Style for table headers */
        .table thead th {
            border-bottom: 2px solid #d1d3e2;
            /* Slightly darker border */
            font-weight: 600;
            /* Slightly less bold */
            color: #6e707e;
            /* Darker header text color */
            text-transform: uppercase;
            font-size: 0.8rem;
            /* Slightly smaller font size */
        }

        /* Style for buttons */
        .btn {
            border-radius: 0.5rem;
            /* Rounded buttons */
            transition: all 0.2s ease-in-out;
        }

        .btn:hover {
            opacity: 0.9;
        }

        /* Ensure form groups have bottom margin for consistent spacing */
        .card-body .form-group {
            margin-bottom: 1rem;
            /* Adjusted vertical spacing in forms */
        }

        /* Adjust margin for form groups specifically within Bootstrap columns to avoid double margin */
        .card-body .row>.col>.form-group,
        .card-body .row>[class^="col-"]>.form-group {
            margin-bottom: 0;
            /* Remove bottom margin here, as row/col handles spacing */
            padding-bottom: 1rem;
            /* Use padding for spacing within the row/col structure */
        }

        /* Adjust alignment for buttons within form groups, particularly after labels */
        .card-body .form-group>.btn {
            margin-top: 0.5rem;
            /* Add a small top margin */
            /* Align buttons nicely below inputs, considering the form-group padding */
        }

        /* Specific style for buttons that are the sole element in a form-group at the end of a form */
        .card-body form>.form-group:last-child>.btn {
            margin-top: 1rem;
            /* Adjusted top margin */
        }

        /* Adjust spacing for buttons within button groups in tables */
        .table .btn-group .btn {
            margin-right: 5px;

            /* Small horizontal space between buttons */
            &:last-child {
                margin-right: 0;
                /* No margin for the last button */
            }
        }

        /* Style for default/secondary buttons */
        .btn-default,
        .btn-secondary {
            background-color: #e9ecef;
            /* Light grey background */
            border-color: #ced4da;
            color: #495057;
        }

        .btn-default:hover,
        .btn-secondary:hover {
            background-color: #d3d9df;
            border-color: #c6ccd3;
            color: #343a40;
        }

        /* Refine alignment for the main submit button in filter forms */
        .card-body form.mb-4 button[type="submit"].btn-primary {
            margin-top: 1.8rem;
            /* Adjust this value to align with form controls */
        }

        /* Specific alignment for the Apply button in the Orders filter form */
        .card-body form.row.g-3 .col-md-1.d-flex.align-items-end .btn {
            margin-top: 0;
            /* Reset any conflicting margin-top */
            /* Bootstrap's align-items-end should handle vertical alignment */
            /* If needed, a small padding-top on the column or adjusting line-height might help */
        }
    </style>
    @stack('styles')
    <!-- Advanced Custom Sidebar Styles and Animations -->
    <style>
        /* Base Sidebar Style */
        .sidebar-animated {
            /* Updated Gradient Animation */
            /* Using a clean, light grey background with accent */
            /* background: #f4f7f6; */
            /* Light grey background */
            /* background: #58a4b0; */
            /* Using a pleasant teal color */
            /* Trying a new, modern gradient */
            /* background: linear-gradient(to bottom, #6dd5ed 0%, #2193b0 100%); */
            /* Blue/Teal gradient */
            /* Applying a dark to light gradient */
            background: linear-gradient(to bottom, #1a2a6c 0%, #b21f1f 50%, #fdbb2d 100%);
            /* Example: Deep blue to light orange-yellow */
            background-size: 100% 100%;
            /* animation: gradientAnimation 10s ease infinite; */

            min-height: 100vh;
            /* Kept for structure, overflow handles scrolling */
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
            /* Adjusted shadow */
            border-radius: 0 8px 8px 0;
            /* Smaller border radius */
            padding-top: 1.5rem;
            /* Adjusted padding */
            padding-bottom: 1.5rem;
            /* Adjusted padding */
            width: 270px;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 100;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease-in-out;
            overflow-y: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .sidebar-animated::-webkit-scrollbar {
            /* Hide scrollbar for Chrome, Safari and Opera */
            display: none;
        }

        /* Gradient Animation Keyframes */
        /* Keeping the keyframes structure if needed later, but removing the background animation for now */
        /* @keyframes gradientAnimation { ... } */

        /* Sidebar Entrance Animation */
        @keyframes sidebarSlideIn {
            0% {
                transform: translateX(-100%);
                opacity: 0;
            }

            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Apply entrance animation to sidebar */
        .sidebar-animated {
            opacity: 0;
            transform: translateX(-100%);
            animation: sidebarSlideIn 0.7s cubic-bezier(.25, .8, .25, 1) forwards;
            /* Applied animation */
            /* Keep other styles below */
            /* Updated Gradient Animation */
            /* background: linear-gradient(to bottom, #1a2a6c 0%, #b21f1f 50%, #fdbb2d 100%); */
            /* Example: Deep blue to light orange-yellow */
            /* background-size: 100% 100%; */
            /* animation: gradientAnimation 10s ease infinite; */

            min-height: 100vh;
            /* Kept for structure, overflow handles scrolling */
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
            /* Adjusted shadow */
            border-radius: 0 8px 8px 0;
            /* Smaller border radius */
            padding-top: 1.5rem;
            /* Adjusted padding */
            padding-bottom: 1.5rem;
            /* Adjusted padding */
            width: 270px;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 100;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease-in-out;
            overflow-y: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        /* Enhanced nav-link styles with animations */
        .sidebar-animated .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 400;
            border-radius: 4px;
            margin: 0.3rem 0.8rem;
            transition: all 0.3s ease;
            /* Slightly longer transition for smoother effects */
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.7rem 1.2rem;
            font-size: 0.9rem;
            letter-spacing: 0.01em;
            position: relative;
            overflow: hidden;
            z-index: 1;
            box-shadow: none;
        }

        /* Hover/Active Effect */
        .sidebar-animated .nav-link:hover {
            color: #fff !important;
            /* White text on hover */
            background: rgba(255, 255, 255, 0.2) !important;
            /* More visible semi-transparent white background on hover */
            transform: translateY(-2px);
            /* Subtle lift effect on hover */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Subtle shadow on hover */
        }

        .sidebar-animated .nav-link.active {
            color: #fff !important;
            /* White text on active */
            background: rgba(255, 255, 255, 0.3) !important;
            /* More prominent semi-transparent white background on active */
            font-weight: 500;
            /* Slightly bolder on active */
            transform: none;
            box-shadow: none;
        }

        /* Icon Styles */
        .sidebar-animated .nav-link .nav-icon {
            font-size: 1.1rem;
            min-width: 20px;
            text-align: center;
            transition: all 0.3s ease;
            /* Slightly longer transition for smoother effects */
            color: rgba(255, 255, 255, 0.7) !important;
        }

        /* Icon Hover/Active Effect */
        .sidebar-animated .nav-link:hover .nav-icon {
            transform: scale(1.1);
            /* Subtle icon scale on hover */
            color: #fff !important;
            /* White icon on hover */
        }

        .sidebar-animated .nav-link.active .nav-icon {
            transform: none;
            /* Remove scale on active */
            color: #fff !important;
            /* White icon on active */
        }

        /* Sidebar Heading Styles */
        .sidebar-animated .sidebar-heading {
            color: rgba(255, 255, 255, 0.5) !important;
            /* Lighter heading color */
            font-size: 0.8rem;
            font-weight: 500;
            margin: 1.2rem 0 0.6rem 1.2rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            text-shadow: none;
        }

        /* Divider Style */
        .sidebar-animated hr {
            border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
            /* Subtle white divider */
            margin: 0.8rem 0.8rem;
        }

        /* Responsive adjustments */
        @media (max-width: 991px) {
            .sidebar-animated {
                width: 100%;
                border-radius: 0;
                min-height: auto;
                position: fixed;
                top: 0;
                left: 0;
                transform: translateX(-100%);
                z-index: 1030;
                transition: transform 0.4s ease-in-out;
                box-shadow: 0 8px 32px 0 rgba(79, 140, 255, 0.18);
            }

            .sidebar-animated.show {
                transform: translateX(0);
            }

            .sidebar-animated .nav-link {
                margin: 0.4rem 1rem;
                padding: 0.9rem 1.5rem;
            }

            .sidebar-animated .sidebar-heading {
                margin: 1.5rem 0 0.8rem 1.5rem;
            }
        }

        /* Optional: Style for the main content area to push it right when sidebar is open */
        body.sidebar-toggled #content-wrapper {
            margin-left: 0;
        }

        @media (min-width: 992px) {
            body.sidebar-toggled .sidebar-animated {
                width: 100px;
                /* Collapsed width */
                border-radius: 0 20px 20px 0;
                /* Smaller border radius when collapsed */
            }

            body.sidebar-toggled #content-wrapper {
                margin-left: 100px;
                /* Adjust content margin */
            }

            body.sidebar-toggled .sidebar-animated .nav-link .nav-text,
            body.sidebar-toggled .sidebar-animated .sidebar-heading {
                display: none;
                /* Hide text when collapsed */
            }

            body.sidebar-toggled .sidebar-animated .nav-link {
                justify-content: center;
                /* Center icons when collapsed */
            }

            body.sidebar-toggled .sidebar-animated .nav-link .nav-icon {
                margin-right: 0;
                /* Remove margin from icon */
                min-width: auto;
            }
        }
    </style>
    <!-- Additional styles for Notifications page -->
    <style>
        /* Style for the New Notification button */
        .card-header .btn-primary {
            background-color: #007bff;
            /* Bootstrap primary blue */
            border-color: #007bff;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .card-header .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
        }

        /* Styles for filter form elements */
        .form-group label {
            font-weight: 500;
            color: #5a5c69;
            margin-bottom: 0.3rem;
            /* Further adjusted margin */
            font-size: 0.85rem;
            /* Slightly smaller font size */
        }

        .form-control,
        .form-select {
            border-radius: 0.4rem;
            border-color: #ced4da;
            /* Adjusted border color */
            box-shadow: none;
            transition: all 0.3s ease;
            padding: 0.5rem 0.75rem;
            /* Adjusted padding */
            font-size: 0.9rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #007bff;
            /* Highlight border color on focus */
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
            /* Add a subtle glow on focus */
        }

        /* Add margin to form groups in the filter area */
        .card-body form.mb-4 .form-group {
            margin-bottom: 0.8rem;
        }

        /* Styles for the small stat boxes */
        .small-box {
            border-radius: 0.5rem;
            /* Match card border radius */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            /* Softer shadow */
            color: #fff;
            /* Ensure text is white on colored background */
            overflow: hidden;
            margin-bottom: 1.5rem;
            /* Add some space below */
            position: relative;
            transition: all 0.3s ease-in-out;
            cursor: pointer;
        }

        .small-box:hover {
            transform: translateY(-5px);
            /* Lift effect on hover */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            /* Slightly larger shadow on hover */
        }

        .small-box .inner {
            padding: 15px;
        }

        .small-box h3 {
            font-size: 2rem;
            /* Slightly smaller font size */
            font-weight: 600;
            /* Adjusted font weight */
            margin: 0 0 5px 0;
            /* Adjusted margin */
            white-space: nowrap;
            padding: 0;
            color: #fff !important;
            /* Ensure heading color is white */
            opacity: 1;
            animation: none;
        }

        .small-box p {
            font-size: 0.9rem;
            /* Slightly smaller font size */
            color: rgba(255, 255, 255, 0.8);
            /* Slightly less opaque white */
        }

        .small-box .icon {
            position: absolute;
            top: 5px;
            /* Adjusted position */
            right: 10px;
            z-index: 0;
            font-size: 3rem;
            /* Slightly smaller icon size */
            color: rgba(0, 0, 0, 0.1);
            /* Softer icon color */
            transition: all 0.3s ease-in-out;
        }

        .small-box:hover .icon {
            transform: scale(1.1);
            /* Subtle scale */
        }

        /* Specific colors for small boxes */
        .small-box.bg-warning {
            background: linear-gradient(45deg, #ffda6a, #fbc531) !important;
            /* Adjusted warning gradient */
        }

        .small-box.bg-success {
            background: linear-gradient(45deg, #29c7ac, #1abc9c) !important;
            /* Adjusted success gradient */
        }

        .small-box.bg-danger {
            background: linear-gradient(45deg, #fc5c65, #eb3b5a) !important;
            /* Adjusted danger gradient */
        }

        /* Style for Bulk Actions button */
        #bulk-action-button {
            font-weight: 500;
            /* Adjusted font weight */
            border-radius: 0.4rem;
            /* Match form elements */
            padding: 0.6rem 1.2rem;
            /* Adjusted padding */
            transition: all 0.3s ease;
            /* Ensure it uses primary blue color consistent with other actions */
            background-color: #007bff;
            /* Standard Bootstrap primary blue */
            border-color: #007bff;
            color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
            /* Softer shadow */
        }

        #bulk-action-button:hover:not(:disabled) {
            background-color: #0056b3;
            border-color: #004085;
            transform: none;
            /* Remove lift effect */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Softer shadow */
        }

        #bulk-action-button:disabled {
            opacity: 0.5;
            /* More visible disabled state */
            cursor: not-allowed;
        }

        /* Styles for the Filter button on Notifications page */
        .form-group .btn-primary {
            border-radius: 0.4rem;
            /* Match form inputs */
            font-weight: 500;
            /* Ensure consistent font weight */
            padding: 0.6rem 1.2rem;
            /* Adjusted padding */
            transition: all 0.3s ease;
            background-color: #007bff;
            /* Standard Bootstrap primary blue */
            border-color: #007bff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
            /* Softer shadow */
        }

        .form-group .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
            transform: none;
            /* Remove lift effect */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            /* Softer shadow */
        }

        /* Ensure table styles are consistent (already mostly covered by general .table styles) */
        /* Add any specific overrides here if needed */

        /* Styles for the badges */
        .badge {
            font-size: 0.8em;
            /* Slightly smaller font size */
            font-weight: 600;
            /* Bolder text */
            padding: 0.4em 0.6em;
            /* Adjusted padding */
            border-radius: 0.25rem;
            /* Slightly rounded corners */
            vertical-align: middle;
            /* Align vertically in tables */
        }

        .badge-primary {
            background-color: #007bff;
            /* Standard Bootstrap primary blue */
        }

        .badge-info {
            background-color: #17a2b8;
            /* Standard Bootstrap info teal */
        }

        .badge-success {
            background-color: #28a745;
            /* Standard Bootstrap success green */
        }

        .badge-warning {
            background-color: #ffc107;
            /* Standard Bootstrap warning yellow */
        }

        .badge-danger {
            background-color: #dc3545;
            /* Standard Bootstrap danger red */
        }
    </style>

    {{-- Custom styles for dashboard and functional pages --}}
    <style>
        /* Refine spacing and alignment */
        .container-fluid {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .card {
            margin-bottom: 1.5rem;
            /* Increase space between cards */
        }

        .card-header {
            padding: 1rem 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Adjust spacing for form elements */
        .mb-3 {
            margin-bottom: 1rem !important;
            /* Standardize margin-bottom */
        }

        .form-label {
            margin-bottom: 0.5rem;
            /* Space below labels */
            font-weight: 600;
            /* Make labels slightly bolder */
        }

        .form-control,
        .form-select {
            margin-bottom: 0.5rem;
            /* Space below input/select */
        }


        /* Table spacing */
        .table {
            margin-bottom: 1.5rem;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            /* Adjust cell padding */
            vertical-align: middle;
            /* Vertically align table content */
        }

        /* Button spacing */
        .btn {
            margin-right: 0.5rem;
            /* Space between buttons */
        }

        .btn:last-child {
            margin-right: 0;
        }

        /* Headings spacing */
        .h1,
        .h2,
        .h3,
        .h4,
        .h5,
        .h6,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            margin-top: 0;
            margin-bottom: 0.5rem;
            /* Space below headings */
        }

        /* Improve typography */
        body {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            /* Use a modern, readable font stack */
            color: #333;
            /* Darker text for better contrast on light backgrounds */
            line-height: 1.6;
            /* Improve readability */
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-weight: 700;
            /* Bolder headings */
            line-height: 1.2;
            /* Tighter line height for headings */
        }

        .card-title {
            font-size: 1.15rem;
            /* Slightly larger card titles */
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .form-label {
            font-size: 0.9rem;
            /* Slightly smaller labels than body text */
        }

        /* Ensure sidebar text color is readable against its background */
        .sidebar-animated .nav-link,
        .sidebar-animated .sidebar-heading {
            color: rgba(255, 255, 255, 0.9);
            /* White/light grey for contrast with gradient */
        }

        /* Highlight interactive elements */
        .btn:hover {
            opacity: 0.9;
            /* Slightly reduce opacity on hover */
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #007bff;
            /* Highlight border color on focus */
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
            /* Add a subtle glow on focus */
        }

        a {
            transition: color 0.2s ease-in-out;
            /* Smooth color transition for links */
        }

        a:hover {
            text-decoration: none;
            /* Remove underline on hover for cleaner look */
            color: #0056b3;
            /* Slightly darker blue on hover */
        }

        /* Pagination styles */
        .pagination {
            margin-top: 1.5rem;
            /* Space above pagination */
            justify-content: center;
            /* Center pagination */
        }

        .pagination .page-link {
            color: #007bff;
            /* Link color */
            border-color: #dee2e6;
            /* Border color */
            margin: 0 2px;
            /* Space between links */
            border-radius: 0.25rem;
            /* Slightly rounded corners */
            transition: all 0.2s ease-in-out;
        }

        .pagination .page-item.active .page-link {
            color: #fff;
            /* Active link text color */
            background-color: #007bff;
            /* Active link background */
            border-color: #007bff;
            /* Active link border */
        }

        .pagination .page-link:hover {
            background-color: #e9ecef;
            /* Hover background */
            border-color: #dee2e6;
            /* Hover border */
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            /* Disabled link color */
            pointer-events: none;
            /* Disable pointer events */
            background-color: #fff;
            border-color: #dee2e6;
        }

        /* Alert styles */
        .alert {
            margin-bottom: 1.5rem;
            /* Space below alerts */
            padding: 1rem 1rem;
            border-radius: 0.25rem;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .alert-warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeeba;
        }

        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }
    </style>

    <!-- Badge/Tag styles -->
    <style>
        .badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .badge-primary {
            color: #fff;
            background-color: #007bff;
        }

        .badge-secondary {
            color: #fff;
            background-color: #6c757d;
        }

        .badge-success {
            color: #fff;
            background-color: #28a745;
        }

        .badge-danger {
            color: #fff;
            background-color: #dc3545;
        }

        .badge-warning {
            color: #212529;
            background-color: #ffc107;
        }

        .badge-info {
            color: #fff;
            background-color: #17a2b8;
        }

        .badge-light {
            color: #212529;
            background-color: #f8f9fa;
        }

        .badge-dark {
            color: #fff;
            background-color: #343a40;
        }
    </style>

    <!-- Data/Statistic Widget Styles -->
    <style>
        /* Data/Statistic Widget Styles (assuming card-like structure) */
        .data-widget,
        .stats-card {
            /* Inherits most styles from .card, but can add specifics */
            padding: 1.25rem;
            /* Slightly more padding */
            text-align: center;
            /* Center content */
        }

        .data-widget h5,
        .stats-card h5 {
            font-size: 1.5rem;
            /* Larger font for main number */
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .data-widget p,
        .stats-card p {
            font-size: 0.9rem;
            /* Smaller font for label */
            color: #6c757d;
            /* Muted color for label */
            margin-bottom: 0;
        }

        /* Basic List Styles */
        ul:not(.nav):not(.pagination):not(.list-unstyled),
        ol:not(.list-unstyled) {
            margin-bottom: 1.5rem;
            padding-left: 20px;
        }

        ul:not(.nav):not(.pagination):not(.list-unstyled) li,
        ol:not(.list-unstyled) li {
            margin-bottom: 0.5rem;
        }
    </style>

    <!-- Bootstrap 5 CDN (ưu tiên mới nhất) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="font-sans antialiased">
    <div id="wrapper" class="d-flex">
        <!-- Sidebar -->
        @include('admin.layouts.sidebar')
        <!-- End of Sidebar -->

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
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{-- <span class="me-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                                <img class="img-profile rounded-circle" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4e73df&color=ffffff&size=128"> --}}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i> {{ Auth::user()->fullname ?? 'Profile' }}
                                </a>
                                <a class="dropdown-item" href="#"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>Profile</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>Logout
                                </a>

                                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->
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
    <!-- Scroll to Top Button-->
    {{-- ... --}}

    <!-- Logout Modal-->
    {{-- ... --}}

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('assets/js/vendor/jquery.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-bundle.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('admin/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Page level plugins -->
    <script src="{{ asset('admin/vendor/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Page level custom scripts -->
    <script src="{{ asset('admin/js/demo/chart-area-demo.js') }}"></script>
    <script src="{{ asset('admin/js/demo/chart-pie-demo.js') }}"></script>
    <script src="{{ asset('admin/js/demo/datatables-demo.js') }}"></script>

    <!-- Vendor JS from public/assets -->
    <script src="{{ asset('assets/js/waypoints.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-bundle.js') }}"></script>
    <script src="{{ asset('assets/js/swiper-bundle.js') }}"></script>
    <script src="{{ asset('assets/js/slick.js') }}"></script>
    <script src="{{ asset('assets/js/nouislider.js') }}"></script>
    <script src="{{ asset('assets/js/magnific-popup.js') }}"></script>
    <script src="{{ asset('assets/js/meanmenu.js') }}"></script>
    <script src="{{ asset('assets/js/imagesloaded-pkgd.js') }}"></script>
    <script src="{{ asset('assets/js/isotope-pkgd.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.shop.details.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.elegantero.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.countdown.js') }}"></script>
    <script src="{{ asset('assets/js/wow.js') }}"></script>
    <script src="{{ asset('assets/js/isInViewport.d.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <!-- Custom admin scripts -->
    <script>
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggleTop');
            const contentWrapper = document.getElementById('content-wrapper');
            const sidebar = document.querySelector('.sidebar-animated');

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('d-none');
                    contentWrapper.style.marginLeft = sidebar.classList.contains('d-none') ? '0' : '270px';
                });
            }
        });
    </script>
    @stack('scripts')

    <!-- Vendor JS -->
    <script src="{{ asset('assets/js/vendor/jquery.js') }}"></script>
    <script src="{{ asset('assets/js/vendor/popper.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-bundle.js') }}"></script>

    @yield('scripts')
</body>

</html>