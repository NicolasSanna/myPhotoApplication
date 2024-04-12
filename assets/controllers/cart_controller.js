import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["badge"];
  
    async addToCart(event) {
        event.preventDefault();

        let productId = this.element.dataset.productId;
        let cartBadge = document.getElementById('cart-badge');

        try 
        {

            const response = await fetch(`/cart/add/${productId}`, { method: "GET" });

            if (!response.ok) 
            {
                throw new Error('Erreur lors de l\'ajout au panier');
            }

            const data = await response.json();

            cartBadge.textContent = data.cart.length;

            alert(`Produit ajouté au panier !, il y a ${data.cart.length} produit(s) dans le panier`);
        } 
        catch (error) 
        {
            console.error("Erreur lors de l'ajout au panier :", error);
        }
    }
}