<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Navbar w/ text</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse navbar-items" id="navbarText">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nb1 nav-link active" aria-current="page" href="#home"><?php echo lang('Home')?></a>
        </li>
        <li class="nav-item">
          <a class="nb2 nav-link" href="#about"><?php echo lang('AboutUs')?></a>
        </li>
        <li class="nav-item">
          <a class="nb3 nav-link" href="#service"><?php echo lang('Services')?></a>
        </li>
        <li class="nav-item">
          <a class="nb4 nav-link" href="#contact"><?php echo lang('ContactUs')?></a>
        </li>
      </ul>
      <div class="navbar-text">
        <ul class="icon-social">
            <li><i class="fab fa-facebook"></i></li>
            <li><i class="fab fa-twitter"></i></li>
            <li><i class="fab fa-instagram"></i></li>
        </ul>
        <span class="lang active"><?php echo lang('fr')?></span>
        <span class="lang lang-sp"> | </span>
        <span class="lang"><?php echo lang('ar') ?></span>
      </div>
    </div>
  </div>
</nav>