<?php
// dashboard.php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Printables-the printing expert</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet" />

    <!-- ==== ANIMATE ON SCROLL CSS CDN  ==== -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" />
    <style>
        .notification-popup {
    position: fixed;
    top: 80px; /* Adjusted to be a bit lower */
    right: 10px;
    width: 300px;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    border-radius: 5px;
    display: none; /* Hidden by default */
    z-index: 9999;
    transition: transform 0.3s ease, opacity 0.3s ease;
}

.notification-popup.show {
    display: block;
    transform: translateX(0);
    opacity: 1;
}

.notification-popup.hide {
    transform: translateX(100%);
    opacity: 0;
}
    </style>
 <script>
    function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'notification';
        notification.innerText = message;
        document.body.appendChild(notification);

        // Display the notification and then hide it after a delay
        setTimeout(() => {
            notification.classList.add('hidden');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 500); // 500ms for transition effect
        }, 1000); // Show notification for 3 seconds
    }

    function checkJobStatus() {
        fetch('check_job_status.php')
            .then(response => response.json())
            .then(data => {
                if (data.jobs.length > 0) {
                    data.jobs.forEach(job => {
                        const message = `Your print job with ID ${job.id} has been marked as ${job.status}.`;
                        showNotification(message);
                    });
                }
            })
            .catch(error => console.error('Error fetching job status:', error));
    }

    // Check for new jobs every 30 seconds
    setInterval(checkJobStatus, 30000);

    // Initial check when the page loads
    checkJobStatus();
</script>


<style>

  
       .notification {
            position: fixed;
            top: 100px;
            right: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            opacity: 1;
            transition: opacity 0.5s;
        }
        .notification.hidden {
            opacity: 0;
        }

        /* Notification Popup Styles */
        .notification-popup {
            position: fixed;
            top: 80px; /* Adjusted to be a bit lower */
            right: 10px;
            width: 300px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 5px;
            display: none; /* Hidden by default */
            z-index: 9999;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        .notification-popup.show {
            display: block;
            transform: translateX(0);
            opacity: 1;
        }

        .notification-popup.hide {
            transform: translateX(100%);
            opacity: 0;
        }

        /* Popup content styles */
        #notification-content {
            max-height: 400px;
            overflow-y: auto;
        }

        #notification-content p {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<>

<div id="loading">
        <div id="spinner"></div>
    </div>

<nav class="navbar navbar-expand-lg navbar-dark menu shadow fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="images/logo.png" alt="logo image" height="60px">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link active" aria-current="page" href="#home">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="additional/about/index.html">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#portfolio">Products</a></li>
                <li class="nav-item"><a class="nav-link" href="orders.php">Order</a></li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
                <!-- Notification Icon -->
                <li class="nav-item">
    <a class="nav-link" href="notification.php" id="notificationIcon">
        <i class="fas fa-bell"></i>
        <span id="notificationCount" class="badge bg-danger"></span>
    </a>
</li>
            </ul>
            <button type="button" class="rounded-pill btn-rounded" data-bs-toggle="modal" data-bs-target="#customizationModal">
                Book a Print Job
                <span>
                    <i class="fas fa-arrow-right"></i>
                </span>
            </button>
        </div>
    </div>
</nav>




</nav>

 

  <!-- Modal -->
<div class="modal fade" id="customizationModal" tabindex="-1" aria-labelledby="customizationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="background: linear-gradient(-45deg, #d122e3 0%, #6610f2 100%); color: #ffffff; border: none; border-radius: 15px;">
      <div class="modal-header" style="border: none; padding: 15px;">
        <h5 class="modal-title" id="customizationModalLabel" style="color: #ffffff;">Customize Your Print Job</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" style="background-color: #ffe6f2; padding: 0;">
        <iframe src="customization.html" style="width:100%; height:80vh; border: none; background-color: #f2ccff; border-radius: 15px 15px 0 0;"></iframe>
      </div>
      <div class="modal-footer" style="border: none; padding: 10px;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background-color: #ff99cc; border: none; color: #ffffff; border-radius: 10px;">Close</button>
      </div>
    </div>
  </div>
</div>


  



<section id="home" class="intro-section">
  <div class="container">
    <div class="row align-items-center text-white">

      <div class="col-md-6 intros text-start">
        <h1 class="display-2">
          <span class="display-2--intro">Welcome! to Printables</span>
          <span class="display-2--description lh-base">
            <b>"Printing Excellence Delivered"</b>

          </span>
        </h1>
        <button type="button" class="rounded-pill btn-rounded" data-bs-toggle="modal" data-bs-target="#customizationModal">
                Book a Print Job
                <span>
                    <i class="fas fa-arrow-right"></i>
                </span>
            </button>
      </div>
      <!-- START THE CONTENT FOR THE VIDEO -->
      <div class="col-md-6 intros text-end">
        <div class="video-box">
          <img src="images/iii.png" alt="video illutration" class="img-fluid">
          
        </div>
      </div>
    </div>
  </div>
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#ffffff" fill-opacity="1" d="M0,160L48,176C96,192,192,224,288,208C384,192,480,128,576,133.3C672,139,768,213,864,202.7C960,192,1056,96,1152,74.7C1248,53,1344,107,1392,133.3L1440,160L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>
</section>



  <section id="campanies" class="campanies">
    <div class="container">
      <div class="row text-center">
        <h3 class="fw-bold lead mb-3"><b>Print Shops Available</b></h3>
        <div class="heading-line mb-5"></div>
      </div>
    </div>
  
    <div class="container">
      <div class="row">
        <div class="col-md-4 col-lg-2">
          <div class="campanies__logo-box shadow-sm">
            <img src="images/campanies/Thodupuzha.png" alt="Campany 1 logo" title="Campany 1 Logo" class="img-fluid">
          </div>
        </div>
        <div class="col-md-4 col-lg-2">
          <div class="campanies__logo-box shadow-sm">
            <img src="images/campanies/Kattappana.png" alt="Campany 2 logo" title="Campany 2 Logo" class="img-fluid">
          </div>
        </div>
        <div class="col-md-4 col-lg-2">
          <div class="campanies__logo-box shadow-sm">
            <img src="images/campanies/Munnar.png" alt="Campany 3 logo" title="Campany 3 Logo" class="img-fluid">
          </div>
        </div>
        <div class="col-md-4 col-lg-2">
          <div class="campanies__logo-box shadow-sm">
            <img src="images/campanies/Kumily.png" alt="Campany 4 logo" title="Campany 4 Logo" class="img-fluid">
          </div>
        </div>
        <div class="col-md-4 col-lg-2">
          <div class="campanies__logo-box shadow-sm">
            <img src="images/campanies/Nedumkandam.png" alt="Campany 5 logo" title="Campany 5 Logo" class="img-fluid">
          </div>
        </div>
        <div class="col-md-4 col-lg-2">
          <div class="campanies__logo-box shadow-sm">
            <img src="images/campanies/Adimali.png" alt="Campany 6 logo" title="Campany 6 Logo" class="img-fluid">
          </div>
        </div>
      </div>
    </div>
  </section>


<section id="services" class="services">
  <div class="container">
    <div class="row text-center">
      <h1 class="display-3 fw-bold">Our Services</h1>
      <div class="heading-line mb-1"></div>
    </div>
    <div class="row pt-2 pb-2 mt-0 mb-3">
      <div class="col-md-6 border-right">
        <div class="bg-white p-3">
          <h2 class="fw-bold text-capitalize text-center">
            Printables is a comprehensive solution designed to streamline and enhance the printing experience for users. 
          </h2>
        </div>
      </div>
      <div class="col-md-6">
        <div class="bg-white p-4 text-start">
          <p class="fw-light">
            encompasses a range of functionalities including user authentication, shop selection, print job customization, cost estimation, payment processing, notifications, job tracking, and additional services for custom products. 
          </p>
        </div>
      </div>
    </div>
  </div>

  <div class="container">


    <div class="row">
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 services mt-4">
        <div class="services__content">
          <div class="icon d-block fas fa-paper-plane"></div>
          <h3 class="display-3--title mt-1">Document Printing</h3>
          <p class="lh-lg">
            Single-sided<br>
            Double-sided<br>
            Black-and-white<br>
            Color
          </p>
          <a href="additional/document printing/index.html">
  <button type="button" class="rounded-pill btn-rounded border-primary">Learn more
    <span><i class="fas fa-arrow-right"></i></span>
  </button>
</a>

        </div>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 services mt-4 text-end">
        <div class="services__pic">
          <img src="images/services/service-1.png" alt="marketing illustration" class="img-fluid">
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 services mt-4 text-start">
        <div class="services__pic">
          <img src="images/services/service-2.png" alt="web development illustration" class="img-fluid">
        </div>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 services mt-4">
        <div class="services__content">
          <div class="icon d-block fas fa-paper-plane"></div>
          <h3 class="display-3--title mt-1">Print Job Customization</h3>
          <p class="lh-lg">
            Paper sizes (e.g., A4, A3, Letter, Legal)<br>
            Paper types (e.g., matte, glossy, cardstock)<br>
            Binding options (e.g., stapled, spiral, hardcover)<br>
            Finishing services (e.g., lamination, trimming)<br>
          </p>
          <a href="additional/print job costamization/index.html">
  <button type="button" class="rounded-pill btn-rounded border-primary">Learn more
    <span><i class="fas fa-arrow-right"></i></span>
  </button>
</a>
        </div>
      </div>
    </div>


    
    <div class="row">
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 services mt-4">
        <div class="services__content">
          <div class="icon d-block fas fa-paper-plane"></div>
          <h3 class="display-3--title mt-1">Touch-Free Instant Payment</h3>
          <p class="lh-lg">
          Handsfree Transaction<br>
          Multiple payment options (UPI,NET BANKING)
          </p>
          <a href="additional/document printing/index.html">
  <button type="button" class="rounded-pill btn-rounded border-primary">Learn more
    <span><i class="fas fa-arrow-right"></i></span>
  </button>
</a>

        </div>
      </div>
      <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 services mt-4 text-end">
        <div class="services__pic">
          <img src="images/services/service-3.png" alt="marketing illustration" class="img-fluid">
        </div>
      </div>
    </div>
</section>


<section id="testimonials" class="testimonials">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#fff" fill-opacity="1" d="M0,96L48,128C96,160,192,224,288,213.3C384,203,480,117,576,117.3C672,117,768,203,864,202.7C960,203,1056,117,1152,117.3C1248,117,1344,203,1392,245.3L1440,288L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path></svg>
  <div class="container">
    <div class="row text-center text-white">
      <h1 class="display-3 fw-bold">Testimonials</h1>
      <hr style="width: 100px; height: 3px; " class="mx-auto">
      <p class="lead pt-1">what our clients are saying</p>
    </div>

  
    <div class="row align-items-center">
      <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
         
          <div class="carousel-item active">
            
            <div class="testimonials__card">
              <p class="lh-lg">
                <i class="fas fa-quote-left"></i>
                "PRINTABLES has transformed the way I handle my printing needs. The customization options are fantastic, and the real-time cost estimation saves me so much time. Plus, the notifications keep me updated on my order status, which is incredibly convenient. I highly recommend PRINTABLES to any business owner!"
                <i class="fas fa-quote-right"></i>
                <div class="ratings p-1">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                </div>
              </p>
            </div>
          
            <div class="testimonials__picture">
              <img src="images/testimonials/client-1.jpg" alt="client-1 picture" class="rounded-circle img-fluid">
            </div>
           
            <div class="testimonials__name">
              <h3>Nygil</h3>
              <p class="fw-light">CEO & founder</p>
            </div>
          </div>     
          
          <div class="carousel-item">
            
            <div class="testimonials__card">
              <p class="lh-lg">
                <i class="fas fa-quote-left"></i>
                "As a student, I often need to print reports and assignments at the last minute. PRINTABLES' user-friendly interface and quick service have been a lifesaver. The variety of printing options ensures my work always looks professional. I love the scheduling feature – it helps me plan my printing needs around my busy schedule."
                <i class="fas fa-quote-right"></i>
                <div class="ratings p-1">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                </div>
              </p>
            </div>
            <!-- client picture  -->
            <div class="testimonials__picture">
              <img src="images/testimonials/client-2.jpg" alt="client-2 picture" class="rounded-circle img-fluid">
            </div>
            <!-- client name & role  -->
            <div class="testimonials__name">
              <h3>Joy Marete</h3>
              <p class="fw-light">Finance Manager</p>
            </div>
          </div>     
          <!-- CAROUSEL ITEM 3 -->
          <div class="carousel-item">
            <!-- testimonials card  -->
            <div class="testimonials__card">
              <p class="lh-lg">
                <i class="fas fa-quote-left"></i>"PRINTABLES has been a game-changer for my classroom. From printing lesson materials to creating custom t-shirts for school events, everything is so easy and efficient. The secure transaction process and order history feature give me peace of mind. It's a must-have for educators!"
                <i class="fas fa-quote-right"></i>
                <div class="ratings p-1">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                </div>
              </p>
            </div>
            <!-- client picture  -->
            <div class="testimonials__picture">
              <img src="images/testimonials/client-3.jpg" alt="client-3 picture" class="rounded-circle img-fluid">
            </div>
            <!-- client name & role  -->
            <div class="testimonials__name">
              <h3>ClaireBelle Zawadi</h3>
              <p class="fw-light">Global brand manager</p>
            </div>
          </div>     
          <!-- CAROUSEL ITEM 4 -->
          <div class="carousel-item">
            <!-- testimonials card  -->
            <div class="testimonials__card">
              <p class="lh-lg">
                <i class="fas fa-quote-left"></i>
                "The customization features of PRINTABLES are incredible. I can choose the exact paper size and material I need for my designs, ensuring the final product is exactly how I envisioned it. The ability to select different shop locations is also a huge plus. Highly recommend for any creative professional!"
                <i class="fas fa-quote-right"></i>
                <div class="ratings p-1">
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                  <i class="fas fa-star"></i>
                </div>
              </p>
            </div>
            <!-- client picture  -->
            <div class="testimonials__picture">
              <img src="images/testimonials/client-4.png" alt="client-4 picture" class="rounded-circle img-fluid">
            </div>
            <!-- client name & role  -->
            <div class="testimonials__name">
              <h3>Joyal Tom</h3>
              <p class="fw-light">repp</p>
            </div>
          </div>     
        </div>
        <div class="text-center">
          <button class="btn btn-outline-light fas fa-long-arrow-alt-left" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
        </button>
        <button class="btn btn-outline-light fas fa-long-arrow-alt-right" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
        </button>
        </div>
      </div>
    </div>
  </div>
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#fff" fill-opacity="1" d="M0,96L48,128C96,160,192,224,288,213.3C384,203,480,117,576,117.3C672,117,768,203,864,202.7C960,203,1056,117,1152,117.3C1248,117,1344,203,1392,245.3L1440,288L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>
</section>

<!-- /////////////////////////////////////////////////////////////////////////////////////////////////
                       START SECTION 6 - THE FAQ 
//////////////////////////////////////////////////////////////////////////////////////////////////////-->
<section id="faq" class="faq">
  <div class="container">
    <div class="row text-center">
      <h1 class="display-3 fw-bold text-uppercase">faq</h1>
      <div class="heading-line"></div>
      <p class="lead">frequently asked questions, get knowledge befere hand</p>
    </div>
    <!-- ACCORDION CONTENT  -->
    <div class="row mt-5">
      <div class="col-md-12">
        <div class="accordion" id="accordionExample">
          <!-- ACCORDION ITEM 1 -->
          <div class="accordion-item shadow mb-3">
            <h2 class="accordion-header" id="headingOne">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                What is PRINTABLES?
              </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <strong>Printables is a comprehensive printing solution designed to enhance your printing experience by offering diverse printing options, customization features, real-time cost estimation, secure transactions, and convenient notifications.</strong>               </div>
            </div>
          </div>
             <!-- ACCORDION ITEM 2 -->
          <div class="accordion-item shadow mb-3">
            <h2 class="accordion-header" id="headingTwo">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                What types of printing options do you offer?
              </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <strong>We offer a variety of printing options, including single-sided, double-sided, color, and black-and-white printing. You can also choose from various paper sizes and materials to suit your specific needs.</strong>
              </div>
            </div>
          </div>
             <!-- ACCORDION ITEM 3 -->
          <div class="accordion-item shadow mb-3">
            <h2 class="accordion-header" id="headingThree">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                Can I customize my print orders?
              </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <strong>Yes, you can customize your print orders with different paper sizes, materials, and additional services. You can also schedule your printing and choose specialty products like custom t-shirts and mugs.</strong>
              </div>
            </div>
          </div>
             <!-- ACCORDION ITEM 4 -->
          <div class="accordion-item shadow mb-3">
            <h2 class="accordion-header" id="headingFour">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                How do I estimate the cost of my print order?
              </button>
            </h2>
            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
              <div class="accordion-body">
                <strong>Our platform provides real-time cost estimation based on your selected printing options, customization features, and additional services. You can see the estimated cost before finalizing your order.</strong>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ///////////////////////////////////////////////////////////////////////////////////////////////////
                              START SECTION 7 - THE PORTFOLIO  
//////////////////////////////////////////////////////////////////////////////////////////////////////-->

<section id="portfolio" class="portfolio">
  <div class="container">
    <div class="row text-center mt-5">
      <h1 class="display-3 fw-bold text-capitalize">Products</h1>
      <div class="heading-line"></div>
      <p class="lead">
        The Printing Expert, we offer a wide range of high-quality printing products to meet all your needs. Here are some of the key products and services we provide:
      </p>
    </div>
    

    <!-- START THE PORTFOLIO ITEMS  -->
    <div class="row">
      <div class="col-lg-4 col-md-6">
        <div class="portfolio-box shadow">
          <img src="images/portfolio/portfolio-1.jpeg" alt="portfolio 1 image" title="portfolio 1 picture" class="img-fluid">
          <div class="portfolio-info">
            <div class="caption">
              <h4>PRINTED MUGS</h4>
              
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="portfolio-box shadow">
          <img src="images/portfolio/portfolio-2.jpeg" alt="portfolio 2 image" title="portfolio 2 picture" class="img-fluid">
          <div class="portfolio-info">
            <div class="caption">
              <h4>PRINTED TSHIRTS</h4>
              
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="portfolio-box shadow">
          <img src="images/portfolio/portfolio-3.jpeg" alt="portfolio 3 image" title="portfolio 3 picture" class="img-fluid">
          <div class="portfolio-info">
            <div class="caption">
              <h4>UNLIMITED COPIES</h4>
              
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="portfolio-box shadow">
          <img src="images/portfolio/portfolio-4.jpeg" alt="portfolio 4 image" title="portfolio 4 picture" class="img-fluid">
          <div class="portfolio-info">
            <div class="caption">
              <h4>DIFFERENT BINDING <BR>OPTIONS</h4>
              
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="portfolio-box shadow">
          <img src="images/portfolio/portfolio-5.jpeg" alt="portfolio 5 image" title="portfolio 5 picture" class="img-fluid">
          <div class="portfolio-info">
            <div class="caption">
              <h4>COLOR COPIES</h4>
             
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="portfolio-box shadow">
          <img src="images/portfolio/portfolio-6.jpeg" alt="portfolio 6 image" title="portfolio 6 picture" class="img-fluid">
          <div class="portfolio-info">
            <div class="caption">
              <h4>PRINTED BOOKS</h4>
              
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="portfolio-box shadow">
          <img src="images/portfolio/portfolio-7.jpeg" alt="portfolio 7 image" title="portfolio 7 picture" class="img-fluid">
          <div class="portfolio-info">
            <div class="caption">
              <h4>BLACK AND WHITE COPIES</h4>
              
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="portfolio-box shadow">
          <img src="images/portfolio/portfolio-8.jpeg" alt="portfolio 8 image" title="portfolio 8 picture" class="img-fluid">
          <div class="portfolio-info">
            <div class="caption">
              <h4>DIFFERENT TYPES OF MATERIALS</h4>
              
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="portfolio-box shadow">
          <img src="images/portfolio/portfolio-9.jpeg" alt="portfolio 9 image" title="portfolio 9 picture" class="img-fluid">
          <div class="portfolio-info">
            <div class="caption">
              <h4>FAST SPOT DELEVERY</h4>
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- /////////////////////////////////////////////////////////////////////////////////////////////////////////
              START SECTION 8 - GET STARTED  
/////////////////////////////////////////////////////////////////////////////////////////////////////////-->
<section id="contact" class="get-started">
  <div class="container">
    <div class="row text-center">
      <h1 class="display-3 fw-bold text-capitalize">Report a Complaint</h1>
      <div class="heading-line"></div>
      <p class="lh-lg">
        If you have any issues or concerns, please let us know. We're here to help and ensure your satisfaction.      </p>
    </div>

    <!-- START THE CTA CONTENT  -->
    <div class="row text-white">
      <div class="col-12 col-lg-6 gradient shadow p-3">
        <div class="cta-info w-100">
          <h4 class="display-4 fw-bold">We're Here to Assist You</h4>
          <p class="lh-lg">
            Your feedback is important to us. Please provide detailed information about your issue, and our support team will get back to you as soon as possible.
          </p>
          <h3 class="display-3--brief">How it works?</h3>
          <ul class="cta-info__list">
            <li>Submit your complaint using the form.</li>
            <li>Our team will review your complaint.</li>
            <li>We will contact you to resolve the issue.</li>
          </ul>
        </div>
      </div>
      <div class="col-12 col-lg-6 bg-white shadow p-3">
        <div class="form w-100 pb-2">
          <h4 class="display-3--title mb-5">Register Your Complaint</h4>
          <form action="submit_complaint.php" method="post" class="row">
            <div class="col-lg-6 col-md mb-3">
              <input type="text" name="first_name" placeholder="First Name" id="inputFirstName" class="shadow form-control form-control-lg" required>
            </div>
            <div class="col-lg-6 col-md mb-3">
              <input type="text" name="last_name" placeholder="Last Name" id="inputLastName" class="shadow form-control form-control-lg" required>
            </div>
            <div class="col-lg-12 mb-3">
              <input type="email" name="email" placeholder="email address" id="inputEmail" class="shadow form-control form-control-lg" required>
            </div>
            <div class="col-lg-12 mb-3">
              <textarea name="message" placeholder="message" id="message" rows="8" class="shadow form-control form-control-lg" required></textarea>
            </div>
            <div class="text-center d-grid mt-1">
              <button type="submit" class="btn btn-primary rounded-pill pt-3 pb-3">
                Submit
                <i class="fas fa-paper-plane"></i>
              </button>
            </div>
          </form>
          <div id="popupMessage" style="display: none; position: fixed; top: 20px; right: 20px; padding: 10px; border: 1px solid #ccc; background-color: #fff;">
        <span id="popupText"></span>
        <button onclick="document.getElementById('popupMessage').style.display='none'">Close</button>
    </div>

    <script>
    $(document).ready(function() {
        $('#complaintForm').on('submit', function(event) {
            event.preventDefault();

            $.ajax({
                url: 'submit_complaint.php',
                type: 'post',
                data: $(this).serialize(),
                success: function(response) {
                    var popup = $('#popupMessage');
                    $('#popupText').text(response);
                    popup.css('display', 'block');

                    // Customize the appearance based on response content
                    if (response.includes("successfully")) {
                        popup.css('border-color', 'green');
                        popup.css('background-color', '#d4edda');
                    } else {
                        popup.css('border-color', 'red');
                        popup.css('background-color', '#f8d7da');
                    }
                },
                error: function() {
                    $('#popupText').text('An error occurred. Please try again.');
                    $('#popupMessage').css('display', 'block');
                }
            });
        });
    });
    </script>
          
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ///////////////////////////////////////////////////////////////////////////////////////////
                           START SECTION 9 - THE FOOTER  
///////////////////////////////////////////////////////////////////////////////////////////////-->
<footer class="footer">
  <div class="container">
    <div class="row">
      <!-- CONTENT FOR THE MOBILE NUMBER  -->
      <div class="col-md-4 col-lg-4 contact-box pt-1 d-md-block d-lg-flex d-flex">
        <div class="contact-box__icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-phone-call" viewBox="0 0 24 24" stroke-width="1" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
            <path d="M15 7a2 2 0 0 1 2 2" />
            <path d="M15 3a6 6 0 0 1 6 6" />
          </svg>
        </div>
        <div class="contact-box__info">
          <a href="#" class="contact-box__info--title">+91 9037003015</a>
          <p class="contact-box__info--subtitle">  Mon-Fri 9am-6pm</p>
        </div>
      </div>  
      <!-- CONTENT FOR EMAIL  -->
      <div class="col-md-4 col-lg-4 contact-box pt-1 d-md-block d-lg-flex d-flex">
        <div class="contact-box__icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mail-opened" viewBox="0 0 24 24" stroke-width="1" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <polyline points="3 9 12 15 21 9 12 3 3 9" />
            <path d="M21 9v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10" />
            <line x1="3" y1="19" x2="9" y2="13" />
            <line x1="15" y1="13" x2="21" y2="19" />
          </svg>
        </div>
        <div class="contact-box__info">
          <a href="#" class="contact-box__info--title">printables@company.com</a>
          <p class="contact-box__info--subtitle">Online support</p>
        </div>
      </div>
      <!-- CONTENT FOR LOCATION  -->
      <div class="col-md-4 col-lg-4 contact-box pt-1 d-md-block d-lg-flex d-flex">
        <div class="contact-box__icon">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-2" viewBox="0 0 24 24" stroke-width="1" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <line x1="18" y1="6" x2="18" y2="6.01" />
            <path d="M18 13l-3.5 -5a4 4 0 1 1 7 0l-3.5 5" />
            <polyline points="10.5 4.75 9 4 3 7 3 20 9 17 15 20 21 17 21 15" />
            <line x1="9" y1="4" x2="9" y2="17" />
            <line x1="15" y1="15" x2="15" y2="20" />
          </svg>
        </div>
        <div class="contact-box__info">
          <a href="#" class="contact-box__info--title">Head Office Kattappana</a>
          <p class="contact-box__info--subtitle">Kattappana,idukki</p>
        </div>
      </div>
    </div>
  </div>

  <!-- START THE SOCIAL MEDIA CONTENT  -->
  <div class="footer-sm" style="background-color: #212121;">
    <div class="container">
      <div class="row py-4 text-center text-white">
        <div class="col-lg-5 col-md-6 mb-4 mb-md-0">
          connect with us on social media
        </div>
        <div class="col-lg-7 col-md-6">
          <a href="#"><i class="fab fa-facebook"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-linkedin"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
    </div>
  </div>

  <!-- START THE CONTENT FOR THE CAMPANY INFO -->
  <div class="container mt-5">
    <div class="row text-white justify-content-center mt-3 pb-3">
      <div class="col-12 col-sm-6 col-lg-6 mx-auto">
        <h5 class="text-capitalize fw-bold">PRINTABLES-<I>the printing expert</I></h5>
        <hr class="bg-white d-inline-block mb-4" style="width: 60px; height: 2px;">
        <p class="lh-lg">
          PRINTABLES - The Printing Expert is a cutting-edge solution designed to revolutionize the printing experience. Our platform offers a wide range of printing options, including single-sided, double-sided, color, and black-and-white prints. With robust customization features, users can select from various paper sizes and materials, schedule their prints, and add additional services as needed.         </p>
      </div>
      <div class="col-12 col-sm-6 col-lg-2 mb-4 mx-auto">
        <h5 class="text-capitalize fw-bold">Links</h5>
        <hr class="bg-white d-inline-block mb-4" style="width: 60px; height: 2px;">
        <ul class="list-inline campany-list">
          <li><a href="#home">Home</a></li>
          <li><a href="#campains">Print Shops</a></li>
          <li><a href="#services">Services</a></li>
          <li><a href="#portfolio">Products</a></li>
        </ul>
      </div>
      
      <div class="col-12 col-sm-6 col-lg-2 mb-4 mx-auto">
        <h5 class="text-capitalize fw-bold">Shops Available</h5>
        <hr class="bg-white d-inline-block mb-4" style="width: 60px; height: 2px;">
        <ul class="list-inline campany-list">
          <li><a href="shop/kattappana/index.html">Kattappana</a></li>
          <li><a href="shop/nedumkandam/index.html">Nedumkandam</a></li>
          <li><a href="shop/kumily/index.html">Kumily</a></li>
          <li><a href="shop/thodupuzha/index.html">Thodupuzha</a></li>
          <li><a href="shop/Adimali/index.html">Adimali</a></li>

        </ul>
      </div>
    </div>
  </div>

  <!-- START THE COPYRIGHT INFO  -->
  <div class="footer-bottom pt-5 pb-5">
    <div class="container">
      <div class="row text-center text-white">
        <div class="col-12">
          <div class="footer-bottom__copyright">
            &COPY; Copyright 2024 <a href="#">Printables</a> | Created by <a href="" target="_blank">Noel & Joel</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</footer>

<!-- BACK TO TOP BUTTON  -->
<a href="#" class="shadow btn-primary rounded-circle back-to-top">
  <i class="fas fa-chevron-up"></i>
</a>



   
    <script src="assets/vendors/js/glightbox.min.js"></script>

    
    
    </script>
     <script src="assets/js/bootstrap.bundle.min.js"></script>
     <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
     <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- ==== GSAP CDN ==== -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.8.0/gsap.min.js"></script>
     
</body>
</html>
