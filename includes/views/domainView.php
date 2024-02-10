<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<head>
    <link href="<?php echo plugins_url('css/domainStyles.css', __FILE__); ?>" rel="stylesheet">
</head>
    <div class="domain-wrap">
        <h1 class="domain-heading">Product Carousel Settings</h1>
        <!-- Explanatory card about the importance of domain settings -->
        <div class="domain-card">
            <h2 class="domain-card-title">Why is setting domain important when calling API?</h2>
            <p class="domain-card-text"> For example, we built an e-commerce website www.myshop.com with a Product Carousel feature that displays recommended products by calling API from our own product database.</p>
            <p class="domain-card-text"> We can configure the domain setting so that the Product Carousel on www.myshop.com can call the API only from this domain, not allowing other websites to call the API and display the results.</p>
            <p class="domain-card-text">This method will keep our Product Carousel data secured, preventing it from being used without permission on other websites.</p>
            <p class="domain-card-text"> I tried to keep the translation simple and easy to understand. Please let me know if you need any clarification or have additional example text you would like me to translate.</p>
        </div>

        <form method="post" action="options.php" class="domain-form">
            <?php settings_fields( 'plugin-settings-group' ); ?>
            <?php do_settings_sections( 'plugin-settings-group' ); ?>
            <div id="domains-container" class="domain-container">
                <!-- รายการ domains ที่มีอยู่จะถูกแสดงที่นี่ -->
                <?php
                $domains = get_option('allowed_domains');
                if (!empty($domains)) {
                    foreach ($domains as $domain) {
                        echo '<div class="domain-entry"><input type="text" name="allowed_domains[]" value="'. esc_attr($domain) .'" class="domain-input"><button type="button" onclick="removeDomain(this)" class="domain-remove-button">Remove</button></div>';
                    }
                }
                ?>
            </div>
            <button type="button" class="domain-add-button" onclick="addDomain()">Add Domain</button>
            <?php submit_button('', 'primary', 'submit', true, ['class' => 'domain-submit-button', 'id' => 'submit-button']); ?>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            updateSubmitButtonState();
        });
        function addDomain() {
            var container = document.getElementById('domains-container');
            var newDomain = document.createElement('div');
            newDomain.classList.add('domain-entry');
            newDomain.innerHTML = '<input type="text" name="allowed_domains[]" class="domain-input"><button type="button" onclick="removeDomain(this)" class="domain-remove-button">Remove</button>';
            container.appendChild(newDomain);
            var newInput = newDomain.querySelector('input');
            newInput.addEventListener('input', updateSubmitButtonState);
            updateSubmitButtonState();
        }

        document.addEventListener('DOMContentLoaded', function() {
            var domainInputs = document.querySelectorAll('input[name="allowed_domains[]"]');
            domainInputs.forEach(input => input.addEventListener('input', updateSubmitButtonState));
            updateSubmitButtonState();
        });


        function removeDomain(button) {
            var domainEntry = button.parentNode;
            domainEntry.remove();
        }

        function updateSubmitButtonState() {
            var domains = document.querySelectorAll('input[name="allowed_domains[]"]');
            var isAnyDomainFilled = Array.from(domains).some(input => input.value.trim() !== '');
            document.getElementById('submit-button').disabled = !isAnyDomainFilled;
        }
    </script>
