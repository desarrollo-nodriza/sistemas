// Your web app's Firebase configuration
var firebaseConfig = {
	apiKey: "AIzaSyD59itPgCqTL3XsG4mxqUj-yneuXVjlSEY",
	authDomain: "sistema-nodriza.firebaseapp.com",
	databaseURL: "https://sistema-nodriza.firebaseio.com",
	projectId: "sistema-nodriza",
	storageBucket: "sistema-nodriza.appspot.com",
	messagingSenderId: "192444218344",
	appId: "1:192444218344:web:64f76310b36b379476825d"
};

firebase.initializeApp(firebaseConfig);

var ui = new firebaseui.auth.AuthUI(firebase.auth());

var uiConfig = {
	callbacks: {
		signInSuccessWithAuthResult: function(authResult, redirectUrl) {
			// User successfully signed in.
			// Return type determines whether we continue the redirect automatically
			// or whether we leave that to developer to handle.
			var email = authResult.user.email;

			$('#LoginForm').addClass('hidden');
			$('#texto-bienvenida-login').addClass('hidden');
			$('#texto-exito-login').removeClass('hidden');

			$('#AdministradorEmail').val(email);
			$('#AdministradorLoginExterno').val(1);

			$('#LoginForm').submit();
		
		},
		uiShown: function() {
			// The widget is rendered.
			// Hide the loader.
			document.getElementById('loader').style.display = 'none';
		}
	},
	// Will use popup for IDP Providers sign-in flow instead of the default, redirect.
	signInFlow: 'popup',
	//signInSuccessUrl: '<url-to-redirect-to-on-success>',
	signInOptions: [
		// Leave the lines as is for the providers you want to offer your users.
		firebase.auth.GoogleAuthProvider.PROVIDER_ID,
		//firebase.auth.FacebookAuthProvider.PROVIDER_ID,
		//firebase.auth.TwitterAuthProvider.PROVIDER_ID,
		//firebase.auth.GithubAuthProvider.PROVIDER_ID,
		//firebase.auth.EmailAuthProvider.PROVIDER_ID,
		//firebase.auth.PhoneAuthProvider.PROVIDER_ID
	],
	// Terms of service url.
	//tosUrl: '<your-tos-url>',
	// Privacy policy url.
	//privacyPolicyUrl: '<your-privacy-policy-url>'
};

$(document).ready(function(){
	if ($('#LoginForm').length) {

		firebase.auth().onAuthStateChanged(function(user) {
			if (user) {
				var email = user.email;

				$('#LoginForm').addClass('hidden');
				$('#texto-bienvenida-login').addClass('hidden');
				$('#texto-exito-login').removeClass('hidden');

				$('#AdministradorEmail').val(email);
				$('#AdministradorLoginExterno').val(1);

				$('#LoginForm').submit();
			} else {
				// User is signed out.
				ui.start('#firebaseui-auth-container', uiConfig);
			}
		});

	}

	if ($('#logout').length) {

		$('#logout').on('click', function(e){
			e.preventDefault();
			var url = $(this).attr('href');
			firebase.auth().signOut().then(function() {
			  window.location.href = url;
			}).catch(function(error) {

				noty({text: error, layout: 'topRight', type: 'error'});
				setTimeout(function(){
					$.noty.closeAll();
				}, 10000);

			  return false;
			});
		});
	}
});