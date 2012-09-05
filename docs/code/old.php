<?php echo $this->doctype(); ?>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <?php echo $this->headTitle('ZF2 '. $this->translate('Skeleton Application'))->setSeparator(' - ')->setAutoEscape(false) ?>

    <?php echo $this->headMeta()->appendName('viewport', 'width=device-width, initial-scale=1.0') ?>

    <!-- Le styles -->
    <?php echo $this->headLink(array('rel' => 'shortcut icon', 'type' => 'image/vnd.microsoft.icon', 'href' => $this->basePath() . '/images/favicon.ico'))
                    ->prependStylesheet($this->basePath() . '/css/bootstrap-responsive.min.css')
                    ->prependStylesheet($this->basePath() . '/css/style.css')
                    ->prependStylesheet($this->basePath() . '/css/bootstrap.min.css') ?>

    <!-- Scripts -->
    <?php echo $this->headScript()->prependFile($this->basePath() . '/js/html5.js', 'text/javascript', array('conditional' => 'lt IE 9',))
                                  ->prependFile($this->basePath() . '/js/jquery-1.7.2.min.js') ?>

  </head>

  <body>

    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">

            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="<?php echo $this->url('home') ?>"><?php echo $this->translate('Skeleton Application') ?></a>
          <div class="nav-collapse">
            <ul class="nav">
              <li class="active"><a href="<?php echo $this->url('home') ?>"><?php echo $this->translate('Home') ?></a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">

      <?php echo $this->content; ?>

      <hr>

      <footer>
        <p>&copy; 2005 - 2012 by Zend Technologies Ltd. <?php echo $this->translate('All rights reserved.') ?></p>
      </footer>

    </div> <!-- /container -->

  <?php echo $this->inlineScript() ?>
  </body>
</html>
