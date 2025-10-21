<!DOCTYPE html>
<html lang="en">
<head>

<meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">


<!-- Open Graph Meta Tags -->



  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
        integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@700&display=swap" rel="stylesheet">
{{--    <link href="{{ asset('bootstrap/bootstrap.min.css') }}" rel="stylesheet" type="text/css" id="bootstrap">--}}

    <link rel="stylesheet" href="{{asset('css/first_page.css')}}">
  <link rel="stylesheet" href="{{asset('css/post_page.css')}}">
  <link rel="stylesheet" href="{{asset('css/blog_page.css')}}">
  <link rel="stylesheet" href="{{asset('css/footer.css')}}">
  <link rel="stylesheet" href="{{asset('css/style.css')}}">
  <link rel="stylesheet" href="{{asset('css/forsale_page.css')}}">

  <link rel="stylesheet" href="{{asset('css/blog_page2.css')}}">
  <link rel="stylesheet" href="{{asset('css/news_articles.css')}}">
  <link rel="stylesheet" href="{{asset('css/contact_us.css')}}">
  <link rel="stylesheet" href="{{asset('css/forum_page.css')}}">
  <link rel="stylesheet" href="{{asset('css/dealer_page.css')}}">
  <link rel="stylesheet" href="{{asset('css/forum_profile.css')}}">
  <link rel="stylesheet" href="{{asset('css/forum_carinfo.css')}}">
  <link rel="stylesheet" href="{{asset('css/custom.css')}}">
  <link rel="stylesheet" href="{{asset('css/file_upload.css')}}">
  <link rel="stylesheet" href="{{asset('css/dealerPage.css')}}">
  <link rel="stylesheet" href="{{asset('css/show-topic.css')}}">
  <link rel="stylesheet" href="{{asset('css/posts.css')}}">
  <link rel="stylesheet" href="{{asset('css/grid_car_cards.css')}}">
  <link rel="stylesheet" href="{{asset('css/cards_grid.css')}}">
  <link rel="stylesheet" href="{{asset('css/hero_section.css')}}">
    <link rel="stylesheet" href="{{asset('css/model.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <link rel="stylesheet" href="{{asset('css/forum_carinfo.css')}}"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Lato&family=Nunito+Sans&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('assets/favicon.jpeg') }}" type="image/jpeg">

    <style>
        *{
            font-family: 'Nunito Sans', 'Lato', sans-serif !important;
        }
        .fa, .fas, .far, .fal, .fab {  
            font-family: "Font Awesome 6 Free" !important;  
        }
        .fab {
        font-family: "Font Awesome 6 Brands" !important;
        }
    </style>

</head>
<body class="border-top">
        



<nav class="navbar navbar-expand-lg mt-lg-2 ps-lg-3 pe-lg-3 pt-0" style="z-index:999;">
    <div class="container-fluid d-flex justify-content-between align-items-center" style="z-index: 1000;">
        <a class="navbar-brand fw-bold d-none d-md-block" href="{{url('/')}}">
             <img class="logo img-fluid" src="{{asset('assets/logowhite.svg')}}" alt="">
            </a>
        <a class="navbar-brand fw-bold d-lg-none" href="{{url('/')}}" style="color: white; font-size: 28px;">
            <img class="logo img-fluid" src="{{asset('assets/logodark.svg')}}" alt="" style="height: 70px; width: 120px;">
        </a>

      <button
                class="btn d-block d-sm-none me-2"  type="button"
                style="background-color: #FF1717; color: white; font-weight: 1000;"
                onclick="redirectToPayment()">
                SELL CAR
            </button>


            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
               <img class="logo img-fluid" src="{{asset('assets/menu.svg')}}" alt="">
            </button>
       
        <div class="collapse navbar-collapse d-lg-flex gap-lg-4 mobile-menu" id="navbarSupportedContent">
            <ul class="navbar-nav mb-2 mb-lg-0 d-lg-flex gap-lg-2 align-items-lg-center justify-content-lg-center w-100">
                <li class="nav-item">
                    <a class="nav-link" href="{{url('/')}}">
                        <i class="fas fa-home d-lg-none me-2"></i>Home
                    </a>
                </li>
                <li class="nav-item d-block d-md-none">
                    <a class="nav-link" href="{{ route('packages.select') }}" 
                        onclick="{{ Auth::guest() ? 'event.preventDefault(); fetch(\'/store-post-advert-session\'); window.location.href = this.href;' : '' }}">
                        <i class="fas fa-plus d-lg-none me-2"></i>Post Advert
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-none d-lg-block" href="{{route('forum.index')}}">Forum</a>
                    <div class="mobile-dropdown d-lg-none">
                        <a class="nav-link d-flex align-items-center justify-content-between" href="#" role="button" onclick="window.location.href='{{route('forum.index')}}'">
                            <div onclick="window.location.href='{{route('forum.index')}}'">
                                <i class="fas fa-comments me-2"></i>Forum
                            </div>
                        </a>
                        @auth
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('forum.index') }}?tab=activity">
                                    <i class="fas fa-history me-2"></i>My Posts
                                </a>
                            </li>
                            @if(App\Models\Moderator::where('user_id', auth()->id())->exists())
                            <li>
                                <a class="dropdown-item" href="{{ route('forum.index') }}?tab=mod">
                                    <i class="fas fa-shield-alt me-2"></i>Moderator
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('forum.index') }}?tab=blocked-users">
                                    <i class="fas fa-lock me-2"></i>Blocked Users
                                </a>
                            </li>
                            @endif
                        </ul>
                        @endauth
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('event.index')}}">
                        <i class="fas fa-calendar d-lg-none me-2"></i>Events
                    </a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link" href="{{route('business.index')}}">
                        <i class="fas fa-bag-shopping d-lg-none me-2"></i>Businesses
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('blog.index')}}">
                        <i class="fas fa-blog d-lg-none me-2"></i>Blogs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('faqs.index') }}">
                        <i class="fas fa-question-circle d-lg-none me-2"></i>FAQs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{route('contact_us')}}">
                        <i class="fas fa-envelope d-lg-none me-2"></i>Contact Us
                    </a>
                </li>
                @if (!Auth::check())
                <li class="nav-item">
                    <a class="nav-link" href="{{route('login')}}">
                        <i class="fas fa-sign-in-alt d-lg-none me-2"></i>Login
                    </a>
                </li>
                @endif
                @if (Auth::check())
                <li class="nav-item d-none d-lg-block">
                    <a class="nav-link" href="{{route('dashboard')}}">
                        Dashboard
                    </a>
                </li>
                @endif
            </ul>
            <a href="javascript:void(0);" id="submitListingBtn" style="min-width: 140px;" class="btn bg-dark text-white pt-2 pb-2 d-none d-lg-block" type="submit" onclick="redirectToPage()">
                Sell Car
            </a>
              @if (Auth::check())
            <a href="javascript:void(0);" id="submitListingBtnMobile" style="width: 100%; margin-top:20px;" class="btn bg-dark text-white pt-2 pb-2 d-lg-none" type="submit" onclick="redirectToPage()">
                Dashboard
            </a>
              @endif
        </div>
    </div>
</nav>

<!-- CSS -->
<style>
    .navbar{
        background: white;
        margin-top: 5px !important;
       
    }
@media (max-width: 991px) {
    .navbar{
       
        margin-top: 0px !important;
       
    }
    .dropdown-toggle::after {
        display: none;
    }
    
    .dropdown-menu {
        border: none;
        background:rgb(255, 255, 255);
        padding: 0;
        margin: 0;
        width: 100%;
    }
    
    .dropdown-item {
        padding: 0.75rem 1.5rem;
        border-bottom: 1px solid #eee;
        color: #333;
    }
    
    .dropdown-item:last-child {
        border-bottom: none;
    }
    
    .dropdown-item i {
        width: 20px;
        text-align: center;
        color: #666;
    }
    
    /* Animation for dropdown */
    .dropdown-menu {
        display: none;
        opacity: 0;
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }
    
    .dropdown-menu.show {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }
    
    /* Rotate arrow icon when dropdown is open */
    .dropdown.show .fa-chevron-down,
    .mobile-dropdown.show .fa-chevron-down {
        transform: rotate(180deg);
    }
    
    .fa-chevron-down {
        transition: transform 0.3s ease;
    }
    
    /* Ensure proper spacing in mobile menu */
    .mobile-dropdown .nav-link {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #eee;
    }
}
@media (max-width: 991px) {
    .navbar {
        background-color: #000 !important;
        /* position: fixed; */
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 1000 !important;
        /* height: 61px !important; */
        padding: 0 15px !important;
        padding-left: 0px !important;
       
    }

    .container-fluid {
        height: 100% !important;
    }

    .navbar-toggler {
        margin: 0 !important;
        padding: 5px !important;
        font-size: 1.75rem !important;
        line-height: 1 !important;
        border: none;
        padding-right: 0px !important;
    }

    /* Updated navbar-collapse styles with correct animation starting point */
    .navbar-collapse {
        position: fixed;
        top: 80px;
        left: 0;
        right: 0;
        background: white;
        padding: 1rem;
        height: calc(100vh - 60px);
        overflow-y: auto;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transform-origin: top; /* Set transform origin to top */
        transition: all 0.3s ease-in-out;
        transform: scaleY(0); /* Use scale instead of translate */
        opacity: 0;
        z-index: 999;
        display: block !important;
    }

    /* Show state */
    .navbar-collapse.show {
        transform: scaleY(1);
        opacity: 1;
    }

    .nav-link {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #eee;
        color: #333 !important;
    }

    .nav-link:hover {
        background-color: #f8f9fa;
    }

    .nav-link i {
        width: 20px;
        text-align: center;
        color: #333;
    }

    #submitListingBtn {
        width: 100%;
        margin-top: 1rem;
        margin-bottom: auto;
    }
    @media (min-width: 992px) {

    body {
        /* padding-top: 60px; */
    }
    }
}
</style>


<style>
/* Custom toast styling */
.toast {
    opacity: 0;
    transform: translateX(-100%);
    transition: all 0.5s ease-in-out;
    border: none;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    border-radius: 8px;
}

.toast-slide-in {
    opacity: 1;
    transform: translateX(0);
}

.toast-container {
    z-index: 1050;
}

.toast-header {
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
    font-size: 0.9rem;
}

.toast-body {
    font-size: 1rem;
    font-weight: 500;
    color: #333;
}

.bg-primary {
    background: linear-gradient(45deg, #007bff, #00c4ff) !important;
}

.bg-success {
    background: linear-gradient(45deg, #28a745, #34ce57) !important;
}
</style>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const chevronIcon = document.getElementById("dropdownToggle");

        if (chevronIcon) {
            const dropdownMenu = chevronIcon.closest("a")?.nextElementSibling;

           
            if (!dropdownMenu || dropdownMenu.children.length === 1) {
                chevronIcon.style.display = "none"; 
                return; 
            }

            chevronIcon.addEventListener("click", function (event) {
                event.stopPropagation(); 
                dropdownMenu.classList.toggle("show");
            });

          
            document.addEventListener("click", function (event) {
                if (!dropdownMenu.previousElementSibling.contains(event.target)) {
                    dropdownMenu.classList.remove("show");
                }
            });
        }
    });
</script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  </div>
      {{------------Body----------------- --}}
      @yield('body')
        <!-------------Footer---------------->
        <footer class="footer-section-container" style="margin-top: 50px;">
            <div  class="footer-section-innerDiv">
                <div class="footer-section-About">
                    <div class="footer-section-About-innerDiv">
                        <h2 style="margin-bottom:20px;"><img class="logo img-fluid" src="{{asset('assets/logodark.svg')}}" alt=""></h2>
                        {{-- <h5 >About us</h5> --}}
                        <p class="pb-2 ">{{ getCompanyDetail('about_us') }}</p>
                    </div>
                </div>
                <div class="footer-middle-container-mobile">
                    <div class="footer-section-company-mobile">
                        {{-- <h2 >Company</h2> --}}
                            <div><a class="footer-section-links" href="{{url('/')}}">Home</a></div>
                            <div><a class="footer-section-links" href="{{route('forum.index')}}">Forum</a></div>
                            <div><a class="footer-section-links" href="{{route('blog.index')}}">Blog</a></div>
                            <div><a class="footer-section-links" href="{{ route('faqs.index') }}">FAQs</a></div>
                            <div><a class="footer-section-links" href="{{route('contact_us')}}">Contact Us</a></div>
                            <!-- <li><a href="{{route('signup_view')}}">Register</a></li> -->
                    </div>            
                    <ul class="footer-section-quickLinks-mobile" style="margin-top: 10px;">
                        {{-- <h2>Quick Links</h2> --}}
                            <div><a class="footer-section-links" href="{{ route('terms.conditions') }}">Terms</a></div>
                            <div><a class="footer-section-links" href="{{ route('privacy.policy') }}">Privacy</a></div>
                            <div><a class="footer-section-links" href="{{ route('refund.policy') }}">Refund</a></div>
                            <div><a class="footer-section-links" href="{{ route('pricing.package') }}">Prices</a></div>
                            <div><a class="footer-section-links" href="{{ route('sitemap.xml') }}">Sitemap</a></div>                        
                    </ul>
                    <div class="footer-section-followus-mobile" style="margin-top: 10px;">
                        {{-- <h2 class="">Follow Us</h2> --}}
                            <div style="margin-bottom:8px;"><a href="{{ getCompanyDetail('facebook') }}"><img src="{{asset('assets/fb-logo.png')}}" width="22" height="22"></a></div>
                            <div style="margin-bottom:8px;"><a href="{{ getCompanyDetail('instagram') }}"><img src="{{asset('assets/insta-logo.png')}}"  width="22" height="22"></a></div>
                            <div style="margin-bottom:8px;"><a href="{{ getCompanyDetail('youtube') }}"><img src="{{asset('assets/youtube-logo.png')}}"  width="22" height="22"></a></div>
                            <div style="margin-bottom:8px;"><a href="{{ getCompanyDetail('linkedin') }}"><img src="{{asset('assets/tiktokblackwhite.png')}}" width="22" height="22"></a></div>
                    </div>
                </div>
                <ul class="footer-section-quickLinks-desktop">
                    {{-- <h2>Quick Links</h2> --}}
                        <li><a class="footer-section-links" href="{{ route('terms.conditions') }}">Terms</a></li>
                        <li><a class="footer-section-links" href="{{ route('privacy.policy') }}">Privacy</a></li>
                        <li><a class="footer-section-links" href="{{ route('refund.policy') }}">Refund</a></li>
                        <li><a class="footer-section-links" href="{{ route('pricing.package') }}">Prices</a></li>
                        <li><a class="footer-section-links" href="{{ route('sitemap.xml') }}">Sitemap</a></li>
                        <li><a class="footer-section-links" href="{{route('forum.index')}}">Forum</a></li>
                        <li><a class="footer-section-links" href="{{route('blog.index')}}">Blogs</a></li>
                        <li><a class="footer-section-links" href="{{ route('faqs.index') }}">FAQs</a></li>
                </ul>
                <div class="footer-section-copyright">
                    <div class="">
                        <p class="" style="padding:0px;margin:0px;">© 2025 {{ getCompanyDetail('name') }}. All rights reserved.</p>
                        <!-- <p class="text-center py-3">
                            © 2025 {{ getCompanyDetail('name') }}. All rights reserved. 
                            <span>Powered by <a href="https://etech.org.pk" target="_blank" rel="noopener noreferrer">eTech</a>.</span>
                        </p> -->
                    </div>
                    <div class="footer-section-followus-desktop">
                        <div class="">
                        <span class="footer-section-icons"><a href="{{ getCompanyDetail('facebook') }}"><i class="fab fa-facebook-f" style="color: #ffffff;"></i></a></span>
        <span class="footer-section-icons"><a href="{{ getCompanyDetail('instagram') }}"><i class="fab fa-instagram" style="color: #ffffff;"></i></a></span>
        <span class="footer-section-icons"><a href="{{ getCompanyDetail('youtube') }}"><i class="fab fa-youtube" style="color: #ffffff;"></i></a></span>
        <span class="footer-section-icons"><a href="{{ getCompanyDetail('linkedin') }}"><i class="fab fa-tiktok" style="color: #ffffff;"></i></a></span>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
         {{-- Footer new design .....  --}}
        <style>
            .footer-section-container{
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .footer-section-innerDiv{
                width:90%;
                margin-top: 70px;
            }
            .footer-section-About{
                display: flex;
                justify-content: center;
                align-items: center;
                width: 100% !important;
            }
            .footer-section-About-innerDiv{
                text-align: center;
                width: 50%;
            }
            .footer-section-quickLinks-desktop{
                list-style: none;
                display: flex;
                justify-content: space-evenly;
                margin: 0;
                padding: 0;
                margin-top: 20px;
                margin-bottom: 20px;
            }
            .footer-section-links{
                text-decoration: none !important;
                color:white !important;
            }
            .footer-section-copyright{
                width: 100%;
                border-top: 1px solid white;
                margin-top: 50px;
                padding-top: 40px;
                margin-bottom: 20px;
                padding-bottom: 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .footer-section-icons{
                border: 1px solid black;
                border-radius: 50%;
                padding: 10px 15px;
            }
            .footer-section-followus-mobile{
                display: none;
            }
            .footer-section-quickLinks-mobile{
                display: none;
            }
            .footer-section-company-mobile{
                display: none;
            }
            @media screen and (max-width: 768px) {
                .footer-section-followus-mobile{
                    display: block;
                }
                .footer-section-quickLinks-mobile{
                    display: block;
                }
                .footer-section-company-mobile{
                    display: block;
                }
                .footer-section-followus-desktop{
                    display: none;
                }
                .footer-section-quickLinks-desktop{
                    display: none;
                }
                .footer-section-company-desktop{
                    display: none;
                }
                .footer-section-About-innerDiv{
                    width: 100%;
                }
                .footer-middle-container-mobile{
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    width: 100%;
                    margin: 0;
                    padding: 0;                
                }
                .footer-section-copyright{
                    width: 100%;
                    border-top: 1px solid white;
                    margin-top: 50px;
                    padding-top: 40px;
                    margin-bottom: 20px;
                    padding-bottom: 20px;
                    display: flex;
                    justify-content: center;
                    align-items: start;
                }
            }
        </style>
<script src="{{asset('js/forum_carinfo.js')}}"></script>
<script src="{{asset('js/data.js')}}"></script>
<script src="{{asset('js/forum_page.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
      const readMoreBtns = document.querySelectorAll('.read-more-btn');

      readMoreBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
          const targetId = btn.getAttribute('data-bs-target');
          const readMoreContent = document.querySelector(targetId);
          const ellipsis = btn.querySelector('.ellipsis');
          const btnText = btn.querySelector('.btn-text');

          if (readMoreContent.classList.contains('show')) {
            btnText.textContent = 'Read More';
            ellipsis.style.display = 'inline'; // Show ellipsis
          } else {
            btnText.textContent = 'Read Less';
            ellipsis.style.display = 'none'; // Hide ellipsis
          }
        });
      });
    });
  </script>
 <script>
  
    function redirectToPage() {
    @auth
        @if(auth()->user()->role === 'admin') // Check if the user has an admin role
            // Change the text to 'Admin Dashboard' for admin users
            document.getElementById("submitListingBtn").innerText = "Admin";
            // Redirect to the admin dashboard route
            window.location.href = "{{ route('admin_dashboard') }}";
        @else
            // Change the text to 'Dashboard' for normal users
            document.getElementById("submitListingBtn").innerText = "Dashboard";
            // Redirect to the normal dashboard route
            window.location.href = "{{ route('packages.select') }}";
        @endif
    @endauth

    @guest
        // Keep the text as 'Submit Listing' for guests
        document.getElementById("submitListingBtn").innerText = "Sell Car";
        // Redirect to the login route for guests
        window.location.href = "{{ route('login') }}";
    @endguest
}
function redirectToPayment() {
    @auth
        @if(auth()->user()->role === 'admin') // Check if the user has an admin role
            // Change the text to 'Admin Dashboard' for admin users
            document.getElementById("submitListingBtn").innerText = "Admin";
            // Redirect to the admin dashboard route
            window.location.href = "{{ route('admin_dashboard') }}";
        @else
            // Change the text to 'Dashboard' for normal users
            document.getElementById("submitListingBtn").innerText = "Dashboard";
            // Redirect to the normal dashboard route
            window.location.href = "{{ route('packages.select') }}";
        @endif
    @endauth

    @guest
        // Keep the text as 'Submit Listing' for guests
        document.getElementById("submitListingBtn").innerText = "Sell Car";
        // Redirect to the login route for guests
        window.location.href = "{{ route('login') }}";
    @endguest
}

// Update the button text based on the user's authentication and role when the page loads
window.onload = function() {
    @auth
        @if(auth()->user()->role === 'admin') // Check if the user has an admin role
            // Change the text to 'Admin Dashboard' for admin users
            document.getElementById("submitListingBtn").innerText = "Admin";
        @else
            // Change the text to 'Dashboard' for normal users
            document.getElementById("submitListingBtn").innerText = "Sell Car";
        @endif
    @endauth

    @guest
        // Keep the text as 'Submit Listing' for guests
        document.getElementById("submitListingBtn").innerText = "Sell Car";
    @endguest
};

</script>





<script src="{{ asset('tinymce/tinymce.min.js') }}"></script>

  
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('richTextModal');
    const openModalBtns = document.querySelectorAll('.open-modal');

    openModalBtns.forEach(button => {
      button.addEventListener('click', () => {
        modal.style.display = 'block';
        tinymce.init({
          selector: '#richTextModal textarea',
          menubar: false,
          plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons'
          ],
          toolbar: 'undo redo | blocks | ' +
            'bold italic forecolor |' +
            'emoticons giphy|image media| bullist numlist outdent indent' +
            'removeformat | alignright | alignjustify alignleft aligncenter',
          content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
          branding: false,
          images_upload_handler: function (blobInfo, progress) {
            return new Promise((resolve, reject) => {
                let data = new FormData();
                data.append('file', blobInfo.blob());

                fetch("{{ route('forum.upload.image') }}", {
                    method: 'POST',
                    body: data,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                })
                .then(response => {
                    console.log('Server response:', response);
                    if (!response.ok) {
                        throw new Error('HTTP error! status: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Parsed JSON response:', data);
                    if (data && data.location) {
                        resolve(data.location); 
                    } else if (data && data.error) {
                        reject(data.error);
                    } else {
                        reject('Invalid response from server'); 
                    }
                })
                .catch(error => {
                    console.error('Image upload failed:', error);
                    reject('Image upload failed: ' + error.message);
                });
            });
        },
        file_picker_types: 'media',
    file_picker_callback: function (callback, value, meta) {
        if (meta.filetype === 'media') {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'video/*');

            input.onchange = function () {
                const file = this.files[0];
                const formData = new FormData();
                formData.append('file', file);

                fetch("{{ route('forum.upload.video') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data && data.location) {
                        callback(data.location, { source: data.location });
                    } else {
                        console.error('Invalid response from server');
                    }
                })
                .catch(error => {
                    console.error('Video upload failed:', error);
                });
            };

            input.click();
        }
    },
         
          setup: function (editor) {
            editor.ui.registry.addButton('giphy', {
              text: 'GIF',
              onAction: function () {
                openGiphyModal(editor);
              }
            });
          }
        });
      });
    });

    const closeModalBtn = document.querySelector('.close-btn');
    closeModalBtn.addEventListener('click', () => {
      modal.style.display = 'none';
      tinymce.remove();
    });

    window.addEventListener('click', (event) => {
      if (event.target === modal) {
        modal.style.display = 'none';
        tinymce.remove();
      }
    });

    function openGiphyModal(editor) {
      const existingModal = document.getElementById('giphyModal');
      if (existingModal) existingModal.remove(); // Remove old modal if it exists

      const giphyModal = document.createElement('div');
      giphyModal.id = 'giphyModal';
      giphyModal.style.position = 'fixed';
      giphyModal.style.top = '50%';
      giphyModal.style.left = '50%';
      giphyModal.style.transform = 'translate(-50%, -50%)';
      giphyModal.style.background = 'white';
      giphyModal.style.padding = '20px';
      giphyModal.style.borderRadius = '10px';
      giphyModal.style.boxShadow = '0 0 10px rgba(0,0,0,0.2)';
      giphyModal.style.width = '400px';
      giphyModal.style.zIndex = '10000';

      giphyModal.innerHTML = `
        <input type="text" id="giphySearch" placeholder="Search for GIFs..." 
          style="width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px;">
        <div id="giphyResults" style="max-height: 300px; overflow-y: auto; display: flex; flex-wrap: wrap; gap: 5px;"></div>
        <button id="closeGiphy" style="margin-top: 10px; padding: 8px 12px; border: none; background: red; color: white; cursor: pointer; border-radius: 5px;">Close</button>
      `;

      document.body.appendChild(giphyModal);

    
      document.getElementById('closeGiphy').addEventListener('click', closeGiphyModal);

     
      document.getElementById('giphySearch').addEventListener('input', function () {
        fetchGiphyGIFs(this.value, editor);
      });
    }

    function closeGiphyModal() {
      const modal = document.getElementById('giphyModal');
      if (modal) modal.remove();
    }

    function fetchGiphyGIFs(query, editor) {
      const API_KEY = 'LoEtBVykwd74rrZOM6oCxaXolWJu8cQn'; 
      const url = `https://api.giphy.com/v1/gifs/search?api_key=${API_KEY}&q=${query}&limit=10`;

      fetch(url)
        .then(response => response.json())
        .then(data => {
          const resultsDiv = document.getElementById('giphyResults');
          resultsDiv.innerHTML = '';

          data.data.forEach(gif => {
            const img = document.createElement('img');
            img.src = gif.images.fixed_height_small.url;
            img.style.cursor = 'pointer';
            img.style.width = '100px';
            img.style.height = '100px';
            img.style.objectFit = 'cover';
            img.style.borderRadius = '5px';

            img.addEventListener('click', function () {
              insertGifIntoEditor(editor, gif.images.original.url);
            });

            resultsDiv.appendChild(img);
          });
        })
        .catch(error => console.error('Error fetching GIFs:', error));
    }

    function insertGifIntoEditor(editor, gifUrl) {
      editor.execCommand('mceInsertContent', false, `<img src="${gifUrl}" alt="GIF" style="max-width:100%;">`);
      closeGiphyModal();
    }
  });
</script>



<script>
let modalStack = JSON.parse(sessionStorage.getItem('modalStack')) || [];

// Universal modal open handler
function handleModalOpen(modalId) {
  modalStack.push(modalId);
  sessionStorage.setItem('modalStack', JSON.stringify(modalStack));
  history.pushState({ action: 'modal-open', id: modalId }, '');
}

// Universal modal close handler
function handleModalClose(modalId) {
  const index = modalStack.indexOf(modalId);
  if (index > -1) {
    modalStack.splice(index, 1);
    sessionStorage.setItem('modalStack', JSON.stringify(modalStack));
  }
}

</script>

@auth

<div id="richTextModal" class="modal p-4">
  <div class="modal-content">
      <span class="close-btn">&times;</span>
      @if(auth()->user()->role != 'admin')
          <div>
              <p id="post-to-reply">Add Topic </p>
              @if(request()->is('forum-topic-category/*'))
                        <form action="{{ route('forum-post.create', ['forum_topic_category' => $forum_topic_category->id]) }}" method="post" enctype="multipart/form-data">
                    @else
                        <form action="" method="post" enctype="multipart/form-data">
                    @endif
                  @csrf
                  <div id="post-editor-container">
                          <div id="text-area">
                              <textarea name="content" placeholder="I am your rich text editor."></textarea>
                          </div>
                  </div>
                  <p id="media-upload">Media Upload</p>
                  <div id="media-upload-container">
                      <div class="upload-area" id="uploadArea">
                          <p>Add your documents here, and you can upload up to 5 files max</p>
                          <p class="subtext">Only support .jpg, .png files.</p>
                          <input type="file" name="media[]" id="fileInput" accept=".jpg, .png" multiple>
                          <p>Drag your file(s) or <span>Browse</span></p>
                          <div id="previewContainer" class="preview-container"></div>
                      </div>
                      <div id="buttons-container">
                          <p class="media-instructions">Only support .jpg, .png files</p>
                          <div id="buttons">
                              <button type="submit" id="submit">Submit</button>
                          </div>
                      </div>
                  </div>
              </form>
          </div>
      @endif
  </div>
</div>

@endauth
<style>
  .modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgba(0, 0, 0, 0.5);
            }

            .modal-content {
                background-color: #fff;
                margin: 5% auto;
                padding: 20px;
                border-radius: 10px;
                width: 95%;
                max-width: 800px;
                position: relative;
            }

            .close-btn {
                position: absolute;
                top: 10px;
                right: 20px;
                font-size: 20px;
                color: #000;
                cursor: pointer;
            }
</style>
  </body>
  <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-9HMSTR74RG"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-9HMSTR74RG');
</script>

</html>
