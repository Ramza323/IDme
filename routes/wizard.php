<?php
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}

function get_values(){
    global $wpdb;
    return $wpdb->get_row( "SELECT * FROM idme_configuration WHERE id = 1" );
}

function clivern_render_custom_page()
{
    registerStyles();
    global $wpdb;
    $values = get_values();
    //start of html
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <section class="d-flex align-items-center">
        <div class="container py-4">
            <div class="row">
                <div class="col-lg-13 mx-auto d-flex justify-content-center flex-column">
                    <div class="card d-flex justify-content-center p-4 shadow-lg">
                        <div class="text-center">
                            <h3 class="text-gradient text-primary">Configuration</h3>
                        </div>
                        <div class="card card-plain mt-0">
                            <form id="setup" method="post">
                                <div class="card-body pb-2">
                                    <div class="col-md-12 mb-2">
                                        <label>Client ID</label>
                                        <div class="input-group mb-4">
                                            <input name="clientid" class="form-control" placeholder="4ds68f4s6d84f6sd654sd6f4sd" aria-label="Client ID" type="text" value="<?= ($values->client_id != NULL) ? $values->client_id: NULL; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <label>Client Secret</label>
                                        <div class="input-group mb-4">
                                            <input name="clientsecret" class="form-control" placeholder="4ds68f4s6d84f6sd654sd6f4sd" aria-label="Client ID" type="text" value="<?= ($values->client_secret != NULL) ? $values->client_secret: NULL; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <p class="mb-0 text-center">
                                            WHO WOULD YOU LIKE TO VERIFY?
                                        </p>
                                        <div class="row justify-space-between text-center py-2 ms-n5">
                                            <div class="col-md-12 mb-2">
                                                <div class="btn-group" role="group" aria-label="Basic checkbox toggle button group">
                                                    <input type="checkbox" class="btn-check" id="responder" name="responder" autocomplete="off" <?= (str_contains($values->scope, 'responder')) ? "checked": NULL; ?> >
                                                    <label class="btn btn-outline-dark" for="responder">Responder</label>

                                                    <input type="checkbox" class="btn-check" id="military"name="military" autocomplete="off" <?= (str_contains($values->scope, 'military')) ? "checked": NULL; ?> >
                                                    <label class="btn btn-outline-dark" for="military">Military</label>

                                                    <input type="checkbox" class="btn-check" id="government" name="government" autocomplete="off" <?= (str_contains($values->scope, 'government')) ? "checked": NULL; ?> >
                                                    <label class="btn btn-outline-dark" for="government">Government</label>

                                                    <input type="checkbox" class="btn-check" id="nurse" name="nurse" autocomplete="off" <?= (str_contains($values->scope, 'nurse')) ? "checked": NULL; ?> >
                                                    <label class="btn btn-outline-dark" for="nurse">Nurse</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <label>Landing page redirect</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="landing_redirect" placeholder="https://myurl/landing" value="<?= ($values->landing != NULL) ? $values->landing: NULL; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <label>Checkout redirect</label>
                                        <div class="input-group">
                                        <input class="form-control" name="checkout_redirect" placeholder="https://myurl/checkout" type="text" value="<?= ($values->checkout != NULL) ? $values->checkout: NULL; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <label>Message</label>
                                        <div class="input-group">
                                            <input class="form-control" name="message" placeholder="Custom message" aria-label="Message" type="text" value="<?= ($values->message != NULL) ? $values->message: NULL; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <label>Discount</label>
                                        <div class="input-group mb-4">
                                            <input class="form-control" name="discount" placeholder="10" aria-label="Discount" type="number" value="<?= ($values->discount != NULL) ? $values->discount: NULL; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <div class="form-check form-switch ps-6">
                                            <input name="enable" class="form-check-input ms-5 mt-1" type="checkbox" id="flexSwitchCheckDefault" <?= ($values->enable == 0) ? NULL: "checked"; ?>>
                                            <label class="form-check-label ms-2" for="flexSwitchCheckDefault">Enable it!</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <button type="submit" id="save" class="btn bg-gradient-primary mt-3 mb-0">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container py-4">
            <div class="row">
                <div class="col-lg-13 mx-auto d-flex justify-content-center flex-column">
                    <div class="card d-flex justify-content-center p-4 shadow-lg">
                        <div class="text-center">
                            <h3 class="text-gradient text-primary">Example of ID.me button</h3>
                            <p class="mb-0">
                                This and example of how looks the button injected on the checkout and landing page (without customize)
                            </p>
                        </div>
                        <div class="card card-plain align-items-center" style="pointer-events: none;">
                            <span 
                                id="idme-wallet-button" 
                                data-scope="" 
                                data-client-id="silence-is-gold" 
                                data-redirect="#" 
                                data-response="code" 
                                data-text="Custom message for advertising the discount" 
                                data-show-verify="true">
                            </span>
                            <script src="https://s3.amazonaws.com/idme/developer/idme-buttons/assets/js/idme-wallet-button.js"></script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
}
