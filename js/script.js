// Simulated order details
const orderDetails = {
    customer: {
        name: "John Doe",
        email: "john.doe@example.com",
        phone: "123-456-7890",
        address: "1234 Elm Street, Some City, ST, 12345"
    },

    shape: "square", // Can be "round", "square", or "heart"
    flavor: "Vanilla",
    size: "2 kg",
    toppings: ["Cherries", "Chocolate Chips"],
    additionalNote: "Please deliver between 5-6 PM"
};

// Function to display order summary
function displayOrderSummary() {
    const orderSummaryDiv = document.getElementById('order-summary');

    
    
    // Create a shape element based on the order details
    const shapeElement = document.createElement('div');
    shapeElement.className = `shape ${orderDetails.shape}`;
    
    // Clear the order summary div before appending new elements
    orderSummaryDiv.innerHTML = '';
    
    // Append the shape element
    orderSummaryDiv.appendChild(shapeElement);
    
    // Append other order details
    orderSummaryDiv.innerHTML += `
        <h3>Customer Details</h3>
        <p><strong>Full Name:</strong> ${orderDetails.customer.name}</p>
        <p><strong>Email:</strong> ${orderDetails.customer.email}</p>
        <p><strong>Phone:</strong> ${orderDetails.customer.phone}</p>
        <p><strong>Address:</strong> ${orderDetails.customer.address}</p>
        <hr>
        <h3>Order Details</h3>
        <p><strong>Shape:</strong> ${orderDetails.shape.charAt(0).toUpperCase() + orderDetails.shape.slice(1)}</p>
        <p><strong>Flavor:</strong> ${orderDetails.flavor}</p>
        <p><strong>Size:</strong> ${orderDetails.size}</p>
        <p><strong>Toppings:</strong> ${orderDetails.toppings.join(', ')}</p>
        <p><strong>Additional Note:</strong> ${orderDetails.additionalNote}</p>
    `;
}

// Function to handle order placement
function placeOrder() {
    alert("Your order has been placed successfully!");
}

// Display the order summary on page load
document.addEventListener('DOMContentLoaded', displayOrderSummary);
