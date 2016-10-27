<?php
require_once './vendor/autoload.php';

$helperLoader = new SplClassLoader('Helpers', './vendor');
$mailLoader   = new SplClassLoader('SimpleMail', './vendor');

$helperLoader->register();
$mailLoader->register();

use Helpers\Config;
use SimpleMail\SimpleMail;

$config = new Config;
$config->load('./config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = stripslashes(trim($_POST['form-name']));
    $email   = stripslashes(trim($_POST['form-email']));
    $phone   = stripslashes(trim($_POST['form-phone']));
    $subject = stripslashes(trim($_POST['form-subject']));
    $message = stripslashes(trim($_POST['form-message']));
    $pattern = '/[\r\n]|Content-Type:|Bcc:|Cc:/i';

    if (preg_match($pattern, $name) || preg_match($pattern, $email) || preg_match($pattern, $subject)) {
        die("Header injection detected");
    }

    $emailIsValid = filter_var($email, FILTER_VALIDATE_EMAIL);

    if ($name && $email && $emailIsValid && $subject && $message) {
        $mail = new SimpleMail();

        $mail->setTo($config->get('emails.to'));
        $mail->setFrom($config->get('emails.from'));
        $mail->setSender($name);
        $mail->setSubject($config->get('subject.prefix') . ' ' . $subject);

        $body = "
        <!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
        <html>
            <head>
                <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
            </head>
            <body>
                <h1>{$subject}</h1>
                <p><strong>{$config->get('fields.name')}:</strong> {$name}</p>
                <p><strong>{$config->get('fields.email')}:</strong> {$email}</p>
                <p><strong>{$config->get('fields.phone')}:</strong> {$phone}</p>
                <p><strong>{$config->get('fields.message')}:</strong> {$message}</p>
            </body>
        </html>";

        $mail->setHtml($body);
        $mail->send();

        $emailSent = true;
    } else {
        $hasError = true;
    }
}
?>
<!DOCTYPE html>
<html>

  <head><meta http-equiv="Content-Type" content="text/html; charset=gb18030">


    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Contact me for whatever you may need">
    <meta name="author" content="hippybear">

    <title>Code Portfolio</title>

    <link  rel="shortcut icon" href="../imgs/favicon.ico">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Amiri:700i" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet">


    <script src="//code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="public/js/contact-form.js"></script>
  </head>

  <body>

  	<!-- NAVBAR -->

  	<div class="container-fluid">
  	  <div class="row">
  	    <div class="col-xs-12 col-lg-8 col-lg-offset-2"  id="site-header">
  	      <h1 id="site-title">&radic;<span style="text-decoration:overline;">&nbsp;Contact Form&nbsp;</span></h1>
  	    </div>
  	  </div>
  	</div>

  	<!-- /NAVBAR -->

  	<!-- MAIN -->



  	<div class="container">
  		<div class="row">

  			<div class="col-md-7 col-md-offset-2 col-md-push-1 col-lg-7 col-lg-offset-2 col-lg-push-1">
  			  <h1 class="heading text-center">Contact Me</h1>

          <?php if(!empty($emailSent)): ?>
              <div class="col-md-6 col-md-offset-3">
                  <div class="alert alert-success text-center"><?php echo $config->get('messages.success'); ?></div>
              </div>
          <?php else: ?>
              <?php if(!empty($hasError)): ?>
              <div class="col-md-5 col-md-offset-4">
                  <div class="alert alert-danger text-center"><?php echo $config->get('messages.error'); ?></div>
              </div>
              <?php endif; ?>

          <div class="col-md-9 col-md-offset-2">
              <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="application/x-www-form-urlencoded;" id="contact-form" class="form-horizontal" role="form" method="post">
                  <div class="form-group">
                      <label for="form-name" class="col-lg-1 control-label"><?php echo $config->get('fields.name'); ?></label><br />
                      <div class="col-lg-12">
                          <input type="text" class="form-control" id="form-name" name="form-name" placeholder="<?php echo $config->get('fields.name'); ?>" required>
                      </div>
                  </div>
                  <div class="form-group">
                      <label for="form-email" class="col-lg-2 control-label"><?php echo $config->get('fields.email'); ?></label><br />
                      <div class="col-lg-12">
                          <input type="email" class="form-control" id="form-email" name="form-email" placeholder="<?php echo $config->get('fields.email'); ?>" required>
                      </div>
                  </div>
                  <div class="form-group">
                      <label for="form-phone" class="col-lg-2 control-label"><?php echo $config->get('fields.phone'); ?></label><br />
                      <div class="col-lg-12">
                          <input type="tel" class="form-control" id="form-phone" name="form-phone" placeholder="<?php echo $config->get('fields.phone'); ?>">
                      </div>
                  </div>
                  <div class="form-group">
                      <label for="form-subject" class="col-lg-2 control-label"><?php echo $config->get('fields.subject'); ?></label><br />
                      <div class="col-lg-12">
                          <input type="text" class="form-control" id="form-subject" name="form-subject" placeholder="<?php echo $config->get('fields.subject'); ?>" required>
                      </div>
                  </div>
                  <div class="form-group">
                      <label for="form-message" class="col-lg-2 control-label"><?php echo $config->get('fields.message'); ?></label><br />
                      <div class="col-lg-12">
                          <textarea class="form-control" rows="3" id="form-message" name="form-message" placeholder="<?php echo $config->get('fields.message'); ?>" required></textarea>
                      </div>
                  </div>
                  <div class="form-group">
                      <div class="col-lg-12">
                          <button type="submit" class="btn btn-block btn-primary"><?php echo $config->get('fields.btn-send'); ?></button>
                      </div>
                  </div>
              </form>
          </div>
          <?php endif; ?>
          <div class="col-md-9 col-md-offset-2">
            <p>If for any reason there is an issue submitting this form just click the 'Open Chat' button below and send me a message</p>
          </div>
  			</div>

  			<div class="col-lg-3 col-lg-pull-8" style="padding-bottom: 50px">
  			  <div>
  			    <h2 class="heading">Resources</h2>
            <ul class="list-unstyled" style="padding-left: 20px;">
  				    <li>
  				      <a href="https://hippybear.github.io/about/" target="_blank">
  				        About Me
  				      </a>
  				    </li>
  				    <li>
  				      <a href="https://hippybear.github.io/" target="_blank">
  				        Blog
  				      </a>
  				    </li>
              <li>
  				      <a href="../portfolio.html">
  				        Portfolio
  				      </a>
  				    </li>
  				    <li>
  				      <a href="https://hippybear.github.io/resume/" target="_blank">
  				        Resume
  				      </a>
  				    </li>
              <li>
  				      <a href="https://wakatime.com/hippybear" target="_blank">
  				        My Coding Metrics
  				      </a>
  				    </li>
  				    <li>
  				      <a href="https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet" target="_blank">
  				        Learn Github Markdown
  				      </a>
  				    </li>
  				  </ul>
  				</div>

  				<div id="social-block">
  				  <h2 class="heading">Connect</h2>

  				  <p class="text-center">
  				    <a href="https://github.com/hippybear"><i class="fa fa-github"></i></a>
  				    <a href="https://twitter.com/codeblindorg"><i class="fa fa-twitter"></i></a>
  				    <a href="https://facebook.com/jparkton"><i class="fa fa-facebook-square"></i></a>
  				    <a data-toggle="tooltip" data-placement="top" title="509-270-1521"><i class="fa fa-phone"></i></a>
  				  </p>
  				  <p></p>
  				</div>
  				<script>
            $(document).ready(function(){
              $('[data-toggle="tooltip"]').tooltip();
            });
          </script>
  				<div class="thumbnail">
  				  <img src="//s3.amazonaws.com/freecodecamp/camper-image-placeholder.png" />
  				  <div class="caption">
  				    <p>Learn to code, and help non profits while you do.</p>
  				    <p><a href="https://freecodecamp.com" target="_blank" class="btn btn-block btn-success"><strong>Sign Up</strong></a></p>
  				  </div>
  				</div>
          <footer class="footer">
            <p class="text-center text-muted">Built with <a href="http://bootstrapcdn.com" target="_blank">Bootstrap</a></p>
          </footer>
  			</div>
  		</div>
  	</div>

  	<!-- /MAIN -->
    <script>
      ((window.gitter = {}).chat = {}).options = {
        room: 'fonyedjs/lobby'
      };
    </script>
    <script src="https://sidecar.gitter.im/dist/sidecar.v1.js" async defer></script>
  </body>

</html>
