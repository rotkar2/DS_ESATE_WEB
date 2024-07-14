document.addEventListener('DOMContentLoaded', function() {
    fetch('load_listings.php')
        .then(response => response.json())
        .then(data => {
            const listingsContainer = document.getElementById('listings-container');
            data.forEach(listing => {
                const listingDiv = document.createElement('div');
                listingDiv.className = 'listing';
                listingDiv.innerHTML = `
                    <img src="${listing.photo}" alt="${listing.title}">
                    <h2>${listing.title}</h2>
                    <p>${listing.region}</p>
                    <p>${listing.rooms} δωμάτια</p>
                    <p>Τιμή: €${listing.price} ανά διανυκτέρευση</p>
                    <button onclick="bookProperty(${listing.id})">Κράτηση</button>
                `;
                listingsContainer.appendChild(listingDiv);
            });
        });
});

function bookProperty(id) {
    if (!isLoggedIn) {
        alert('Πρέπει να συνδεθείτε για να κάνετε κράτηση.');
        window.location.href = 'login.php';
    } else {
        window.location.href = `book_property.php?id=${id}`;
    }
}