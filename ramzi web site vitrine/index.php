<?php
$pageTitle = "Site Vitrine";

include 'init.php';
include $tpl . "navbar.php";
include $tpl . "sidebar.php";
?>

<button class="back-to-top-btn"><i class="fas fa-angle-double-up"></i></button>

<!-- start slide presentation  -->
<div class="slide-presentation home-sb" id="home">
    <div id="carouselExampleIndicators" class="carousel slide carousel-fade" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="data/images/slide1.png" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
            <img src="data/images/slide2.png" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
            <img src="data/images/slide3.png" class="d-block w-100" alt="...">
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
    </div>
    <div class="text-box">
        <p>I'M</p>
        <h1>MAYRA</h1>
        <h3>I HELP COMPANIES MAKE A BEAUTIFUL WEBSITES</h3>
        <span>you can explore more about my website and my services and contact here.</span>
    </div>
</div>
<!-- end slide presentation  -->

<!-- start about us  -->
<div class="about-us container about-sb" id="about">
    <div class="title">
        <h1><?php echo lang('AboutUs')?></h1>
        <p><?php echo lang('au-subtitle') ?></p>
    </div>
    <div class="side">
        <img src="data/images/slide2.png" alt="">
        <div class="details">
            <div class="descp">
                <div class="txt">
                    <h5><?php echo lang('au-subtitle1') ?></h5>
                    <p><?php echo lang('au-desp1') ?></p>
                </div>
            </div>
            <div class="descp">
                <div class="txt">
                    <h5><?php echo lang('au-subtitle1') ?></h5>
                    <p><?php echo lang('au-desp1') ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="descp">
        <div class="txt">
            <h5><?php echo lang('au-subtitle2') ?></h5>
            <p><?php echo lang('au-desp2') ?></p>
        </div>
    </div>
</div>
<!-- end about us  -->

<!-- start services  -->
<div class="services container service-sb" id="service">
    <div class="title">
        <h1><?php echo lang('Services')?></h1>
        <p><?php echo lang('s-subtitle') ?></p>
    </div>
    <div class="main-carousel">
    <div class="cell">
        <img src="data/images/slide1.png" alt="">
        <div class="txt">
            <p><?php echo lang('ser1') ?></p>
            <p>05/11/2021</p>
        </div>
    </div>
    <div class="cell">
        <img src="data/images/slide2.png" alt="">
        <div class="txt">
            <p><?php echo lang('ser1') ?></p>
            <p>05/11/2021</p>
        </div>
    </div>
    <div class="cell">
        <img src="data/images/slide3.png" alt="">
        <div class="txt">
            <p><?php echo lang('ser1') ?></p>
            <p>05/11/2021</p>
        </div>
    </div>
    <div class="cell">
        <img src="data/images/slide2.png" alt="">
        <div class="txt">
            <p><?php echo lang('ser1') ?></p>
            <p>05/11/2021</p>
        </div>
    </div>
    <div class="cell">
        <img src="data/images/slide3.png" alt="">
        <div class="txt">
            <p><?php echo lang('ser1') ?></p>
            <p>05/11/2021</p>
        </div>
    </div>
    <div class="cell">
        <img src="data/images/slide1.png" alt="">
        <div class="txt">
            <p><?php echo lang('ser1') ?></p>
            <p>05/11/2021</p>
        </div>
    </div>
    </div>
</div>
<!-- end services  -->

<!-- start contact  -->
<div class="contact contact-sb" id="contact">
    <div class="title">
        <h1><?php echo lang('touch')?></h1>
        <p><?php echo lang('touch-descp')?></p>
    </div>
    <div class="details container">
        <div class="infos">
            <h5><?php echo lang('cu-subtitle1')?></h5>
            <p><?php echo lang('cu-descp1-1')?><span> hi@mohamed.com</span></p>
            <div>
                <div class="info">
                    <i class="fas fa-envelope"></i>
                    <div>
                        <h6><?php echo lang('cu-sub11')?></h6>
                        <p><?php echo lang('cu-descp12')?></p>
                    </div>
                </div>
                <div class="info">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <h6><?php echo lang('cu-sub21')?></h6>
                        <p><?php echo lang('cu-descp22')?></p>
                    </div>
                </div>
                <div class="info">
                    <i class="fas fa-phone"></i>
                    <div>
                        <h6><?php echo lang('cu-sub31')?></h6>
                        <p><?php echo lang('cu-descp32')?></p>
                    </div>
                </div>
            </div>
            <div class="circle"></div>
            <div class="circle2"></div>
        </div>
        <div class="form">
            <form action="">
                <div class="info-personal">
                    <div>
                        <label for="name"><?php echo lang('name')?></label><br>
                        <input type="text" name="" id="name" placeholder="<?php echo lang('your name')?>">
                    </div>
                    <div>
                        <label for="email"><?php echo lang('email')?></label><br>
                        <input type="email" name="" id="email" placeholder="you@example.com">
                    </div>
                </div>
                <label for="subject"><?php echo lang('subject')?></label><br>
                <input type="text" name="" id="subject" placeholder='<?php echo lang('subject-placeholder')?>'><br>
                <label for="msg"><?php echo lang('msg')?></label><br>
                <textarea name="" id="msg" cols="30" rows="10" placeholder='<?php echo lang('msg-placeholder')?>'></textarea><br>
                <input type="checkbox" name="" id="agree">
                <label for="agree"><?php echo lang('agree')?></label><br>
                <input type="submit" value="<?php echo lang('send msg')?>">
            </form>
        </div>
    </div>
</div>
<!-- end contact  -->

<?php
include $tpl . 'footer.php';
?>