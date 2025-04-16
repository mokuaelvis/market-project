<form action="charge.php" method="POST" id="payment-form">
  <div id="card-element">
    <!-- A Stripe Element will be inserted here. -->
  </div>

  <!-- Used to display form errors. -->
  <div id="card-errors" role="alert"></div>

  <button type="submit">Submit Payment</button>
</form>

<script src="https://js.stripe.com/v3/"></script>
<script>
  var stripe = Stripe('your-publishable-key-here');
  var elements = stripe.elements();
  var card = elements.create('card');
  card.mount('#card-element');

  var form = document.getElementById('payment-form');
  form.addEventListener('submit', function(event) {
    event.preventDefault();

    stripe.createToken(card).then(function(result) {
      if (result.error) {
        // Display error.message in your UI.
        var errorElement = document.getElementById('card-errors');
        errorElement.textContent = result.error.message;
      } else {
        // Send the token to your server.
        stripeTokenHandler(result.token);
      }
    });
  });

  function stripeTokenHandler(token) {
    var form = document.getElementById('payment-form');
    var hiddenInput = document.createElement('input');
    hiddenInput.setAttribute('type', 'hidden');
    hiddenInput.setAttribute('name', 'stripeToken');
    hiddenInput.setAttribute('value', token.id);
    form.appendChild(hiddenInput);

    form.submit();
  }
</script>
