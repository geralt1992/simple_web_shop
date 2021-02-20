@extends('layouts.app')
@section('style')

<style>

.StripeElement{
    box-sizing: border-box;
    height: 40px;
    padding: 10px 12pc;
    border: 1px solid transparent;
    border-radius: 4px;
    background-color: white;
    box-shadow: 0 1px 3px #e6ebf1;
    -webkit-transition: box-shadow 150ms ease;
    transition: box-shadow 150ms ease;
}

.StripeElement--focus{
    box-shadow: 0 1px 3px #cfd7df;
}

.StripeElement--invalid{
    border-color: #fa755a;
}

.StripeElement--webkit-autofill{
    border-color: #fefde5 !important;
}

</style>
@endsection



@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-9">
            <p class="mb-4">
            Total Amount is <strong> {{$amount}} $</strong>
            </p>

        <form action="{{route('cart.charge' , $amount)}}" method="post" id="payment-form">
                @csrf

                <div class="">

                  <label for="card-element">
                    Credit or debit card
                  </label>
                  <div id="card-element">
                    <!-- A Stripe Element will be inserted here. -->
                  </div>

                  <!-- Used to display Element errors. -->
                  <div id="card-errors" role="alert"></div>
                </div>

                <button class="btn btn-primary btn-md">Submit Payment</button>

                <div id="loading" style="display:none;">Payment is in process . čekaj brate</div>
              </form>

        </div>
    </div>
</div>


@endsection

@section('script')
<script src="https://js.stripe.com/v3/"></script>
<script>
    window.onload = function () {
        var stripe = Stripe('pk_test_yuqis1GmzZJ9mHJaek69WInE00VWGlLc05');
        var elements = stripe.elements();
        var style = {
                base: {
                 // Add your base input styles here. For example:
                 fontSize: '16px',
                 color: '#32325d',
                 },
                    };

// Create an instance of the card Element.
var card = elements.create('card', {style: style});

// Add an instance of the card Element into the `card-element` <div>.
card.mount('#card-element');



card.addEventListener('change', function(event) {
    var displayError = document.getElementById('card-errors');
    if(event.error){
        displayError.textContent = event.error.message;
    }else{
        displayError.textContent = '';
    }
});

var form = document.getElementById('payment-form');
form.addEventListener('submit', function(event) {
  event.preventDefault();

  stripe.createToken(card).then(function(result) {
    if (result.error) {
      // Inform the customer that there was an error.
      var errorElement = document.getElementById('card-errors');
      errorElement.textContent = result.error.message;
    } else {
      // Send the token to your server.
      stripeTokenHandler(result.token);
    }
  });
});

function stripeTokenHandler(token) {
  // Insert the token ID into the form so it gets submitted to the server
  var form = document.getElementById('payment-form');
  var hiddenInput = document.createElement('input');
  hiddenInput.setAttribute('type', 'hidden');
  hiddenInput.setAttribute('name', 'stripeToken');
  hiddenInput.setAttribute('value', token.id);
  form.appendChild(hiddenInput);

  // Submit the form
  var loading = document.getElementById('loading');
  loading.style.display = "block";
  form.submit();
}








     }
</script>
@endsection
