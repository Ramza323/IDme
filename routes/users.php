<?php



if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}

function clivern_render_about_page()
{
    registerStyles();
    global $wpdb;
    //start of html
?><div class="container">
            <div class="col-lg-11 text-start">
                <div class="table-responsive">
                    <table class="table align-items-center mb-0" id="example">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">First name</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Last name</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">State</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Group</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
    <script>
        jQuery(document).ready(function() {
            jQuery('#example').DataTable({
                "dom": 'Bfrtp',
                "processing": true,
                "serverSide": true,
                "ajax": "../wp-content/plugins/idme-mindtrust/inc/server_processing.php",
                "columns": [{
                        "width": "10%"
                    },
                    {
                        "width": "10%"
                    },
                    {
                        "width": "10%"
                    },
                    {
                        "width": "10%"
                    },
                    {
                        "width": "10%"
                    },
                ]
            });
        });
    </script>
    <style>
        .paginate_button.next, .paginate_button.previous{
            margin-bottom: 1rem !important;
            letter-spacing: -0.025rem!important;
            text-transform: uppercase;
            box-shadow: 0 4px 7px -1px rgb(0 0 0 / 11%), 0 2px 4px -1px rgb(0 0 0 / 7%)!important;
            background-size: 150%!important;
            background-position-x: 25%!important;
            color: #fff!important;
            border: 0!important;
            display: inline-block!important;
            font-weight: 700;
            line-height: 1.4;
            color: #67748e;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            user-select: none;
            background-color: transparent!important;
            border: 1px solid transparent!important;
            padding: 0.75rem 1.5rem!important;
            font-size: 0.75rem!important;
            border-radius: 0.5rem!important;
            transition: all 0.15s ease-in!important;
            background-image: linear-gradient(
                310deg
                , #2FC877 0%, #2FC877 100%)!important;
        }

        .paginate_button {
            margin-bottom: 1rem !important;
            letter-spacing: -0.025rem!important;
            text-transform: uppercase;
            background-size: 150%!important;
            background-position-x: 25%!important;
            color: #fff!important;
            border: 0!important;
            display: inline-block!important;
            font-weight: 700;
            line-height: 1.4;
            color: #67748e;
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
            user-select: none;
            background-color: transparent!important;
            border: 1px solid transparent!important;
            padding: 0.75rem 1.5rem!important;
            font-size: 0.75rem!important;
            border-radius: 0.5rem!important;
            transition: all 0.15s ease-in!important;
        }

        .paginate_button:hover, .active{
            background: linear-gradient(to bottom, #58585800 0%, #99d5858a 100%)!important;
        }
    </style>

<?php
}
?>