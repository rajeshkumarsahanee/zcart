<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <!--[if IE]>
                <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
        <![endif]-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Spice Shoppe Stores - Bootstrap 3 Template</title>
        <?php include_once('css.php'); ?>
        <!--[if lt IE 9]>
                <script src="js/ie8-responsive-file-warning.js"></script>
        <![endif]-->

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

        <!-- Fav and touch icons -->
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="images/fav-144.html">
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="images/fav-114.html">
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="images/fav-72.html">
        <link rel="apple-touch-icon-precomposed" href="images/fav-57.html">
        <link rel="shortcut icon" href="images/fav.html">	
    </head>
    <body>
        <!-- Header Section Starts -->
        <header id="header-area">
            <!-- Header Top Starts -->
            <div class="header-top">
                <div class="container">
                    <!-- Header Links Starts -->
                    <div class="col-sm-8 col-xs-12">
                        <div class="header-links">
                            <ul class="nav navbar-nav pull-left">
                                <li>
                                    <a href="index.html">
                                        <i class="fa fa-home" title="Home"></i>
                                        <span class="hidden-sm hidden-xs">
                                            Home
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">	
                                        <i class="fa fa-heart" title="Wish List"></i>
                                        <span class="hidden-sm hidden-xs">
                                            Wish List(0)
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-user" title="My Account"></i>
                                        <span class="hidden-sm hidden-xs">
                                            My Account
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="cart.html">
                                        <i class="fa fa-shopping-cart" title="Shopping Cart"></i>
                                        <span class="hidden-sm hidden-xs">
                                            Shopping Cart
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <i class="fa fa-crosshairs" title="Checkout"></i>
                                        <span class="hidden-sm hidden-xs">
                                            Checkout
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="register.html">
                                        <i class="fa fa-unlock" title="Register"></i>
                                        <span class="hidden-sm hidden-xs">
                                            Register
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="login.html">
                                        <i class="fa fa-lock" title="Login"></i>
                                        <span class="hidden-sm hidden-xs">
                                            Login
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- Header Links Ends -->
                    <!-- Currency & Languages Starts -->
                    <div class="col-sm-4 col-xs-12">
                        <div class="pull-right">
                            <!-- Currency Starts -->
                            <div class="btn-group">
                                <button class="btn btn-link dropdown-toggle" data-toggle="dropdown">
                                    Currency
                                    <i class="fa fa-caret-down"></i>
                                </button>
                                <ul class="pull-right dropdown-menu">
                                    <li><a tabindex="-1" href="#">Pound </a></li>
                                    <li><a tabindex="-1" href="#">US Dollar</a></li>
                                    <li><a tabindex="-1" href="#">Euro</a></li>
                                </ul>
                            </div>
                            <!-- Currency Ends -->
                            <!-- Languages Starts -->
                            <div class="btn-group">
                                <button class="btn btn-link dropdown-toggle" data-toggle="dropdown">
                                    Language
                                    <i class="fa fa-caret-down"></i>
                                </button>
                                <ul class="pull-right dropdown-menu">
                                    <li>
                                        <a tabindex="-1" href="#">English</a>
                                    </li>
                                    <li>
                                        <a tabindex="-1" href="#">French</a>
                                    </li>
                                </ul>
                            </div>
                            <!-- Languages Ends -->
                        </div>
                    </div>
                    <!-- Currency & Languages Ends -->
                </div>
            </div>
            <!-- Header Top Ends -->
            <!-- Starts -->
            <div class="container">
                <!-- Main Header Starts -->
                <div class="main-header">
                    <div class="row">
                        <!-- Logo Starts -->
                        <div class="col-md-6">
                            <div id="logo">
                                <a href="index.html"><img src="images/logo.png" title="Spice Shoppe" alt="Spice Shoppe" class="img-responsive" /></a>
                            </div>
                        </div>
                        <!-- Logo Starts -->
                        <!-- Search Starts -->
                        <div class="col-md-3">
                            <div id="search">
                                <div class="input-group">
                                    <input type="text" class="form-control input-lg" placeholder="Search">
                                    <span class="input-group-btn">
                                        <button class="btn btn-lg" type="button">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>	
                        </div>
                        <!-- Search Ends -->
                        <!-- Shopping Cart Starts -->
                        <div class="col-md-3">
                            <div id="cart" class="btn-group btn-block">
                                <button type="button" data-toggle="dropdown" class="btn btn-block btn-lg dropdown-toggle">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span class="hidden-md">Cart:</span> 
                                    <span id="cart-total">2 item(s) - $340.00</span>
                                    <i class="fa fa-caret-down"></i>
                                </button>
                                <ul class="dropdown-menu pull-right">
                                    <li>
                                        <table class="table hcart">
                                            <tr>
                                                <td class="text-center">
                                                    <a href="product.html">
                                                        <img src="images/product-images/hcart-thumb1.jpg" alt="image" title="image" class="img-thumbnail img-responsive" />
                                                    </a>
                                                </td>
                                                <td class="text-left">
                                                    <a href="product-full.html">
                                                        Seeds
                                                    </a>
                                                </td>
                                                <td class="text-right">x 1</td>
                                                <td class="text-right">$120.68</td>
                                                <td class="text-center">
                                                    <a href="#">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">
                                                    <a href="product.html">
                                                        <img src="images/product-images/hcart-thumb2.jpg" alt="image" title="image" class="img-thumbnail img-responsive" />
                                                    </a>
                                                </td>
                                                <td class="text-left">
                                                    <a href="product-full.html">
                                                        Organic
                                                    </a>
                                                </td>
                                                <td class="text-right">x 2</td>
                                                <td class="text-right">$240.00</td>
                                                <td class="text-center">
                                                    <a href="#">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </li>
                                    <li>
                                        <table class="table table-bordered total">
                                            <tbody>
                                                <tr>
                                                    <td class="text-right"><strong>Sub-Total</strong></td>
                                                    <td class="text-left">$1,101.00</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right"><strong>Eco Tax (-2.00)</strong></td>
                                                    <td class="text-left">$4.00</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right"><strong>VAT (17.5%)</strong></td>
                                                    <td class="text-left">$192.68</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right"><strong>Total</strong></td>
                                                    <td class="text-left">$1,297.68</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <p class="text-right btn-block1">
                                            <a href="cart.html">
                                                View Cart
                                            </a>
                                            <a href="#">
                                                Checkout
                                            </a>
                                        </p>
                                    </li>									
                                </ul>
                            </div>
                        </div>
                        <!-- Shopping Cart Ends -->
                    </div>
                </div>
                <!-- Main Header Ends -->
                <!-- Main Menu Starts -->
                <nav id="main-menu" class="navbar" role="navigation">
                    <!-- Nav Header Starts -->
                    <div class="navbar-header">
                        <button type="button" class="btn btn-navbar navbar-toggle" data-toggle="collapse" data-target=".navbar-cat-collapse">
                            <span class="sr-only">Toggle Navigation</span>
                            <i class="fa fa-bars"></i>
                        </button>
                    </div>
                    <!-- Nav Header Ends -->
                    <!-- Navbar Cat collapse Starts -->
                    <div class="collapse navbar-collapse navbar-cat-collapse">
                        <ul class="nav navbar-nav">
                            <li><a href="category-list.html">Spices &amp; Herbs</a></li>
                            <li class="dropdown">
                                <a href="category-list.html" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="10">
                                    Chili Powder 
                                </a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a tabindex="-1" href="#">Red Chilly</a></li>
                                    <li><a tabindex="-1" href="#">Green Chilly</a></li>
                                    <li><a tabindex="-1" href="#">Italian Chilly</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="category-list.html" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="10">Curry Powder</a>
                                <div class="dropdown-menu">
                                    <div class="dropdown-inner">
                                        <ul class="list-unstyled">
                                            <li class="dropdown-header">Sub Category</li>
                                            <li><a tabindex="-1" href="#">item 1</a></li>
                                            <li><a tabindex="-1" href="#">item 2</a></li>
                                            <li><a tabindex="-1" href="#">item 3</a></li>
                                        </ul>										
                                        <ul class="list-unstyled">
                                            <li class="dropdown-header">Sub Category</li>
                                            <li><a tabindex="-1" href="#">item 1</a></li>
                                            <li><a tabindex="-1" href="#">item 2</a></li>
                                            <li><a tabindex="-1" href="#">item 3</a></li>
                                        </ul>
                                        <ul class="list-unstyled">
                                            <li class="dropdown-header">Sub Category</li>
                                            <li><a tabindex="-1" href="#">item 1</a></li>
                                            <li><a tabindex="-1" href="#">item 2</a></li>
                                            <li><a tabindex="-1" href="#">item 3</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            <li><a href="category-list.html">Herb Blends</a></li>
                            <li><a href="category-list.html">Seasonings</a></li>
                            <li><a href="category-list.html">Salt Free Spices</a></li>
                            <li><a href="category-list.html">Sambar Powders</a></li>
                            <li class="dropdown">
                                <a href="category-list.html" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="10">
                                    Pages
                                </a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a tabindex="-1" href="index.html">Home</a></li>
                                    <li><a tabindex="-1" href="about.html">About</a></li>
                                    <li><a tabindex="-1" href="category-list.html">Category List</a></li>
                                    <li><a tabindex="-1" href="category-grid.html">Category Grid</a></li>
                                    <li><a tabindex="-1" href="product.html">Product</a></li>
                                    <li><a tabindex="-1" href="product-full.html">Product Full Width</a></li>
                                    <li><a tabindex="-1" href="cart.html">Cart</a></li>
                                    <li><a tabindex="-1" href="login.html">Login</a></li>
                                    <li><a tabindex="-1" href="compare.html">Compare Products</a></li>
                                    <li><a tabindex="-1" href="typography.html">Typography</a></li>
                                    <li><a tabindex="-1" href="register.html">Register</a></li>
                                    <li><a tabindex="-1" href="contact.html">Contact</a></li>
                                    <li><a tabindex="-1" href="404.html">404</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    <!-- Navbar Cat collapse Ends -->
                </nav>
                <!-- Main Menu Ends -->
            </div>
            <!-- Ends -->
        </header>
        <!-- Header Section Ends -->
        <!-- Slider Section Starts -->
        <div class="slider">
            <div class="container">
                <div id="main-carousel" class="carousel slide" data-ride="carousel">
                    <!-- Wrapper For Slides Starts -->
                    <div class="carousel-inner">
                        <div class="item active">
                            <img src="images/slider-imgs/slide1-img.jpg" alt="Slider" class="img-responsive" />
                        </div>
                        <div class="item">
                            <img src="images/slider-imgs/slide1-img.jpg" alt="Slider" class="img-responsive" />
                        </div>
                    </div>
                    <!-- Wrapper For Slides Ends -->
                    <!-- Controls Starts -->
                    <a class="left carousel-control" href="#main-carousel" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                    </a>
                    <a class="right carousel-control" href="#main-carousel" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right"></span>
                    </a>
                    <!-- Controls Ends -->
                </div>				
            </div>
        </div>
        <!-- Slider Section Ends -->
        <!-- 3 Column Banners Starts -->
        <div class="col3-banners">
            <div class="container">
                <ul class="row list-unstyled">
                    <li class="col-sm-4">
                        <img src="images/banners/3col-banner1.jpg" alt="banners" class="img-responsive" />
                    </li>
                    <li class="col-sm-4">
                        <img src="images/banners/3col-banner2.jpg" alt="banners" class="img-responsive" />
                    </li>
                    <li class="col-sm-4">
                        <img src="images/banners/3col-banner3.jpg" alt="banners" class="img-responsive" />
                    </li>
                </ul>
            </div>
        </div>
        <!-- 3 Column Banners Ends -->
        <!-- Latest Products Starts -->
        <section class="products-list">			
            <div class="container">
                <!-- Heading Starts -->
                <h2 class="product-head">Latest Products</h2>
                <!-- Heading Ends -->
                <!-- Products Row Starts -->
                <div class="row">
                    <!-- Product #1 Starts -->
                    <div class="col-md-3 col-sm-6">
                        <div class="product-col">
                            <div class="image">
                                <img src="images/product-images/1.jpg" alt="product" class="img-responsive" />
                            </div>
                            <div class="caption">
                                <h4><a href="product.html">Simply Organic Seeds</a></h4>
                                <div class="description">
                                    We are so lucky living in such a wonderful time. Our almost unlimited ...
                                </div>
                                <div class="price">
                                    <span class="price-new">$199.50</span> 
                                    <span class="price-old">$249.50</span>
                                </div>
                                <div class="cart-button button-group">
                                    <button type="button" title="Wishlist" class="btn btn-wishlist">
                                        <i class="fa fa-heart"></i>
                                    </button>
                                    <button type="button" title="Compare" class="btn btn-compare">
                                        <i class="fa fa-bar-chart-o"></i>
                                    </button>
                                    <button type="button" class="btn btn-cart">
                                        Add to cart
                                        <i class="fa fa-shopping-cart"></i> 
                                    </button>									
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Product #1 Ends -->
                    <!-- Product #2 Starts -->
                    <div class="col-md-3 col-sm-6">
                        <div class="product-col">
                            <div class="image">
                                <img src="images/product-images/2.jpg" alt="product" class="img-responsive" />
                            </div>
                            <div class="caption">
                                <h4><a href="product.html">Simply Organic Seeds</a></h4>
                                <div class="description">
                                    We are so lucky living in such a wonderful time. Our almost unlimited ...
                                </div>
                                <div class="price">
                                    <span class="price-new">$199.50</span> 
                                    <span class="price-old">$249.50</span>
                                </div>
                                <div class="cart-button button-group">
                                    <button type="button" title="Wishlist" class="btn btn-wishlist">
                                        <i class="fa fa-heart"></i>
                                    </button>
                                    <button type="button" title="Compare" class="btn btn-compare">
                                        <i class="fa fa-bar-chart-o"></i>
                                    </button>
                                    <button type="button" class="btn btn-cart">
                                        Add to cart
                                        <i class="fa fa-shopping-cart"></i> 
                                    </button>									
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Product #2 Ends -->
                    <!-- Product #3 Starts -->
                    <div class="col-md-3 col-sm-6">
                        <div class="product-col">
                            <div class="image">
                                <img src="images/product-images/3.jpg" alt="product" class="img-responsive" />
                            </div>
                            <div class="caption">
                                <h4><a href="product.html">Simply Organic Seeds</a></h4>
                                <div class="description">
                                    We are so lucky living in such a wonderful time. Our almost unlimited ...
                                </div>
                                <div class="price">
                                    <span class="price-new">$199.50</span> 
                                    <span class="price-old">$249.50</span>
                                </div>
                                <div class="cart-button button-group">
                                    <button type="button" title="Wishlist" class="btn btn-wishlist">
                                        <i class="fa fa-heart"></i>
                                    </button>
                                    <button type="button" title="Compare" class="btn btn-compare">
                                        <i class="fa fa-bar-chart-o"></i>
                                    </button>
                                    <button type="button" class="btn btn-cart">
                                        Add to cart
                                        <i class="fa fa-shopping-cart"></i> 
                                    </button>									
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Product #3 Ends -->
                    <!-- Product #4 Starts -->
                    <div class="col-md-3 col-sm-6">
                        <div class="product-col">
                            <div class="image">
                                <img src="images/product-images/4.jpg" alt="product" class="img-responsive" />
                            </div>
                            <div class="caption">
                                <h4><a href="product.html">Simply Organic Seeds</a></h4>
                                <div class="description">
                                    We are so lucky living in such a wonderful time. Our almost unlimited ...
                                </div>
                                <div class="price">
                                    <span class="price-new">$199.50</span> 
                                    <span class="price-old">$249.50</span>
                                </div>
                                <div class="cart-button button-group">
                                    <button type="button" title="Wishlist" class="btn btn-wishlist">
                                        <i class="fa fa-heart"></i>
                                    </button>
                                    <button type="button" title="Compare" class="btn btn-compare">
                                        <i class="fa fa-bar-chart-o"></i>
                                    </button>
                                    <button type="button" class="btn btn-cart">
                                        Add to cart
                                        <i class="fa fa-shopping-cart"></i> 
                                    </button>									
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Product #4 Ends -->
                </div>
                <!-- Products Row Ends -->
            </div>
        </section>
        <!-- Latest Products Ends -->
        <!-- 2 Column Banners Starts -->
        <div class="col2-banners">
            <div class="container">
                <ul class="row list-unstyled">
                    <li class="col-sm-4">
                        <img src="images/banners/2col-banner1.jpg" alt="banners" class="img-responsive" />
                    </li>
                    <li class="col-sm-8">
                        <img src="images/banners/2col-banner2.jpg" alt="banners" class="img-responsive" />
                    </li>
                </ul>
            </div>
        </div>
        <!-- 2 Column Banners Ends -->
        <!-- Specials Products Starts -->
        <section class="products-list">			
            <div class="container">
                <!-- Heading Starts -->
                <h2 class="product-head">Specials Products</h2>
                <!-- Heading Ends -->
                <!-- Products Row Starts -->
                <div class="row">
                    <!-- Product #1 Starts -->
                    <div class="col-md-3 col-sm-6">
                        <div class="product-col">
                            <div class="image">
                                <img src="images/product-images/8.jpg" alt="product" class="img-responsive" />
                            </div>
                            <div class="caption">
                                <h4>
                                    <a href="product-full.html">Simply Organic Seeds</a>
                                </h4>
                                <div class="description">
                                    We are so lucky living in such a wonderful time. Our almost unlimited ...
                                </div>
                                <div class="price">
                                    <span class="price-new">$199.50</span> 
                                    <span class="price-old">$249.50</span>
                                </div>
                                <div class="cart-button button-group">
                                    <button type="button" title="Wishlist" class="btn btn-wishlist">
                                        <i class="fa fa-heart"></i>
                                    </button>
                                    <button type="button" title="Compare" class="btn btn-compare">
                                        <i class="fa fa-bar-chart-o"></i>
                                    </button>
                                    <button type="button" class="btn btn-cart">
                                        Add to cart
                                        <i class="fa fa-shopping-cart"></i> 
                                    </button>									
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Product #1 Ends -->
                    <!-- Product #2 Starts -->
                    <div class="col-md-3 col-sm-6">
                        <div class="product-col">
                            <div class="image">
                                <img src="images/product-images/5.jpg" alt="product" class="img-responsive" />
                            </div>
                            <div class="caption">
                                <h4>
                                    <a href="product-full.html">Simply Organic Seeds</a>
                                </h4>
                                <div class="description">
                                    We are so lucky living in such a wonderful time. Our almost unlimited ...
                                </div>
                                <div class="price">
                                    <span class="price-new">$199.50</span> 
                                    <span class="price-old">$249.50</span>
                                </div>
                                <div class="cart-button button-group">
                                    <button type="button" title="Wishlist" class="btn btn-wishlist">
                                        <i class="fa fa-heart"></i>
                                    </button>
                                    <button type="button" title="Compare" class="btn btn-compare">
                                        <i class="fa fa-bar-chart-o"></i>
                                    </button>
                                    <button type="button" class="btn btn-cart">
                                        Add to cart
                                        <i class="fa fa-shopping-cart"></i> 
                                    </button>									
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Product #2 Ends -->
                    <!-- Product #3 Starts -->
                    <div class="col-md-3 col-sm-6">
                        <div class="product-col">
                            <div class="image">
                                <img src="images/product-images/6.jpg" alt="product" class="img-responsive" />
                            </div>
                            <div class="caption">
                                <h4>
                                    <a href="product-full.html">Simply Organic Seeds</a>
                                </h4>
                                <div class="description">
                                    We are so lucky living in such a wonderful time. Our almost unlimited ...
                                </div>
                                <div class="price">
                                    <span class="price-new">$199.50</span> 
                                    <span class="price-old">$249.50</span>
                                </div>
                                <div class="cart-button button-group">
                                    <button type="button" title="Wishlist" class="btn btn-wishlist">
                                        <i class="fa fa-heart"></i>
                                    </button>
                                    <button type="button" title="Compare" class="btn btn-compare">
                                        <i class="fa fa-bar-chart-o"></i>
                                    </button>
                                    <button type="button" class="btn btn-cart">
                                        Add to cart
                                        <i class="fa fa-shopping-cart"></i> 
                                    </button>									
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Product #3 Ends -->
                    <!-- Product #4 Starts -->
                    <div class="col-md-3 col-sm-6">
                        <div class="product-col">
                            <div class="image">
                                <img src="images/product-images/7.jpg" alt="product" class="img-responsive" />
                            </div>
                            <div class="caption">
                                <h4>
                                    <a href="product-full.html">Simply Organic Seeds</a>
                                </h4>
                                <div class="description">
                                    We are so lucky living in such a wonderful time. Our almost unlimited ...
                                </div>
                                <div class="price">
                                    <span class="price-new">$199.50</span> 
                                    <span class="price-old">$249.50</span>
                                </div>
                                <div class="cart-button button-group">
                                    <button type="button" title="Wishlist" class="btn btn-wishlist">
                                        <i class="fa fa-heart"></i>
                                    </button>
                                    <button type="button" title="Compare" class="btn btn-compare">
                                        <i class="fa fa-bar-chart-o"></i>
                                    </button>
                                    <button type="button" class="btn btn-cart">
                                        Add to cart
                                        <i class="fa fa-shopping-cart"></i> 
                                    </button>									
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Product #4 Ends -->
                </div>
                <!-- Products Row Ends -->
            </div>
        </section>
        <!-- Specials Products Ends -->
        <!-- Footer Section Starts -->
        <footer id="footer-area">
            <!-- Footer Links Starts -->
            <div class="footer-links">
                <!-- Container Starts -->
                <div class="container">
                    <!-- Information Links Starts -->
                    <div class="col-md-2 col-sm-6">
                        <h5>Information</h5>
                        <ul>
                            <li><a href="about.html">About Us</a></li>
                            <li><a href="#">Delivery Information</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                            <li><a href="#">Terms &amp; Conditions</a></li>
                        </ul>
                    </div>
                    <!-- Information Links Ends -->
                    <!-- My Account Links Starts -->
                    <div class="col-md-2 col-sm-6">
                        <h5>My Account</h5>
                        <ul>
                            <li><a href="#">My orders</a></li>
                            <li><a href="#">My merchandise returns</a></li>
                            <li><a href="#">My credit slips</a></li>
                            <li><a href="#">My addresses</a></li>
                            <li><a href="#">My personal info</a></li>
                        </ul>
                    </div>
                    <!-- My Account Links Ends -->					
                    <!-- Customer Service Links Starts -->
                    <div class="col-md-2 col-sm-6">
                        <h5>Service</h5>
                        <ul>
                            <li><a href="contact.html">Contact Us</a></li>
                            <li><a href="#">Returns</a></li>
                            <li><a href="#">Site Map</a></li>
                            <li><a href="#">Affiliates</a></li>
                            <li><a href="#">Specials</a></li>
                        </ul>
                    </div>
                    <!-- Customer Service Links Ends -->
                    <!-- Follow Us Links Starts -->
                    <div class="col-md-2 col-sm-6">
                        <h5>Follow Us</h5>
                        <ul>
                            <li><a href="#">Facebook</a></li>
                            <li><a href="#">Twitter</a></li>
                            <li><a href="#">RSS</a></li>
                            <li><a href="#">YouTube</a></li>
                        </ul>
                    </div>
                    <!-- Follow Us Links Ends -->
                    <!-- Contact Us Starts -->
                    <div class="col-md-4 col-sm-12 last">
                        <h5>Contact Us</h5>
                        <ul>
                            <li>My Company</li>
                            <li>
                                1247 LB Nagar Road, Hyderabad, Telangana - 35
                            </li>
                            <li>
                                Email: <a href="#">info@demolink.com</a>
                            </li>								
                        </ul>
                        <h4 class="lead">
                            Tel: <span>1(234) 567-9842</span>
                        </h4>
                    </div>
                    <!-- Contact Us Ends -->
                </div>
                <!-- Container Ends -->
            </div>
            <!-- Footer Links Ends -->
            <!-- Copyright Area Starts -->
            <div class="copyright">
                <!-- Container Starts -->
                <div class="container">
                    <!-- Starts -->
                    <p class="pull-left">
                        &nbsp; 2014 Spice Shoppe Stores. Bootstrap Layout All Rights Reserved. Designed By <a href="http://sainathchillapuram.com/">Sainath Chillapuram</a>
                    </p>
                    <!-- Ends -->
                    <!-- Payment Gateway Links Starts -->
                    <ul class="pull-right list-inline">
                        <li>
                            <img src="images/payment-icon/cirrus.png" alt="PaymentGateway" />
                        </li>
                        <li>
                            <img src="images/payment-icon/paypal.png" alt="PaymentGateway" />
                        </li>
                        <li>
                            <img src="images/payment-icon/visa.png" alt="PaymentGateway" />
                        </li>
                        <li>
                            <img src="images/payment-icon/mastercard.png" alt="PaymentGateway" />
                        </li>
                        <li>
                            <img src="images/payment-icon/americanexpress.png" alt="PaymentGateway" />
                        </li>
                    </ul>
                    <!-- Payment Gateway Links Ends -->
                </div>
                <!-- Container Ends -->
            </div>
            <!-- Copyright Area Ends -->
        </footer>
        <!-- Footer Section Ends -->
        <!-- JavaScript Files -->
        <?php include_once 'script.php'; ?>
    </body>
</html>