/* code relatif au paiement stripe */

const stripe = Stripe(stripePublicKey);

const elements = stripe.elements();

const card = elements.create("card");
// on ne s'occupe pas du style pour l'instant

const style = {
base: {
color: "#32325d",
fontFamily: 'Arial, sans-serif',
fontSmoothing: "antialiased",
fontSize: "16px",
"::placeholder": {
color: "#32325d"
}
},
invalid: {
fontFamily: 'Arial, sans-serif',
color: "#fa755a",
iconColor: "#fa755a"
}
};


// Stripe injection de l'iframe dans le DOM
card.mount("#card-element");
card.on("change", function (event) { // Désactiver le bouton si il n'y a pas de détail sur le paiement
document.querySelector("button").disabled = event.empty;
document.querySelector("#card-error").textContent = event.error ? event.error.message : "";
});
const form = document.getElementById("payment-form");
form.addEventListener("submit", function (event) {
  
event.preventDefault();
// Completer le paiement si le bouton submit est cliqué
stripe.confirmCardPayment(clientSecret, {
payment_method: {
card: card
}
}).then(function (result) {
if (result.error) { // Si il y a une erreur la montrer a l'utilisateur
console.log(result.error.message);
} else { // Le paiement est un succès
    // voir les Webhooks de stripe pour que la commande soit mise en payée méme si la redirection ne fonctionne pas
window.location.href = redirectAfterSuccessUrl;
}
});

});