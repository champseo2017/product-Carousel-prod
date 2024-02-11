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
            <li>2: The Carousel has 3 language options to choose from: Thai, English, Chinese.</li>
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
