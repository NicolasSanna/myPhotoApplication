import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['results'];

    async onInput(event) {
        const resultsTarget = document.getElementById('results');
        const query = event.target.value;
        if (query.length > 0) 
            {
            try 
            {
                const response = await fetch(`/tag/search/${encodeURIComponent(query)}`);
                if (!response.ok) 
                {
                    
                    throw new Error('Network response was not ok ' + response.statusText);
                }
                const results = await response.json();
                resultsTarget.innerHTML = '';
                for (const result of results) {
                    const li = document.createElement('li');
                    const a = document.createElement('a');
                    a.style.backgroundColor = 'white';
                    li.style.listStyle = 'none';
                    a.href = `/tag/${result.id}`;
                    a.textContent = result.name;
                    li.appendChild(a);
                    resultsTarget.appendChild(li);
                }

            } catch (error) {
                console.error('There was a problem with the fetch operation:', error);
                resultsTarget.innerHTML = '';
            }
        } else {
            resultsTarget.innerHTML = '';
        }
    }
}