<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>@yield('title')</title>

    <!-- Bootstrap -->
    <link href="/vendor/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="/css/app.css" rel="stylesheet">

  </head>
  <body>

    @yield('body')

    <footer class="footer">
      <div class="container">
        <div class="row">
          <div class="col-md-7 col-md-offset-2">
            <div class="row">
              <div class="col-sm-9">
                <small class="text-muted">Made by <a href="https://github.com/andrewhood125/">@andrewhood125</a></small>
              </div>
              <div class="col-sm-2 col-sm-offset-1">
                <small><a href="https://github.com/andrewhood125/super-potato/issues/new">Feedback?</a></small>
              </div>
            </div>
          </div>
        </div>
      </div>

    </footer>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/vendor/jquery/dist/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/vendor/bootstrap/dist/js/bootstrap.min.js"></script>

    <script src="/vendor/typeahead.js/dist/typeahead.bundle.min.js"></script>

    <script>
        @yield('script')
    </script>
  </body>
</html>
