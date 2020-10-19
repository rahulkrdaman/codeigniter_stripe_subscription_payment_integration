<!DOCTYPE html>
<html lang="en-US">
<head>
<title>Stripe Subscription Payment API Integration by CodexWorld</title>
<meta charset="utf-8">

<!-- Stylesheet file -->
<link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
	
<!-- Stripe JS library -->
<script src="https://js.stripe.com/v3/"></script>
</head>
<body>
<div class="container">
	<h1>Stripe Subscription Payment Gateway Integration</h1>
	<div class="panel">
		<form action="" method="POST" id="paymentFrm">
			<div class="panel-heading">
				<h3 class="panel-title">Plan Subscription with Stripe</h3>
				<!-- Plan Info -->
				<p>
					<b>Select Plan:</b>
					<select name="subscr_plan" id="subscr_plan">
						<?php foreach($plans as $plan){ ?>
						<option value="<?php echo $plan['id']; ?>"><?php echo $plan['name'].' [$'.$plan['price'].'/'.$plan['interval'].']'; ?></option>
						<?php } ?>
					</select>
				</p>
			</div>
			<div class="panel-body">
				<!-- Display errors returned by createToken -->
				<div id="paymentResponse"></div>
				
				<!-- Payment form -->
				<div class="form-group">
					<label>NAME</label>
					<input type="text" name="name" id="name" class="field" placeholder="Enter name" required="" autofocus="">
				</div>
				<div class="form-group">
					<label>EMAIL</label>
					<input type="email" name="email" id="email" class="field" placeholder="Enter email" required="">
				</div>
				<div class="form-group">
					<label>CARD NUMBER</label>
					<div id="card_number" class="field"></div>
				</div>
				<div class="row">
					<div class="left">
						<div class="form-group">
							<label>EXPIRY DATE</label>
							<div id="card_expiry" class="field"></div>
						</div>
					</div>
					<div class="right">
						<div class="form-group">
							<label>CVC CODE</label>
							<div id="card_cvc" class="field"></div>
						</div>
					</div>
				</div>
				<button type="submit" class="btn btn-success" id="payBtn">Submit Payment</button>
			</div>
		</form>
	</div>
</div>

<script>
// Set your publishable API key
var stripe = Stripe('<?php echo $this->config->item('stripe_publishable_key'); ?>');

// Create an instance of elements
var elements = stripe.elements();

var style = {
    base: {
		fontWeight: 400,
		fontFamily: 'Roboto, Open Sans, Segoe UI, sans-serif',
		fontSize: '16px',
		lineHeight: '1.4',
		color: '#555',
		backgroundColor: '#fff',
		'::placeholder': {
			color: '#888',
		},
	},
	invalid: {
	  color: '#eb1c26',
	}
};

var cardElement = elements.create('cardNumber', {
	style: style
});
cardElement.mount('#card_number');

var exp = elements.create('cardExpiry', {
  'style': style
});
exp.mount('#card_expiry');

var cvc = elements.create('cardCvc', {
  'style': style
});
cvc.mount('#card_cvc');

// Validate input of the card elements
var resultContainer = document.getElementById('paymentResponse');
cardElement.addEventListener('change', function(event) {
	if (event.error) {
		resultContainer.innerHTML = '<p>'+event.error.message+'</p>';
	}else{
		resultContainer.innerHTML = '';
	}
});

// Get payment form element
var form = document.getElementById('paymentFrm');

// Create a token when the form is submitted.
form.addEventListener('submit', function(e) {
	e.preventDefault();
	createToken();
});

// Create single-use token to charge the user
function createToken() {
	stripe.createToken(cardElement).then(function(result) {
		if (result.error) {
			// Inform the user if there was an error
			resultContainer.innerHTML = '<p>'+result.error.message+'</p>';
		} else {
			// Send the token to your server
			stripeTokenHandler(result.token);
		}
	});
}

// Callback to handle the response from stripe
function stripeTokenHandler(token) {
	// Insert the token ID into the form so it gets submitted to the server
	var hiddenInput = document.createElement('input');
	hiddenInput.setAttribute('type', 'hidden');
	hiddenInput.setAttribute('name', 'stripeToken');
	hiddenInput.setAttribute('value', token.id);
	form.appendChild(hiddenInput);
	
	// Submit the form
	form.submit();
}
</script>
</body>
</html>