<?php

$cooktops = get_fields();
unset($cooktops['post']);
registerScript();
/**
 * Extract $cooktops vars
 * @var array $hero
 * @var array $cta
 * @var array $pre_footer
 * @var array $idme_section
 * @var array $Persons
 * @var array $popular_cooktops
 * @var array $faqs
 **/
extract($cooktops);
get_header();
?>
<section class="img-bleed" style="background-image: url(<?php echo $hero['image']; ?>)">
  <div class="idme-hero-overlay flex align-center">
    <div class="py-10 sm:py-24 mx-auto w-full">
      <div class="container mx-auto text-center text-white">
        <?php if( $hero['headline'] ): ?>
          <h1 class="w-5/6 sm:w-8/12 text-white mx-auto"><?php echo $hero['headline']; ?></h1>
        <?php endif; ?>
        <?php if( $hero['lead'] ): ?>
          <p class="w-5/6 sm:w-8/12 mb-0 mt-2 md:mt-3 mx-auto font-normal md:text-xl"><?php echo $hero['lead'];?></p>
        <?php endif; ?>
	    </div>
    </div>
  </div>
</section>

<hr class="cwk-separator">

<section class="py-6" style="background: #F8F8F8;">
  <div class="fee container mx-auto justify-center text-center" style="display: none;">You've been verified</div>
  <div class="container mx-auto idme-verification_group">
    <h2 class="w-5/6 sm:w-7/12 text-center pt-12 mx-auto"><?php echo $idme_section['headline']; ?></h2>
    <p class="w-5/6 sm:w-7/12 text-center pb-8 mx-auto"><?php echo $idme_section['lead']; ?></p>
    <div class="flex-default justify-center">
      <div class="cwk-flex-6-4-3 no-underline">
        <?php
        $values = get_values();
        if($values->enable == 1){
            echo '<span
                id="idme-wallet-button"
                data-scope="'.$values->scope.'"
                data-client-id="'.$values->client_id.'"
                data-redirect="'.$values->checkout.'"
                data-response="code"
                data-text="'.$values->message.'"
                data-show-verify="true">
            </span>';
        }
        ?>
        <script src="https://s3.amazonaws.com/idme/developer/idme-buttons/assets/js/idme-wallet-button.js"></script>
      </div>
    </div>
  </div>
</section>


<section class="py-6">
  <div class="container mx-auto">
    <?php $oddEven = 0; ?>
    <?php foreach ($Persons['cards'] as $card) : ?>
      <div class="grid-default py-6">
        <div class="flex justify-center align-center m-auto overflow-hidden sm:m-0 h-full w-full self-center col-span-12 <?php echo ($oddEven % 2 === 0 ? 'sm:col-span-6' : 'sm:col-span-6 order-1 sm:order-2') ?>">
          <img class="mx-auto self-center" src="<?php echo $card['image'] ?>" />
        </div>
        <div class="self-left w-full col-span-12 p-24 <?php echo ($oddEven % 2 === 0 ? 'sm:col-span-6 sm:col-start-7' : 'sm:col-start-1 sm:col-span-6 order-2 sm:order-1') ?>">
          <h3 class="w-full mb-6 font-normal"><?php echo $card['headline']; ?></h3>
          <p class="text-left pb-6"><?php echo $card['lead'] ?></p>
          <div>
            <?php 
              if(!$card['links'] == NULL){
                foreach ($card['links'] as $link) : ?>
                  <a href="<?php echo $link['url'] ?>" class="<?php echo $link['button'] ?> mb-6 lg:mb-0"><?php echo $link['text'] ?></a>
              <?php endforeach;
              }
            ?>
          </div>
        </div>
      </div>
      <?php $oddEven++; ?>
    <?php endforeach; ?>
  </div>
</section>

<section class="py-6" style="background: #F8F8F8;">
  <div class="md:grid grid-cols-2 gap-2">
    <div>
      <div class="self-left w-full col-span-12 p-24 sm:col-span-6 sm:col-start-7">
        <h3 class="w-full mb-6 font-normal"><?php echo $cta['headline']; ?></h3>
        <p class="text-left pb-6"><?php echo $cta['lead'] ?></p>
        <div>
          <div bis_skin_checked="1">
            <a href="<?= $cta['button_url'] ?>" class="button-black-orange mb-6 lg:mb-0"><?= $cta['button_name'] ?></a>
          </div>
        </div>
      </div>
    </div>
    <div>
      <div class="flex justify-center align-center m-auto overflow-hidden sm:m-0 h-full w-full self-center col-span-12 sm:col-span-6">
        <img class="mx-auto self-center" src="<?php echo $cta['image'] ?>" />
      </div>
    </div>
  </div>
</section>

<section class="mb-24 sm:mb-32">
  <div class="container mx-auto">
    <h2 class=""><?php echo $faqs['headline']; ?></h2>
    <div id="faqs" class="mt-8 mx-auto flex flex-col flex-wrap max-w-screen-sm lg:max-w-screen-lg ">
      <?php foreach ($faqs['faqs'] as $key => $faq) : ?>
        <div class="question-and-answer select-none <?php if ($key >= 5) echo 'see-more hidden';
                                                    else echo 'block'; ?> border-b border-cwk-gray-200 my-2 p-6 no-underline cursor-pointer relative bg-white flex-1 group">
          <dt class="question-<?php echo $key ?>" @click="toogleAnswer(<?php echo $key ?>)">
            <div class="flex flex-row">
              <div class="font-roboto ml-4 font-bold flex-1">
                <?php echo $faq['question']; ?>
              </div>
              <div class="self-end">
                <svg fill="#2C2721" class="question-btn-s-<?php echo $key ?> group-hover:bg-gray-500 h-5 block bg-op-blue-main rounded-full p-1" viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                  <g stroke="#2C2721" stroke-width="1" fill="#2C2721" fill-rule="evenodd">
                    <g>
                      <polygon points="9.29289322 12.9497475 10 13.6568542 15.6568542 8 14.2426407 6.58578644 10 10.8284271 5.75735931 6.58578644 4.34314575 8"></polygon>
                    </g>
                  </g>
                </svg>
                <svg fill="#2C2721" class="question-btn-h-<?php echo $key ?> group-hover:bg-gray-500 h-5 block bg-op-blue-main rounded-full p-1 hidden" viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                  <g stroke="#2C2721" stroke-width="1" fill="#2C2721" fill-rule="evenodd">
                    <g>
                      <polygon points="10.7071068 7.05025253 10 6.34314575 4.34314575 12 5.75735931 13.4142136 10 9.17157288 14.2426407 13.4142136 15.6568542 12"></polygon>
                    </g>
                  </g>
                </svg>
              </div>

            </div>
          </dt>
          <dd class="answer-<?php echo $key ?> mt-2 hidden">
            <?php echo $faq['answer']; ?>
          </dd>
        </div>
      <?php endforeach; ?>
      <div class="button-sec-outline mt-12 mx-auto" @click="viewAll($event)" style="<?php if (sizeof($faqs['faqs']) <= 5) echo 'display:none' ?>">View all FAQ</div>
    </div>
  </div>
</section>
<div id="fee" style="display:none"></div>

<?php
get_template_part('template-parts/module', 'wide-hero-idme', $pre_footer);
get_footer();
