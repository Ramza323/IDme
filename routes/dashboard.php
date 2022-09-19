<?php
function clivern_render_plugin_page()
{
    registerStyles();
?>

    <!-- -------- START HEADER 2 w/ waves and typed text ------- -->
    <header class="position-relative">
        <nav class="navbar navbar-expand-lg navbar-dark navbar-absolute bg-transparent shadow-none">
            <div class="container">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-header-2" aria-controls="navbar-header-2" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </nav>
        <div class="page-header min-vh-100" style="background-size: contain;background-repeat: repeat-x;background-image: url(<?php echo plugins_url('../assets/img/icon.png', __FILE__) ?>);">
            <span class="mask bg-gradient-primary"></span>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 text-start">
                        <h1 class="text-white">Welcome to the Mindtrust plugin to setup the ID.me behavior </h1>
                        <p class="lead text-white text-start pe-5 mt-4">With this plugin, you can configure different options for ID.me integration and you can see the users verified with that integration</p>
                        <br />
                        <div class="buttons">
                            <a class="btn btn-lg btn-white me-4" href="admin.php?page=idme-mindtrust/idme-mindtrust.php/configuration">Configuration</a>
                            <a class="btn btn-lg btn-white" href="admin.php?page">Users verified</a>
                            <a class="btn btn-lg btn-link text-white" target="_blank" href="https://mindtrust.com">About Mindtrust</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="position-absolute w-100 z-index-1 bottom-0">
            <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 40" preserveAspectRatio="none" shape-rendering="auto">
                <defs>
                    <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
                </defs>
                <g class="moving-waves">
                    <use xlink:href="#gentle-wave" x="48" y="-1" fill="rgba(251,251,251,0.40" />
                    <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(251,251,251,0.35)" />
                    <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(251,251,251,0.25)" />
                    <use xlink:href="#gentle-wave" x="48" y="8" fill="rgba(251,251,251,0.20)" />
                    <use xlink:href="#gentle-wave" x="48" y="13" fill="rgba(251,251,251,0.15)" />
                    <use xlink:href="#gentle-wave" x="48" y="16" fill="rgba(251,251,251,0.95" />
                </g>
            </svg>
        </div>
    </header>
    </script>
<?php
    //finish html
}
