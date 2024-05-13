import { Controller } from '@hotwired/stimulus';

export default class extends Controller
{
    static targets = ["badge"];

    connect()
    {
        this.getCarts();
    }

    async getCarts()
    {
        let cartBadge = document.getElementById('cart-badge');

        const url = '/cart/async';

        const options = {
            method:'GET'
        };

        try
        {
            const response = await fetch(url, options);

            if (!response.ok) 
            {
                throw new Error('Erreur lors de l\'ajout au panier');
            }

            const data = await response.json();

            cartBadge.textContent = data.cart.length;
        }
        catch (error) 
        {
            console.error("Erreur : ", error);
        }
    }
}