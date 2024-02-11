<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<head>
    <link href="<?php echo plugins_url('css/howToUseStyles.css', __FILE__); ?>" rel="stylesheet">
</head>
    <div class="domain-wrap">
        <h1 class="domain-heading">How to Use Plugin Product Carousel</h1>
        <div class="steps-wrap">
    <ul class="steps-list">
        <li class="step" data-step="step1">Step 1 : Setting domain</li>
        <li class="step" data-step="step2">Step 2 : Add New Carousel</li>
        <li class="step" data-step="step3">Step 3: View the details of the Carousel that was added.</li>
        <li class="step" data-step="step4">Step 4: You can view the Products in the Carousel that you added.</li>
        <li class="step" data-step="step5">Step 5: How to use ShortCode</li>
    </ul>
    <div class="step-content" id="step1" style="display:none;">
        <p>Instructions</p>
        <ul class="step-content-detail">
            <li>1: Add domain to Product Carousel Settings example: http://test01.local
                <div>
                    <img src="<?php echo plugins_url('./assets/step1_01.png', __FILE__); ?>" alt="Add domain">
                </div>
            </li>
        </ul>
        </div>
        <div class="step-content" id="step2" style="display:none;">
        <p>Instructions</p>
        <ul class="step-content-detail">
            <li>1: Add New Product Carousel to Add New Carousel menu.
            <div>
                    <img src="<?php echo plugins_url('./assets/S1_02.png', __FILE__); ?>" alt="Add domain">
                </div>
            </li>
            <li>2: The Carousel has 3 language options to choose from: Thai, English, Chinese.</li>
        </ul>
        </div>
        <div class="step-content" id="step3" style="display:none;">
        <p>Instructions</p>
        <ul class="step-content-detail">
            <li>1: View the details of the Carousel that was created at the List Carousel menu.
            <div>
                    <img src="<?php echo plugins_url('./assets/S3_03_02.png', __FILE__); ?>" alt="Add domain">
                </div>
            </li>
            <li>2: You can filter the Carousel by each language.
            <div>
                    <img src="<?php echo plugins_url('./assets/S3_04.png', __FILE__); ?>" alt="Add domain">
                </div>
            </li>
            <li>3: You can add products into each Carousel ID.
            <div>
                    <img src="<?php echo plugins_url('./assets/S3_05.png', __FILE__); ?>" alt="Add domain">
                </div>
            </li>
            <li>4: You can add Product details in the Carousel
            <div>
                    <img src="<?php echo plugins_url('./assets/S3_06.png', __FILE__); ?>" alt="Add domain">
                </div>
                <ul>
                    <li>
                        <div>
                            <p>1. Product name</p>
                        </div>
                        <div>
                            <p>2. Description</p>
                        </div>
                        <div>
                            <p>3. Product Link</p>
                        </div>
                        <div>
                            <p>4. Status default is draft.</p>
                            <div>
                    <img src="<?php echo plugins_url('./assets/S3_07.png', __FILE__); ?>" alt="Add domain">
                            </div>
                        </div>
                        <div>
                            <p>5. Select Image ( Image from Library )</p>
                            <div>
                    <img src="<?php echo plugins_url('./assets/S3_0801.png', __FILE__); ?>" alt="Add domain">
                            </div>
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
        </div>
        <div class="step-content" id="step4" style="display:none;">
        <p>Instructions</p>
        <ul class="step-content-detail">
        <li>1: You can click on List Product in Carousel to view the Products that have been added to the Carousel.
            <div>
                    <img src="<?php echo plugins_url('./assets/S3_09.png', __FILE__); ?>" alt="Add domain">
                </div>
            </li>
            <li>2: You will see the Products that have been created here.
            <div>
                    <img src="<?php echo plugins_url('./assets/S3_10.png', __FILE__); ?>" alt="Add domain">
                </div>
            </li>
        </ul>
        </div>
        <div class="step-content" id="step5" style="display:none;">
            <p>Instructions</p>
            <ul class="step-content-detail">
            <li>1: Change the Status of the Carousel to Public.
            <div>
                    <img src="<?php echo plugins_url('./assets/S3_11.png', __FILE__); ?>" alt="Add domain">
                </div>
            </li>
            <li>2: Copy the Carousel ID to use the Shortcode [product_carousel carousel_id="272"]
            <div>
                    <img src="<?php echo plugins_url('./assets/S3_12.png', __FILE__); ?>" alt="Add domain">
                </div>
            </li>
            <li>3: Use the Shortcode [product_carousel carousel_id="272"] on the desired post or page.
            <div>
                    <img src="<?php echo plugins_url('./assets/S3_13.png', __FILE__); ?>" alt="Add domain">
                </div>
            </li>
            <li>4: When you use the Shortcode you will see the following information:
            <div>
                    <img src="<?php echo plugins_url('./assets/S3_14.png', __FILE__); ?>" alt="Add domain">
                </div>
            </li>
            </ul>
        </div>
</div>

    </div>

    <script>
    jQuery(document).ready(function($) {
        // Directly add 'active' class to the first step and show its content
        $('.step').first().addClass('active');
        $('#step1').show();

        $('.step').click(function() {
            var stepToShow = $(this).data('step');
            $('.step-content').hide();
            $('#' + stepToShow).show();

            $('.step').removeClass('active');
            $(this).addClass('active');
        });
    });
</script>
